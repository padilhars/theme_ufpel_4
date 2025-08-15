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
 * Language file for theme_ufpel - English (Complete with ALL required strings)
 *
 * @package    theme_ufpel
 * @copyright  2025 Universidade Federal de Pelotas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// General strings
$string['pluginname'] = 'UFPel';
$string['choosereadme'] = 'UFPel is a modern theme based on Boost, customized for the Federal University of Pelotas, fully compatible with Moodle 5.x and Bootstrap 5.';

// Settings page strings
$string['configtitle'] = 'UFPel theme settings';
$string['generalsettings'] = 'General settings';
$string['advancedsettings'] = 'Advanced settings';
$string['features'] = 'Features';
$string['performance'] = 'Performance';
$string['accessibility'] = 'Accessibility';
$string['default'] = 'Default';

// Settings headings
$string['colorsheading'] = 'Color Settings';
$string['colorsheading_desc'] = 'Configure the color scheme for your theme. These colors will be applied throughout the site.';
$string['logoheading'] = 'Logo and Branding';
$string['logoheading_desc'] = 'Upload your institution logo and configure branding elements.';
$string['loginheading'] = 'Login Page Settings';
$string['loginheading_desc'] = 'Customize the appearance of the login page.';
$string['cssheading'] = 'Custom CSS/SCSS';
$string['cssheading_desc'] = 'Add custom CSS or SCSS code to further customize the theme appearance.';

// Color settings
$string['primarycolor'] = 'Primary color';
$string['primarycolor_desc'] = 'The primary color for the theme. This will be used for main elements like the header and buttons.';
$string['secondarycolor'] = 'Secondary color';
$string['secondarycolor_desc'] = 'The secondary color for the theme. Used for links and secondary elements.';
$string['accentcolor'] = 'Accent color';
$string['accentcolor_desc'] = 'The accent color used for highlights and special elements throughout the site.';
$string['backgroundcolor'] = 'Background color';
$string['backgroundcolor_desc'] = 'The main background color for the site pages.';
$string['highlightcolor'] = 'Highlight color';
$string['highlightcolor_desc'] = 'The color used for highlighting important elements and accents.';
$string['contenttextcolor'] = 'Content text color';
$string['contenttextcolor_desc'] = 'The color for general text content throughout the site.';
$string['highlighttextcolor'] = 'Highlight text color';
$string['highlighttextcolor_desc'] = 'The color for text that appears on primary colored backgrounds.';

// Feature settings
$string['showcourseimage'] = 'Show course image';
$string['showcourseimage_desc'] = 'Display the course image in the header of course pages.';
$string['showteachers'] = 'Show teachers';
$string['showteachers_desc'] = 'Display teacher names in the header of course pages.';
$string['courseheaderoverlay'] = 'Course header overlay';
$string['courseheaderoverlay_desc'] = 'Add a dark overlay to the course header to improve text readability.';
$string['footercontent'] = 'Footer content';
$string['footercontent_desc'] = 'Custom HTML content to display in the site footer.';

// Logo and images
$string['logo'] = 'Logo';
$string['logo_desc'] = 'Upload your institution logo. This will replace the site name in the navigation bar. Recommended height: 40px.';
$string['footerlogo'] = 'Footer logo';
$string['footerlogo_desc'] = 'A separate logo for the footer area. If not set, the main logo will be used.';
$string['loginbackgroundimage'] = 'Login page background image';
$string['loginbackgroundimage_desc'] = 'An image that will be displayed as the background of the login page. Recommended size: 1920x1080 or larger.';
$string['favicon'] = 'Favicon';
$string['favicon_desc'] = 'Upload a custom favicon. Should be an .ico, .png or .svg file.';

