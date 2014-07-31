<nav role="navigation" class="navbar <?php echo $navbartype; ?>">
    <div class="fullwidth">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#moodle-navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        
    </div>

    <div id="moodle-navbar" class="navbar-collapse collapse">
        
        <?php echo $OUTPUT->user_menu(); ?>
        <!--<?php echo $OUTPUT->search_form(new moodle_url("$CFG->wwwroot/$CFG->admin/search.php"), optional_param('query', '', PARAM_RAW)); ?>   -->
       <?php echo $OUTPUT->admin_menu(); ?>
      <!--  Not Sure what this is doing
      <ul class="nav pull-right">
            <li><?php echo $OUTPUT->page_heading_menu(); ?></li>
        </ul>
        -->
    </div>
    </div>
</nav>