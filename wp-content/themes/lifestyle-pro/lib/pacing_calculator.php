<?php

add_action( 'genesis_after_entry_content', 'add__calculator_javascript' );
add_action( 'wp_enqueue_scripts', 'add_calculator_css' );

function add_calculator_javascript() {	
	if ( get_the_title() == "Marathon Pacing Calculator" )	{
		//Register the javascript file that contains client-side logic
		wp_register_script( 'marathon-pacing-calculator', CHILD_URL . '/marathon-pacing-calculator/marathon-pacing-calculator.js', array( 'jquery' ), '', true );
		//declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php) so it gets stored in the html for the page and can be picked up by the javascript
		wp_localize_script( 'marathon-pacing-calculator', 'ajax_parameters', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

		//Queue the script to be included in the html
		wp_enqueue_script( 'marathon-pacing-calculator');
		
		//Register and queue the jquery sparkline script
		wp_register_script( 'fr-sparkline', CHILD_URL . '/marathon-pacing-calculator/jquery.sparkline.js', array( 'jquery' ), '', true );
		wp_enqueue_script( 'fr-sparkline');			
	}
}

function add_calculator_css() {	
	if ( get_the_title() == "Marathon Pacing Calculator" )	{
		//Queue our css with chartist overrides etc.
		wp_enqueue_style( 'fr-marathon-pacing-stylesheet', CHILD_URL . '/marathon-pacing-calculator/marathon-pacing.css', false, filemtime( get_stylesheet_directory() . '/marathon-pacing-calculator/marathon-pacing.css' ) );		
        
		wp_enqueue_style( 'fr-pacing-calculator-stylesheet', CHILD_URL . '/marathon-pacing-calculator/marathon-pacing-calculator.css', false, filemtime( get_stylesheet_directory() . '/marathon-pacing-calculator/marathon-pacing-calculator.css' ) );	

	}
}

?>