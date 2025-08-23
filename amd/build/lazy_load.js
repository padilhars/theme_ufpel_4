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
    var LazyLoad = {
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
         * @type {Object}
         */
        observers: {},

        /**
         * Configuration object.
         * 
         * @type {Object}
         */
        config: {},

        /**
         * Initialize lazy loading.
         * 
         * @method init
         * @param {Object} config Configuration options
         * @return {void}
         */
        init: function(config) {
            var pendingPromise = new Pending('theme_ufpel/lazy_load:init');
            
            // Merge config with defaults
            this.config = this.extend({}, this.defaults, config || {});

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
            var observerOptions = {
                rootMargin: this.config.rootMargin,
                threshold: this.config.threshold
            };

            var self = this;
            var imageObserver = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        self.loadElement(entry.target);
                        imageObserver.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            this.observers.images = imageObserver;
        },

        /**
         * Setup background image observer.
         * 
         * @method setupBackgroundObserver
         * @return {void}
         */
        setupBackgroundObserver: function() {
            var observerOptions = {
                rootMargin: this.config.rootMargin,
                threshold: this.config.threshold
            };

            var self = this;
            var backgroundObserver = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        self.loadBackgroundImage(entry.target);
                        backgroundObserver.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            this.observers.backgrounds = backgroundObserver;
        },

        /**
         * Setup mutation observer for dynamic content.
         * 
         * @method setupMutationObserver
         * @return {void}
         */
        setupMutationObserver: function() {
            var self = this;
            var mutationObserver = new MutationObserver(function(mutations) {
                var shouldScan = false;
                
                mutations.forEach(function(mutation) {
                    // Check if lazy load elements were added
                    if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                        mutation.addedNodes.forEach(function(node) {
                            if (node.nodeType === 1) { // Element node
                                if (node.matches && (
                                    node.matches(self.config.selector) ||
                                    node.matches(self.config.backgroundSelector) ||
                                    node.querySelector(self.config.selector) ||
                                    node.querySelector(self.config.backgroundSelector)
                                )) {
                                    shouldScan = true;
                                }
                            }
                        });
                    }
                });
                
                if (shouldScan) {
                    self.scan();
                }
            });

            mutationObserver.observe(document.body, {
                childList: true,
                subtree: true
            });

            this.observers.mutations = mutationObserver;
        },

        /**
         * Scan for lazy load elements.
         * 
         * @method scan
         * @return {void}
         */
        scan: function() {
            // Scan for images and iframes
            var elements = document.querySelectorAll(this.config.selector);
            var imageObserver = this.observers.images;
            var self = this;
            
            if (imageObserver) {
                elements.forEach(function(element) {
                    if (!element.classList.contains(self.config.loadedClass) &&
                        !element.classList.contains(self.config.loadingClass)) {
                        imageObserver.observe(element);
                    }
                });
            }

            // Scan for background images
            var bgElements = document.querySelectorAll(this.config.backgroundSelector);
            var backgroundObserver = this.observers.backgrounds;
            
            if (backgroundObserver) {
                bgElements.forEach(function(element) {
                    if (!element.classList.contains(self.config.loadedClass) &&
                        !element.classList.contains(self.config.loadingClass)) {
                        backgroundObserver.observe(element);
                    }
                });
            }
        },

        /**
         * Load an element (image or iframe).
         * 
         * @method loadElement
         * @param {HTMLElement} element The element to load
         * @return {void}
         */
        loadElement: function(element) {
            var src = element.dataset.src;
            var srcset = element.dataset.srcset;
            
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
            var tempImg = new Image();
            var self = this;
            
            tempImg.onload = function() {
                if (srcset) {
                    img.srcset = srcset;
                }
                if (src) {
                    img.src = src;
                }
                
                img.classList.remove(self.config.loadingClass);
                img.classList.add(self.config.loadedClass);
                
                // Remove data attributes
                delete img.dataset.src;
                delete img.dataset.srcset;
                
                // Fade in effect
                self.fadeIn(img);
                
                // Call success callback
                if (typeof self.config.successCallback === 'function') {
                    self.config.successCallback(img);
                }
            };
            
            tempImg.onerror = function() {
                img.classList.remove(self.config.loadingClass);
                img.classList.add(self.config.errorClass);
                
                // Set fallback image if available
                if (img.dataset.fallback) {
                    img.src = img.dataset.fallback;
                }
                
                // Call error callback
                if (typeof self.config.errorCallback === 'function') {
                    self.config.errorCallback(img);
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
            var self = this;
            
            iframe.onload = function() {
                iframe.classList.remove(self.config.loadingClass);
                iframe.classList.add(self.config.loadedClass);
                
                // Remove data attribute
                delete iframe.dataset.src;
                
                // Fade in effect
                self.fadeIn(iframe);
                
                // Call success callback
                if (typeof self.config.successCallback === 'function') {
                    self.config.successCallback(iframe);
                }
            };
            
            iframe.onerror = function() {
                iframe.classList.remove(self.config.loadingClass);
                iframe.classList.add(self.config.errorClass);
                
                // Call error callback
                if (typeof self.config.errorCallback === 'function') {
                    self.config.errorCallback(iframe);
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
            var url = element.dataset.backgroundUrl;
            
            if (!url) {
                return;
            }

            element.classList.add(this.config.loadingClass);

            var tempImg = new Image();
            var self = this;
            
            tempImg.onload = function() {
                element.style.backgroundImage = "url('" + url + "')";
                element.classList.remove(self.config.loadingClass);
                element.classList.add(self.config.loadedClass);
                
                // Remove data attribute
                delete element.dataset.backgroundUrl;
                
                // Fade in effect
                self.fadeIn(element);
                
                // Call success callback
                if (typeof self.config.successCallback === 'function') {
                    self.config.successCallback(element);
                }
            };
            
            tempImg.onerror = function() {
                element.classList.remove(self.config.loadingClass);
                element.classList.add(self.config.errorClass);
                
                // Set fallback if available
                if (element.dataset.fallbackColor) {
                    element.style.backgroundColor = element.dataset.fallbackColor;
                }
                
                // Call error callback
                if (typeof self.config.errorCallback === 'function') {
                    self.config.errorCallback(element);
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
            if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                return;
            }

            element.style.opacity = '0';
            element.style.transition = 'opacity 0.5s ease-in-out';
            
            // Force reflow
            element.offsetHeight;
            
            requestAnimationFrame(function() {
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
            var elements = document.querySelectorAll(this.config.selector);
            var self = this;
            
            elements.forEach(function(element) {
                self.loadElement(element);
            });

            // Load background images
            var bgElements = document.querySelectorAll(this.config.backgroundSelector);
            bgElements.forEach(function(element) {
                self.loadBackgroundImage(element);
            });
        },

        /**
         * Destroy all observers.
         * 
         * @method destroy
         * @return {void}
         */
        destroy: function() {
            var self = this;
            Object.keys(this.observers).forEach(function(key) {
                var observer = self.observers[key];
                if (observer && observer.disconnect) {
                    observer.disconnect();
                }
            });
            this.observers = {};
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
                elements = Array.prototype.slice.call(elements);
            }

            var self = this;
            elements.forEach(function(element) {
                if (element.matches && element.matches(self.config.selector)) {
                    self.loadElement(element);
                } else if (element.matches && element.matches(self.config.backgroundSelector)) {
                    self.loadBackgroundImage(element);
                }
            });
        },

        /**
         * Utility function to extend objects.
         * 
         * @method extend
         * @param {Object} target Target object
         * @param {...Object} sources Source objects
         * @return {Object} Extended object
         */
        extend: function(target) {
            var sources = Array.prototype.slice.call(arguments, 1);
            
            sources.forEach(function(source) {
                if (source) {
                    Object.keys(source).forEach(function(key) {
                        target[key] = source[key];
                    });
                }
            });
            
            return target;
        }
    };

    return LazyLoad;
});