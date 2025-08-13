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
 * Course header JavaScript module for UFPel theme.
 *
 * @module     theme_ufpel/course_header
 * @copyright  2025 Universidade Federal de Pelotas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([
    'jquery',
    'core/ajax',
    'core/notification',
    'core/str',
    'core/templates',
    'core/pending'
], function(
    $,
    Ajax,
    Notification,
    Str,
    Templates,
    Pending
) {
    'use strict';

    /**
     * Course header module.
     * 
     * @class
     */
    const CourseHeader = {
        /**
         * Selectors.
         * 
         * @type {Object}
         */
        SELECTORS: {
            WRAPPER: '.ufpel-course-header-wrapper',
            HEADER: '.ufpel-course-header',
            BACKGROUND: '.ufpel-course-header-background',
            BACKGROUND_IMAGE: '.ufpel-course-header-bg-image',
            CONTENT: '.ufpel-course-header-content',
            PROGRESS: '.ufpel-course-progress',
            PROGRESS_BAR: '.progress-bar',
            META: '.ufpel-course-meta',
            EXPAND_BTN: '[data-action="expand-header"]',
            COLLAPSE_BTN: '[data-action="collapse-header"]'
        },

        /**
         * CSS classes.
         * 
         * @type {Object}
         */
        CLASSES: {
            EXPANDED: 'expanded',
            COLLAPSED: 'collapsed',
            LOADING: 'loading',
            HAS_BACKGROUND: 'has-background',
            PARALLAX: 'parallax-enabled',
            LOADED: 'loaded'
        },

        /**
         * Initialize the course header.
         * 
         * @method init
         * @param {HTMLElement|string} element The course header element or selector
         * @return {void}
         */
        init: function(element) {
            const pendingPromise = new Pending('theme_ufpel/course_header:init');
            
            this.element = typeof element === 'string' ? document.querySelector(element) : element;
            
            if (!this.element) {
                pendingPromise.resolve();
                return;
            }

            this.wrapper = this.element.closest(this.SELECTORS.WRAPPER);
            
            // Load any lazy images immediately for course header
            this.loadLazyImages();
            this.setupBackgroundImage();
            this.setupParallaxEffect();
            this.setupProgressAnimation();
            this.setupEventListeners();
            
            pendingPromise.resolve();
        },

        /**
         * Load lazy images in the course header immediately.
         * 
         * @method loadLazyImages
         * @return {void}
         */
        loadLazyImages: function() {
            // Find all images with data-src in the course header
            const lazyImages = this.element.querySelectorAll('img[data-src]');
            
            lazyImages.forEach((img) => {
                const src = img.dataset.src;
                if (src) {
                    // Load the image
                    const tempImg = new Image();
                    tempImg.onload = () => {
                        img.src = src;
                        img.classList.add(this.CLASSES.LOADED);
                        // Remove data-src after loading
                        delete img.dataset.src;
                        
                        // If it's a background image, also set it as CSS background
                        if (img.classList.contains('ufpel-course-header-bg-image')) {
                            const parent = img.closest(this.SELECTORS.HEADER);
                            if (parent) {
                                parent.style.backgroundImage = `url('${src}')`;
                                parent.classList.add(this.CLASSES.HAS_BACKGROUND);
                                // Hide the img element as we're using CSS background
                                img.style.display = 'none';
                            }
                        }
                    };
                    
                    tempImg.onerror = () => {
                        console.error('Failed to load course header image:', src);
                        // Fallback: try to load directly
                        img.src = src;
                    };
                    
                    // Start loading
                    tempImg.src = src;
                }
            });
        },

        /**
         * Setup background image with proper loading.
         * 
         * @method setupBackgroundImage
         * @return {void}
         */
        setupBackgroundImage: function() {
            // Check if there's a data-background-url attribute
            const bgUrl = this.element.dataset.backgroundUrl;
            if (bgUrl) {
                // Create an image element for preloading
                const img = new Image();
                
                img.onload = () => {
                    this.element.style.backgroundImage = `url('${bgUrl}')`;
                    this.element.classList.add(this.CLASSES.HAS_BACKGROUND);
                    
                    // Fade in effect
                    requestAnimationFrame(() => {
                        this.element.style.opacity = '0';
                        this.element.offsetHeight; // Force reflow
                        this.element.style.transition = 'opacity 0.5s ease-in-out';
                        this.element.style.opacity = '1';
                    });
                };
                
                img.onerror = () => {
                    console.error('Failed to load course header background image:', bgUrl);
                    // Fallback to gradient
                    this.element.classList.add('no-image');
                };
                
                // Start loading
                img.src = bgUrl;
            }
            
            // Also check for existing background image in CSS
            const computedStyle = window.getComputedStyle(this.element);
            const existingBg = computedStyle.backgroundImage;
            if (existingBg && existingBg !== 'none') {
                this.element.classList.add(this.CLASSES.HAS_BACKGROUND);
            }
        },

        /**
         * Setup parallax scrolling effect.
         * 
         * @method setupParallaxEffect
         * @return {void}
         */
        setupParallaxEffect: function() {
            if (!this.element.classList.contains(this.CLASSES.HAS_BACKGROUND)) {
                return;
            }

            // Check if user prefers reduced motion
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                return;
            }

            this.element.classList.add(this.CLASSES.PARALLAX);

            let ticking = false;
            const updateParallax = () => {
                const scrolled = window.pageYOffset;
                const rate = scrolled * -0.5;
                
                if (Math.abs(rate) < 500) { // Limit the effect
                    this.element.style.transform = `translateY(${rate}px)`;
                }
                
                ticking = false;
            };

            window.addEventListener('scroll', () => {
                if (!ticking) {
                    window.requestAnimationFrame(updateParallax);
                    ticking = true;
                }
            }, { passive: true });
        },

        /**
         * Setup progress bar animation.
         * 
         * @method setupProgressAnimation
         * @return {void}
         */
        setupProgressAnimation: function() {
            const progressBar = this.element.querySelector(this.SELECTORS.PROGRESS_BAR);
            if (!progressBar) {
                return;
            }

            // Get the target width
            const targetWidth = progressBar.style.width;
            
            // Reset width for animation
            progressBar.style.width = '0%';
            
            // Use Intersection Observer for animation trigger
            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            // Animate progress bar
                            setTimeout(() => {
                                progressBar.style.transition = 'width 1.5s ease-out';
                                progressBar.style.width = targetWidth;
                            }, 200);
                            
                            // Stop observing once animated
                            observer.unobserve(entry.target);
                        }
                    });
                }, {
                    threshold: 0.5
                });

                observer.observe(progressBar.closest(this.SELECTORS.PROGRESS));
            } else {
                // Fallback for browsers without IntersectionObserver
                setTimeout(() => {
                    progressBar.style.transition = 'width 1.5s ease-out';
                    progressBar.style.width = targetWidth;
                }, 500);
            }
        },

        /**
         * Toggle header state.
         * 
         * @method toggle
         * @return {void}
         */
        toggle: function() {
            if (this.element.classList.contains(this.CLASSES.COLLAPSED)) {
                this.expand();
            } else {
                this.collapse();
            }
        },

        /**
         * Expand header.
         * 
         * @method expand
         * @param {boolean} animate Whether to animate the expansion
         * @return {void}
         */
        expand: function(animate = true) {
            if (animate) {
                this.element.style.transition = 'min-height 0.3s ease-in-out';
            }
            
            this.element.classList.remove(this.CLASSES.COLLAPSED);
            this.element.classList.add(this.CLASSES.EXPANDED);
            
            // Update button icon
            const btn = this.element.querySelector('[data-action="toggle-header"]');
            if (btn) {
                btn.innerHTML = '<i class="fa fa-chevron-up" aria-hidden="true"></i>';
            }
            
            // Save state
            localStorage.setItem('ufpel_course_header_state', 'expanded');
            
            // Trigger custom event
            this.element.dispatchEvent(new CustomEvent('courseheader:expanded'));
        },

        /**
         * Collapse header.
         * 
         * @method collapse
         * @param {boolean} animate Whether to animate the collapse
         * @return {void}
         */
        collapse: function(animate = true) {
            if (animate) {
                this.element.style.transition = 'min-height 0.3s ease-in-out';
            }
            
            this.element.classList.remove(this.CLASSES.EXPANDED);
            this.element.classList.add(this.CLASSES.COLLAPSED);
            
            // Update button icon
            const btn = this.element.querySelector('[data-action="toggle-header"]');
            if (btn) {
                btn.innerHTML = '<i class="fa fa-chevron-down" aria-hidden="true"></i>';
            }
            
            // Save state
            localStorage.setItem('ufpel_course_header_state', 'collapsed');
            
            // Trigger custom event
            this.element.dispatchEvent(new CustomEvent('courseheader:collapsed'));
        },

        /**
         * Setup event listeners.
         * 
         * @method setupEventListeners
         * @return {void}
         */
        setupEventListeners: function() {
            // Update progress dynamically if activities are completed
            document.addEventListener('core_course/activity:completed', (e) => {
                this.updateProgress(e.detail.progress);
            });

            // Handle window resize
            let resizeTimer;
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(() => {
                    this.handleResize();
                }, 250);
            });
        },

        /**
         * Update progress bar.
         * 
         * @method updateProgress
         * @param {number} progress Progress percentage (0-100)
         * @return {void}
         */
        updateProgress: function(progress) {
            const progressBar = this.element.querySelector(this.SELECTORS.PROGRESS_BAR);
            if (!progressBar) {
                return;
            }

            // Update width
            progressBar.style.width = progress + '%';
            progressBar.setAttribute('aria-valuenow', progress);
            
            // Update label
            const label = this.element.querySelector('.progress-label');
            if (label) {
                Str.get_string('progress', 'core_completion').then(str => {
                    label.textContent = `${str}: ${Math.round(progress)}%`;
                    return;
                }).catch(Notification.exception);
            }

            // Add completion animation if 100%
            if (progress >= 100) {
                progressBar.classList.add('completed');
                this.element.classList.add('course-completed');
                
                // Show completion message
                this.showCompletionMessage();
            }
        },

        /**
         * Show course completion message.
         * 
         * @method showCompletionMessage
         * @return {void}
         */
        showCompletionMessage: function() {
            Str.get_strings([
                { key: 'coursecompleted', component: 'theme_ufpel' },
                { key: 'congratulations', component: 'theme_ufpel' }
            ]).then(strings => {
                const message = `<div class="completion-message alert alert-success">
                    <h4>${strings[1]}</h4>
                    <p>${strings[0]}</p>
                </div>`;
                
                const content = this.element.querySelector(this.SELECTORS.CONTENT);
                content.insertAdjacentHTML('beforeend', message);
                
                // Auto-hide after 5 seconds
                setTimeout(() => {
                    const msg = content.querySelector('.completion-message');
                    if (msg) {
                        msg.style.transition = 'opacity 0.5s';
                        msg.style.opacity = '0';
                        setTimeout(() => msg.remove(), 500);
                    }
                }, 5000);
                return;
            }).catch(Notification.exception);
        },

        /**
         * Handle window resize.
         * 
         * @method handleResize
         * @return {void}
         */
        handleResize: function() {
            // Adjust header height for mobile
            if (window.innerWidth < 768) {
                this.element.style.minHeight = '150px';
            } else if (window.innerWidth < 992) {
                this.element.style.minHeight = '200px';
            } else {
                this.element.style.minHeight = '250px';
            }
        }
    };

    return CourseHeader;
});