<?php
/*
Plugin Name: Genesis Custom Footer
Plugin URI: http://www.nutsandboltsmedia.com
Description: Allows you to change the Genesis footer credits from the Genesis Theme Settings page.
Version: 0.8
Author: Nuts and Bolts Media, LLC
Author URI: http://www.nutsandboltsmedia.com/

This plugin is released under the GPLv2 license. The images packaged with this plugin are the property of
their respective owners, and do not, necessarily, inherit the GPLv2 license.
*/

// Add settings link on plugin page
function nabm_settings_link($links) { 
  $settings_link = '<a href="admin.php?page=genesis">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}
 
$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'nabm_settings_link' );


// Register defaults

function nabm_footer_defaults( $defaults ) {
 
	$defaults['nabm_footer_creds'] = 'Copyright [footer_copyright] [footer_childtheme_link] &amp;middot; [footer_genesis_link] [footer_studiopress_link] &amp;middot; [footer_wordpress_link] &amp;middot; [footer_loginout]';
 
	return $defaults;
}
add_filter( 'genesis_theme_settings_defaults', 'nabm_footer_defaults' );

// Sanitization

function nabm_sanitization_filters() {
	genesis_add_option_filter( 'safe_html', GENESIS_SETTINGS_FIELD,
		array(
			'nabm_footer_creds',
		) );
}
add_action( 'genesis_settings_sanitizer_init', 'nabm_sanitization_filters' );

// Register metabox

function nabm_footer_settings_box( $_genesis_theme_settings_pagehook ) {
	add_meta_box('nabm-footer-box', 'Custom Footer Text', 'nabm_footer_box', $_genesis_theme_settings_pagehook, 'main', 'high');
}
add_action('genesis_theme_settings_metaboxes', 'nabm_footer_settings_box');

// Create metabox

function nabm_footer_box() {
	?>
	<p><?php _e("Enter your custom credits text, including HTML if desired. <strong>Please note:</strong> This option will not work if your functions.php already has a function for custom footer text.", 'nabm_footer'); ?></p>
	<label>Custom Footer Text:</label>
	<textarea id="nabm_footer_creds" class="large-text" name="<?php echo GENESIS_SETTINGS_FIELD; ?>[nabm_footer_creds]" cols="78" rows="8" /><?php echo htmlspecialchars( genesis_get_option('nabm_footer_creds') ); ?></textarea>
    <p><span class="description"><?php _e('<b>Default Text:</b><br/> Copyright [footer_copyright] [footer_childtheme_link] &amp;middot; [footer_genesis_link] [footer_studiopress_link] &amp;middot; [footer_wordpress_link] &amp;middot; [footer_loginout]', 'nabm_footer'); ?></span></p>
	<?php
}


// Customize the footer credits text

add_filter('genesis_footer_output', 'nabm_footer_creds_text', 10, 3);
function nabm_footer_creds_text($creds) {
	$custom_creds = genesis_get_option('nabm_footer_creds');
	if ($custom_creds) return $custom_creds;
	else return $creds;
}   