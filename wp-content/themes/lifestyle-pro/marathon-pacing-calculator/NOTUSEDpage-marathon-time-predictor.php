<?php
/**
 * Genesis Framework.
 *
 * WARNING: This file is part of the core Genesis Framework. DO NOT edit this file under any circumstances.
 * Please do all modifications in the form of a child theme.
 *
 * @package Genesis\Templates
 * @author  StudioPress
 * @license GPL-2.0+
 * @link    http://my.studiopress.com/themes/genesis/
 */

//wp_enqueue_script( 'time-predictor-global', CHILD_URL . '/marathon-time-predictor/global.js', array('jquery'), '1.0.0', false );
 
 // embed the javascript file that makes the AJAX request
 //my-ajax-request
wp_enqueue_script( 'time-predictor-global', CHILD_URL . '/marathon-time-predictor/global.js', array( 'jquery' ) );

// declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
wp_localize_script( 'time-predictor-global', 'MyAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );


 ?>

<html>
	<head>
		<title>Marathon Time Predictor</title>
		<style>
			<!-- Start by setting display:none to make this hidden.
	   Then we position it in relation to the viewport window
	   with position:fixed. Width, height, top and left speak
	   speak for themselves. Background we set to 80% white with
	   our animation centered, and no-repeating -->
			.modal {
				display:    none;
				position:   fixed;
				z-index:    1000;
				top:        0;
				left:       0;
				height:     100%;
				width:      100%;
				background: rgba( 255, 255, 255, .8 ) 
							url('http://sampsonresume.com/labs/pIkfp.gif') 
							50% 50% 
							no-repeat;
			}

			<!-- When the body has the loading class, we turn
			   the scrollbar off with overflow:hidden -->
			body.loading {
				overflow: hidden;   
			}

			<!-- Anytime the body has the loading class, our
			   modal element will be visible -->
			body.loading .modal {
				display: block;
			}
		</style>
	</head>
	<body>
		Name: <input type="text" id="name">
		<div id="name-data">NONE YET</div>
		<input type="submit" id="name-submit" value="Grab">
		
		
		<!--<script src="http://code.jquery.com/jquery-1.8.0.min.js"></script>-->
		
		<div class="modal"><!-- Place at bottom of page --></div>
	</body>
</html>

<?php
//* This file handles pages, but only exists for the sake of child theme forward compatibility.
genesis();