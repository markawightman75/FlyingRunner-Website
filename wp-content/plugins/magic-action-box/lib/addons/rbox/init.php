<?php
/** Definitions **/
define( 'MAB_RBOX_TYPE', 'rbox' );
define( 'MAB_RBOX_DOMAIN', MAB_DOMAIN );

/** Initialize our addon **/
add_action( 'mab_init', 'mab_rbox_init' );

function mab_rbox_init(){

	$status = 'enabled';
	$description = __('Use this box type to display a different random action box on every page load.', MAB_RBOX_DOMAIN );
	$title = __('Random Box', MAB_RBOX_DOMAIN);

	$boxType = array(
		'type' => MAB_RBOX_TYPE,
		'name' => $title,
		'description' => $description,
		'status' => $status
	);
	mab_register_action_box( $boxType );
}