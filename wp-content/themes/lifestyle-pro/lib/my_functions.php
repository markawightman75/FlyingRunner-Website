<?php
 
include_once( CHILD_DIR . '/lib/facebook.php' );
include_once( CHILD_DIR . '/lib/woocommerce.php' );
include_once( CHILD_DIR . '/lib/banners.php' );
include_once( CHILD_DIR . '/lib/content_areas_and_widgets.php' );
include_once( CHILD_DIR . '/lib/pacing_calculator.php' );
include_once( CHILD_DIR . '/lib/explore_research_data.php' );

// Display a custom favicon
add_filter( 'genesis_pre_load_favicon', 'sp_favicon_filter' );

//* Remove the site title
remove_action( 'genesis_site_title', 'genesis_seo_site_title' );

//* Remove the site description
remove_action( 'genesis_site_description', 'genesis_seo_site_description' );

// Add search form to nav
add_filter( 'wp_nav_menu_items', 'theme_menu_extras', 10, 2 );

// Set text inside search box in nav
add_filter( 'genesis_search_text', 'modify_search_text' );

// Add support for 3-column footer widgets. Styled to full-width in style.css with .footer-widgets-4 class
add_theme_support( 'genesis-footer-widgets', 4 );

// Enable shortcodes in text widgets, for Monarch social following icons
add_filter('widget_text', 'do_shortcode');

// Add shortcode that lets us dynamically include the url of the site in text, widgets etc., e.g. <a href="[url]/pretend-page/">
add_shortcode('url','home_url');

// Remove post meta info (Filed under: [category]   Tagged with: [tags]) from end of post
remove_action( 'genesis_entry_footer', 'genesis_post_meta' );

// Load the custom.css, headlines.css and adverts.css styles
add_action( 'wp_enqueue_scripts', 'custom_load_custom_style_sheet' );

// Force IE to render using IE9 rendering engine, to fix crashes in IE10
add_action( 'wp_head', 'wc_add_IE_10_meta_tag' , 2 );

//* Disable the emojicons that were added in WP4.2 and create an unnecessary mess in the HTML
add_action( 'init', 'disable_wp_emojicons' );

/** Dequeue style.css and enqueue it again with version number (for cache busting) */
/** For details of more reliable htaccess-based cache busting see https://wordimpress.com/wordpress-css-and-js-cache-busting/ */
wp_dequeue_style( 'lifestyle-pro-theme-css' );
if ( ! is_admin() )
{
  wp_enqueue_style( 'lifestyle-pro-theme', CHILD_URL . '/style.css', false, filemtime( get_stylesheet_directory() . '/style.css' ) );
}

wp_enqueue_style( 'fr-font-awesome', CHILD_URL . '/font-awesome-4.4.0/css/font-awesome.min.css', false, filemtime( get_stylesheet_directory() . '/font-awesome-4.4.0/css/font-awesome.min.css') );

//An open-sans style (which pulls a google font) is added by default by WP for admin pages
 if ( ! (is_admin() or is_user_logged_in() or in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) ) )) {
     wp_deregister_style('open-sans');
} 

$this_page_title = get_the_title();
if ($this_page_title != "Contact Us") {
    add_filter( 'wpcf7_load_js', '__return_false' );
    add_filter( 'wpcf7_load_css', '__return_false' );
}

//*********************** Functions ********************************************************************************//

// Disable the emojicons that were added in WP4.2 and create an unnecessary mess in the HTML
// See http://wordpress.stackexchange.com/questions/185577/disable-emojicons-introduced-with-wp-4-2 
function disable_wp_emojicons() {
  // all actions related to emojis
  remove_action( 'admin_print_styles', 'print_emoji_styles' );
  remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
  remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
  remove_action( 'wp_print_styles', 'print_emoji_styles' );
  remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
  remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
  remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );

  // filter to remove TinyMCE emojis
  add_filter( 'tiny_mce_plugins', 'disable_emojicons_tinymce' );
}

function disable_emojicons_tinymce( $plugins ) {
  if ( is_array( $plugins ) ) {
    return array_diff( $plugins, array( 'wpemoji' ) );
  } else {
    return array();
  }
}

// Display a custom favicon
function sp_favicon_filter( $favicon_url ) {
	//return 'http://www.flyingrunner.co.uk/favicon.ico';
    return CHILD_URL . '/images/favicons/favicon.ico';
}

// Remove Post Info, Post Meta from Archive Pages
function themeprefix_remove_post_meta() {
	if (is_archive()) {
		remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
		remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
		}
}

// Force IE to render using IE9 rendering engine, to fix crashes in IE10
function wc_add_IE_10_meta_tag() {
  echo '<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE9" >' . "\n";
}

function custom_load_custom_style_sheet() {
	wp_enqueue_style( 'fr-main-stylesheet', CHILD_URL . '/custom.css', false, filemtime( get_stylesheet_directory() . '/custom.css' ) );
	wp_enqueue_style( 'fr-headlines-stylesheet', CHILD_URL . '/headlines.css', false, filemtime( get_stylesheet_directory() . '/headlines.css' ) );
	wp_enqueue_style( 'fr-adverts-stylesheet', CHILD_URL . '/adverts.css', false, filemtime( get_stylesheet_directory() . '/adverts.css' ) );	
}

/**
 * Filter menu items, appending either a search form or today's date.
 *
 * @param string   $menu HTML string of list items.
 * @param stdClass $args Menu arguments.
 *
 * @return string Amended HTML string of list items.
 */
function theme_menu_extras( $menu, $args ) {

	//* Change 'primary' to 'secondary' to add extras to the secondary navigation menu
	if ( 'secondary' !== $args->theme_location )
		return $menu;

	//* Uncomment this block to add a search form to the navigation menu
	
	ob_start();
	get_search_form();
	$search = ob_get_clean();
	$menu  .= '<li class="right search">' . $search . '</li>';
	

	//* Uncomment this block to add the date to the navigation menu
	/*
	$menu .= '<li class="right date">' . date_i18n( get_option( 'date_format' ) ) . '</li>';
	*/

	return $menu;

}
 
function modify_search_text( $text ) {
	return esc_attr( "I'm looking for..." );
}