// Logo settings
$string['logowidth'] = 'Logo width';
$string['logowidth_desc'] = 'Set a custom width for the logo in pixels. Leave empty for automatic sizing.';
$string['showsitenamewithlogo'] = 'Show site name with logo';
$string['showsitenamewithlogo_desc'] = 'Display the site name alongside the logo in the navigation bar.';
$string['compactlogo'] = 'Compact logo';
$string['compactlogo_desc'] = 'A smaller version of the logo for mobile devices. If not set, the main logo will be used.';
$string['logodisplaymode'] = 'Logo display mode';
$string['logodisplaymode_desc'] = 'Choose how the logo should be displayed in different screen sizes.';
$string['logodisplaymode_responsive'] = 'Responsive (adapts to screen size)';
$string['logodisplaymode_fixed'] = 'Fixed size';
$string['logodisplaymode_compact'] = 'Always compact';

// Custom CSS/SCSS
$string['customcss'] = 'Custom CSS';
$string['customcss_desc'] = 'Whatever CSS rules you add to this textarea will be reflected in every page, making it easier to customize this theme.';
$string['rawscss'] = 'Raw SCSS';
$string['rawscss_desc'] = 'Use this field to provide SCSS code which will be injected at the end of the stylesheet.';
$string['rawscsspre'] = 'Raw initial SCSS';
$string['rawscsspre_desc'] = 'In this field you can provide initializing SCSS code, it will be injected before everything else. Most of the time you will use this setting to define variables.';

// Preset settings
$string['preset'] = 'Theme preset';
$string['preset_desc'] = 'Pick a preset to broadly change the look of the theme.';
$string['preset_default'] = 'Default';
$string['preset_dark'] = 'Dark mode';
$string['presetfiles'] = 'Additional theme preset files';
$string['presetfiles_desc'] = 'Preset files can be used to dramatically alter the appearance of the theme. See <a href="https://docs.moodle.org/dev/Boost_Presets">Boost Presets</a> for information on creating and sharing your own preset files.';

// Font settings
$string['customfonts'] = 'Custom fonts URL';
$string['customfonts_desc'] = 'Enter URL to import custom fonts (e.g., Google Fonts). Use the complete @import statement.';

// Footer strings - REQUIRED for footer template
$string['footerdescription'] = 'Learning management system of the Federal University of Pelotas';
$string['quicklinks'] = 'Quick links';
$string['support'] = 'Support';
$string['policies'] = 'Policies';
$string['contactus'] = 'Contact us';
$string['mobileapp'] = 'Mobile app';
$string['downloadapp'] = 'Download the Moodle app';
$string['allrightsreserved'] = 'All rights reserved';
$string['poweredby'] = 'Powered by';
$string['theme'] = 'Theme';

// Navigation strings - REQUIRED for navigation
$string['home'] = 'Home';
$string['courses'] = 'Courses';
$string['myhome'] = 'Dashboard';
$string['calendar'] = 'Calendar';
$string['help'] = 'Help';
$string['documentation'] = 'Documentation';
$string['login'] = 'Log in';
$string['logout'] = 'Log out';
$string['privacy'] = 'Privacy';
$string['privacypolicy'] = 'Privacy policy';
$string['dataprivacy'] = 'Data privacy';

// Login page strings
$string['username'] = 'Username';
$string['password'] = 'Password';
$string['rememberusername'] = 'Remember username';
$string['loginsite'] = 'Log in to the site';
$string['startsignup'] = 'Create new account';
$string['forgotten'] = 'Forgotten your username or password?';
$string['firsttime'] = 'Is this your first time here?';
$string['newaccount'] = 'Create a new account';
$string['loginguest'] = 'Log in as a guest';
$string['someallowguest'] = 'Some courses may allow guest access';
$string['forgotaccount'] = 'Lost password?';

// Course page strings - CORRECTED
$string['teacher'] = 'Teacher';
$string['teachers'] = 'Teachers';
$string['enrolledusers'] = '{$a} enrolled users';
$string['startdate'] = 'Start date';
$string['enddate'] = 'End date';
$string['coursecompleted'] = 'Congratulations! You have completed this course.';
$string['congratulations'] = 'Congratulations!';
$string['progress'] = 'Progress';
$string['complete'] = 'complete';
$string['courseheader'] = 'Course header';
$string['breadcrumb'] = 'Breadcrumb navigation';
$string['courseprogress'] = 'Course progress';
$string['coursecompletion'] = 'Course completion';

