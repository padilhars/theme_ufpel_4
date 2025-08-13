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
 * Theme UFPel event handlers.
 *
 * @package    theme_ufpel
 * @copyright  2025 Universidade Federal de Pelotas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Event observers for theme_ufpel.
$observers = [
    // Clear course teachers cache when role assignments change.
    [
        'eventname'   => '\core\event\role_assigned',
        'callback'    => '\theme_ufpel\event\observer::role_assigned',
        'priority'    => 0,
    ],
    [
        'eventname'   => '\core\event\role_unassigned',
        'callback'    => '\theme_ufpel\event\observer::role_unassigned',
        'priority'    => 0,
    ],
    // Clear cache when course is updated.
    [
        'eventname'   => '\core\event\course_updated',
        'callback'    => '\theme_ufpel\event\observer::course_updated',
        'priority'    => 0,
    ],
    // Clear theme settings cache when config changes.
    [
        'eventname'   => '\core\event\config_log_created',
        'callback'    => '\theme_ufpel\event\observer::config_changed',
        'priority'    => 0,
    ],
];