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
 * FIXED: Only initialize JavaScript when actually needed and ensure proper error handling.
 *
 * @param moodle_page $page The page object.
 * @return void
 */
function theme_ufpel_page_init(moodle_page $page) {
    global $CFG;
    
    // Only initialize JavaScript on pages that need it
    if (!theme_ufpel_should_init_js($page)) {
        return;
    }
    
    try {
        // Initialize theme JavaScript with proper configuration
        $config = [
            'enableDarkMode' => (bool)get_config('theme_ufpel', 'enabledarkmode'),
            'enableCompactView' => (bool)get_config('theme_ufpel', 'enablecompactview'),
            'enableLazyLoad' => true,
            'enableStickyHeader' => true,
            'enableScrollTop' => true,
            'scrollTopOffset' => 300
        ];
        
        // Call AMD module with error handling
        $page->requires->js_call_amd('theme_ufpel/theme', 'init', [$config]);
        
        // Add strings for JavaScript only if they exist
        $strings = [];
        if (get_string_manager()->string_exists('darkmodeon', 'theme_ufpel')) {
            $strings[] = 'darkmodeon';
        }
        if (get_string_manager()->string_exists('darkmodeoff', 'theme_ufpel')) {
            $strings[] = 'darkmodeoff';
        }
        if (get_string_manager()->string_exists('totop', 'theme_ufpel')) {
            $strings[] = 'totop';
        }
        if (get_string_manager()->string_exists('skipmain', 'theme_ufpel')) {
            $strings[] = 'skipmain';
        }
        if (get_string_manager()->string_exists('loading', 'theme_ufpel')) {
            $strings[] = 'loading';
        }
        if (get_string_manager()->string_exists('error', 'theme_ufpel')) {
            $strings[] = 'error';
        }
        if (get_string_manager()->string_exists('close', 'theme_ufpel')) {
            $strings[] = 'close';
        }
        
        if (!empty($strings)) {
            $page->requires->strings_for_js($strings, 'theme_ufpel');
        }
        
    } catch (Exception $e) {
        // Log error but don't break the page
        debugging('Error initializing theme_ufpel JavaScript: ' . $e->getMessage(), DEBUG_DEVELOPER);
    }
}

/**
 * Check if JavaScript should be initialized for this page.
 *
 * @param moodle_page $page The page object.
 * @return bool True if JavaScript should be initialized.
 */
function theme_ufpel_should_init_js($page) {
    // Don't initialize on maintenance pages
    if ($page->pagelayout === 'maintenance') {
        return false;
    }
    
    // Don't initialize on print pages
    if ($page->pagelayout === 'print') {
        return false;
    }
    
    // Don't initialize on embedded pages unless specifically needed
    if ($page->pagelayout === 'embedded') {
        return false;
    }
    
    // Don't initialize during AJAX requests unless needed
    if (defined('AJAX_SCRIPT') && AJAX_SCRIPT) {
        return false;
    }
    
    // Initialize on all other pages
    return true;
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
 * Additional hook for page setup (called early in page lifecycle).
 * This ensures proper initialization without conflicts.
 *
 * @param moodle_page $page The page object.
 * @return void
 *
function theme_ufpel_before_http_headers($page) {
    // This function can be used for early page setup that needs to happen
    // before headers are sent, but currently we don't need any such setup
}
*/

/**
 * Additional hook for standard head HTML generation.
 * This ensures proper CSS and meta tag inclusion.
 *
 * @param moodle_page $page The page object.
 * @return string Additional HTML for head section.
 */
function theme_ufpel_before_standard_head_html($page) {
    $html = '';
    
    // Add theme color meta tag
    $primarycolor = get_config('theme_ufpel', 'primarycolor') ?: '#003366';
    $html .= '<meta name="theme-color" content="' . $primarycolor . '">' . "\n";
    $html .= '<meta name="apple-mobile-web-app-status-bar-style" content="'. $primarycolor .'">' . "\n";
    $html .= '<meta name="msapplication-navbutton-color" content="'. $primarycolor .'">' . "\n";
    
    
    // Add generator meta tag
    $html .= '<meta name="generator" content="Moodle - UFPel Theme">' . "\n";
    
    
    return $html;
}
