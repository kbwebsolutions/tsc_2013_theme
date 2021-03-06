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
 * The tsc_2013 theme is built upon  Bootstrapbase 3 (non-core).
 *
 * @package    theme
 * @subpackage theme_tsc_2013
 * @author     Julian (@moodleman) Ridden
 * @author     Based on code originally written by G J Bernard, Mary Evans, Bas Brands, Stuart Lamour and David Scotson.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$hassidepost = $PAGE->blocks->region_has_content('side-post', $OUTPUT);
$hascopyright = (empty($PAGE->theme->settings->copyright)) ? false : $PAGE->theme->settings->copyright;
$hasfootnote = (empty($PAGE->theme->settings->footnote)) ? false : $PAGE->theme->settings->footnote;
$hastiles = (!empty($PAGE->theme->settings->tiles));
$haslogo = (empty($PAGE->theme->settings->logo)) ? false : $PAGE->theme->settings->logo;
$invert = (!empty($PAGE->theme->settings->invert)) ? true : $PAGE->theme->settings->invert;
$fluid = (!empty($PAGE->layout_options['fluid']));

$usereader = (!empty($PAGE->layout_options['usereader']));
$navbarbtn = '';

if ($usereader) {
    theme_tsc_2013_initialise_reader($PAGE);
    $navbarbtn = $OUTPUT->navbar_button_reader('#region-main', 'hidden-xs');
}

if (!empty($CFG->themedir) and file_exists("$CFG->themedir/tsc_2013")) {
    $themedir = "$CFG->themedir/tsc_2013";
} else {
    $themedir = $CFG->dirroot."/theme/tsc_2013";
}

if ($haslogo) {
    $logo = '<div id="logo"></div>';
} else {
    $logo = $SITE->shortname;
}

if ($invert) {
  $navbartype = 'navbar-inverse';
} else {
  $navbartype = 'navbar-default';
}

$container = 'container';
if (isset($PAGE->theme->settings->fluidwidth) && ($PAGE->theme->settings->fluidwidth == true)) {
    $container = 'container-fluid';
}
if ($fluid) {
    $container = 'container-fluid';
}


$knownregionpost = $PAGE->blocks->is_known_region('side-post');

$regions = theme_tsc_2013_bootstrap3_grid($hassidepost);
$PAGE->set_popup_notification_allowed(false);
$PAGE->requires->jquery();

echo $OUTPUT->doctype() ?>
<html <?php echo $OUTPUT->htmlattributes(); ?>>
<head>
    <title><?php echo $OUTPUT->page_title(); ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->favicon(); ?>" />
    <link href='//fonts.googleapis.com/css?family=Roboto:400,300,700' rel='stylesheet' type='text/css'>
    <?php echo $OUTPUT->standard_head_html() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body <?php echo $OUTPUT->body_attributes(); ?>>
<?php echo $OUTPUT->standard_top_of_body_html() ?>

<div id="page" class="<?php echo $container; ?>">

<header id="page-header" class="clearfix">
        <div id="course-header">
            <?php echo $OUTPUT->course_header(); ?>
            
                </div>
    </header>

    <div id="page-content" class="row">
    <div id="page-banner" class="banner"><img src="<?php echo $OUTPUT->pix_url('banner', 'theme'); ?>" alt="logo" width="100%"/></div>

        <div id="moodle-navbar" class="navbar-collapse collapse">
        <?php echo $OUTPUT->custom_menu(); ?>

        </div>
      <div id="page-navbar" class="container">
        <div class="breadcrumb-button"><?php echo $OUTPUT->page_heading_button(); ?></div>
    </div>  

                     <?php
        if ($knownregionpost) {
            echo $OUTPUT->blocks('side-post', $regions['post']);
        }?>
         <div id="course-banner" class="col-sm-11 col-md-12">
            	<?php echo $OUTPUT->blocks('notices'); ?>
            </div>

            
        <div id="region-main" class="col-sm-11 col-md-12">
                    <?php
            echo $OUTPUT->main_content();
            ?>
        </div>

  
    </div>

</div>

<footer id="page-footer">
	<?php require_once(dirname(__FILE__).'/includes/footer.php'); ?>
</footer>

<?php echo $OUTPUT->standard_end_of_body_html() ?>

<script>
    $('body').show();
    $('.version').text(NProgress.version);
    NProgress.start();
    setTimeout(function() { NProgress.done(); $('.fade').removeClass('out'); }, 1000);

    $("#b-0").click(function() { NProgress.start(); });
    $("#b-40").click(function() { NProgress.set(0.4); });
    $("#b-inc").click(function() { NProgress.inc(); });
    $("#b-100").click(function() { NProgress.done(); });
</script>

<script type="text/javascript">
jQuery(document).ready(function() {
    var offset = 220;
    var duration = 500;
    jQuery(window).scroll(function() {
        if (jQuery(this).scrollTop() > offset) {
            jQuery('.back-to-top').fadeIn(duration);
        } else {
            jQuery('.back-to-top').fadeOut(duration);
        }
    });

    jQuery('.back-to-top').click(function(event) {
        event.preventDefault();
        jQuery('html, body').animate({scrollTop: 0}, duration);
        return false;
    })
    
    jQuery(" ul.navbar-nav li ul li").has("ul").addClass("dropdown-submenu");
    jQuery("div.supermenu > ul > li > a").not(":only-child").append("<b class='caret' />");
    jQuery("div.supermenu a:empty").addClass("display:none;");
});
</script>
 <a href="#top" class="back-to-top"><i class="fa fa-angle-up "></i></a>
</body>
</html>
