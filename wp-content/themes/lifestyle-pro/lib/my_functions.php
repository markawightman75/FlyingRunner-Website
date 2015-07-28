<?php
 
/**
 * My Custom Functions
*/

//* Add our custom "main featured post" widget
include_once( CHILD_DIR . '/lib/widgets/headline-large-widget.php' );
include_once( CHILD_DIR . '/lib/widgets/sub-featured-posts-widget.php' );

function add_custom_widgets() {  
  // register our custom widget..
  register_widget( 'Headline_Large_Widget' );
  register_widget( 'Sub_Featured_Posts' );
}
add_action( 'widgets_init', 'add_custom_widgets' );


// Filter function that is a modified version of the default in markup.php
// This function adds the classes to an entry (post) 
// The modified part adds column classes on pages that aren't single pages or posts (e.g. home page)
// This function is set as the filter in sub-featured-posts widget.
remove_filter( 'genesis_attr_entry', 'genesis_attributes_entry' );
add_filter( 'genesis_attr_entry', 'custom_add_entryclasses_attr' );

function custom_add_entryclasses_attr( $attributes ) {
 
	global $post;
	global $current_post;
	$attributes['class']     = join( ' ', get_post_class() );
	$attributes['itemscope'] = 'itemscope';
	$attributes['itemtype']  = 'http://schema.org/CreativeWork';

	//* Blog posts microdata
	if ( 'post' === $post->post_type ) {

		$attributes['itemtype']  = 'http://schema.org/BlogPosting';

		//* If main query,
		if ( is_main_query() )
			$attributes['itemprop']  = 'blogPost';

	}
	
	if( is_singular() )
	{
		// This is a single post or page. We don't want to add column classes to this, so leave here.
		return $attributes;
	}
 
	 if ( has_category ('main-feature', $post) || has_category ('main-news', $post) )
	 {
		 //This is a main feature post. We don't want to add column classes to this, so leave here.
		 return $attributes;
	 }
    
	
	 global $wp_query;
	
	 // add extra 'one-third' column CSS class
	 $attributes['class'] .= ' one-third';
	 
	 // If this is the 1st, 4th etc. post in the loop then add the 'first' column CSS class
	 if( 0 == $wp_query->current_post || 0 == $wp_query->current_post % 3 )
		$attributes['class'] .= ' first';

	 // return the attributes
	 return $attributes;
 
}

add_theme_support( 'genesis-connect-woocommerce' );

//* Add support for 3-column footer widgets. Styled to full-width in style.css with .footer-widgets-4 class
add_theme_support( 'genesis-footer-widgets', 4 );

//* Add shortcode that lets us dynamically include the url of the site in text, widgets etc., e.g. <a href="[url]/pretend-page/">
add_shortcode('url','home_url');

add_action( 'wp_enqueue_scripts', 'custom_load_custom_style_sheet' );
function custom_load_custom_style_sheet() {
	wp_enqueue_style( 'mycustom-stylesheet', CHILD_URL . '/custom.css', false, filemtime( get_stylesheet_directory() . '/custom.css' ) );
}

/** Use copies of the Magic Action Box css files that are in our theme
    folder instead of the ones added by default which are in the plugin folder
    and therefore not under version control
*/
wp_dequeue_style( 'mab-user-style-1-css' );
wp_dequeue_style( 'mab-actionbox-style-709-css' );
wp_enqueue_style( 'mab-user-style-1', CHILD_URL . '/magic-action-box/style-1.css', false, filemtime(get_stylesheet_directory() . '/magic-action-box/style-1.css' )) ;
wp_enqueue_style( 'mab-actionbox-style-709', CHILD_URL . '/magic-action-box/actionbox-709.css', false, filemtime(get_stylesheet_directory() . '/magic-action-box/actionbox-709.css' )) ;

/** Dequeue style.css and enqueue it again with version number (for cache busting) */
/** For details of more reliable htaccess-based cache busting see https://wordimpress.com/wordpress-css-and-js-cache-busting/ */
wp_dequeue_style( 'lifestyle-pro-theme-css' );
if ( ! is_admin() )
{
  wp_enqueue_style( 'lifestyle-pro-theme', CHILD_URL . '/style.css', false, filemtime( get_stylesheet_directory() . '/style.css' ) );
}

wp_enqueue_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css', array(), CHILD_THEME_VERSION );
//* Load the fonts we need
//*add_action( 'wp_enqueue_scripts', 'lifestyle_google_fonts' );
//*function lifestyle_google_fonts() {
//*	wp_enqueue_style( 'google-fonts', '//fonts.googleapis.com/css?family=Droid+Sans:400,700|Roboto+Slab:400,300,700|Roboto:400', array(), CHILD_THEME_VERSION );
//*}

//* Disable the emojicons that were added in WP4.2 and create an unnecessary mess in the HTML
//* See http://wordpress.stackexchange.com/questions/185577/disable-emojicons-introduced-with-wp-4-2 
add_action( 'init', 'disable_wp_emojicons' );
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


//*************************************************
/**
 * Optimize WooCommerce Scripts
 * Remove WooCommerce Generator tag, styles, and scripts from non WooCommerce pages.
 * MAW Added 18/1/2015
 * See: https://wordimpress.com/how-to-load-woocommerce-scripts-and-styles-only-in-shop/
 */

