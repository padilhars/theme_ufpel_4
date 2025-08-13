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
 * Version information for theme_ufpel
 *
 * @package    theme_ufpel
 * @copyright  2025 Universidade Federal de Pelotas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Plugin version (YYYYMMDDXX where XX is the daily increment).
$plugin->version   = 2025120203; // Fixed URL duplication issues definitively
$plugin->requires  = 2024100700; // Moodle 5.0 or later

// Plugin component name.
$plugin->component = 'theme_ufpel';

// Plugin maturity level.
$plugin->maturity  = MATURITY_STABLE;

// Human-readable release version.
$plugin->release   = '1.2.5'; // Fixed URL duplication definitively

// Dependencies.
$plugin->dependencies = [
    'theme_boost' => 2024100700, // Require Boost theme from Moodle 5.0
];

// Optional: supported Moodle versions.
$plugin->supported = [50, 51]; // Moodle 5.0 to 5.1