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
 * Event observer for theme_ufpel.
 *
 * @package    theme_ufpel
 * @copyright  2025 Universidade Federal de Pelotas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_ufpel\event;

use cache;

defined('MOODLE_INTERNAL') || die();

/**
 * Event observer class.
 *
 * @package    theme_ufpel
 * @copyright  2025 Universidade Federal de Pelotas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class observer {
    
    /**
     * Handle role assigned event.
     *
     * @param \core\event\role_assigned $event The event.
     * @return void
     */
    public static function role_assigned(\core\event\role_assigned $event) {
        self::clear_course_teachers_cache($event->courseid);
    }
    
    /**
     * Handle role unassigned event.
     *
     * @param \core\event\role_unassigned $event The event.
     * @return void
     */
    public static function role_unassigned(\core\event\role_unassigned $event) {
        self::clear_course_teachers_cache($event->courseid);
    }
    
    /**
     * Handle course updated event.
     *
     * @param \core\event\course_updated $event The event.
     * @return void
     */
    public static function course_updated(\core\event\course_updated $event) {
        self::clear_course_teachers_cache($event->courseid);
    }
    
    /**
     * Handle config changed event.
     *
     * @param \core\event\config_log_created $event The event.
     * @return void
     */
    public static function config_changed(\core\event\config_log_created $event) {
        // Only clear cache if it's a theme_ufpel config change.
        $eventdata = $event->get_data();
        if (isset($eventdata['other']['plugin']) && $eventdata['other']['plugin'] === 'theme_ufpel') {
            self::clear_theme_settings_cache();
        }
    }
    
    /**
     * Clear course teachers cache for a specific course.
     *
     * @param int $courseid The course ID.
     * @return void
     */
    protected static function clear_course_teachers_cache($courseid) {
        if ($courseid) {
            $cache = cache::make('theme_ufpel', 'courseteachers');
            $cache->delete($courseid);
        }
    }
    
    /**
     * Clear theme settings cache.
     *
     * @return void
     */
    protected static function clear_theme_settings_cache() {
        $cache = cache::make('theme_ufpel', 'themesettings');
        $cache->purge();
        
        // Also reset theme caches.
        theme_reset_all_caches();
    }
}