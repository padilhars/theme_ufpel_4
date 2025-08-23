<?php
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
 * Output hook callbacks for theme_ufpel
 *
 * @package    theme_ufpel
 * @copyright  2025 Universidade Federal de Pelotas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_ufpel\hooks;

use core\hook\output\before_standard_head_html_generation;
use core\hook\output\before_footer_html_generation;
use html_writer;
use stdClass;

defined('MOODLE_INTERNAL') || die();

/**
 * Output callbacks for theme hooks.
 *
 * @package    theme_ufpel
 * @copyright  2025 Universidade Federal de Pelotas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class output_callbacks {
    
    /**
     * Hook callback for before_standard_head_html_generation.
     * Replaces the old before_standard_html_head callback.
     *
     * @param before_standard_head_html_generation $hook The hook instance
     * @return void
     */
    public static function before_standard_head_html_generation(before_standard_head_html_generation $hook): void {
        global $PAGE, $CFG;
        
        // CORREÇÃO: Não inicializar JavaScript aqui para evitar conflitos
        // A inicialização do JS é feita via theme_ufpel_page_init quando necessário
        
        // Add critical CSS inline for performance
        $criticalcss = self::get_critical_css();
        if (!empty($criticalcss)) {
            $hook->add_html(html_writer::tag('style', $criticalcss, [
                'id' => 'ufpel-critical-css',
                'data-purpose' => 'critical-css'
            ]));
        }
        
        // Add preload hints for performance
        $preloadhtml = self::get_preload_hints();
        if (!empty($preloadhtml)) {
            $hook->add_html($preloadhtml);
        }
        
        // Add theme meta tags
        $metatags = self::get_theme_meta_tags();
        if (!empty($metatags)) {
            $hook->add_html($metatags);
        }
        
        // Initialize theme JavaScript properly
        self::initialize_theme_javascript($PAGE);
    }
    
    /**
     * Hook callback for before_footer_html_generation.
     *
     * @param before_footer_html_generation $hook The hook instance
     * @return void
     */
    public static function before_footer_html_generation(before_footer_html_generation $hook): void {
        // Add any footer-specific HTML if needed
        $footerscripts = self::get_footer_scripts();
        if (!empty($footerscripts)) {
            $hook->add_html($footerscripts);
        }
    }
    
    /**
     * Initialize theme JavaScript properly.
     * CORREÇÃO: Usar try-catch para evitar erros que quebram a página.
     *
     * @param \moodle_page $page The page object
     * @return void
     */
    protected static function initialize_theme_javascript(\moodle_page $page): void {
        try {
            // Check if the function exists and call it safely
            if (function_exists('theme_ufpel_page_init')) {
                theme_ufpel_page_init($page);
            } else if (function_exists('theme_ufpel_should_init_js') && theme_ufpel_should_init_js($page)) {
                // Fallback initialization if theme_ufpel_page_init doesn't exist
                $config = [
                    'enableDarkMode' => (bool)get_config('theme_ufpel', 'enabledarkmode'),
                    'enableCompactView' => (bool)get_config('theme_ufpel', 'enablecompactview'),
                    'enableLazyLoad' => true,
                    'enableStickyHeader' => true,
                    'enableScrollTop' => true,
                    'scrollTopOffset' => 300
                ];
                
                $page->requires->js_call_amd('theme_ufpel/theme', 'init', [$config]);
            }
        } catch (\Exception $e) {
            // Log error but don't break the page
            debugging('Error initializing theme_ufpel JavaScript in hook: ' . $e->getMessage(), DEBUG_DEVELOPER);
        }
    }
        
    /**
     * Get critical CSS for inline inclusion.
     *
     * @return string The critical CSS
     */
    protected static function get_critical_css(): string {
        global $CFG;
        
        // Try to use helper class if available
        $settings = null;
        if (file_exists($CFG->dirroot . '/theme/ufpel/classes/helper.php')) {
            require_once($CFG->dirroot . '/theme/ufpel/classes/helper.php');
            
            if (class_exists('\theme_ufpel\helper') && method_exists('\theme_ufpel\helper', 'get_theme_settings')) {
                try {
                    $settings = \theme_ufpel\helper::get_theme_settings();
                } catch (\Exception $e) {
                    // Fallback to default settings
                    $settings = null;
                }
            }
        }
        
        // Use default settings if helper is not available
        if (!$settings) {
            $settings = new stdClass();
            $settings->primarycolor = get_config('theme_ufpel', 'primarycolor') ?: '#003366';
            $settings->secondarycolor = get_config('theme_ufpel', 'secondarycolor') ?: '#0066cc';
            $settings->backgroundcolor = get_config('theme_ufpel', 'backgroundcolor') ?: '#ffffff';
            $settings->highlightcolor = get_config('theme_ufpel', 'highlightcolor') ?: '#ffc107';
            $settings->contenttextcolor = get_config('theme_ufpel', 'contenttextcolor') ?: '#212529';
            $settings->highlighttextcolor = get_config('theme_ufpel', 'highlighttextcolor') ?: '#ffffff';
        }
        
        $css = "
        :root {
            --ufpel-primary: {$settings->primarycolor};
            --ufpel-secondary: {$settings->secondarycolor};
            --ufpel-background: {$settings->backgroundcolor};
            --ufpel-highlight: {$settings->highlightcolor};
            --ufpel-text: {$settings->contenttextcolor};
            --ufpel-text-highlight: {$settings->highlighttextcolor};
        }
        
        body {
            background-color: var(--ufpel-background);
            color: var(--ufpel-text);
        }
        
        .navbar {
            background-color: var(--ufpel-primary) !important;
        }
        
        a {
            color: var(--ufpel-secondary);
        }
        
        .btn-primary {
            background-color: var(--ufpel-primary);
            border-color: var(--ufpel-primary);
        }
        
        .visually-hidden {
            position: absolute !important;
            width: 1px !important;
            height: 1px !important;
            padding: 0 !important;
            margin: -1px !important;
            overflow: hidden !important;
            clip: rect(0,0,0,0) !important;
            white-space: nowrap !important;
            border: 0 !important;
        }
        
        .ufpel-loading {
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }
        
        .ufpel-loaded {
            opacity: 1;
        }
        ";
        
        // Minify CSS
        $css = preg_replace('/\s+/', ' ', trim($css));
        $css = str_replace(': ', ':', $css);
        $css = str_replace('; ', ';', $css);
        $css = str_replace(' {', '{', $css);
        $css = str_replace('} ', '}', $css);
        
        return $css;
    }
    
    /**
     * Get preload hints for performance.
     *
     * @return string The preload HTML
     */
    protected static function get_preload_hints(): string {
        global $CFG;
        
        $preloads = [];
        
        // Preload theme fonts if configured
        $customfonts = get_config('theme_ufpel', 'customfonts');
        if (!empty($customfonts) && preg_match('/@import\s+url\([\'"]?([^\'")]+)[\'"]?\)/', $customfonts, $matches)) {
            $preloads[] = '<link rel="preconnect" href="https://fonts.googleapis.com">';
            $preloads[] = '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
            $preloads[] = '<link rel="preload" href="' . $matches[1] . '" as="style">';
        }
        
        return implode("\n", $preloads);
    }
    
    /**
     * Get theme meta tags.
     *
     * @return string The meta tags HTML
     */
    protected static function get_theme_meta_tags(): string {
        $tags = [];
        
        // Theme color for mobile browsers
        $primarycolor = get_config('theme_ufpel', 'primarycolor') ?: '#003366';
        $tags[] = '<meta name="theme-color" content="' . $primarycolor . '">';
        
        // Generator meta tag
        $tags[] = '<meta name="generator" content="Moodle - UFPel Theme">';
        
        return implode("\n", $tags);
    }
    
    /**
     * Get footer scripts.
     *
     * @return string The footer scripts HTML
     */
    protected static function get_footer_scripts(): string {
        // Add any deferred scripts or analytics here
        // Currently empty but can be extended as needed
        return '';
    }
    
    /**
     * Check if dark mode should be used.
     *
     * @return bool
     */
    protected static function should_use_dark_mode(): bool {
        // Check user preference first
        $userpref = get_user_preferences('theme_ufpel_darkmode', null);
        if ($userpref !== null) {
            return (bool)$userpref;
        }
        
        // Check system setting
        return (bool)get_config('theme_ufpel', 'enabledarkmode');
    }
}