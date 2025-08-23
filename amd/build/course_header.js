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
    var CourseHeader = {
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
            var pendingPromise = new Pending('theme_ufpel/course_header:init');
            
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
            var lazyImages = this.element.querySelectorAll('img[data-src]');
            var self = this;
            
            lazyImages.forEach(function(img) {
                var src = img.dataset.src;
                if (src) {
                    // Load the image
                    var tempImg = new Image();
                    tempImg.onload = function() {
                        img.src = src;
                        img.classList.add(self.CLASSES.LOADED);
                        // Remove data-src after loading
                        delete img.dataset.src;
                        
                        // If it's a background image, also set it as CSS background
                        if (img.classList.contains('ufpel-course-header-bg-image')) {
                            var parent = img.closest(self.SELECTORS.HEADER);
                            if (parent) {
                                parent.style.backgroundImage = "url('" + src + "')";
                                parent.classList.add(self.CLASSES.HAS_BACKGROUND);
                                // Hide the img element as we're using CSS background
                                img.style.display = 'none';
                            }
                        }
                    };
                    
                    tempImg.onerror = function() {
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
            var bgUrl = this.element.dataset.backgroundUrl;
            var self = this;
            
            if (bgUrl) {
                // Create an image element for preloading
                var img = new Image();
                
                img.onload = function() {
                    self.element.style.backgroundImage = "url('" + bgUrl + "')";
                    self.element.classList.add(self.CLASSES.HAS_BACKGROUND);
                    
                    // Fade in effect
                    requestAnimationFrame(function() {
                        self.element.style.opacity = '0';
                        self.element.offsetHeight; // Force reflow
                        self.element.style.transition = 'opacity 0.5s ease-in-out';
                        self.element.style.opacity = '1';
                    });
                };
                
                img.onerror = function() {
                    console.error('Failed to load course header background image:', bgUrl);
                    // Fallback to gradient
                    self.element.classList.add('no-image');
                };
                
                // Start loading
                img.src = bgUrl;
            }
            
            // Also check for existing background image in CSS
            var computedStyle = window.getComputedStyle(this.element);
            var existingBg = computedStyle.backgroundImage;
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
            var self = this;

            var ticking = false;
            var updateParallax = function() {
                var scrolled = window.pageYOffset;
                var rate = scrolled * -0.5;
                
                if (Math.abs(rate) < 500) { // Limit the effect
                    self.element.style.transform = 'translateY(' + rate + 'px)';
                }
                
                ticking = false;
            };

            window.addEventListener('scroll', function() {
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
            var progressBar = this.element.querySelector(this.SELECTORS.PROGRESS_BAR);
            if (!progressBar) {
                return;
            }

            // Get the target width
            var targetWidth = progressBar.style.width;
            
            // Reset width for animation
            progressBar.style.width = '0%';
            
            // Use Intersection Observer for animation trigger
            if ('IntersectionObserver' in window) {
                var observer = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            // Animate progress bar
                            setTimeout(function() {
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
                setTimeout(function() {
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
        expand: function(animate) {
            animate = animate !== false; // Default to true
            
            if (animate) {
                this.element.style.transition = 'min-height 0.3s ease-in-out';
            }
            
            this.element.classList.remove(this.CLASSES.COLLAPSED);
            this.element.classList.add(this.CLASSES.EXPANDED);
            
            // Update button icon
            var btn = this.element.querySelector('[data-action="toggle-header"]');
            if (btn) {
                btn.innerHTML = '<i class="fa fa-chevron-up" aria-hidden="true"></i>';
            }
            
            // Save state
            localStorage.setItem('ufpel_course_header_state', 'expanded');
            
            // Trigger custom event
            if (this.element.dispatchEvent) {
                this.element.dispatchEvent(new CustomEvent('courseheader:expanded'));
            }
        },

        /**
         * Collapse header.
         * 
         * @method collapse
         * @param {boolean} animate Whether to animate the collapse
         * @return {void}
         */
        collapse: function(animate) {
            animate = animate !== false; // Default to true
            
            if (animate) {
                this.element.style.transition = 'min-height 0.3s ease-in-out';
            }
            
            this.element.classList.remove(this.CLASSES.EXPANDED);
            this.element.classList.add(this.CLASSES.COLLAPSED);
            
            // Update button icon
            var btn = this.element.querySelector('[data-action="toggle-header"]');
            if (btn) {
                btn.innerHTML = '<i class="fa fa-chevron-down" aria-hidden="true"></i>';
            }
            
            // Save state
            localStorage.setItem('ufpel_course_header_state', 'collapsed');
            
            // Trigger custom event
            if (this.element.dispatchEvent) {
                this.element.dispatchEvent(new CustomEvent('courseheader:collapsed'));
            }
        },

        /**
         * Setup event listeners.
         * 
         * @method setupEventListeners
         * @return {void}
         */
        setupEventListeners: function() {
            var self = this;
            
            // Update progress dynamically if activities are completed
            document.addEventListener('core_course/activity:completed', function(e) {
                if (e.detail && e.detail.progress) {
                    self.updateProgress(e.detail.progress);
                }
            });

            // Handle window resize
            var resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    self.handleResize();
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
            var progressBar = this.element.querySelector(this.SELECTORS.PROGRESS_BAR);
            if (!progressBar) {
                return;
            }

            // Update width
            progressBar.style.width = progress + '%';
            progressBar.setAttribute('aria-valuenow', progress);
            
            // Update label
            var label = this.element.querySelector('.progress-label');
            if (label) {
                try {
                    Str.get_string('progress', 'core_completion').then(function(str) {
                        label.textContent = str + ': ' + Math.round(progress) + '%';
                    }).catch(function(error) {
                        // Fallback text
                        label.textContent = 'Progress: ' + Math.round(progress) + '%';
                    });
                } catch (e) {
                    // Fallback if Str module has issues
                    label.textContent = 'Progress: ' + Math.round(progress) + '%';
                }
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
            var self = this;
            
            try {
                Str.get_strings([
                    { key: 'coursecompleted', component: 'theme_ufpel' },
                    { key: 'congratulations', component: 'theme_ufpel' }
                ]).then(function(strings) {
                    var message = '<div class="completion-message alert alert-success">' +
                        '<h4>' + strings[1] + '</h4>' +
                        '<p>' + strings[0] + '</p>' +
                        '</div>';
                    
                    var content = self.element.querySelector(self.SELECTORS.CONTENT);
                    if (content) {
                        content.insertAdjacentHTML('beforeend', message);
                        
                        // Auto-hide after 5 seconds
                        setTimeout(function() {
                            var msg = content.querySelector('.completion-message');
                            if (msg) {
                                msg.style.transition = 'opacity 0.5s';
                                msg.style.opacity = '0';
                                setTimeout(function() {
                                    if (msg.parentNode) {
                                        msg.parentNode.removeChild(msg);
                                    }
                                }, 500);
                            }
                        }, 5000);
                    }
                }).catch(function(error) {
                    // Fallback message
                    var message = '<div class="completion-message alert alert-success">' +
                        '<h4>Congratulations!</h4>' +
                        '<p>You have completed this course.</p>' +
                        '</div>';
                    
                    var content = self.element.querySelector(self.SELECTORS.CONTENT);
                    if (content) {
                        content.insertAdjacentHTML('beforeend', message);
                    }
                });
            } catch (e) {
                // Fallback if string loading fails
                console.error('Error loading completion strings:', e);
            }
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