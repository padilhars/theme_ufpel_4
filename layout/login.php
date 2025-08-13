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
 * Login layout for theme_ufpel - Fixed version for Moodle 5.x
 *
 * @package    theme_ufpel
 * @copyright  2025 Universidade Federal de Pelotas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Get the renderer
$renderer = $PAGE->get_renderer('core');

// Build template context
$templatecontext = [
    'sitename' => format_string($SITE->fullname, true, [
        'context' => context_system::instance(),
        'escape' => false
    ]),
    'output' => $OUTPUT,
    'bodyattributes' => $OUTPUT->body_attributes(['class' => 'pagelayout-login']),
];

// Handle logo URL
if (method_exists($renderer, 'get_logo_url')) {
    $logourl = $renderer->get_logo_url();
    if ($logourl) {
        $templatecontext['logourl'] = $logourl->out(false);
    }
} else {
    // Fallback for older versions or custom logo settings
    $logo = $PAGE->theme->setting_file_url('logo', 'logo');
    if ($logo) {
        $templatecontext['logourl'] = $logo;
    }
}

// Handle background image
$loginbgimg = $PAGE->theme->setting_file_url('loginbackgroundimage', 'loginbackgroundimage');
if (!empty($loginbgimg)) {
    $templatecontext['loginbackgroundimage'] = $loginbgimg;
    $templatecontext['hasloginbackgroundimage'] = true;
}

// Add URLs for login links
$templatecontext['homeurl'] = (new moodle_url('/'))->out(false);
$templatecontext['forgotpasswordurl'] = (new moodle_url('/login/forgot_password.php'))->out(false);

// Check if signup is enabled
$cansignup = false;
$authplugins = get_enabled_auth_plugins();
foreach ($authplugins as $authplugin) {
    $authpluginobj = get_auth_plugin($authplugin);
    if ($authpluginobj && method_exists($authpluginobj, 'can_signup') && $authpluginobj->can_signup()) {
        $cansignup = true;
        break;
    }
}

if ($cansignup) {
    $templatecontext['cansignup'] = true;
    $templatecontext['signupurl'] = (new moodle_url('/login/signup.php'))->out(false);
}

$templatecontext['haslogininfo'] = true;

// IMPORTANTE: Esta é a correção principal!
// Devemos chamar $OUTPUT->doctype() ANTES de renderizar qualquer conteúdo
echo $OUTPUT->doctype();
?>
<html <?php echo $OUTPUT->htmlattributes(); ?>>
<head>
    <title><?php echo get_string('login'); ?> - <?php echo format_string($SITE->fullname); ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->favicon(); ?>" />
    <?php echo $OUTPUT->standard_head_html(); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body <?php echo $templatecontext['bodyattributes']; ?>>
<?php echo $OUTPUT->standard_top_of_body_html(); ?>

<div id="page-wrapper">
    <div id="page" class="container-fluid d-flex align-items-center justify-content-center min-vh-100">
        <div class="row justify-content-center w-100">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card login-container">
                    <div class="card-body p-4">
                        <?php if (!empty($templatecontext['logourl'])): ?>
                        <div class="text-center mb-4">
                            <img src="<?php echo $templatecontext['logourl']; ?>" 
                                 class="img-fluid" 
                                 alt="<?php echo $templatecontext['sitename']; ?>" 
                                 style="max-height: 60px;">
                        </div>
                        <?php else: ?>
                        <h2 class="text-center mb-4"><?php echo $templatecontext['sitename']; ?></h2>
                        <?php endif; ?>
                        
                        <div id="region-main">
                            <?php
                            // Renderizar o conteúdo principal do login
                            echo $OUTPUT->course_content_header();
                            echo $OUTPUT->main_content();
                            echo $OUTPUT->course_content_footer();
                            ?>
                        </div>
                    </div>
                </div>
                
                <?php if ($templatecontext['haslogininfo']): ?>
                <div class="mt-3 text-center small">
                    <a href="<?php echo $templatecontext['homeurl']; ?>" class="text-muted">
                        <?php echo get_string('home'); ?>
                    </a>
                    <?php if (!empty($templatecontext['cansignup'])): ?>
                    | <a href="<?php echo $templatecontext['signupurl']; ?>" class="text-muted">
                        <?php echo get_string('startsignup'); ?>
                    </a>
                    <?php endif; ?>
                    | <a href="<?php echo $templatecontext['forgotpasswordurl']; ?>" class="text-muted">
                        <?php echo get_string('forgotten'); ?>
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php echo $OUTPUT->standard_footer_html(); ?>
</div>

<?php
// Background image style
if (!empty($templatecontext['hasloginbackgroundimage'])): ?>
<style>
    body.pagelayout-login {
        background-image: url('<?php echo $templatecontext['loginbackgroundimage']; ?>');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed;
    }
    .login-container {
        background: rgba(255, 255, 255, 0.95);
    }
</style>
<?php endif; ?>

<?php echo $OUTPUT->standard_end_of_body_html(); ?>
</body>
</html>