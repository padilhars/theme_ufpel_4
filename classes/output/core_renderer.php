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
     * Get the logo URL.
     * FIXED: Properly handles URL conversion to prevent duplication.
     *
     * @param int|null $maxwidth The maximum width, or null when the maximum width does not matter.
     * @param int $maxheight The maximum height, or null when the maximum height does not matter.
     * @return moodle_url|null The logo URL or null if not set.
     */
    public function get_logo_url($maxwidth = null, $maxheight = 200) {
        // Check if we have a custom logo from theme settings
        $logo = $this->page->theme->setting_file_url('logo', 'logo');
        
        if (!empty($logo)) {
            // If it's already a moodle_url object, return it directly
            if ($logo instanceof moodle_url) {
                return $logo;
            }
            
            // Convert to string for processing
            $logostr = (string)$logo;
            
            // Check if it's empty after conversion
            if (empty($logostr)) {
                return parent::get_logo_url($maxwidth, $maxheight);
            }
            
            // Parse the URL to check if it's absolute
            $parsed = parse_url($logostr);
            
            // If the URL has a scheme (http/https), it's absolute
            if (!empty($parsed['scheme'])) {
                // It's an absolute URL, create moodle_url without modification
                // Use parse_url components to avoid duplication
                try {
                    // Extract just the path and query from the absolute URL
                    $path = $parsed['path'] ?? '';
                    if (!empty($parsed['query'])) {
                        $path .= '?' . $parsed['query'];
                    }
                    if (!empty($parsed['fragment'])) {
                        $path .= '#' . $parsed['fragment'];
                    }
                    
                    // If the path starts with the wwwroot path, use it as relative
                    global $CFG;
                    $wwwroot_parsed = parse_url($CFG->wwwroot);
                    $wwwroot_path = $wwwroot_parsed['path'] ?? '';
                    
                    if (!empty($wwwroot_path) && strpos($path, $wwwroot_path) === 0) {
                        // Remove the wwwroot path to make it relative
                        $relative_path = substr($path, strlen($wwwroot_path));
                        return new moodle_url($relative_path);
                    } else {
                        // Use the path as is
                        return new moodle_url($path);
                    }
                } catch (\Exception $e) {
                    // If there's any error, try returning the original URL
                    // but wrapped in a moodle_url that won't duplicate
                    return new moodle_url($logostr, [], '', '');
                }
            } else {
                // It's a relative URL, safe to use with moodle_url constructor
                return new moodle_url($logostr);
            }
        }
        
        // Fall back to parent implementation if no custom logo
        return parent::get_logo_url($maxwidth, $maxheight);
    }
    
    /**
     * Override navbar brand to use logo if available.
     *
     * @return string HTML for navbar brand
     */
    public function navbar_brand() {
        $logourl = $this->get_logo_url(null, 40);
        
        // Since get_logo_url returns a moodle_url object or null, handle appropriately
        $templatecontext = [
            'logourl' => $logourl ? $logourl->out(false) : null,
            'sitename' => format_string($this->page->course->shortname, true, 
                ['context' => context_course::instance($this->page->course->id)]),
            'homeurl' => (new moodle_url('/'))->out(false)
        ];
        
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
     * Get the main logo URL for the footer.
     *
     * @return string|null
     */
    public function get_footer_logo_url() {
        $logo = $this->page->theme->setting_file_url('footerlogo', 'footerlogo');
        if (!empty($logo)) {
            if (is_object($logo) && method_exists($logo, 'out')) {
                return $logo->out(false);
            }
            return (string)$logo;
        }
        
        // Fall back to main logo
        $mainlogo = $this->get_logo_url();
        if ($mainlogo) {
            // get_logo_url returns a moodle_url object or null
            return $mainlogo->out(false);
        }
        return null;
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
    
    /**
     * Construct user menu.
     * Note: In Moodle 5.x, this method signature might be different
     *
     * @param stdClass|null $user The user object
     * @return array|string The user menu
     */
    public function construct_user_menu($user = null) {
        // Get the parent menu first
        $usermenu = parent::construct_user_menu($user);
        
        // Only add our custom item if user is logged in
        if (isloggedin() && !isguestuser()) {
            // Check if the parent returned an array (expected in most versions)
            if (is_array($usermenu)) {
                $preferencesurl = new moodle_url('/theme/ufpel/preferences.php');
                
                // Add theme preferences link to the menu
                $usermenu[] = [
                    'itemtype' => 'link',
                    'url' => $preferencesurl->out(false),
                    'title' => get_string('themepreferences', 'theme_ufpel'),
                    'icon' => 'i/settings',
                    'pix' => 'i/settings'
                ];
            }
        }
        
        return $usermenu;
    }
    
    /**
     * Override login template context to include background image
     * 
     * @param array|\stdClass $templatecontext The template context
     * @return array|\stdClass Modified template context
     */
    public function login_templatecontext($templatecontext = null) {
        // Get parent context first
        if (method_exists(get_parent_class($this), 'login_templatecontext')) {
            $templatecontext = parent::login_templatecontext($templatecontext);
        } else {
            // Initialize if parent doesn't have this method
            if (!$templatecontext) {
                $templatecontext = new stdClass();
            }
        }
        
        // Convert to object if array
        if (is_array($templatecontext)) {
            $templatecontext = (object) $templatecontext;
        }
        
        // Add login background image URL if available
        $loginbgimg = $this->page->theme->setting_file_url('loginbackgroundimage', 'loginbackgroundimage');
        if (!empty($loginbgimg)) {
            // Ensure it's a proper URL string
            if (is_object($loginbgimg) && method_exists($loginbgimg, 'out')) {
                $templatecontext->loginbackgroundimage = $loginbgimg->out(false);
            } else {
                $templatecontext->loginbackgroundimage = (string)$loginbgimg;
            }
            $templatecontext->hasloginbackgroundimage = true;
        } else {
            $templatecontext->hasloginbackgroundimage = false;
        }
        
        return $templatecontext;
    }
}