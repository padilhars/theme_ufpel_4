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
 * Theme UFPel lib functions - Fixed for Moodle 5.x.
 *
 * @package    theme_ufpel
 * @copyright  2025 Universidade Federal de Pelotas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Returns the main SCSS content for the theme.
 *
 * @param theme_config $theme The theme config object.
 * @return string The SCSS content.
 */
function theme_ufpel_get_main_scss_content($theme) {
    global $CFG;
    
    $scss = '';
    $filename = !empty($theme->settings->preset) ? $theme->settings->preset : 'default.scss';
    
    // Security check for filename
    $filename = clean_param($filename, PARAM_FILE);
    
    $fs = get_file_storage();
    $context = context_system::instance();
    
    // CORREÇÃO PRINCIPAL: Primeiro tenta carregar preset customizado do file storage
    $presetfile = $fs->get_file($context->id, 'theme_ufpel', 'preset', 0, '/', $filename);
    
    if ($presetfile) {
        // Preset customizado enviado pelo usuário
        $scss .= $presetfile->get_content();
    } else {
        // CORREÇÃO: Carrega preset do diretório do tema
        $presetpath = $CFG->dirroot . '/theme/ufpel/scss/preset/' . $filename;
        
        if (file_exists($presetpath) && is_readable($presetpath)) {
            $scss .= file_get_contents($presetpath);
        } else {
            // Fallback para o preset default
            $defaultpath = $CFG->dirroot . '/theme/ufpel/scss/preset/default.scss';
            if (file_exists($defaultpath) && is_readable($defaultpath)) {
                $scss .= file_get_contents($defaultpath);
            } else {
                // Último fallback para o Boost
                $boostdefault = $CFG->dirroot . '/theme/boost/scss/preset/default.scss';
                if (file_exists($boostdefault) && is_readable($boostdefault)) {
                    $scss .= file_get_contents($boostdefault);
                }
            }
        }
    }
    
    // Append post.scss content
    $postscss = file_get_contents($CFG->dirroot . '/theme/ufpel/scss/post.scss');
    if ($postscss !== false) {
        $scss .= "\n" . $postscss;
    }
    
    return $scss;
}

/**
 * Get default preset content.
 *
 * @param string $dirroot The Moodle dirroot.
 * @return string The default preset content.
 */
function theme_ufpel_get_default_preset_content($dirroot) {
    $defaultfile = $dirroot . '/theme/ufpel/scss/preset/default.scss';
    
    if (file_exists($defaultfile) && is_readable($defaultfile)) {
        return file_get_contents($defaultfile);
    }
    
    // Fallback to Boost's default
    $boostdefault = $dirroot . '/theme/boost/scss/preset/default.scss';
    if (file_exists($boostdefault) && is_readable($boostdefault)) {
        return file_get_contents($boostdefault);
    }
    
    return '';
}

/**
 * Get pre-SCSS code.
 * Injects variables before SCSS compilation.
 *
 * @param theme_config $theme The theme config object.
 * @return string The pre-SCSS code.
 */
