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
 * Course header renderable class.
 *
 * @package    theme_ufpel
 * @copyright  2025 Universidade Federal de Pelotas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_ufpel\output;

use renderable;
use renderer_base;
use templatable;
use stdClass;
use moodle_url;
use context_course;
use cache;

defined('MOODLE_INTERNAL') || die();

/**
 * Course header renderable.
 *
 * @package    theme_ufpel
 * @copyright  2025 Universidade Federal de Pelotas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_header implements renderable, templatable {
    
    /** @var stdClass The course object */
    protected $course;
    
    /** @var \moodle_page The page object */
    protected $page;
    
    /** @var context_course The course context */
    protected $context;
    
    /** @var array Course teachers cache */
    protected $teachers = null;
    
    /** @var string Course image URL */
    protected $imageurl = null;
    
    /**
     * Constructor.
     *
     * @param stdClass $course The course object
     * @param \moodle_page $page The page object
     */
    public function __construct($course, $page) {
        $this->course = $course;
        $this->page = $page;
        $this->context = context_course::instance($course->id);
    }
    
    /**
     * Export data for template.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        
        // Basic course information
        $data->courseid = $this->course->id;
        $data->coursename = format_string($this->course->fullname, true, [
            'context' => $this->context,
            'escape' => false
        ]);
        $data->courseshortname = format_string($this->course->shortname, true, [
            'context' => $this->context,
            'escape' => false
        ]);
        
        // Check if we should show the header
        $data->showcourseheader = $this->should_show_header();
        
        if ($data->showcourseheader) {
            // Course image
            if (get_config('theme_ufpel', 'showcourseimage')) {
                $data->imageurl = $this->get_course_image_url();
                $data->hasimage = !empty($data->imageurl);
            }
            
            // Course teachers
            if (get_config('theme_ufpel', 'showteachers')) {
                $teachers = $this->get_course_teachers();
                if (!empty($teachers)) {
                    $data->hasteachers = true;
                    $data->teachers = array_map(function($teacher, $index) use ($teachers) {
                        return [
                            'name' => $teacher,
                            'last' => ($index === count($teachers) - 1)
                        ];
                    }, $teachers, array_keys($teachers));
                    
                    // Teacher label
                    $data->teacherlabel = count($teachers) > 1 
                        ? get_string('teachers', 'theme_ufpel')
                        : get_string('teacher', 'theme_ufpel');
                }
            }
            
            // Additional settings
            $data->hasoverlay = get_config('theme_ufpel', 'courseheaderoverlay');
            
            // Course categories
            $data->categories = $this->get_course_categories();
            $data->hascategories = !empty($data->categories);
            
            // Course summary
            if (get_config('theme_ufpel', 'showcoursesummary')) {
                $summary = $this->get_course_summary();
                if (!empty($summary)) {
                    $data->coursesummary = $summary;
                    $data->hassummary = true;
                }
            }
            
            // Course progress (if enabled and user is enrolled)
            if (get_config('theme_ufpel', 'showcourseprogressinheader')) {
                $progress = $this->get_course_progress();
                if ($progress !== null) {
                    $data->progress = $progress;
                    $data->hasprogress = true;
                    $data->progresspercentage = round($progress);
                }
            }
            
            // Custom fields
            $customfields = $this->get_custom_fields();
            if (!empty($customfields)) {
                $data->customfields = $customfields;
                $data->hascustomfields = true;
            }
            
            // Enrollment info
            $enrollmentinfo = $this->get_enrollment_info();
            if (!empty($enrollmentinfo)) {
                $data->enrollmentinfo = $enrollmentinfo;
                $data->hasenrollmentinfo = true;
            }
        }
        
        return $data;
    }
    
    /**
     * Check if header should be shown.
     *
     * @return bool
     */
    protected function should_show_header() {
        // Don't show on site home
        if ($this->course->id == SITEID) {
            return false;
        }
        
        // Check page layout
        if (!in_array($this->page->pagelayout, ['course', 'incourse'])) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Get course image URL.
     *
     * @return string|null
     */
    protected function get_course_image_url() {
        if ($this->imageurl !== null) {
            return $this->imageurl;
        }
        
        $fs = get_file_storage();
        $files = $fs->get_area_files($this->context->id, 'course', 'overviewfiles', 0, 'filename', false);
        
        if ($files) {
            foreach ($files as $file) {
                if ($file->is_valid_image()) {
                    $this->imageurl = moodle_url::make_pluginfile_url(
                        $this->context->id,
                        'course',
                        'overviewfiles',
                        null,
                        '/',
                        $file->get_filename()
                    )->out();
                    return $this->imageurl;
                }
            }
        }
        
        $this->imageurl = false;
        return null;
    }
    
    /**
     * Get course teachers.
     *
     * @return array
     */
    protected function get_course_teachers() {
        if ($this->teachers !== null) {
            return $this->teachers;
        }
        
        // Try cache first
        $cache = cache::make('theme_ufpel', 'courseteachers');
        $teachers = $cache->get($this->course->id);
        
        if ($teachers === false) {
            $teachers = $this->fetch_course_teachers();
            $cache->set($this->course->id, $teachers);
        }
        
        $this->teachers = $teachers;
        return $teachers;
    }
    
    /**
     * Fetch course teachers from database.
     *
     * @return array
     */
    protected function fetch_course_teachers() {
        global $DB;
        
        $teachers = [];
        
        // Get teacher and editingteacher roles
        $roleids = [];
        $roles = $DB->get_records_sql("
            SELECT id FROM {role} 
            WHERE archetype IN ('teacher', 'editingteacher')
        ");
        
        if (empty($roles)) {
            return [];
        }
        
        $roleids = array_keys($roles);
        
        // Get users with teacher roles
        list($insql, $params) = $DB->get_in_or_equal($roleids, SQL_PARAMS_NAMED);
        $params['contextid'] = $this->context->id;
        
        $sql = "SELECT DISTINCT u.id, u.firstname, u.lastname, u.firstnamephonetic, 
                       u.lastnamephonetic, u.middlename, u.alternatename
                  FROM {user} u
                  JOIN {role_assignments} ra ON ra.userid = u.id
                 WHERE ra.contextid = :contextid
                   AND ra.roleid $insql
                   AND u.deleted = 0
                   AND u.suspended = 0
              ORDER BY u.lastname, u.firstname";
        
        $users = $DB->get_records_sql($sql, $params);
        
        foreach ($users as $user) {
            $teachers[] = fullname($user);
        }
        
        return $teachers;
    }
    
    /**
     * Get course categories.
     *
     * @return array
     */
    protected function get_course_categories() {
        global $DB;
        
        $categories = [];
        
        if ($this->course->category) {
            $category = $DB->get_record('course_categories', ['id' => $this->course->category]);
            if ($category) {
                $categories[] = [
                    'name' => format_string($category->name),
                    'url' => (new moodle_url('/course/index.php', ['categoryid' => $category->id]))->out()
                ];
                
                // Get parent categories
                $path = explode('/', trim($category->path, '/'));
                array_pop($path); // Remove current category
                
                foreach (array_reverse($path) as $catid) {
                    if ($catid) {
                        $parentcat = $DB->get_record('course_categories', ['id' => $catid]);
                        if ($parentcat) {
                            array_unshift($categories, [
                                'name' => format_string($parentcat->name),
                                'url' => (new moodle_url('/course/index.php', ['categoryid' => $parentcat->id]))->out()
                            ]);
                        }
                    }
                }
            }
        }
        
        return $categories;
    }
    
    /**
     * Get course summary.
     *
     * @return string
     */
    protected function get_course_summary() {
        if (!empty($this->course->summary)) {
            $summary = format_text($this->course->summary, $this->course->summaryformat, [
                'context' => $this->context,
                'noclean' => true
            ]);
            
            // Truncate if too long
            $maxlength = 200;
            if (strlen($summary) > $maxlength) {
                $summary = shorten_text($summary, $maxlength, true);
            }
            
            return $summary;
        }
        
        return '';
    }
    
    /**
     * Get course progress.
     *
     * @return float|null Progress percentage or null if not available
     */
    protected function get_course_progress() {
        global $USER;
        
        // Check if user is enrolled
        if (!is_enrolled($this->context, $USER->id, '', true)) {
            return null;
        }
        
        // Get completion info
        $completion = new \completion_info($this->course);
        
        if (!$completion->is_enabled()) {
            return null;
        }
        
        // Get progress percentage
        $progressinfo = \core_completion\progress::get_course_progress_percentage($this->course, $USER->id);
        
        return $progressinfo;
    }
    
    /**
     * Get custom fields.
     *
     * @return array
     */
    protected function get_custom_fields() {
        $customfields = [];
        
        $handler = \core_course\customfield\course_handler::create();
        $datas = $handler->get_instance_data($this->course->id, true);
        
        foreach ($datas as $data) {
            if (!$data->get_field()->get_configdata_property('visibility')) {
                continue;
            }
            
            $customfields[] = [
                'name' => $data->get_field()->get('name'),
                'value' => $data->export_value()
            ];
        }
        
        return $customfields;
    }
    
    /**
     * Get enrollment information.
     *
     * @return array
     */
    protected function get_enrollment_info() {
        global $DB;
        
        $info = [];
        
        // Count enrolled users
        $enrolledusers = count_enrolled_users($this->context, '', 0, true);
        if ($enrolledusers > 0) {
            $info['enrolledusers'] = $enrolledusers;
            $info['enrolleduserstext'] = get_string('enrolledusers', 'theme_ufpel', $enrolledusers);
        }
        
        // Get start date
        if ($this->course->startdate) {
            $info['startdate'] = userdate($this->course->startdate, get_string('strftimedatefullshort'));
        }
        
        // Get end date
        if ($this->course->enddate) {
            $info['enddate'] = userdate($this->course->enddate, get_string('strftimedatefullshort'));
        }
        
        return $info;
    }
}