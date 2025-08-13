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
 * Unit tests for theme_ufpel helper functions.
 *
 * @package    theme_ufpel
 * @category   test
 * @copyright  2025 Universidade Federal de Pelotas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_ufpel;

use advanced_testcase;
use cache;

defined('MOODLE_INTERNAL') || die();

/**
 * Unit tests for theme_ufpel helper class.
 *
 * @package    theme_ufpel
 * @category   test
 * @copyright  2025 Universidade Federal de Pelotas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper_test extends advanced_testcase {
    
    /**
     * Set up before each test.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);
    }
    
    /**
     * Test color lightening function.
     */
    public function test_lighten_color() {
        // Test basic lightening.
        $result = helper::lighten_color('#003366', 20);
        $this->assertMatchesRegularExpression('/^#[0-9a-f]{6}$/i', $result);
        
        // Test edge cases.
        $white = helper::lighten_color('#ffffff', 50);
        $this->assertEquals('#ffffff', $white);
        
        // Test without hash.
        $result = helper::lighten_color('003366', 20);
        $this->assertMatchesRegularExpression('/^#[0-9a-f]{6}$/i', $result);
    }
    
    /**
     * Test color darkening function.
     */
    public function test_darken_color() {
        // Test basic darkening.
        $result = helper::darken_color('#0066cc', 20);
        $this->assertMatchesRegularExpression('/^#[0-9a-f]{6}$/i', $result);
        
        // Test edge cases.
        $black = helper::darken_color('#000000', 50);
        $this->assertEquals('#000000', $black);
    }
    
    /**
     * Test theme settings retrieval.
     */
    public function test_get_theme_settings() {
        // Set some config values.
        set_config('brandcolor', '#112233', 'theme_ufpel');
        set_config('showcourseimage', '1', 'theme_ufpel');
        
        // Clear cache to ensure fresh retrieval.
        $cache = cache::make('theme_ufpel', 'themesettings');
        $cache->purge();
        
        // Get settings.
        $settings = helper::get_theme_settings();
        
        // Verify settings.
        $this->assertIsObject($settings);
        $this->assertEquals('#112233', $settings->brandcolor);
        $this->assertTrue($settings->showcourseimage);
        
        // Test caching by modifying config and checking cache returns old value.
        set_config('brandcolor', '#445566', 'theme_ufpel');
        $settings2 = helper::get_theme_settings();
        $this->assertEquals('#112233', $settings2->brandcolor);
    }
    
    /**
     * Test CSS variables generation.
     */
    public function test_get_css_variables() {
        // Set config.
        set_config('brandcolor', '#003366', 'theme_ufpel');
        set_config('secondarycolor', '#0066cc', 'theme_ufpel');
        
        // Clear cache.
        $cache = cache::make('theme_ufpel', 'themesettings');
        $cache->purge();
        
        // Get CSS variables.
        $css = helper::get_css_variables();
        
        // Verify output.
        $this->assertStringContainsString(':root {', $css);
        $this->assertStringContainsString('--ufpel-primary: #003366;', $css);
        $this->assertStringContainsString('--ufpel-secondary: #0066cc;', $css);
        $this->assertStringContainsString('--ufpel-primary-light:', $css);
        $this->assertStringContainsString('--ufpel-primary-dark:', $css);
    }
    
    /**
     * Test preset availability check.
     */
    public function test_get_available_presets() {
        $presets = helper::get_available_presets();
        
        // Should always have at least default.scss.
        $this->assertIsArray($presets);
        $this->assertContains('default.scss', $presets);
    }
    
    /**
     * Test course image URL retrieval.
     */
    public function test_get_course_image_url() {
        global $CFG;
        require_once($CFG->dirroot . '/course/lib.php');
        
        // Create a course.
        $course = $this->getDataGenerator()->create_course();
        
        // Test with no image.
        $url = helper::get_course_image_url($course->id);
        $this->assertNull($url);
        
        // Test with site course.
        $url = helper::get_course_image_url(SITEID);
        $this->assertNull($url);
    }
    
    /**
     * Test critical CSS generation.
     */
    public function test_get_critical_css() {
        // Set config.
        set_config('brandcolor', '#003366', 'theme_ufpel');
        set_config('secondarycolor', '#0066cc', 'theme_ufpel');
        
        // Clear cache.
        $cache = cache::make('theme_ufpel', 'themesettings');
        $cache->purge();
        
        // Get critical CSS.
        $css = helper::get_critical_css();
        
        // Verify it's minified (no multiple spaces).
        $this->assertStringNotContainsString('  ', $css);
        
        // Verify content.
        $this->assertStringContainsString('--ufpel-primary: #003366;', $css);
        $this->assertStringContainsString('.navbar', $css);
        $this->assertStringContainsString('.btn-primary', $css);
    }
    
    /**
     * Test dark mode detection.
     */
    public function test_should_use_dark_mode() {
        // Test with no preferences.
        $this->assertFalse(helper::should_use_dark_mode());
        
        // Test with system setting.
        set_config('enabledarkmode', '1', 'theme_ufpel');
        $this->assertTrue(helper::should_use_dark_mode());
        
        // Test with user preference overriding system.
        $this->setUser($this->getDataGenerator()->create_user());
        set_user_preference('theme_ufpel_darkmode', '0');
        $this->assertFalse(helper::should_use_dark_mode());
    }
}

/**
 * Unit tests for theme_ufpel lib functions.
 *
 * @package    theme_ufpel
 * @category   test
 * @copyright  2025 Universidade Federal de Pelotas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lib_test extends advanced_testcase {
    
    /**
     * Test get presets list function.
     */
    public function test_theme_ufpel_get_presets_list() {
        global $CFG;
        require_once($CFG->dirroot . '/theme/ufpel/settings.php');
        
        $presets = theme_ufpel_get_presets_list();
        
        $this->assertIsArray($presets);
        $this->assertArrayHasKey('default.scss', $presets);
        $this->assertEquals(get_string('default', 'theme_ufpel'), $presets['default.scss']);
    }
}