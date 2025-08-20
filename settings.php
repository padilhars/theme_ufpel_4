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
 * Theme UFPel settings.
 *
 * @package    theme_ufpel
 * @copyright  2025 Universidade Federal de Pelotas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Required for Moodle 5.0+
require_once($CFG->dirroot . '/lib/adminlib.php');

if ($ADMIN->fulltree) {
    
    // Create settings page with tabs.
    $settings = new theme_boost_admin_settingspage_tabs('themesettingufpel', get_string('configtitle', 'theme_ufpel'));
    
    // =========================================================================
    // GENERAL SETTINGS TAB
    // =========================================================================
    $page = new admin_settingpage('theme_ufpel_general', get_string('generalsettings', 'theme_ufpel'));
    
    // Preset setting.
    $name = 'theme_ufpel/preset';
    $title = get_string('preset', 'theme_ufpel');
    $description = get_string('preset_desc', 'theme_ufpel');
    $default = 'default.scss';
    
    // Dynamically list available presets.
    $choices = [];
    $presetsdir = $CFG->dirroot . '/theme/ufpel/scss/preset/';
    
    if (is_dir($presetsdir)) {
        $presets = scandir($presetsdir);
        foreach ($presets as $preset) {
            if (substr($preset, -5) === '.scss' && $preset !== '.' && $preset !== '..') {
                $presetname = substr($preset, 0, -5);
                $choices[$preset] = ucfirst(str_replace('_', ' ', $presetname));
            }
        }
    }
    
    // Ensure default is always available
    if (!isset($choices['default.scss'])) {
        $choices['default.scss'] = get_string('default', 'theme_ufpel');
    }
    
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    
    // Preset files setting.
    $name = 'theme_ufpel/presetfiles';
    $title = get_string('presetfiles', 'theme_ufpel');
    $description = get_string('presetfiles_desc', 'theme_ufpel');
    
    $setting = new admin_setting_configstoredfile(
        $name, 
        $title, 
        $description, 
        'preset', 
        0,
        ['maxfiles' => 10, 'accepted_types' => ['.scss']]
    );
    $page->add($setting);
    
    // Primary color setting (renamed from Brand color).
    $name = 'theme_ufpel/primarycolor';
    $title = get_string('primarycolor', 'theme_ufpel');
    $description = get_string('primarycolor_desc', 'theme_ufpel');
    $default = '#003366';
    
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    
    // Secondary color setting.
    $name = 'theme_ufpel/secondarycolor';
    $title = get_string('secondarycolor', 'theme_ufpel');
    $description = get_string('secondarycolor_desc', 'theme_ufpel');
    $default = '#0066cc';
    
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    
    // Background color setting.
    $name = 'theme_ufpel/backgroundcolor';
    $title = get_string('backgroundcolor', 'theme_ufpel');
    $description = get_string('backgroundcolor_desc', 'theme_ufpel');
    $default = '#ffffff';
    
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    
    // Highlight color setting.
    $name = 'theme_ufpel/highlightcolor';
    $title = get_string('highlightcolor', 'theme_ufpel');
    $description = get_string('highlightcolor_desc', 'theme_ufpel');
    $default = '#ffc107';
    
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    
    // Content text color setting.
    $name = 'theme_ufpel/contenttextcolor';
    $title = get_string('contenttextcolor', 'theme_ufpel');
    $description = get_string('contenttextcolor_desc', 'theme_ufpel');
    $default = '#212529';
    
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    
    // Highlight text color setting.
    $name = 'theme_ufpel/highlighttextcolor';
    $title = get_string('highlighttextcolor', 'theme_ufpel');
    $description = get_string('highlighttextcolor_desc', 'theme_ufpel');
    $default = '#ffffff';
    
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    
    // Logo setting.
    $name = 'theme_ufpel/logo';
    $title = get_string('logo', 'theme_ufpel');
    $description = get_string('logo_desc', 'theme_ufpel');
    
    $setting = new admin_setting_configstoredfile(
        $name,
        $title,
        $description,
        'logo',
        0,
        ['maxfiles' => 1, 'accepted_types' => ['image']]
    );
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    
    // Login background image.
    $name = 'theme_ufpel/loginbackgroundimage';
    $title = get_string('loginbackgroundimage', 'theme_ufpel');
    $description = get_string('loginbackgroundimage_desc', 'theme_ufpel');
    
    $setting = new admin_setting_configstoredfile(
        $name, 
        $title, 
        $description, 
        'loginbackgroundimage',
        0,
        ['maxfiles' => 1, 'accepted_types' => ['image']]
    );
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    
    // Favicon setting.
    $name = 'theme_ufpel/favicon';
    $title = get_string('favicon', 'theme_ufpel');
    $description = get_string('favicon_desc', 'theme_ufpel');
    
    $setting = new admin_setting_configstoredfile(
        $name,
        $title,
        $description,
        'favicon',
        0,
        ['maxfiles' => 1, 'accepted_types' => ['.ico', '.png', '.svg']]
    );
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    
    // Custom fonts setting.
    $name = 'theme_ufpel/customfonts';
    $title = get_string('customfonts', 'theme_ufpel');
    $description = get_string('customfonts_desc', 'theme_ufpel');
    $default = '';
    
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    
    $settings->add($page);
    
    // =========================================================================
    // ADVANCED SETTINGS TAB
    // =========================================================================
    $page = new admin_settingpage('theme_ufpel_advanced', get_string('advancedsettings', 'theme_ufpel'));
    
    // Raw SCSS - Use with caution.
    $name = 'theme_ufpel/rawscss';
    $title = get_string('rawscss', 'theme_ufpel');
    $description = get_string('rawscss_desc', 'theme_ufpel');
    $default = '';
    
    $setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    
    // Raw pre-SCSS - Variables and mixins.
    $name = 'theme_ufpel/rawscsspre';
    $title = get_string('rawscsspre', 'theme_ufpel');
    $description = get_string('rawscsspre_desc', 'theme_ufpel');
    $default = '';
    
    $setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    
    // Custom CSS - For simple CSS rules.
    $name = 'theme_ufpel/customcss';
    $title = get_string('customcss', 'theme_ufpel');
    $description = get_string('customcss_desc', 'theme_ufpel');
    $default = '';
    
    $setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    
    $settings->add($page);
    
    // =========================================================================
    // FEATURES TAB
    // =========================================================================
    $page = new admin_settingpage('theme_ufpel_features', get_string('features', 'theme_ufpel'));
    
    // Show course image in header.
    $name = 'theme_ufpel/showcourseimage';
    $title = get_string('showcourseimage', 'theme_ufpel');
    $description = get_string('showcourseimage_desc', 'theme_ufpel');
    $default = 1;
    
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $page->add($setting);
    
    // Show teachers in course header.
    $name = 'theme_ufpel/showteachers';
    $title = get_string('showteachers', 'theme_ufpel');
    $description = get_string('showteachers_desc', 'theme_ufpel');
    $default = 1;
    
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $page->add($setting);
    
    // Enable course header overlay.
    $name = 'theme_ufpel/courseheaderoverlay';
    $title = get_string('courseheaderoverlay', 'theme_ufpel');
    $description = get_string('courseheaderoverlay_desc', 'theme_ufpel');
    $default = 1;
    
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $page->add($setting);
    
    // Footer content.
    $name = 'theme_ufpel/footercontent';
    $title = get_string('footercontent', 'theme_ufpel');
    $description = get_string('footercontent_desc', 'theme_ufpel');
    $default = '';
    
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $page->add($setting);
    
    $settings->add($page);
}