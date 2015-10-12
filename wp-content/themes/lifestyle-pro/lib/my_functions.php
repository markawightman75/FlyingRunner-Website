<?php

//*MAW Enable shortcodes in text widgets, for Monarch social following icons
add_filter('widget_text', 'do_shortcode');
 
/**
 * My Custom Functions
*/

//* Include our widgets
include_once( CHILD_DIR . '/lib/widgets/headline-large-widget.php' );
include_once( CHILD_DIR . '/lib/widgets/headlines-small-widget.php' );
include_once( CHILD_DIR . '/lib/widgets/adverts-small-widget.php' );
include_once( CHILD_DIR . '/lib/widgets/section-title-widget.php' );
include_once( CHILD_DIR . '/marathon-pacing-calculator/explore-marathon-pacings-widget.php' );
include_once( CHILD_DIR . '/marathon-pacing-calculator/marathon-pacing-calculator-widget.php' );

function add_custom_widgets() {  
  // register our custom widget..
  register_widget( 'Headline_Large_Widget' );
  register_widget( 'Headlines_Small_Widget' );
  register_widget( 'Adverts_Small_Widget' );
  register_widget( 'Section_Title_Widget' );
  register_widget( 'Explore_Marathon_Pacings_Widget' );
  register_widget( 'Marathon_Pacing_Calculator_Widget' );
}

add_action( 'widgets_init', 'add_custom_widgets' );