function theme_ufpel_get_pre_scss($theme) {
    $scss = '';
    $configurable = [];
    
    // Primary color (formerly brand color) with validation
    $primarycolor = get_config('theme_ufpel', 'primarycolor');
    // Check for legacy brandcolor setting if primarycolor is not set
    if (empty($primarycolor)) {
        $primarycolor = get_config('theme_ufpel', 'brandcolor');
    }
    
    if (!empty($primarycolor) && preg_match('/^#[a-f0-9]{6}$/i', $primarycolor)) {
        $configurable['primarycolor'] = $primarycolor;
        $configurable['primary'] = $primarycolor; // Bootstrap variable
    } else {
        $configurable['primarycolor'] = '#003366';
        $configurable['primary'] = '#003366';
    }
    
    // Secondary color with validation
    $secondarycolor = get_config('theme_ufpel', 'secondarycolor');
    if (!empty($secondarycolor) && preg_match('/^#[a-f0-9]{6}$/i', $secondarycolor)) {
        $configurable['secondarycolor'] = $secondarycolor;
        $configurable['secondary'] = $secondarycolor;
    } else {
        $configurable['secondarycolor'] = '#0066cc';
        $configurable['secondary'] = '#0066cc';
    }
    
    // Background color
    $backgroundcolor = get_config('theme_ufpel', 'backgroundcolor');
    if (!empty($backgroundcolor) && preg_match('/^#[a-f0-9]{6}$/i', $backgroundcolor)) {
        $configurable['backgroundcolor'] = $backgroundcolor;
        $configurable['body-bg'] = $backgroundcolor;
    } else {
        $configurable['backgroundcolor'] = '#ffffff';
        $configurable['body-bg'] = '#ffffff';
    }
    
    // Highlight color
    $highlightcolor = get_config('theme_ufpel', 'highlightcolor');
    if (!empty($highlightcolor) && preg_match('/^#[a-f0-9]{6}$/i', $highlightcolor)) {
        $configurable['highlightcolor'] = $highlightcolor;
        $configurable['warning'] = $highlightcolor;
    } else {
        $configurable['highlightcolor'] = '#ffc107';
        $configurable['warning'] = '#ffc107';
    }
    
    // Content text color
    $contenttextcolor = get_config('theme_ufpel', 'contenttextcolor');
    if (!empty($contenttextcolor) && preg_match('/^#[a-f0-9]{6}$/i', $contenttextcolor)) {
        $configurable['contenttextcolor'] = $contenttextcolor;
        $configurable['body-color'] = $contenttextcolor;
    } else {
        $configurable['contenttextcolor'] = '#212529';
        $configurable['body-color'] = '#212529';
    }
    
    // Highlight text color
    $highlighttextcolor = get_config('theme_ufpel', 'highlighttextcolor');
    if (!empty($highlighttextcolor) && preg_match('/^#[a-f0-9]{6}$/i', $highlighttextcolor)) {
        $configurable['highlighttextcolor'] = $highlighttextcolor;
    } else {
        $configurable['highlighttextcolor'] = '#ffffff';
    }
    
    // Custom fonts
    $customfonts = get_config('theme_ufpel', 'customfonts');
    if (!empty($customfonts)) {
        $scss .= $customfonts . "\n";
    }
    
    // Build SCSS variables
    foreach ($configurable as $configkey => $configval) {
        $scss .= sprintf('$%s: %s !default;' . "\n", $configkey, $configval);
    }
    
    // Import utility files first - only if they exist
    $utilityfiles = [
        'utilities/variables',
        'utilities/mixins',
        'utilities/functions'
    ];
    
    foreach ($utilityfiles as $file) {
        $filepath = __DIR__ . '/scss/' . $file . '.scss';
        if (file_exists($filepath)) {
            $scss .= '@import "' . $file . '";' . "\n";
        }
    }
    
    // Prepend custom pre-scss
    if (!empty($theme->settings->rawscsspre)) {
        $scss .= "\n" . $theme->settings->rawscsspre . "\n";
    }
    
    return $scss;
}

/**
 * Get extra SCSS to append.
 *
 * @param theme_config $theme The theme config object.
 * @return string The extra SCSS.
 */
function theme_ufpel_get_extra_scss($theme) {
    $scss = '';
    
    // Add custom SCSS
    if (!empty($theme->settings->rawscss)) {
        $scss .= "\n" . $theme->settings->rawscss;
    }
    
    // Add custom CSS (will be processed as SCSS)
    if (!empty($theme->settings->customcss)) {
        $scss .= "\n" . $theme->settings->customcss;
    }
    
    return $scss;
}

/**
 * CSS tree post processor - Updated for Moodle 5.x.
 * Uses the new CSS post-processing API instead of string manipulation.
 *
 * @param string $css The CSS.
 * @param theme_config $theme The theme config.
 * @return string The processed CSS.
 */
function theme_ufpel_css_tree_post_processor($css, $theme) {
    // No longer manipulate CSS directly with string replacements
    // The theme settings are now handled via SCSS variables
    return $css;
}

/**
 * Serves any files associated with the theme settings.
 * FIXED: Better handling of file serving to prevent URL issues.
 *
 * @param stdClass $course The course object.
 * @param stdClass $cm The course module object.
 * @param context $context The context.
 * @param string $filearea The file area.
 * @param array $args The arguments.
 * @param bool $forcedownload Whether to force download.
 * @param array $options Additional options.
 * @return bool
 */
