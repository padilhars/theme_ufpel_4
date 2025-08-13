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
 * Theme UFPel config file - Fixed for Moodle 5.x
 *
 * @package    theme_ufpel
 * @copyright  2025 Universidade Federal de Pelotas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Theme name
$THEME->name = 'ufpel';

// Theme parent - inherits from Boost
$THEME->parents = ['boost'];

// Theme sheets - empty as we use SCSS
$THEME->sheets = [];

// Editor sheets
$THEME->editor_sheets = [];

// SCSS processing function
$THEME->scss = function($theme) {
    return theme_ufpel_get_main_scss_content($theme);
};

// Pre-SCSS callback
$THEME->prescsscallback = 'theme_ufpel_get_pre_scss';

// Extra SCSS callback
$THEME->extrascsscallback = 'theme_ufpel_get_extra_scss';

// CSS post-processing callback
$THEME->csstreepostprocessor = 'theme_ufpel_css_tree_post_processor';

// Layouts configuration - CORREÇÃO PRINCIPAL
$THEME->layouts = [
    // Base layouts inherit from parent
    'base' => array(
        'theme' => 'boost',
        'file' => 'drawers.php',
    ),
    // Standard layout
    'standard' => array(
        'theme' => 'boost',
        'file' => 'drawers.php',
        'regions' => array('side-pre'),
        'defaultregion' => 'side-pre',
    ),
    // Course layout
    'course' => array(
        'theme' => 'boost',
        'file' => 'drawers.php',
        'regions' => array('side-pre'),
        'defaultregion' => 'side-pre',
        'options' => array('langmenu' => true),
    ),
    // Course category
    'coursecategory' => array(
        'theme' => 'boost',
        'file' => 'drawers.php',
        'regions' => array('side-pre'),
        'defaultregion' => 'side-pre',
    ),
    // Front page
    'frontpage' => array(
        'theme' => 'boost',
        'file' => 'drawers.php',
        'regions' => array('side-pre'),
        'defaultregion' => 'side-pre',
    ),
    // Admin
    'admin' => array(
        'theme' => 'boost',
        'file' => 'drawers.php',
        'regions' => array('side-pre'),
        'defaultregion' => 'side-pre',
    ),
    // My Dashboard
    'mydashboard' => array(
        'theme' => 'boost',
        'file' => 'drawers.php',
        'regions' => array('side-pre'),
        'defaultregion' => 'side-pre',
    ),
    // My courses
    'mycourses' => array(
        'theme' => 'boost',
        'file' => 'drawers.php',
        'regions' => array('side-pre'),
        'defaultregion' => 'side-pre',
    ),
    // LOGIN LAYOUT - Configuração específica para corrigir o erro
    'login' => array(
        'file' => 'login.php',  // Usa nosso arquivo login.php customizado
        'regions' => array(),
        'options' => array(
            'langmenu' => true,
            'nonavbar' => true,
            'nofooter' => true,
            'nocourseheaderfooter' => true
        ),
    ),
    // Popup layout
    'popup' => array(
        'theme' => 'boost',
        'file' => 'embedded.php',
        'regions' => array(),
        'options' => array(
            'nofooter' => true,
            'nonavbar' => true,
            'nocourseheaderfooter' => true
        ),
    ),
    // Embedded pages
    'embedded' => array(
        'theme' => 'boost',
        'file' => 'embedded.php',
        'regions' => array(),
        'options' => array(
            'nofooter' => true,
            'nonavbar' => true,
            'nocourseheaderfooter' => true
        ),
    ),
    // Maintenance layout
    'maintenance' => array(
        'theme' => 'boost',
        'file' => 'maintenance.php',
        'regions' => array(),
    ),
    // Print layout
    'print' => array(
        'theme' => 'boost',
        'file' => 'print.php',
        'regions' => array(),
        'options' => array('nofooter' => true, 'nonavbar' => false),
    ),
    // Report layout
    'report' => array(
        'theme' => 'boost',
        'file' => 'drawers.php',
        'regions' => array('side-pre'),
        'defaultregion' => 'side-pre',
    ),
    // Secure layout (for payments, etc)
    'secure' => array(
        'theme' => 'boost',
        'file' => 'secure.php',
        'regions' => array('side-pre'),
        'defaultregion' => 'side-pre',
    ),
];

// Renderer factory
$THEME->rendererfactory = 'theme_overridden_renderer_factory';

// Required blocks
$THEME->requiredblocks = '';

// Add block position
$THEME->addblockposition = BLOCK_ADDBLOCK_POSITION_FLATNAV;

// Icon system
$THEME->iconsystem = \core\output\icon_system::FONTAWESOME;

// Features from Boost
$THEME->haseditswitch = true;
$THEME->usescourseindex = true;
$THEME->primary_navigation_favourites = true;
$THEME->usescombolistbox = true;

// Activity header configuration
$THEME->activityheaderconfig = [
    'notitle' => false,
    'nocompletion' => false,
    'nodescription' => false,
    'noavailability' => false,
    'notitlelink' => false
];

// Block RTL manipulations
$THEME->blockrtlmanipulations = [
    'side-pre' => 'side-post',
    'side-post' => 'side-pre'
];

// CSS optimisation support
$THEME->supportscssoptimisation = true;

// Enable course AJAX
$THEME->enablecourseajax = true;

// Dock is not used in modern themes
$THEME->enable_dock = false;

$THEME->presetsfiles = [
    'default.scss',
    'dark.scss',
    'plain.scss',
];

// Hide from theme selector (set to false for production)
$THEME->hidefromselector = false;

// Support for content bank
$THEME->usescontentbank = true;

// Support for activity chooser
$THEME->useactivitychooser = true;

// Support for user tours
$THEME->usesusertours = true;

// Support for notifications
$THEME->usesnotifications = true;

// Support for CSS Grid
$THEME->usescssGrid = true;