add_action( 'genesis_after_entry_content', 'add_pacing_calculator_javascript' );
function add_pacing_calculator_javascript() {
	if ( get_the_title() == "Explore Marathon Pacings" )	{
		//Register the javascript file that contains client-side logic
		wp_register_script( 'explore-marathon-pacings', CHILD_URL . '/marathon-pacing-calculator/explore-marathon-pacings.js', array( 'jquery' ), '', true );
		//declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php) so it gets stored in the html for the page and can be picked up by the javascript
		wp_localize_script( 'explore-marathon-pacings', 'ajax_parameters', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		//Queue the script to be included in the html
		wp_enqueue_script( 'explore-marathon-pacings');
		
		//Register and queue the jquery sparkline script
		wp_register_script( 'fr-sparkline', CHILD_URL . '/marathon-pacing-calculator/jquery.sparkline.js', array( 'jquery' ), '', true );
		wp_enqueue_script( 'fr-sparkline');
	}
	
	if ( get_the_title() == "Marathon Pacing Calculator" )	{
		//Register the javascript file that contains client-side logic
		wp_register_script( 'marathon-pacing-calculator', CHILD_URL . '/marathon-pacing-calculator/marathon-pacing-calculator.js', array( 'jquery' ), '', true );
		//Queue the script to be included in the html
		wp_enqueue_script( 'marathon-pacing-calculator');
		
		//Register and queue the jquery sparkline script
		wp_register_script( 'fr-sparkline', CHILD_URL . '/marathon-pacing-calculator/jquery.sparkline.js', array( 'jquery' ), '', true );
		wp_enqueue_script( 'fr-sparkline');
	
	}
}

genesis_register_sidebar( array(
	'id'		=> 'runningandtrainingpagecontentarea',
	'name'		=> __( 'Flying Runner Running & Training Page Content Area', 'Flying Runner' ),
	'description'	=> __( 'This is the widget area for the articles on the running & training page. Put headlines, adverts widgets etc. in here.', 'Flying Runner' ),
) );
genesis_register_sidebar( array(
	'id'		=> 'featurespagecontentarea',
	'name'		=> __( 'Flying Runner Features Page Content Area', 'Flying Runner' ),
	'description'	=> __( 'This is the widget area for the articles on the features page. Put headlines, adverts widgets etc. in here.', 'Flying Runner' ),
) );
genesis_register_sidebar( array(
	'id'		=> 'newsspagecontentarea',
	'name'		=> __( 'Flying Runner News Page Content Area', 'Flying Runner' ),
	'description'	=> __( 'This is the widget area for the articles on the news page. Put headlines, adverts widgets etc. in here.', 'Flying Runner' ),
) );
genesis_register_sidebar( array(
	'id'		=> 'marathonpacingcalculatorcontentarea',
	'name'		=> __( 'Flying Runner Marathon Pacing Calculator  Content Area', 'Flying Runner' ),
	'description'	=> __( 'This is the widget area for the marathon pacing calculator tool.', 'Flying Runner' ),
) );
genesis_register_sidebar( array(
	'id'		=> 'exploremarathonpacingscontentarea',
	'name'		=> __( 'Flying Runner Explore Marathon Pacings Content Area', 'Flying Runner' ),
	'description'	=> __( 'This is the widget area for the explore marathon pacings tool.', 'Flying Runner' ),
) );



add_filter( 'wp_nav_menu_items', 'theme_menu_extras', 10, 2 );
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
add_filter( 'genesis_search_text', 'modify_search_text' );
function modify_search_text( $text ) {
	return esc_attr( "I'm looking for..." );
}


//* Add the page widget in the content - HTML5
//add_action( 'genesis_entry_footer', 'nabm_add_page_content' );
add_action( 'genesis_after_entry_content', 'add_features_and_news_page_content' );
function add_features_and_news_page_content() {
	$page_id_features = '67';
	$page_id_news = '144';
	$page_id_timepredictor = '3714';
	
	$this_page_title = get_the_title();
	
	if ( is_page($page_id_features) )
	{	
		genesis_widget_area ('featurespagecontentarea', array(
			'before' => '<div class="featurespagecontentarea"><div class="wrap">',
			'after' => '</div></div>',
		) );
	}
	if ( is_page($page_id_news) )
	{	
		genesis_widget_area ('newsspagecontentarea', array(
			'before' => '<div class="newsspagecontentarea"><div class="wrap">',
			'after' => '</div></div>',
		) );
	}
	if ( $this_page_title == "Marathon Pacing Calculator" )
	{	
		genesis_widget_area ('marathonpacingcalculatorcontentarea', array(
			'before' => '<div class="marathonpacingcalculatorcontentarea"><div class="wrap">',
			'after' => '</div></div>',
		) );		
	}

	if ( $this_page_title == "Explore Marathon Pacings" )
	{	
		genesis_widget_area ('exploremarathonpacingscontentarea', array(
			'before' => '<div class="exploremarathonpacingscontentarea"><div class="wrap">',
			'after' => '</div></div>',
		) );		
	}
	
	if ($this_page_title == "Running and Training")
	{
		genesis_widget_area ('runningandtrainingpagecontentarea', array(
			'before' => '<div class="runningandtrainingpagecontentarea"><div class="wrap">',
			'after' => '</div></div>',
		) );
		
	}
	
	if ($this_page_title == "All Posts")
	{
			// List all posts and pages
			$args = array(
					'nopaging' => true, 
					'post_type' => 'post',
					'post_status' => 'publish',
					'orderby' => 'date',   
					'order' => 'DESC',
				);
			
			$posts = new WP_Query( $args );
			if( $posts->have_posts() ):
				//* Build an option tag for each post, selecting the one that matches the currently-set post id
				?>
				<table style="width:100%; table-layout:fixed; font-family:arial; font-size: 14px;">
				<tr>
				<td>Title</td>
				<td style="width: 280px;">Featured image</td>
				<td style="width: 80px;">Post ID</td>
				<td style="width: 180px;">Categories</td>
				<td style="width: 55px;">Date</td>
				<tr>
				<?php
				
				while ( $posts->have_posts() ) : $posts->the_post();
					echo "<tr>";
					
					echo "<td>" . get_the_title() . "</td>";
					
					
					//Get the url of the featured image
					$thumb_id = get_post_thumbnail_id();
					$thumb_url_array = wp_get_attachment_image_src($thumb_id, 'thumbnail-size', true);
					$thumb_url = $thumb_url_array[0];
					$pos = strpos ($thumb_url, "wp-content");
					$short_thumb_url = substr($thumb_url, $pos+11);
					echo "<td>" . $short_thumb_url . "</td>";
					
					
					$post_id = get_the_ID();
					echo "<td>" . $post_id . "</td>";
					$cats = array();
					foreach(wp_get_post_categories($post_id) as $c)
					{
						$cat = get_category($c);
						array_push($cats,$cat->name);
					}

					if(sizeOf($cats)>0)
					{
						$post_categories = implode(',',$cats);
					} else {
						$post_categories = "Not Assigned";
					}

					echo "<td>";
					echo  $post_categories;
					echo "</td>";
					
					echo "<td>" . get_the_date("j/n/y") . "</td>";										
					
					echo "</tr>";
				endwhile;
				?>
				</table>
				<?php
			endif;
			wp_reset_postdata();				
			
	}
	
}

// Filter function that is a modified version of the default in markup.php
// This function adds the classes to an entry (post) 
// The modified part adds column classes on pages that aren't single pages or posts (e.g. home page)
// This function is set as the filter in sub-featured-posts widget.
//remove_filter( 'genesis_attr_entry', 'genesis_attributes_entry' );
//add_filter( 'genesis_attr_entry', 'custom_add_entryclasses_attr' );

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
	//return $attributes;
	if( is_singular() )
	{
		// This is a single post or page. We don't want to add column classes to this, so leave here.
		return $attributes;
	}
 
	//echo $attributes['class'];
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
	wp_enqueue_style( 'fr-main-stylesheet', CHILD_URL . '/custom.css', false, filemtime( get_stylesheet_directory() . '/custom.css' ) );
	wp_enqueue_style( 'fr-headlines-stylesheet', CHILD_URL . '/headlines.css', false, filemtime( get_stylesheet_directory() . '/headlines.css' ) );
	wp_enqueue_style( 'fr-adverts-stylesheet', CHILD_URL . '/adverts.css', false, filemtime( get_stylesheet_directory() . '/adverts.css' ) );	
	
	if ( get_the_title() == "Marathon Pacing Calculator" || get_the_title() == "Explore Marathon Pacings" )	{
		wp_enqueue_style( 'fr-pacing-calculator-stylesheet', CHILD_URL . '/marathon-pacing-calculator/marathon-pacing-calculator.css', false, filemtime( get_stylesheet_directory() . '/marathon-pacing-calculator/marathon-pacing-calculator.css' ) );	
	}	
}

