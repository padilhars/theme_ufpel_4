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
 * Helper functions for theme_ufpel.
 *
 * @package    theme_ufpel
 * @copyright  2025 Universidade Federal de Pelotas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_ufpel;

use context_course;
use moodle_url;
use cache;

defined('MOODLE_INTERNAL') || die();

/**
 * Helper class for theme_ufpel.
 *
 * @package    theme_ufpel
 * @copyright  2025 Universidade Federal de Pelotas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {
    
    /**
     * Get theme settings with caching.
     *
     * @return \stdClass Theme settings object.
     */
    public static function get_theme_settings() {
        $cache = cache::make('theme_ufpel', 'themesettings');
        $settings = $cache->get('settings');
        
        if ($settings === false) {
            $settings = new \stdClass();
            
            // Get all theme settings.
            $settings->primarycolor = get_config('theme_ufpel', 'primarycolor');
            $settings->secondarycolor = get_config('theme_ufpel', 'secondarycolor');
            $settings->backgroundcolor = get_config('theme_ufpel', 'backgroundcolor');
            $settings->highlightcolor = get_config('theme_ufpel', 'highlightcolor');
            $settings->contenttextcolor = get_config('theme_ufpel', 'contenttextcolor');
            $settings->highlighttextcolor = get_config('theme_ufpel', 'highlighttextcolor');
            $settings->showcourseimage = get_config('theme_ufpel', 'showcourseimage');
            $settings->showteachers = get_config('theme_ufpel', 'showteachers');
            $settings->courseheaderoverlay = get_config('theme_ufpel', 'courseheaderoverlay');
            $settings->footercontent = get_config('theme_ufpel', 'footercontent');
            $settings->customfonts = get_config('theme_ufpel', 'customfonts');
            
            // Process and validate settings.
            $settings = self::validate_settings($settings);
            
            // Cache the settings.
            $cache->set('settings', $settings);
        }
        
        return $settings;
    }
    
    /**
     * Validate and process theme settings.
     *
     * @param \stdClass $settings Raw settings object.
     * @return \stdClass Validated settings object.
     */
    protected static function validate_settings($settings) {
        // Validate colors.
        if (empty($settings->primarycolor) || !preg_match('/^#[a-f0-9]{6}$/i', $settings->primarycolor)) {
            $settings->primarycolor = '#003366';
        }
        
        if (empty($settings->secondarycolor) || !preg_match('/^#[a-f0-9]{6}$/i', $settings->secondarycolor)) {
            $settings->secondarycolor = '#0066cc';
        }
        
        if (empty($settings->backgroundcolor) || !preg_match('/^#[a-f0-9]{6}$/i', $settings->backgroundcolor)) {
            $settings->backgroundcolor = '#ffffff';
        }
        
        if (empty($settings->highlightcolor) || !preg_match('/^#[a-f0-9]{6}$/i', $settings->highlightcolor)) {
            $settings->highlightcolor = '#ffc107';
        }
        
        if (empty($settings->contenttextcolor) || !preg_match('/^#[a-f0-9]{6}$/i', $settings->contenttextcolor)) {
            $settings->contenttextcolor = '#212529';
        }
        
        if (empty($settings->highlighttextcolor) || !preg_match('/^#[a-f0-9]{6}$/i', $settings->highlighttextcolor)) {
            $settings->highlighttextcolor = '#ffffff';
        }
        
        // Ensure boolean values.
        $settings->showcourseimage = !empty($settings->showcourseimage);
        $settings->showteachers = !empty($settings->showteachers);
        $settings->courseheaderoverlay = !empty($settings->courseheaderoverlay);
        
        // Clean HTML content.
        if (!empty($settings->footercontent)) {
            $settings->footercontent = clean_text($settings->footercontent, FORMAT_HTML);
        }
        
        return $settings;
    }
    
    /**
     * Get course image URL with fallback.
     *
     * @param int $courseid Course ID.
     * @param string $filearea File area to check (default: overviewfiles).
     * @return string|null Image URL or null if not found.
     */
    public static function get_course_image_url($courseid, $filearea = 'overviewfiles') {
        if (empty($courseid) || $courseid == SITEID) {
            return null;
        }
        
        $context = context_course::instance($courseid);
        $fs = get_file_storage();
        
        // Try to get course image.
        $files = $fs->get_area_files($context->id, 'course', $filearea, 0, 'filename', false);
        
        if ($files) {
            foreach ($files as $file) {
                if ($file->is_valid_image()) {
                    return moodle_url::make_pluginfile_url(
                        $context->id,
                        'course',
                        $filearea,
                        0,
                        '/',
                        $file->get_filename()
                    )->out();
                }
            }
        }
        
        // Try legacy course image location.
        if ($filearea === 'overviewfiles') {
            return self::get_course_image_url($courseid, 'images');
        }
        
        return null;
    }
    
}