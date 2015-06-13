<?php
/** Definitions **/
define( 'MAB_CF7_TYPE', 'cf7' );


/** Initialize our addon **/
add_action( 'mab_init', 'mab_cf7_init' );

function mab_cf7_init(){

	mab_cf7_register_action_box_type();
}

/**
 * Register Contact Form 7 action box type
 * @return none
 */
function mab_cf7_register_action_box_type( $disabled = false ){

	$status = $disabled ? 'disabled' : 'enabled';
	$description = $disabled ? 'This plugin requires the <a href="http://wordpress.org/extend/plugins/contact-form-7/" target="_blank">Contact Form 7 plugin</a> to be installed and activated.<br />Make it easy for visitors to contact you right away. Integrates with the awesome Contact Form 7 plugin.' : 'Make it easy for visitors to contact you right away. Integrates with the awesome Contact Form 7 plugin.';
	$title = 'Contact Form 7';

	$boxType = array(
		'type' => MAB_CF7_TYPE,
		'name' => $title,
		'description' => $description,
		'status' => $status
	);
	mab_register_action_box( $boxType );
}