/** Use copies of the Magic Action Box css files that are in our theme
    folder instead of the ones added by default which are in the plugin folder
    and therefore not under version control
*/
//wp_dequeue_style( 'mab-user-style-1-css' );
//wp_dequeue_style( 'mab-actionbox-style-709-css' );
//wp_enqueue_style( 'mab-user-style-1', CHILD_URL . '/magic-action-box/style-1.css', false, filemtime(get_stylesheet_directory() . '/magic-action-box/style-1.css' )) ;
//wp_enqueue_style( 'mab-actionbox-style-709', CHILD_URL . '/magic-action-box/actionbox-709.css', false, filemtime(get_stylesheet_directory() . '/magic-action-box/actionbox-709.css' )) ;

/** Dequeue style.css and enqueue it again with version number (for cache busting) */
/** For details of more reliable htaccess-based cache busting see https://wordimpress.com/wordpress-css-and-js-cache-busting/ */
wp_dequeue_style( 'lifestyle-pro-theme-css' );
if ( ! is_admin() )
{
  wp_enqueue_style( 'lifestyle-pro-theme', CHILD_URL . '/style.css', false, filemtime( get_stylesheet_directory() . '/style.css' ) );
}

wp_enqueue_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css', array(), CHILD_THEME_VERSION );

$this_page_title = get_the_title();
if ($this_page_title != "Contact Us") {
add_filter( 'wpcf7_load_js', '__return_false' );
add_filter( 'wpcf7_load_css', '__return_false' );
}

//An open-sans style (which pulls a google font) is added by default by WP for admin pages
 if ( ! (is_admin() or is_user_logged_in() or in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) ) )) {
wp_deregister_style('open-sans');
} 
//Monarch and Bloom both pull in open-sans. No point in duplicating, so remove the bloom one
//Doesn't work...
//wp_dequeue_style( 'et_bloom-open-sans-css' );
//wp_deregister_style('et_bloom-open-sans-css');



//* Marathon time predictor tool
//* TODO: Only load these on the time predictor page
 // embed the javascript file that makes the AJAX request
 //my-ajax-request
//THIS JQUERY LINE CAUSES THE WOOCOMMERCE ADD NEW ORDER SCREEN TO BREAK (CAN'T EDIT ORDER DETAILS) - NEEDS FIXING
//CHECK FIREBUG CONSOLE FOR "UNCAUGHT ERROR" - THAT'S WHAT I'M GETTING WITH THIS ENABLED
 //DEBUG wp_enqueue_script( 'jquery-1.11.3.min', CHILD_URL . '/marathon-time-predictor/jquery-1.11.3.min.js', array( 'jquery' ) );
//wp_enqueue_script( 'time-predictor-global', CHILD_URL . '/marathon-time-predictor/global.js', array( 'jquery' ) );
	
	


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
		else
		{
			//This is our custom WC overrides css
			wp_enqueue_style( 'fr-woocommerce-overrides-stylesheet', CHILD_URL . '/woocommerce-overrides.css', false, filemtime( get_stylesheet_directory() . '/woocommerce-overrides.css' ) );
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

// Change number or products per row to 3
add_filter('loop_shop_columns', 'loop_columns');
if (!function_exists('loop_columns')) {
	function loop_columns() {
		return 3; // 3 products per row
	}
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