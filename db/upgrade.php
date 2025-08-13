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
 * Theme UFPel upgrade script - Fixed version without function redeclaration.
 *
 * @package    theme_ufpel
 * @copyright  2025 Universidade Federal de Pelotas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade function for theme_ufpel.
 *
 * @param int $oldversion The version we are upgrading from.
 * @return bool Result of upgrade.
 */
function xmldb_theme_ufpel_upgrade($oldversion) {
    global $DB, $CFG;
    
    $dbman = $DB->get_manager();
    
    // Version 2025072901 - Migrate brandcolor to primarycolor
    if ($oldversion < 2025072901) {
        // Log message using mtrace (standard Moodle way)
        //mtrace('theme_ufpel: Migrating brand color to primary color');
        
        // Migrate brandcolor setting to primarycolor if it exists
        $brandcolor = get_config('theme_ufpel', 'brandcolor');
        if ($brandcolor !== false && get_config('theme_ufpel', 'primarycolor') === false) {
            set_config('primarycolor', $brandcolor, 'theme_ufpel');
            //mtrace('theme_ufpel: Brand color migrated to primary color: ' . $brandcolor);
        }
        
        // Set default values for new color settings if not already set
        $colorsettings = [
            'backgroundcolor' => '#ffffff',
            'highlightcolor' => '#ffc107',
            'contenttextcolor' => '#212529',
            'highlighttextcolor' => '#ffffff'
        ];
        
        foreach ($colorsettings as $setting => $default) {
            if (get_config('theme_ufpel', $setting) === false) {
                set_config($setting, $default, 'theme_ufpel');
                //mtrace("theme_ufpel: Set default {$setting}: {$default}");
            }
        }
        
        // Clear all theme caches to ensure new styles are loaded
        //theme_reset_all_caches();
        
        upgrade_plugin_savepoint(true, 2025072901, 'theme', 'ufpel');
    }
    
    // Version 2025080100 - Migrate to Bootstrap 5 classes
    if ($oldversion < 2025080100) {
        //mtrace('theme_ufpel: Migrating to Bootstrap 5');
        
        // Update any stored HTML content with old Bootstrap classes
        // This would typically update content in settings that contain HTML
        $htmlsettings = ['footercontent', 'customhtml'];
        
        foreach ($htmlsettings as $setting) {
            $content = get_config('theme_ufpel', $setting);
            if ($content !== false) {
                // Replace Bootstrap 4 classes with Bootstrap 5
                $replacements = [
                    'ml-' => 'ms-',
                    'mr-' => 'me-',
                    'pl-' => 'ps-',
                    'pr-' => 'pe-',
                    'text-left' => 'text-start',
                    'text-right' => 'text-end',
                    'float-left' => 'float-start',
                    'float-right' => 'float-end',
                    'sr-only' => 'visually-hidden',
                    'sr-only-focusable' => 'visually-hidden-focusable',
                    'badge-pill' => 'rounded-pill',
                    'badge-' => 'bg-',
                    'close' => 'btn-close',
                    'custom-control' => 'form-check',
                    'custom-checkbox' => 'form-check',
                    'custom-control-input' => 'form-check-input',
                    'custom-control-label' => 'form-check-label',
                    'custom-switch' => 'form-switch',
                    'custom-select' => 'form-select',
                    'custom-file' => 'd-none',
                    'form-control-file' => 'form-control',
                    'input-group-append' => 'input-group-text',
                    'input-group-prepend' => 'input-group-text',
                ];
                
                foreach ($replacements as $old => $new) {
                    $content = str_replace($old, $new, $content);
                }
                
                set_config($setting, $content, 'theme_ufpel');
                //mtrace("theme_ufpel: Updated Bootstrap classes in {$setting}");
            }
        }
        
        // Clear theme caches
        //theme_reset_all_caches();
        
        upgrade_plugin_savepoint(true, 2025080100, 'theme', 'ufpel');
    }
    
    // Version 2025090100 - Add new features for Moodle 5.x
    if ($oldversion < 2025090100) {
        //mtrace('theme_ufpel: Adding Moodle 5.x features');
        
        // Add new settings for Moodle 5.x features
        $newsettings = [
            'enabledarkmode' => '0',
            'enablecompactview' => '0',
            'showcourseprogressinheader' => '1',
            'showcoursesummary' => '1',
            'enablelazyloading' => '1',
            'enableanimations' => '1',
            'enableaccessibilitytools' => '1',
        ];
        
        foreach ($newsettings as $setting => $default) {
            if (get_config('theme_ufpel', $setting) === false) {
                set_config($setting, $default, 'theme_ufpel');
                //mtrace("theme_ufpel: Added setting {$setting}: {$default}");
            }
        }
        
        // Purge caches with new definitions - with error handling
        try {
            if (class_exists('cache_helper')) {
                cache_helper::purge_by_definition('theme_ufpel', 'courseteachers');
                cache_helper::purge_by_definition('theme_ufpel', 'themesettings');
            }
        } catch (Exception $e) {
            //mtrace('theme_ufpel: Cache definitions not found (will be created on first use)');
        }
        
        upgrade_plugin_savepoint(true, 2025090100, 'theme', 'ufpel');
    }
    
    // Version 2025100100 - Performance optimizations
    if ($oldversion < 2025100100) {
        //mtrace('theme_ufpel: Applying performance optimizations');
        
        // Enable CSS optimization by default
        if (get_config('theme_ufpel', 'enablecssoptimization') === false) {
            set_config('enablecssoptimization', '1', 'theme_ufpel');
        }
        
        // Enable resource hints
        if (get_config('theme_ufpel', 'enableresourcehints') === false) {
            set_config('enableresourcehints', '1', 'theme_ufpel');
        }
        
        // Clear all caches and rebuild
        //theme_reset_all_caches();
        //cache_helper::purge_all();
        
        // Rebuild course cache to apply new features
        //rebuild_course_cache(0, true);
        
        upgrade_plugin_savepoint(true, 2025100100, 'theme', 'ufpel');
    }
    
    // Version 2025110100 - Clean up deprecated settings
    if ($oldversion < 2025110100) {
        //mtrace('theme_ufpel: Cleaning up deprecated settings');
        
        // Remove deprecated settings
        $deprecated = [
            'brandcolor',  // Migrated to primarycolor
            'oldsettingname',  // Example deprecated setting
        ];
        
        foreach ($deprecated as $setting) {
            if (get_config('theme_ufpel', $setting) !== false) {
                unset_config($setting, 'theme_ufpel');
                //mtrace("theme_ufpel: Removed deprecated setting: {$setting}");
            }
        }
        
        // Final cache clear
        //theme_reset_all_caches();
        
        upgrade_plugin_savepoint(true, 2025110100, 'theme', 'ufpel');
    }
    
    // Version 2025120100 - Migrate to new hooks system
    if ($oldversion < 2025120100) {
        //mtrace('theme_ufpel: Migrating to new hooks system');
        
        // Clear all caches to ensure new hooks are registered
        //cache_helper::purge_all();
        //theme_reset_all_caches();
        
        // The actual migration is handled by the new files
        // Old callbacks will still work but show deprecation notices
        
        //mtrace('theme_ufpel: Hooks system migration completed');
        
        upgrade_plugin_savepoint(true, 2025120100, 'theme', 'ufpel');
    }
    
    // Version 2025120102 - Remove deprecated callbacks completely
    if ($oldversion < 2025120102) {
        //mtrace('theme_ufpel: Removing deprecated callback functions');
        
        // Clear all caches to ensure the new system is used
        //purge_all_caches();
        
        // Clear compiled mustache templates
        $cachedir = $CFG->dataroot . '/localcache/mustache';
        if (is_dir($cachedir)) {
            $ufpeldir = $cachedir . '/-1/ufpel';
            if (is_dir($ufpeldir)) {
                // Use Moodle's remove_dir function if available
                if (function_exists('remove_dir')) {
                    remove_dir($ufpeldir, true);
                } else {
                    // Fallback to manual deletion
                    $files = glob($ufpeldir . '/*');
                    foreach ($files as $file) {
                        if (is_file($file)) {
                            unlink($file);
                        }
                    }
                    @rmdir($ufpeldir);
                }
                //mtrace('theme_ufpel: Cleared compiled mustache templates');
            }
        }
        
        // Rebuild theme cache
        //theme_reset_all_caches();
        
        // Note: The deprecated functions have been removed from lib.php
        // The new hooks system in classes/hooks/output_callbacks.php handles all functionality
        
        //mtrace('theme_ufpel: Deprecated callbacks removed, using hooks system exclusively');
        
        upgrade_plugin_savepoint(true, 2025120102, 'theme', 'ufpel');
    }
    
    // Version 2025120103 - Fix string references and renderer issues
    if ($oldversion < 2025120103) {
        //mtrace('theme_ufpel: Fixing string references and renderer issues');
        
        // Clear all caches
        //theme_reset_all_caches();
        //purge_all_caches();
        
        // Ensure required settings are configured
        $requiredSettings = [
            'primarycolor' => '#003366',
            'secondarycolor' => '#0066cc',
            'backgroundcolor' => '#ffffff',
            'highlightcolor' => '#ffc107',
            'contenttextcolor' => '#212529',
            'highlighttextcolor' => '#ffffff'
        ];
        
        foreach ($requiredSettings as $setting => $default) {
            if (get_config('theme_ufpel', $setting) === false) {
                set_config($setting, $default, 'theme_ufpel');
                //mtrace("theme_ufpel: Set required setting {$setting}: {$default}");
            }
        }
        
        //mtrace('theme_ufpel: String references and renderer issues fixed');
        
        upgrade_plugin_savepoint(true, 2025120103, 'theme', 'ufpel');
    }
    
    // Version 2025120200 - Fix template string references
    if ($oldversion < 2025120200) {
        //mtrace('theme_ufpel: Fixing template string references');
        
        // Clear template cache to ensure updated templates are used
        $cachedir = $CFG->dataroot . '/localcache/mustache/-1/ufpel';
        if (is_dir($cachedir)) {
            // Remove all cached templates
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($cachedir, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );
            
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    unlink($file->getPathname());
                } elseif ($file->isDir()) {
                    rmdir($file->getPathname());
                }
            }
            
            @rmdir($cachedir);
            //mtrace('theme_ufpel: Cleared all cached templates');
        }
        
        // Clear all caches
        //theme_reset_all_caches();
        //purge_all_caches();
        
        //mtrace('theme_ufpel: Template string references fixed');
        
        upgrade_plugin_savepoint(true, 2025120200, 'theme', 'ufpel');
    }
    
    // Version 2025120201 - Fix course header image loading issue
    if ($oldversion < 2025120201) {
        //mtrace('theme_ufpel: Fixing course header image loading issue');
        
        // Clear all template caches
        $cachedir = $CFG->dataroot . '/localcache/mustache/-1/ufpel';
        if (is_dir($cachedir)) {
            // Remove all cached templates
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($cachedir, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );
            
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    unlink($file->getPathname());
                } elseif ($file->isDir()) {
                    rmdir($file->getPathname());
                }
            }
            
            @rmdir($cachedir);
            //mtrace('theme_ufpel: Cleared all cached templates for image fix');
        }
        
        // Clear JavaScript cache
        $jscachedir = $CFG->dataroot . '/localcache/js';
        if (is_dir($jscachedir)) {
            $jsfiles = glob($jscachedir . '/*ufpel*');
            foreach ($jsfiles as $jsfile) {
                if (is_file($jsfile)) {
                    unlink($jsfile);
                }
            }
            //mtrace('theme_ufpel: Cleared JavaScript cache');
        }
        
        // Clear all caches
        //theme_reset_all_caches();
        //purge_all_caches();
        
        // Rebuild course cache to ensure new template is used
        //rebuild_course_cache(0, true);
        
        //mtrace('theme_ufpel: Course header image loading issue fixed');
       // mtrace('theme_ufpel: Templates now use direct image loading instead of lazy loading for course headers');
        
        upgrade_plugin_savepoint(true, 2025120201, 'theme', 'ufpel');
    }
    
    // Version 2025120202 - Fix login page logo URL duplication issue
    if ($oldversion < 2025120202) {
        //mtrace('theme_ufpel: Fixing login page logo URL duplication issue');
        
        // Clear all template caches
        $cachedir = $CFG->dataroot . '/localcache/mustache/-1/ufpel';
        if (is_dir($cachedir)) {
            // Remove all cached templates
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($cachedir, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );
            
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    unlink($file->getPathname());
                } elseif ($file->isDir()) {
                    rmdir($file->getPathname());
                }
            }
            
            @rmdir($cachedir);
            //mtrace('theme_ufpel: Cleared all cached templates for URL fix');
        }
        
        // Clear PHP cache if opcache is enabled
        if (function_exists('opcache_reset')) {
            opcache_reset();
            //mtrace('theme_ufpel: Cleared PHP opcache');
        }
        
        // Clear all caches
        //theme_reset_all_caches();
        //purge_all_caches();
        
        //mtrace('theme_ufpel: Login page logo URL duplication issue fixed');
        //mtrace('theme_ufpel: URLs are now properly handled to prevent duplication');
        
        upgrade_plugin_savepoint(true, 2025120202, 'theme', 'ufpel');
    }

    // Version 2025120203 - Fix URL duplication issues definitively
    if ($oldversion < 2025120203) {
        //mtrace('theme_ufpel: Fixing URL duplication issues definitively');
        
        // Clear all template caches to ensure new templates are used
        $cachedir = $CFG->dataroot . '/localcache/mustache/-1/ufpel';
        if (is_dir($cachedir)) {
            // Remove all cached templates
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($cachedir, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );
            
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    unlink($file->getPathname());
                } elseif ($file->isDir()) {
                    rmdir($file->getPathname());
                }
            }
            
            @rmdir($cachedir);
            //mtrace('theme_ufpel: Cleared all cached templates');
        }
        
        // Clear all JavaScript cache  
        $jscachedir = $CFG->dataroot . '/localcache/js';
        if (is_dir($jscachedir)) {
            $jsfiles = glob($jscachedir . '/*theme_ufpel*');
            foreach ($jsfiles as $jsfile) {
                if (is_file($jsfile)) {
                    unlink($jsfile);
                }
            }
            //mtrace('theme_ufpel: Cleared JavaScript cache');
        }
        
        // Clear PHP opcache if available
        if (function_exists('opcache_reset')) {
            opcache_reset();
            //mtrace('theme_ufpel: Cleared PHP opcache');
        }
        
        // Clear all Moodle caches
        //theme_reset_all_caches();
        //purge_all_caches();
        
        // Rebuild course cache
        //rebuild_course_cache(0, true);
        
        //mtrace('theme_ufpel: URL duplication issues fixed definitively');
        //mtrace('theme_ufpel: The get_logo_url() method now properly returns moodle_url objects');
        //mtrace('theme_ufpel: Login layout now correctly handles moodle_url objects');
        
        upgrade_plugin_savepoint(true, 2025120203, 'theme', 'ufpel');
    }
    
    // Always clear theme caches at the end of upgrade
    theme_reset_all_caches();
    
    // Log successful upgrade
    //mtrace('theme_ufpel: Theme UFPel upgrade completed successfully');
    
    return true;
}