// User interface strings
$string['darkmodeon'] = 'Dark mode enabled';
$string['darkmodeoff'] = 'Dark mode disabled';
$string['totop'] = 'Back to top';
$string['skipmain'] = 'Skip to main content';
$string['skipnav'] = 'Skip navigation';
$string['skipnavigation'] = 'Skip navigation';
$string['skipmainmenu'] = 'Skip main menu';
$string['skipmaincontent'] = 'Skip to main content';
$string['skipsettingsmenu'] = 'Skip settings menu';
$string['skipfooter'] = 'Skip to footer';
$string['themepreferences'] = 'Theme preferences';

// Privacy strings
$string['privacy:metadata'] = 'The UFPel theme does not store any personal data.';
$string['privacy:metadata:preference:darkmode'] = 'User preference for dark mode.';
$string['privacy:metadata:preference:compactview'] = 'User preference for compact view.';
$string['privacy:metadata:preference:draweropen'] = 'User preference for navigation drawer state.';

// Region strings
$string['region-side-pre'] = 'Left';
$string['region-side-post'] = 'Right';

// Accessibility strings
$string['skipto'] = 'Skip to {$a}';
$string['accessibilitymenu'] = 'Accessibility menu';
$string['increasefontsize'] = 'Increase font size';
$string['decreasefontsize'] = 'Decrease font size';
$string['resetfontsize'] = 'Reset font size';
$string['highcontrast'] = 'High contrast';
$string['normalcontrast'] = 'Normal contrast';

// Notification strings
$string['loading'] = 'Loading...';
$string['error'] = 'Error';
$string['success'] = 'Success';
$string['warning'] = 'Warning';
$string['info'] = 'Information';
$string['close'] = 'Close';
$string['expand'] = 'Expand';
$string['collapse'] = 'Collapse';
$string['menu'] = 'Menu';
$string['search'] = 'Search';
$string['filter'] = 'Filter';
$string['sort'] = 'Sort';
$string['settings'] = 'Settings';
$string['notifications'] = 'Notifications';

// Additional feature strings
$string['showcourseprogressinheader'] = 'Show progress in header';
$string['showcourseprogressinheader_desc'] = 'Display the course progress bar in the header when completion tracking is enabled.';
$string['showcoursesummary'] = 'Show course summary';
$string['showcoursesummary_desc'] = 'Display the course summary in the course page header.';
$string['enablelazyloading'] = 'Enable lazy loading';
$string['enablelazyloading_desc'] = 'Load images and iframes only when needed to improve performance.';
$string['enablecssoptimization'] = 'Optimize CSS';
$string['enablecssoptimization_desc'] = 'Enable CSS optimization and minification for better performance.';
$string['enableresourcehints'] = 'Enable resource hints';
$string['enableresourcehints_desc'] = 'Use preload and prefetch to improve resource loading.';
$string['enableanimations'] = 'Enable animations';
$string['enableanimations_desc'] = 'Enable smooth animations and transitions. Disable for better performance on slower devices.';
$string['enableaccessibilitytools'] = 'Accessibility tools';
$string['enableaccessibilitytools_desc'] = 'Enable additional accessibility tools like contrast adjustment and font size controls.';
$string['enabledarkmode'] = 'Enable dark mode';
$string['enabledarkmode_desc'] = 'Allow users to switch to dark mode.';
$string['enablecompactview'] = 'Enable compact view';
$string['enablecompactview_desc'] = 'Allow users to switch to a more compact view.';

// Social media strings
$string['social_facebook'] = 'Facebook URL';
$string['social_facebook_desc'] = 'URL of the institution\'s Facebook page';
$string['social_twitter'] = 'Twitter/X URL';
$string['social_twitter_desc'] = 'URL of the institution\'s Twitter/X page';
$string['social_linkedin'] = 'LinkedIn URL';
$string['social_linkedin_desc'] = 'URL of the institution\'s LinkedIn page';
$string['social_youtube'] = 'YouTube URL';
$string['social_youtube_desc'] = 'URL of the institution\'s YouTube channel';
$string['social_instagram'] = 'Instagram URL';
$string['social_instagram_desc'] = 'URL of the institution\'s Instagram page';

