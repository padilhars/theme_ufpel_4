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
 * Renderers to align UFPel theme with Moodle's bootstrap renderer.
 *
 * @package    theme_ufpel
 * @copyright  2025 Universidade Federal de Pelotas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_ufpel\output;

use html_writer;
use moodle_url;
use context_course;
use cache;
use stdClass;

defined('MOODLE_INTERNAL') || die;

/**
 * Renderers to align UFPel theme with Moodle's bootstrap renderer.
 *
 * @package    theme_ufpel
 * @copyright  2025 Universidade Federal de Pelotas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_renderer extends \theme_boost\output\core_renderer {
        
    /**
     * Returns the URL for the favicon.
     *
     * @return moodle_url The favicon URL
     */
    public function favicon() {
        // Check if we have a custom favicon.
        $favicon = $this->page->theme->setting_file_url('favicon', 'favicon');
        if (!empty($favicon)) {
            return $favicon;
        }
        
        return parent::favicon();
    }
    
    /**
     * Get the logo URL for the theme.
     *
     * @param int $maxwidth The maximum width, or null when the maximum width does not matter.
     * @param int $maxheight The maximum height, or null when the maximum height does not matter.
     * @return moodle_url|bool The logo URL or false
     */
    public function get_logo_url($maxwidth = null, $maxheight = 200) {
        // Check if logo setting exists and has a file
        $logo_setting = get_config('theme_ufpel', 'logo');
        
        if (!empty($logo_setting)) {
            $logo = $this->page->theme->setting_file_url('logo', 'logo');
            if ($logo) {
                // If it's already a moodle_url object, return it
                if ($logo instanceof moodle_url) {
                    return $logo;
                }
                // If it's a string (which is valid), convert to moodle_url
                if (is_string($logo)) {
                    // Handle protocol-relative URLs (starting with //) using current protocol
                    if (strpos($logo, '//') === 0) {
                        global $CFG;
                        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https:' : 'http:';
                        $logo = $protocol . $logo;
                    }
                    return new moodle_url($logo);
                }
                // If it's some other type, log and return false
                debugging('Logo setting returned unexpected type: ' . gettype($logo) . ' - Value: ' . print_r($logo, true), DEBUG_DEVELOPER);
                return false;
            }
        }
        
        return false;
    }
    
    /**
     * Override navbar brand to use logo if available from theme settings.
     * Simplified version without compact logo support.
     *
     * @return string HTML for navbar brand
     */
    public function navbar_brand() {
        global $SITE, $CFG;
        
        // Get logo URL
        $logourl = $this->get_logo_url(null, 40);
        
        // Determine the site name to display
        $sitename = '';
        if ($this->page->pagelayout === 'frontpage' || $this->page->pagetype === 'site-index') {
            // On frontpage, use full site name
            $sitename = format_string($SITE->fullname, true, 
                ['context' => \context_system::instance()]);
        } else if (isset($this->page->course) && $this->page->course->id != SITEID) {
            // On course pages, use course name
            $sitename = format_string($this->page->course->fullname, true, 
                ['context' => \context_course::instance($this->page->course->id)]);
        } else {
            // Default to site shortname
            $sitename = format_string($SITE->shortname, true, 
                ['context' => \context_system::instance()]);
        }
        
        // Build home URL
        $homeurl = new \moodle_url('/');
        
        // Check if we should show text alongside logo
        $showtext = get_config('theme_ufpel', 'showsitenamewithlogo');
        
        // Prepare template context
        $templatecontext = [
            'logourl' => $logourl ? $logourl->out(false) : null,
            'sitename' => $sitename,
            'homeurl' => $homeurl->out(false),
            'showtext' => $showtext,
            'haslogo' => !empty($logourl)
        ];
        
        // Add responsive logo dimensions if configured
        $logowidth = get_config('theme_ufpel', 'logowidth');
        if (!empty($logowidth) && is_numeric($logowidth)) {
            $templatecontext['logowidth'] = $logowidth;
        }
        
        return $this->render_from_template('theme_ufpel/navbar_brand', $templatecontext);
    }
    
    /**
     * Returns HTML to display the main header.
     *
     * @return string
     */
    public function full_header() {
        global $COURSE, $PAGE;
        
        // Get parent header first.
        $header = parent::full_header();
        
        // Check if course header should be displayed.
        if (!$this->should_display_course_header()) {
            return $header;
        }
        
        // Check if course_header class exists
        if (!class_exists('\theme_ufpel\output\course_header')) {
            return $header;
        }
        
        // Get course header renderable.
        $courseheader = new \theme_ufpel\output\course_header($COURSE, $this->page);
        
        // Render course header using template.
        $customheader = $this->render($courseheader);
        
        // Inject before the page header.
        return $customheader . $header;
    }
    
    /**
     * Render course header from template.
     *
     * @param \theme_ufpel\output\course_header $courseheader
     * @return string HTML
     */
    protected function render_course_header(\theme_ufpel\output\course_header $courseheader) {
        $data = $courseheader->export_for_template($this);
        return $this->render_from_template('theme_ufpel/course_header_full', $data);
    }
    
    /**
     * Check if course header should be displayed.
     *
     * @return bool
     */
    protected function should_display_course_header() {
        global $COURSE, $PAGE;
        
        // Check if feature is enabled.
        if (!get_config('theme_ufpel', 'showcourseimage')) {
            return false;
        }
        
        // Only show on course pages.
        if (!in_array($PAGE->pagelayout, ['course', 'incourse'])) {
            return false;
        }
        
        // Don't show on site home.
        if ($COURSE->id == SITEID) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Override to add custom classes to body tag.
     * This is called early, before output starts, so it's safe to add classes here.
     *
     * @param array $additionalclasses Additional classes to add.
     * @return string HTML attributes to use within the body tag.
     */
    public function body_attributes($additionalclasses = []) {
        global $PAGE;
        
        // Add theme-specific classes.
        $additionalclasses[] = 'theme-ufpel';
        
        // Add version class for CSS targeting.
        $additionalclasses[] = 'ufpel-v1';
        
        // Check if we're on the login page.
        if ($this->page->pagelayout == 'login') {
            $loginbgimg = $this->page->theme->setting_file_url('loginbackgroundimage', 'loginbackgroundimage');
            if (!empty($loginbgimg)) {
                $additionalclasses[] = 'has-login-background';
            }
        }
        
        // Add class for course pages with custom header.
        if ($this->should_display_course_header()) {
            $additionalclasses[] = 'has-course-header';
        }
        
        // Add Bootstrap 5 compatibility classes
        $additionalclasses[] = 'bootstrap-5';
        
        // Check dark mode preference
        if ($this->should_use_dark_mode()) {
            $additionalclasses[] = 'ufpel-dark-mode';
        }
        
        // Check compact view preference
        if (get_user_preferences('theme_ufpel_compactview', false)) {
            $additionalclasses[] = 'ufpel-compact-view';
        }
        
        // Add device type classes
        $devicetype = \core_useragent::get_device_type();
        switch ($devicetype) {
            case \core_useragent::DEVICETYPE_MOBILE:
                $additionalclasses[] = 'ufpel-mobile';
                break;
            case \core_useragent::DEVICETYPE_TABLET:
                $additionalclasses[] = 'ufpel-tablet';
                break;
            default:
                $additionalclasses[] = 'ufpel-desktop';
                break;
        }
        
        return parent::body_attributes($additionalclasses);
    }
    
    /**
     * Check if dark mode should be used.
     *
     * @return bool
     */
    protected function should_use_dark_mode() {
        // Check user preference first
        $userpref = get_user_preferences('theme_ufpel_darkmode', null);
        if ($userpref !== null) {
            return (bool)$userpref;
        }
        
        // Check system setting
        return (bool)get_config('theme_ufpel', 'enabledarkmode');
    }
    
    /**
     * Returns the HTML for the footer.
     *
     * @return string HTML footer
     */
    public function footer() {
        $output = '';
        
        // Get footer content from settings
        $footercontent = get_config('theme_ufpel', 'footercontent');
        
        // Prepare template context
        $templatecontext = [
            'footercontent' => $footercontent ? format_text($footercontent, FORMAT_HTML) : '',
            'hasfootercontent' => !empty($footercontent),
            'year' => date('Y'),
            'sitename' => format_string($GLOBALS['SITE']->fullname),
            'loginurl' => (new moodle_url('/login/index.php'))->out(false),
            'homeurl' => (new moodle_url('/'))->out(false),
            'privacyurl' => (new moodle_url('/admin/tool/policy/index.php'))->out(false),
            'contacturl' => (new moodle_url('/user/contactsitesupport.php'))->out(false),
            'sociallinks' => $this->get_social_links(),
            'hassociallinks' => !empty($this->get_social_links())
        ];
        
        // Render custom footer
        $output .= $this->render_from_template('theme_ufpel/footer_custom', $templatecontext);
        
        // Add parent footer.
        $output .= parent::footer();
        
        return $output;
    }
    
    /**
     * Get social media links for footer.
     *
     * @return array
     */
    public function get_social_links() {
        $links = [];
        
        $socialnetworks = ['facebook', 'twitter', 'linkedin', 'youtube', 'instagram'];
        
        foreach ($socialnetworks as $network) {
            $url = get_config('theme_ufpel', 'social_' . $network);
            if (!empty($url)) {
                $links[] = [
                    'network' => $network,
                    'url' => $url,
                    'title' => ucfirst($network),
                    'icon' => 'fa-' . $network
                ];
            }
        }
        
        return $links;
    }
    
}