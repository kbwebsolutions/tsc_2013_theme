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
	       <?php echo $OUTPUT->admin_menu(); ?>
	       <?php echo $OUTPUT->user_menu(); ?>
	    </div>
	</div>
</nav>