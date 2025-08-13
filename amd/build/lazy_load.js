// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Lazy loading module for UFPel theme.
 *
 * @module     theme_ufpel/lazy_load
 * @copyright  2025 Universidade Federal de Pelotas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['core/pending'], function(Pending) {
    'use strict';

    /**
     * Lazy loading module for images and iframes.
     * 
     * @class
     */
    const LazyLoad = {
        /**
         * Default configuration.
         * 
         * @type {Object}
         */
        defaults: {
            selector: 'img.lazyload, iframe.lazyload',
            rootMargin: '50px 0px',
            threshold: 0.01,
            loadedClass: 'loaded',
            loadingClass: 'loading',
            errorClass: 'error',
            backgroundSelector: '[data-background-url]',
            successCallback: null,
            errorCallback: null
        },

        /**
         * Active observers.
         * 
         * @type {Map}
         */
        observers: new Map(),

        /**
         * Initialize lazy loading.
         * 
         * @method init
         * @param {Object} config Configuration options
         * @return {void}
         */
        init: function(config = {}) {
            const pendingPromise = new Pending('theme_ufpel/lazy_load:init');
            
            // Merge config with defaults
            this.config = Object.assign({}, this.defaults, config);

            // Check for IntersectionObserver support
            if (!('IntersectionObserver' in window)) {
                this.loadAllImages();
                pendingPromise.resolve();
                return;
            }

            // Setup observers
            this.setupImageObserver();
            this.setupBackgroundObserver();
            
            // Initial scan
            this.scan();

            // Listen for dynamic content
            this.setupMutationObserver();
            
            pendingPromise.resolve();
        },

        /**
         * Setup image observer.
         * 
         * @method setupImageObserver
         * @return {void}
         */
        setupImageObserver: function() {
            const observerOptions = {
                rootMargin: this.config.rootMargin,
                threshold: this.config.threshold
            };

            const imageObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        this.loadElement(entry.target);
                        imageObserver.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            this.observers.set('images', imageObserver);
        },

        /**
         * Setup background image observer.
         * 
         * @method setupBackgroundObserver
         * @return {void}
         */
        setupBackgroundObserver: function() {
            const observerOptions = {
                rootMargin: this.config.rootMargin,
                threshold: this.config.threshold
            };

            const backgroundObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        this.loadBackgroundImage(entry.target);
                        backgroundObserver.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            this.observers.set('backgrounds', backgroundObserver);
        },

        /**
         * Setup mutation observer for dynamic content.
         * 
         * @method setupMutationObserver
         * @return {void}
         */
        setupMutationObserver: function() {
            const mutationObserver = new MutationObserver((mutations) => {
                let shouldScan = false;
                
                mutations.forEach(mutation => {
                    // Check if lazy load elements were added
                    if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                        mutation.addedNodes.forEach(node => {
                            if (node.nodeType === 1) { // Element node
                                if (node.matches && (
                                    node.matches(this.config.selector) ||
                                    node.matches(this.config.backgroundSelector) ||
                                    node.querySelector(this.config.selector) ||
                                    node.querySelector(this.config.backgroundSelector)
                                )) {
                                    shouldScan = true;
                                }
                            }
                        });
                    }
                });
                
                if (shouldScan) {
                    this.scan();
                }
            });

            mutationObserver.observe(document.body, {
                childList: true,
                subtree: true
            });

            this.observers.set('mutations', mutationObserver);
        },

        /**
         * Scan for lazy load elements.
         * 
         * @method scan
         * @return {void}
         */
        scan: function() {
            // Scan for images and iframes
            const elements = document.querySelectorAll(this.config.selector);
            const imageObserver = this.observers.get('images');
            
            elements.forEach(element => {
                if (!element.classList.contains(this.config.loadedClass) &&
                    !element.classList.contains(this.config.loadingClass)) {
                    imageObserver.observe(element);
                }
            });

            // Scan for background images
            const bgElements = document.querySelectorAll(this.config.backgroundSelector);
            const backgroundObserver = this.observers.get('backgrounds');
            
            bgElements.forEach(element => {
                if (!element.classList.contains(this.config.loadedClass) &&
                    !element.classList.contains(this.config.loadingClass)) {
                    backgroundObserver.observe(element);
                }
            });
        },

        /**
         * Load an element (image or iframe).
         * 
         * @method loadElement
         * @param {HTMLElement} element The element to load
         * @return {void}
         */
        loadElement: function(element) {
            const src = element.dataset.src;
            const srcset = element.dataset.srcset;
            
            if (!src && !srcset) {
                return;
            }

            element.classList.add(this.config.loadingClass);

            if (element.tagName === 'IMG') {
                this.loadImage(element, src, srcset);
            } else if (element.tagName === 'IFRAME') {
                this.loadIframe(element, src);
            }
        },

        /**
         * Load an image.
         * 
         * @method loadImage
         * @param {HTMLImageElement} img The image element
         * @param {string} src The image source
         * @param {string} srcset The image srcset
         * @return {void}
         */
        loadImage: function(img, src, srcset) {
            const tempImg = new Image();
            
            tempImg.onload = () => {
                if (srcset) {
                    img.srcset = srcset;
                }
                if (src) {
                    img.src = src;
                }
                
                img.classList.remove(this.config.loadingClass);
                img.classList.add(this.config.loadedClass);
                
                // Remove data attributes
                delete img.dataset.src;
                delete img.dataset.srcset;
                
                // Fade in effect
                this.fadeIn(img);
                
                // Call success callback
                if (typeof this.config.successCallback === 'function') {
                    this.config.successCallback(img);
                }
            };
            
            tempImg.onerror = () => {
                img.classList.remove(this.config.loadingClass);
                img.classList.add(this.config.errorClass);
                
                // Set fallback image if available
                if (img.dataset.fallback) {
                    img.src = img.dataset.fallback;
                }
                
                // Call error callback
                if (typeof this.config.errorCallback === 'function') {
                    this.config.errorCallback(img);
                }
            };
            
            // Start loading
            if (srcset) {
                tempImg.srcset = srcset;
            }
            if (src) {
                tempImg.src = src;
            }
        },

        /**
         * Load an iframe.
         * 
         * @method loadIframe
         * @param {HTMLIFrameElement} iframe The iframe element
         * @param {string} src The iframe source
         * @return {void}
         */
        loadIframe: function(iframe, src) {
            iframe.onload = () => {
                iframe.classList.remove(this.config.loadingClass);
                iframe.classList.add(this.config.loadedClass);
                
                // Remove data attribute
                delete iframe.dataset.src;
                
                // Fade in effect
                this.fadeIn(iframe);
                
                // Call success callback
                if (typeof this.config.successCallback === 'function') {
                    this.config.successCallback(iframe);
                }
            };
            
            iframe.onerror = () => {
                iframe.classList.remove(this.config.loadingClass);
                iframe.classList.add(this.config.errorClass);
                
                // Call error callback
                if (typeof this.config.errorCallback === 'function') {
                    this.config.errorCallback(iframe);
                }
            };
            
            iframe.src = src;
        },

        /**
         * Load background image.
         * 
         * @method loadBackgroundImage
         * @param {HTMLElement} element The element with background image
         * @return {void}
         */
        loadBackgroundImage: function(element) {
            const url = element.dataset.backgroundUrl;
            
            if (!url) {
                return;
            }

            element.classList.add(this.config.loadingClass);

            const tempImg = new Image();
            
            tempImg.onload = () => {
                element.style.backgroundImage = `url('${url}')`;
                element.classList.remove(this.config.loadingClass);
                element.classList.add(this.config.loadedClass);
                
                // Remove data attribute
                delete element.dataset.backgroundUrl;
                
                // Fade in effect
                this.fadeIn(element);
                
                // Call success callback
                if (typeof this.config.successCallback === 'function') {
                    this.config.successCallback(element);
                }
            };
            
            tempImg.onerror = () => {
                element.classList.remove(this.config.loadingClass);
                element.classList.add(this.config.errorClass);
                
                // Set fallback if available
                if (element.dataset.fallbackColor) {
                    element.style.backgroundColor = element.dataset.fallbackColor;
                }
                
                // Call error callback
                if (typeof this.config.errorCallback === 'function') {
                    this.config.errorCallback(element);
                }
            };
            
            tempImg.src = url;
        },

        /**
         * Fade in effect for loaded elements.
         * 
         * @method fadeIn
         * @param {HTMLElement} element The element to fade in
         * @return {void}
         */
        fadeIn: function(element) {
            // Check for reduced motion preference
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                return;
            }

            element.style.opacity = '0';
            element.style.transition = 'opacity 0.5s ease-in-out';
            
            // Force reflow
            element.offsetHeight;
            
            requestAnimationFrame(() => {
                element.style.opacity = '1';
            });
        },

        /**
         * Load all images immediately (fallback for no IntersectionObserver).
         * 
         * @method loadAllImages
         * @return {void}
         */
        loadAllImages: function() {
            // Load regular images
            const elements = document.querySelectorAll(this.config.selector);
            elements.forEach(element => {
                this.loadElement(element);
            });

            // Load background images
            const bgElements = document.querySelectorAll(this.config.backgroundSelector);
            bgElements.forEach(element => {
                this.loadBackgroundImage(element);
            });
        },

        /**
         * Destroy all observers.
         * 
         * @method destroy
         * @return {void}
         */
        destroy: function() {
            this.observers.forEach(observer => {
                if (observer.disconnect) {
                    observer.disconnect();
                }
            });
            this.observers.clear();
        },

        /**
         * Force load specific elements.
         * 
         * @method forceLoad
         * @param {NodeList|Array|HTMLElement} elements Elements to force load
         * @return {void}
         */
        forceLoad: function(elements) {
            // Convert to array if needed
            if (elements instanceof HTMLElement) {
                elements = [elements];
            } else if (elements instanceof NodeList) {
                elements = Array.from(elements);
            }

            elements.forEach(element => {
                if (element.matches(this.config.selector)) {
                    this.loadElement(element);
                } else if (element.matches(this.config.backgroundSelector)) {
                    this.loadBackgroundImage(element);
                }
            });
        }
    };

    return LazyLoad;
});