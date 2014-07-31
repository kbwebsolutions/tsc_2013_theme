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
        <nav class="breadcrumb-nav" role="navigation" aria-label="breadcrumb"><?php echo $OUTPUT->navbar(); ?></nav>
        <div class="breadcrumb-button"><?php echo $OUTPUT->page_heading_button(); ?></div>
    </div>  
                <?php
        if ($knownregionpost) {
            echo $OUTPUT->blocks('side-post', $regions['post']);
        }?>