add_action( 'wp_enqueue_scripts', 'child_manage_woocommerce_styles', 99 );

function child_manage_woocommerce_styles() {
	//remove generator meta tag
	remove_action( 'wp_head', array( $GLOBALS['woocommerce'], 'generator' ) );

	//first check that woo exists to prevent fatal errors
	if ( function_exists( 'is_woocommerce' ) ) {
		//dequeue scripts and styles
		if ( ! is_woocommerce() && ! is_cart() && ! is_checkout() ) {
			wp_dequeue_style( 'woocommerce_frontend_styles' );
			wp_dequeue_style( 'woocommerce_fancybox_styles' );
			wp_dequeue_style( 'woocommerce_chosen_styles' );
			wp_dequeue_style( 'woocommerce_prettyPhoto_css' );
wp_dequeue_style( 'woocommerce' );
wp_dequeue_style( 'woocommerce-layout' );
wp_dequeue_style( 'woocommerce-smallscreen' );
wp_dequeue_style( 'woocommerce-general' );
wp_dequeue_style( 'pac-styles' );
wp_dequeue_style( 'pac-layout-styles' );
			wp_dequeue_script( 'wc_price_slider' );
			wp_dequeue_script( 'wc-single-product' );
			wp_dequeue_script( 'wc-add-to-cart' );
			wp_dequeue_script( 'wc-cart-fragments' );
			wp_dequeue_script( 'wc-checkout' );
			wp_dequeue_script( 'wc-add-to-cart-variation' );
			wp_dequeue_script( 'wc-single-product' );
			wp_dequeue_script( 'wc-cart' );
			wp_dequeue_script( 'wc-chosen' );
			wp_dequeue_script( 'woocommerce' );
			wp_dequeue_script( 'prettyPhoto' );
			wp_dequeue_script( 'prettyPhoto-init' );
			wp_dequeue_script( 'jquery-blockui' );
			wp_dequeue_script( 'jquery-placeholder' );
			wp_dequeue_script( 'fancybox' );
			wp_dequeue_script( 'jqueryui' );
		}
	}

}

//*************************************************

//* Override the message displayed when you add a product to the cart, to use "Basket" not "Cart"
add_filter( 'wc_add_to_cart_message', 'custom_add_to_cart_message' ,10,2);
function custom_add_to_cart_message($message, $product_id) {
	 
     if ( is_array( $product_id ) ) {
          $titles = array();
  
          foreach ( $product_id as $id ) {
              $titles[] = get_the_title( $id );
          }
 
          $added_text = sprintf( __( 'Added %s to your basket.', 'woocommerce' ), wc_format_list_of_items( $titles ) );
  
      } else {
          $added_text = sprintf( __( '&quot;%s&quot; was successfully added to your basket.', 'woocommerce' ), get_the_title( $product_id ) );
      }
 
      // Output success messages
      if ( get_option( 'woocommerce_cart_redirect_after_add' ) == 'yes' ) :
  
          $return_to  = apply_filters( 'woocommerce_continue_shopping_redirect', wp_get_referer() ? wp_get_referer() : home_url() );
  
          $message    = sprintf('<a href="%s" class="button wc-forward">%s</a> %s', $return_to, __( 'Continue Shopping', 'woocommerce' ), $added_text );
  
      else :
  
          $message    = sprintf('<a href="%s" class="button wc-forward">%s</a> %s', get_permalink( wc_get_page_id( 'cart' ) ), __( 'View Cart', 'woocommerce' ), $added_text );
  
      endif;
  
	return $message; 

  }



//* Remove the site title
remove_action( 'genesis_site_title', 'genesis_seo_site_title' );

//* Remove the site description
remove_action( 'genesis_site_description', 'genesis_seo_site_description' );

//* Display a custom favicon
add_filter( 'genesis_pre_load_favicon', 'sp_favicon_filter' );
function sp_favicon_filter( $favicon_url ) {
	return 'http://www.flyingrunner.co.uk/favicon.ico';}

add_action( 'woocommerce_before_cart_table', 'woo_add_continue_shopping_button_to_cart' );

function woo_add_continue_shopping_button_to_cart() {

$shop_page_url = get_permalink( woocommerce_get_page_id( 'shop' ) );

echo '<div class="woocommerce-message">';

echo ' <a href="'.$shop_page_url.'" class="button">Continue Shopping ?</a> Need some more products?';

echo '</div>';
}

/** Customize the post header function by wptron */
/**add_filter('genesis_post_info', 'wpt_info_filter');**/

/**Remove post meta info (Filed under: [category]   Tagged with: [tags]) from end of post **/
remove_action( 'genesis_entry_footer', 'genesis_post_meta' );

function wpt_info_filter($post_info) {
if (!is_page()) {
$post_info = 'Written by [post_author_posts_link] [post_edit]';
}
return $post_info;
}


// Remove Post Info, Post Meta from Archive Pages
function themeprefix_remove_post_meta() {
	if (is_archive()) {
		remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
		remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
		}
}
/**add_action ( 'genesis_entry_header', 'themeprefix_remove_post_meta' );**/

?>