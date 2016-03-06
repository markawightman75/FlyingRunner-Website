<?php

add_action( 'genesis_after_entry_content', 'add_research_data_javascript' );
add_action( 'wp_enqueue_scripts', 'add_research_data_css' );

function add_research_data_javascript() {
	if ( get_the_title() == "Explore Marathon Research Data" )	{
		//Register the javascript file that contains client-side logic
		wp_register_script( 'explore-marathon-pacings', CHILD_URL . '/marathon-pacing-calculator/explore-marathon-pacings.js', array( 'jquery' ), '', true );
		//declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php) so it gets stored in the html for the page and can be picked up by the javascript
		wp_localize_script( 'explore-marathon-pacings', 'ajax_parameters', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		//Queue the script to be included in the html
		wp_enqueue_script( 'explore-marathon-pacings');
		
		//Register and queue the jquery sparkline script
		wp_register_script( 'fr-sparkline', CHILD_URL . '/marathon-pacing-calculator/jquery.sparkline.js', array( 'jquery' ), '', true );
		wp_enqueue_script( 'fr-sparkline');		
		
		//Register and queue the chartist.js script
		wp_register_script( 'fr-chartist', CHILD_URL . '/marathon-pacing-calculator/chartist.js/chartist.min.js', array( 'jquery' ), '', true );
		wp_enqueue_script( 'fr-chartist');
		
		//Queue our css with chartist overrides etc.
		//wp_enqueue_style( 'fr-marathon-pacing-stylesheet', CHILD_URL . '/marathon-pacing-calculator/marathon-pacing.css', false, filemtime( CHILD_URL . '/marathon-pacing-  calculator/marathon-pacing.css' ) );
		
		//Queue the Pocketgrid css
		//wp_enqueue_style( 'fr-pocketgrid', CHILD_URL . '/pocketgrid/pocketgrid.min.css', false, filemtime( CHILD_URL . '/pocketgrid/pocketgrid.min.css' ) );
	}
}

function add_research_data_css() {
	if ( get_the_title() == "Explore Marathon Research Data" )	{
		//Register and queue the chartist.js default css 
		wp_enqueue_style( 'fr-chartist-default-stylesheet', CHILD_URL . '/marathon-pacing-calculator/chartist.js/chartist.min.css', false, filemtime( get_stylesheet_directory() . '/marathon-pacing-calculator/chartist.js/chartist.min.css' ) );

		//Queue our css with chartist overrides etc.
		wp_enqueue_style( 'fr-marathon-pacing-stylesheet', CHILD_URL . '/marathon-pacing-calculator/marathon-pacing.css', false, filemtime( get_stylesheet_directory() . '/marathon-pacing-calculator/marathon-pacing.css' ) );
		
		wp_enqueue_style( 'fr-pacing-calculator-stylesheet', CHILD_URL . '/marathon-pacing-calculator/marathon-pacing-calculator.css', false, filemtime( get_stylesheet_directory() . '/marathon-pacing-calculator/marathon-pacing-calculator.css' ) );	

		//Queue the Pocketgrid css
		wp_enqueue_style( 'fr-pocketgrid', CHILD_URL . '/pocketgrid/pocketgrid.min.css', false, filemtime( get_stylesheet_directory() . '/pocketgrid/pocketgrid.min.css' ) );
	}	
}


?>