// Additional strings for completeness
$string['dashboard'] = 'Dashboard';
$string['sitehome'] = 'Site home';
$string['participants'] = 'Participants';
$string['reports'] = 'Reports';
$string['badges'] = 'Badges';
$string['competencies'] = 'Competencies';
$string['grades'] = 'Grades';
$string['messages'] = 'Messages';
$string['preferences'] = 'Preferences';
$string['timeline'] = 'Timeline';
$string['mycourses'] = 'My courses';
$string['allcourses'] = 'All courses';
$string['coursecategories'] = 'Course categories';
$string['coursecategory'] = 'Course category';
$string['recentactivity'] = 'Recent activity';
$string['nocoursesyet'] = 'No courses available yet';
$string['viewallcourses'] = 'View all courses';
$string['nocourses'] = 'No courses';
$string['enrollmentkey'] = 'Enrollment key';
$string['courseaccess'] = 'Course access';
$string['userprofile'] = 'User profile';
$string['editprofile'] = 'Edit profile';
$string['termsofuse'] = 'Terms of use';
$string['datasecurity'] = 'Data security';
$string['copyright'] = 'Copyright';
$string['siteadmin'] = 'Site administration';
$string['sitemenu'] = 'Site menu';
$string['navigationmenu'] = 'Navigation menu';
$string['usermenu'] = 'User menu';
$string['languagemenu'] = 'Language menu';

// Time related
$string['today'] = 'Today';
$string['yesterday'] = 'Yesterday';
$string['tomorrow'] = 'Tomorrow';
$string['lastweek'] = 'Last week';
$string['nextweek'] = 'Next week';
$string['lastmonth'] = 'Last month';
$string['nextmonth'] = 'Next month';

// Actions
$string['edit'] = 'Edit';
$string['delete'] = 'Delete';
$string['save'] = 'Save';
$string['cancel'] = 'Cancel';
$string['submit'] = 'Submit';
$string['view'] = 'View';
$string['download'] = 'Download';
$string['upload'] = 'Upload';
$string['select'] = 'Select';
$string['open'] = 'Open';
$string['more'] = 'More';
$string['less'] = 'Less';
$string['browsecourses'] = 'Browse courses';
$string['popularlinks'] = 'Popular links';
$string['quickaccess'] = 'Quick access';
$string['needhelp'] = 'Need help?';
$string['contactsupport'] = 'Contact support';
$string['welcomeback'] = 'Welcome back!';
$string['logintitle'] = 'Log in to UFPel Moodle';
$string['logindescription'] = 'Please enter your credentials to access the learning platform.';

// Status messages
$string['completed'] = 'Completed';
$string['incomplete'] = 'Incomplete';
$string['inprogress'] = 'In progress';
$string['notstarted'] = 'Not started';
$string['processing'] = 'Processing...';

// Development and debugging
$string['version'] = 'Version';
$string['author'] = 'Author';
$string['license'] = 'License';
$string['website'] = 'Website';
$string['repository'] = 'Repository';
$string['issuetracker'] = 'Issue tracker';
$string['documentation_link'] = 'Documentation link';

// Error messages
$string['error:missinglogo'] = 'Logo not found';
$string['error:invalidcolor'] = 'Invalid color code';
$string['error:fileuploadfailed'] = 'File upload failed';

// Help strings
$string['help:primarycolor'] = 'This color will be applied to the main interface elements';
$string['help:darkmode'] = 'Dark mode reduces eye strain in low-light environments';
$string['help:lazyloading'] = 'Lazy loading significantly improves performance on pages with many images';

// Administrative strings
$string['themesettings'] = 'UFPel theme settings';
$string['resetsettings'] = 'Reset settings';
$string['resetsettings_desc'] = 'Reset all theme settings to default values';
$string['settingssaved'] = 'Settings saved successfully';
$string['settingsreset'] = 'Settings reset to default values';