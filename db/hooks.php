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
 * Hook callbacks for theme_ufpel
 *
 * @package    theme_ufpel
 * @copyright  2025 Universidade Federal de Pelotas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Define hook callbacks for Moodle 5.x
$callbacks = [
    [
        'hook' => \core\hook\output\before_standard_head_html_generation::class,
        'callback' => \theme_ufpel\hooks\output_callbacks::class . '::before_standard_head_html_generation',
        'priority' => 100,
    ],
    [
        'hook' => \core\hook\output\before_footer_html_generation::class,
        'callback' => \theme_ufpel\hooks\output_callbacks::class . '::before_footer_html_generation',
        'priority' => 100,
    ],
    // Removed before_http_headers hook as it doesn't support header modifications in Moodle 5.x
    // Security headers should be set at web server level (.htaccess or nginx.conf)
];