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
 * Main theme JavaScript module for UFPel theme.
 *
 * @module     theme_ufpel/theme
 * @copyright  2025 Universidade Federal de Pelotas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([
    'jquery',
    'core/ajax',
    'core/notification',
    'core/templates',
    'core/str',
    'core/pubsub',
    'core/pending',
    'theme_ufpel/course_header',
    'theme_ufpel/lazy_load'
], function(
    $,
    Ajax,
    Notification,
    Templates,
    Str,
    PubSub,
    Pending,
    CourseHeader,
    LazyLoad
) {
    'use strict';

    /**
     * Theme initialization.
     * 
     * @class
     */
    const ThemeUFPel = {
        /**
         * Selectors used throughout the theme.
         * 
         * @type {Object}
         */
        SELECTORS: {
            BODY: 'body',
            NAVBAR: '.navbar',
            DRAWER: '.drawer',
            COURSE_HEADER: '[data-region="course-header"]',
            LAZY_IMAGES: 'img.lazyload',
            STICKY_FOOTER: '#page-footer',
            USER_MENU: '.usermenu',
            THEME_TOGGLE: '[data-action="theme-toggle"]',
            SCROLL_TOP: '[data-action="scroll-top"]'
        },

        /**
         * CSS classes used by the theme.
         * 
         * @type {Object}
         */
        CLASSES: {
            LOADING: 'ufpel-loading',
            LOADED: 'ufpel-loaded',
            SCROLLED: 'ufpel-scrolled',
            DARK_MODE: 'ufpel-dark-mode',
            COMPACT_NAVBAR: 'ufpel-compact-navbar',
            HAS_HEADER: 'has-course-header'
        },

        /**
         * Storage keys for user preferences.
         * 
         * @type {Object}
         */
        STORAGE_KEYS: {
            DARK_MODE: 'theme_ufpel_dark_mode',
            COMPACT_VIEW: 'theme_ufpel_compact_view',
            SIDEBAR_STATE: 'theme_ufpel_sidebar_state'
        },

        /**
         * Initialize the theme.
         * 
         * @method init
         * @param {Object} config Configuration object
         * @return {void}
         */
        init: function(config = {}) {
            const pendingPromise = new Pending('theme_ufpel/theme:init');
            
            this.config = Object.assign({
                enableDarkMode: false,
                enableCompactView: false,
                enableLazyLoad: true,
                enableStickyHeader: true,
                enableScrollTop: true,
                scrollTopOffset: 300
            }, config);

            // Initialize components
            this.initializePreferences();
            this.initializeNavbar();
            this.initializeCourseHeader();
            this.initializeLazyLoad();
            this.initializeScrollFeatures();
            this.initializeEventListeners();
            this.initializeAccessibility();
            
            // Mark initialization complete
            $(this.SELECTORS.BODY).removeClass(this.CLASSES.LOADING).addClass(this.CLASSES.LOADED);
            
            pendingPromise.resolve();
        },

        /**
         * Initialize user preferences.
         * 
         * @method initializePreferences
         * @return {void}
         */
        initializePreferences: function() {
            // Check for dark mode preference
            const darkMode = localStorage.getItem(this.STORAGE_KEYS.DARK_MODE);
            if (darkMode === 'true' || (darkMode === null && this.config.enableDarkMode)) {
                this.enableDarkMode();
            }

            // Check for compact view preference
            const compactView = localStorage.getItem(this.STORAGE_KEYS.COMPACT_VIEW);
            if (compactView === 'true' || (compactView === null && this.config.enableCompactView)) {
                this.enableCompactView();
            }
        },

        /**
         * Initialize navbar features.
         * 
         * @method initializeNavbar
         * @return {void}
         */
        initializeNavbar: function() {
            const navbar = document.querySelector(this.SELECTORS.NAVBAR);
            if (!navbar || !this.config.enableStickyHeader) {
                return;
            }

            let lastScrollTop = 0;
            const scrollThreshold = 100;

            window.addEventListener('scroll', () => {
                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                
                // Add scrolled class when scrolled down
                if (scrollTop > scrollThreshold) {
                    document.body.classList.add(this.CLASSES.SCROLLED);
                    
                    // Hide/show navbar based on scroll direction
                    if (scrollTop > lastScrollTop) {
                        navbar.classList.add('navbar-hidden');
                    } else {
                        navbar.classList.remove('navbar-hidden');
                    }
                } else {
                    document.body.classList.remove(this.CLASSES.SCROLLED);
                    navbar.classList.remove('navbar-hidden');
                }
                
                lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
            }, { passive: true });
        },

        /**
         * Initialize course header.
         * 
         * @method initializeCourseHeader
         * @return {void}
         */
        initializeCourseHeader: function() {
            const courseHeader = document.querySelector(this.SELECTORS.COURSE_HEADER);
            if (courseHeader) {
                CourseHeader.init(courseHeader);
            }
        },

        /**
         * Initialize lazy loading for images.
         * 
         * @method initializeLazyLoad
         * @return {void}
         */
        initializeLazyLoad: function() {
            if (!this.config.enableLazyLoad) {
                return;
            }

            LazyLoad.init({
                selector: this.SELECTORS.LAZY_IMAGES,
                rootMargin: '50px 0px',
                threshold: 0.01
            });
        },

        /**
         * Initialize scroll features.
         * 
         * @method initializeScrollFeatures
         * @return {void}
         */
        initializeScrollFeatures: function() {
            if (!this.config.enableScrollTop) {
                return;
            }

            // Create scroll to top button
            const scrollTopBtn = this.createScrollTopButton();
            document.body.appendChild(scrollTopBtn);

            // Show/hide based on scroll position
            window.addEventListener('scroll', () => {
                const scrolled = window.pageYOffset || document.documentElement.scrollTop;
                if (scrolled > this.config.scrollTopOffset) {
                    scrollTopBtn.classList.add('visible');
                } else {
                    scrollTopBtn.classList.remove('visible');
                }
            }, { passive: true });

            // Handle click
            scrollTopBtn.addEventListener('click', (e) => {
                e.preventDefault();
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        },

        /**
         * Create scroll to top button.
         * 
         * @method createScrollTopButton
         * @return {HTMLElement} The button element
         */
        createScrollTopButton: function() {
            const button = document.createElement('button');
            button.className = 'ufpel-scroll-top btn btn-primary';
            button.setAttribute('data-action', 'scroll-top');
            button.setAttribute('aria-label', M.util.get_string('totop', 'theme_ufpel'));
            button.innerHTML = '<i class="fa fa-chevron-up" aria-hidden="true"></i>';
            return button;
        },

        /**
         * Initialize event listeners.
         * 
         * @method initializeEventListeners
         * @return {void}
         */
        initializeEventListeners: function() {
            // Dark mode toggle
            $(document).on('click', this.SELECTORS.THEME_TOGGLE, (e) => {
                e.preventDefault();
                this.toggleDarkMode();
            });

            // Listen for preference changes from other tabs
            window.addEventListener('storage', (e) => {
                if (e.key === this.STORAGE_KEYS.DARK_MODE) {
                    if (e.newValue === 'true') {
                        this.enableDarkMode();
                    } else {
                        this.disableDarkMode();
                    }
                }
            });

            // Listen for Moodle events
            PubSub.subscribe('nav-drawer-toggle-start', () => {
                this.handleDrawerToggle();
            });

            // Handle responsive behavior
            this.handleResponsive();
            window.addEventListener('resize', this.debounce(() => {
                this.handleResponsive();
            }, 250));
        },

        /**
         * Initialize accessibility features.
         * 
         * @method initializeAccessibility
         * @return {void}
         */
        initializeAccessibility: function() {
            // Skip to main content link
            this.ensureSkipLink();

            // Keyboard navigation improvements
            this.improveKeyboardNavigation();

            // ARIA live regions for dynamic content
            this.setupAriaLiveRegions();

            // Focus management
            this.manageFocus();
        },

        /**
         * Ensure skip link exists and works properly.
         * 
         * @method ensureSkipLink
         * @return {void}
         */
        ensureSkipLink: function() {
            let skipLink = document.querySelector('.skip-main');
            if (!skipLink) {
                skipLink = document.createElement('a');
                skipLink.className = 'skip-main visually-hidden-focusable';
                skipLink.href = '#main-content';
                skipLink.textContent = M.util.get_string('skipmain', 'theme_ufpel');
                document.body.insertBefore(skipLink, document.body.firstChild);
            }

            skipLink.addEventListener('click', (e) => {
                e.preventDefault();
                const main = document.getElementById('main-content') || document.querySelector('[role="main"]');
                if (main) {
                    main.setAttribute('tabindex', '-1');
                    main.focus();
                    main.scrollIntoView();
                }
            });
        },

        /**
         * Improve keyboard navigation.
         * 
         * @method improveKeyboardNavigation
         * @return {void}
         */
        improveKeyboardNavigation: function() {
            // Add keyboard shortcuts
            document.addEventListener('keydown', (e) => {
                // Alt + M: Jump to main content
                if (e.altKey && e.key === 'm') {
                    e.preventDefault();
                    const main = document.getElementById('main-content') || document.querySelector('[role="main"]');
                    if (main) {
                        main.focus();
                    }
                }

                // Alt + N: Toggle navigation
                if (e.altKey && e.key === 'n') {
                    e.preventDefault();
                    const navToggle = document.querySelector('[data-toggler="drawers"]');
                    if (navToggle) {
                        navToggle.click();
                    }
                }

                // Escape: Close modals/drawers
                if (e.key === 'Escape') {
                    this.closeActiveOverlays();
                }
            });
        },

        /**
         * Setup ARIA live regions.
         * 
         * @method setupAriaLiveRegions
         * @return {void}
         */
        setupAriaLiveRegions: function() {
            // Create status region if it doesn't exist
            let statusRegion = document.getElementById('theme-status-region');
            if (!statusRegion) {
                statusRegion = document.createElement('div');
                statusRegion.id = 'theme-status-region';
                statusRegion.className = 'visually-hidden';
                statusRegion.setAttribute('aria-live', 'polite');
                statusRegion.setAttribute('aria-atomic', 'true');
                document.body.appendChild(statusRegion);
            }

            // Create alert region if it doesn't exist
            let alertRegion = document.getElementById('theme-alert-region');
            if (!alertRegion) {
                alertRegion = document.createElement('div');
                alertRegion.id = 'theme-alert-region';
                alertRegion.className = 'visually-hidden';
                alertRegion.setAttribute('aria-live', 'assertive');
                alertRegion.setAttribute('aria-atomic', 'true');
                document.body.appendChild(alertRegion);
            }
        },

        /**
         * Manage focus for better accessibility.
         * 
         * @method manageFocus
         * @return {void}
         */
        manageFocus: function() {
            // Track focus for drawer
            const drawer = document.querySelector(this.SELECTORS.DRAWER);
            if (drawer) {
                let previousFocus = null;

                // Save focus when drawer opens
                drawer.addEventListener('drawer:show', () => {
                    previousFocus = document.activeElement;
                    // Focus first focusable element in drawer
                    const firstFocusable = drawer.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
                    if (firstFocusable) {
                        firstFocusable.focus();
                    }
                });

                // Restore focus when drawer closes
                drawer.addEventListener('drawer:hide', () => {
                    if (previousFocus) {
                        previousFocus.focus();
                        previousFocus = null;
                    }
                });
            }
        },

        /**
         * Enable dark mode.
         * 
         * @method enableDarkMode
         * @return {void}
         */
        enableDarkMode: function() {
            document.body.classList.add(this.CLASSES.DARK_MODE);
            localStorage.setItem(this.STORAGE_KEYS.DARK_MODE, 'true');
            this.announceStatus(M.util.get_string('darkmodeon', 'theme_ufpel'));
        },

        /**
         * Disable dark mode.
         * 
         * @method disableDarkMode
         * @return {void}
         */
        disableDarkMode: function() {
            document.body.classList.remove(this.CLASSES.DARK_MODE);
            localStorage.setItem(this.STORAGE_KEYS.DARK_MODE, 'false');
            this.announceStatus(M.util.get_string('darkmodeoff', 'theme_ufpel'));
        },

        /**
         * Toggle dark mode.
         * 
         * @method toggleDarkMode
         * @return {void}
         */
        toggleDarkMode: function() {
            if (document.body.classList.contains(this.CLASSES.DARK_MODE)) {
                this.disableDarkMode();
            } else {
                this.enableDarkMode();
            }
        },

        /**
         * Enable compact view.
         * 
         * @method enableCompactView
         * @return {void}
         */
        enableCompactView: function() {
            document.body.classList.add(this.CLASSES.COMPACT_NAVBAR);
            localStorage.setItem(this.STORAGE_KEYS.COMPACT_VIEW, 'true');
        },

        /**
         * Handle drawer toggle.
         * 
         * @method handleDrawerToggle
         * @return {void}
         */
        handleDrawerToggle: function() {
            const drawer = document.querySelector(this.SELECTORS.DRAWER);
            if (drawer) {
                const isOpen = drawer.classList.contains('show');
                localStorage.setItem(this.STORAGE_KEYS.SIDEBAR_STATE, isOpen ? 'closed' : 'open');
            }
        },

        /**
         * Handle responsive behavior.
         * 
         * @method handleResponsive
         * @return {void}
         */
        handleResponsive: function() {
            const width = window.innerWidth;
            const body = document.body;

            // Remove all responsive classes
            body.classList.remove('ufpel-mobile', 'ufpel-tablet', 'ufpel-desktop');

            // Add appropriate class
            if (width < 768) {
                body.classList.add('ufpel-mobile');
            } else if (width < 1024) {
                body.classList.add('ufpel-tablet');
            } else {
                body.classList.add('ufpel-desktop');
            }
        },

        /**
         * Close active overlays.
         * 
         * @method closeActiveOverlays
         * @return {void}
         */
        closeActiveOverlays: function() {
            // Close drawers
            $('.drawer.show').removeClass('show');
            
            // Close dropdowns
            $('.dropdown-menu.show').removeClass('show');
            
            // Close modals
            $('.modal.show').modal('hide');
        },

        /**
         * Announce status to screen readers.
         * 
         * @method announceStatus
         * @param {string} message The message to announce
         * @return {void}
         */
        announceStatus: function(message) {
            const statusRegion = document.getElementById('theme-status-region');
            if (statusRegion) {
                statusRegion.textContent = message;
                // Clear after announcement
                setTimeout(() => {
                    statusRegion.textContent = '';
                }, 1000);
            }
        },

        /**
         * Debounce function for performance.
         * 
         * @method debounce
         * @param {Function} func The function to debounce
         * @param {number} wait The wait time in milliseconds
         * @return {Function} The debounced function
         */
        debounce: function(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
    };

    return ThemeUFPel;
});