function theme_ufpel_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    if ($context->contextlevel == CONTEXT_SYSTEM && 
        ($filearea === 'loginbackgroundimage' || $filearea === 'logo' || $filearea === 'favicon')) {
        
        $theme = theme_config::load('ufpel');
        return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
    }
    
    send_file_not_found();
}

/**
 * Get the current user preferences that are available for the theme.
 *
 * @return array The preferences
 */
function theme_ufpel_get_user_preferences() {
    return [
        'drawer-open-index' => [
            'type' => PARAM_BOOL,
            'null' => NULL_NOT_ALLOWED,
            'default' => true,
            'permissioncallback' => [core_user::class, 'is_current_user'],
        ],
        'drawer-open-block' => [
            'type' => PARAM_BOOL,
            'null' => NULL_NOT_ALLOWED,
            'default' => false,
            'permissioncallback' => [core_user::class, 'is_current_user'],
        ],
        'theme_ufpel_darkmode' => [
            'type' => PARAM_BOOL,
            'null' => NULL_ALLOWED,
            'default' => null,
            'permissioncallback' => [core_user::class, 'is_current_user'],
        ],
        'theme_ufpel_compactview' => [
            'type' => PARAM_BOOL,
            'null' => NULL_ALLOWED,
            'default' => null,
            'permissioncallback' => [core_user::class, 'is_current_user'],
        ],
    ];
}

/**
 * Get icon mapping for font-awesome.
 *
 * @return array
 */
function theme_ufpel_get_fontawesome_icon_map() {
    return [
        'theme_ufpel:course' => 'fa-graduation-cap',
        'theme_ufpel:teacher' => 'fa-user-tie',
        'theme_ufpel:progress' => 'fa-chart-line',
        'theme_ufpel:calendar' => 'fa-calendar-alt',
        'theme_ufpel:notification' => 'fa-bell',
        'theme_ufpel:settings' => 'fa-cog',
        'theme_ufpel:help' => 'fa-question-circle',
        'theme_ufpel:expand' => 'fa-expand',
        'theme_ufpel:collapse' => 'fa-compress',
        'theme_ufpel:menu' => 'fa-bars',
        'theme_ufpel:close' => 'fa-times',
        'theme_ufpel:search' => 'fa-search',
        'theme_ufpel:filter' => 'fa-filter',
        'theme_ufpel:sort' => 'fa-sort',
        'theme_ufpel:edit' => 'fa-edit',
        'theme_ufpel:delete' => 'fa-trash',
        'theme_ufpel:add' => 'fa-plus',
        'theme_ufpel:remove' => 'fa-minus',
        'theme_ufpel:check' => 'fa-check',
        'theme_ufpel:warning' => 'fa-exclamation-triangle',
        'theme_ufpel:info' => 'fa-info-circle',
        'theme_ufpel:success' => 'fa-check-circle',
        'theme_ufpel:error' => 'fa-times-circle',
    ];
}

/**
 * Initialize page requirements for the theme.
 * This function initializes JavaScript but does NOT add body classes.
 *
 * @param moodle_page $page The page object.
 * @return void
 */
function theme_ufpel_page_init(moodle_page $page) {
    global $CFG;
    
    $page->requires->js_call_amd('theme_ufpel/theme', 'init', [
        [
            'enableDarkMode' => get_config('theme_ufpel', 'enabledarkmode'),
            'enableCompactView' => get_config('theme_ufpel', 'enablecompactview'),
            'enableLazyLoad' => true,
            'enableStickyHeader' => true,
            'enableScrollTop' => true,
            'scrollTopOffset' => 300
        ]
    ]);
    
    // Add strings for JavaScript - only if component exists
    if (get_string_manager()->string_exists('darkmodeon', 'theme_ufpel')) {
        $page->requires->strings_for_js([
            'darkmodeon',
            'darkmodeoff',
            'totop',
            'skipmain',
            'loading',
            'error',
            'close',
        ], 'theme_ufpel');
    }
}

/**
 * Check if dark mode should be used.
 *
 * @return bool
 */
