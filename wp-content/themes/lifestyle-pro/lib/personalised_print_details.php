<?php

add_action( 'genesis_after_entry_content', 'add_personalised_print_javascript' );
add_action( 'wp_enqueue_scripts', 'add_personalised_print_css' );

function add_personalised_print_javascript() {	
	if ( get_the_title() == "Confirm Personalised Art Print Details" )	{
		//Register the javascript file that contains client-side logic
		wp_register_script( 'confirm-personalised-print-details', CHILD_URL . '/personalised-print-details/personalised-print-details.js', array( 'jquery' ), '', true );
		//declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php) so it gets stored in the html for the page and can be picked up by the javascript
		wp_localize_script( 'confirm-personalised-print-details', 'ajax_parameters', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

		//Queue the script to be included in the html
		wp_enqueue_script( 'confirm-personalised-print-details');
	}
}

function add_personalised_print_css() {	
	if ( get_the_title() == "Confirm Personalised Art Print Details" )	{
		
		wp_enqueue_style( 'confirm-personalised-print-details', CHILD_URL . '/personalised-print-details/personalised-print-details.css', false, filemtime( get_stylesheet_directory() . '/personalised-print-details/personalised-print-details.css' ) );		        
	}
}


?>