function theme_ufpel_should_use_dark_mode() {
    // Check user preference first
    $userpref = get_user_preferences('theme_ufpel_darkmode', null);
    if ($userpref !== null) {
        return (bool)$userpref;
    }
    
    // Check system setting
    return (bool)get_config('theme_ufpel', 'enabledarkmode');
}

/**
 * Process a file URL to ensure it's not duplicated.
 * This helper function handles the conversion of theme file URLs
 * to prevent duplication issues.
 *
 * @param mixed $url The URL to process (can be string, moodle_url, or null)
 * @return string|null The processed URL string or null if empty
 */
function theme_ufpel_process_file_url($url) {
    if (empty($url)) {
        return null;
    }
    
    // If it's already a moodle_url object, get its string representation
    if ($url instanceof moodle_url) {
        return $url->out(false);
    }
    
    // Convert to string for processing
    $urlstr = (string)$url;
    
    // Check if it's empty after conversion
    if (empty($urlstr)) {
        return null;
    }
    
    // Check if it's already an absolute URL
    // Matches: http://, https://, // (protocol-relative)
    if (preg_match('#^(https?:)?//#', $urlstr)) {
        // It's already absolute, return as is
        return $urlstr;
    }
    
    // Check if it starts with a slash (site-relative URL)
    if (strpos($urlstr, '/') === 0) {
        global $CFG;
        // It's a site-relative URL, prepend the wwwroot
        return $CFG->wwwroot . $urlstr;
    }
    
    // It's a relative URL or needs processing
    // Create a proper moodle_url and return its string representation
    try {
        $moodleurl = new moodle_url($urlstr);
        return $moodleurl->out(false);
    } catch (Exception $e) {
        // If there's an error creating the URL, return the original
        debugging('Error processing URL in theme_ufpel: ' . $e->getMessage(), DEBUG_DEVELOPER);
        return $urlstr;
    }
}

/**
 * Validate and clean a file URL to ensure it's properly formatted.
 * This function also checks for common issues like URL duplication.
 *
 * @param string $url The URL to validate
 * @return string|false The cleaned URL or false if invalid
 */
function theme_ufpel_validate_file_url($url) {
    if (empty($url)) {
        return false;
    }
    
    // Convert to string if needed
    if (is_object($url) && method_exists($url, '__toString')) {
        $url = (string)$url;
    }
    
    if (!is_string($url)) {
        return false;
    }
    
    // Check for URL duplication
    // This pattern catches URLs like: http://domain//domain/path
    if (preg_match('#^(https?://[^/]+)/+\1#i', $url)) {
        // URL is duplicated, try to fix it
        debugging('Duplicated URL detected in theme_ufpel: ' . $url, DEBUG_DEVELOPER);
        
        // Extract the duplicated part and remove it
        $parsed = parse_url($url);
        if ($parsed && isset($parsed['scheme']) && isset($parsed['host'])) {
            $base = $parsed['scheme'] . '://' . $parsed['host'];
            $path = isset($parsed['path']) ? $parsed['path'] : '';
            
            // Remove the duplicated base from the path if it exists
            $path = preg_replace('#^/+' . preg_quote($parsed['host'], '#') . '#', '', $path);
            
            // Reconstruct the URL
            $url = $base . $path;
            if (isset($parsed['query'])) {
                $url .= '?' . $parsed['query'];
            }
            if (isset($parsed['fragment'])) {
                $url .= '#' . $parsed['fragment'];
            }
        }
    }
    
    // Additional validation
    $parsed = parse_url($url);
    if (!$parsed || !isset($parsed['path'])) {
        return false;
    }
    
    // Check for double slashes in the path (except at the beginning)
    $path = $parsed['path'];
    $path = preg_replace('#/{2,}#', '/', $path);
    
    // Rebuild the URL with the cleaned path
    $cleanurl = '';
    if (isset($parsed['scheme'])) {
        $cleanurl .= $parsed['scheme'] . '://';
    }
    if (isset($parsed['host'])) {
        $cleanurl .= $parsed['host'];
    }
    if (isset($parsed['port'])) {
        $cleanurl .= ':' . $parsed['port'];
    }
    $cleanurl .= $path;
    if (isset($parsed['query'])) {
        $cleanurl .= '?' . $parsed['query'];
    }
    if (isset($parsed['fragment'])) {
        $cleanurl .= '#' . $parsed['fragment'];
    }
    
    return $cleanurl;
}

// Add this to the existing lib.php file, do not duplicate the entire file
// These are helper functions to be added to the existing lib.php

/**
 * Process a theme file URL to ensure it returns a proper moodle_url object
 * without duplication issues.
 *
 * @param mixed $url The URL to process (can be string or moodle_url)
 * @return moodle_url|null The processed moodle_url object or null if empty
 */
function theme_ufpel_process_theme_file_url($url) {
    global $CFG;
    
    if (empty($url)) {
        return null;
    }
    
    // If it's already a moodle_url object, return it
    if ($url instanceof moodle_url) {
        return $url;
    }
    
    // Convert to string for processing
    $urlstr = (string)$url;
    
    if (empty($urlstr)) {
        return null;
    }
    
    // Parse the URL to check its structure
    $parsed = parse_url($urlstr);
    
    // If the URL has a scheme (http/https), it's absolute
    if (!empty($parsed['scheme'])) {
        // Extract the path and query components
        $path = $parsed['path'] ?? '';
        if (!empty($parsed['query'])) {
            $path .= '?' . $parsed['query'];
        }
        if (!empty($parsed['fragment'])) {
            $path .= '#' . $parsed['fragment'];
        }
        
        // Check if the path contains the wwwroot path
        $wwwroot_parsed = parse_url($CFG->wwwroot);
        $wwwroot_path = $wwwroot_parsed['path'] ?? '';
        
        if (!empty($wwwroot_path) && strpos($path, $wwwroot_path) === 0) {
            // Remove the wwwroot path to make it relative
            $relative_path = substr($path, strlen($wwwroot_path));
            // Ensure it starts with /
            if (strpos($relative_path, '/') !== 0) {
                $relative_path = '/' . $relative_path;
            }
            return new moodle_url($relative_path);
        } else {
            // Use the path as is
            return new moodle_url($path);
        }
    } else {
        // It's a relative URL, safe to use with moodle_url constructor
        return new moodle_url($urlstr);
    }
}

/**
 * Safely convert a theme file URL to a string without duplication.
 *
 * @param mixed $url The URL to convert (can be string, moodle_url, or null)
 * @return string|null The URL string or null if empty
 */
function theme_ufpel_url_to_string($url) {
    if (empty($url)) {
        return null;
    }
    
    // If it's a moodle_url object, get its string representation
    if ($url instanceof moodle_url) {
        return $url->out(false);
    }
    
    // If it's already a string, process it to avoid duplication
    $urlstr = (string)$url;
    
    if (empty($urlstr)) {
        return null;
    }
    
    // Check for obvious duplication patterns
    global $CFG;
    $wwwroot = rtrim($CFG->wwwroot, '/');
    
    // Pattern: http://domain//domain/path or http://domain/domain/path
    if (preg_match('#^(https?://[^/]+)/+\1#i', $urlstr)) {
        // URL is duplicated, extract the correct part
        $parsed = parse_url($urlstr);
        if ($parsed && isset($parsed['scheme']) && isset($parsed['host'])) {
            $base = $parsed['scheme'] . '://' . $parsed['host'];
            $path = $parsed['path'] ?? '';
            
            // Remove duplicated host from path
            $path = preg_replace('#^/+' . preg_quote($parsed['host'], '#') . '#', '', $path);
            
            $urlstr = $base . $path;
            if (!empty($parsed['query'])) {
                $urlstr .= '?' . $parsed['query'];
            }
            if (!empty($parsed['fragment'])) {
                $urlstr .= '#' . $parsed['fragment'];
            }
        }
    }
    
    // Check if the URL contains the wwwroot twice
    $wwwroot_escaped = preg_quote($wwwroot, '#');
    if (preg_match('#' . $wwwroot_escaped . '/+' . $wwwroot_escaped . '#', $urlstr)) {
        // Remove the duplicate
        $urlstr = preg_replace('#' . $wwwroot_escaped . '/+' . $wwwroot_escaped . '#', $wwwroot, $urlstr);
    }
    
    return $urlstr;
}