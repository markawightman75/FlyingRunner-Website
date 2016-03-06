<?php

//*MAW Enable shortcodes in text widgets, for Monarch social following icons
add_filter('widget_text', 'do_shortcode');
 
/**
 * My Custom Functions
*/

include_once( CHILD_DIR . '/lib/facebook.php' );
include_once( CHILD_DIR . '/lib/woocommerce.php' );

//* Include our widgets
include_once( CHILD_DIR . '/lib/widgets/headline-large-widget.php' );
include_once( CHILD_DIR . '/lib/widgets/headlines-small-widget.php' );
include_once( CHILD_DIR . '/lib/widgets/adverts-small-widget.php' );
include_once( CHILD_DIR . '/lib/widgets/section-title-widget.php' );
include_once( CHILD_DIR . '/marathon-pacing-calculator/explore-marathon-pacings-widget.php' );
include_once( CHILD_DIR . '/marathon-pacing-calculator/explore-marathon-groups-widget.php' );
include_once( CHILD_DIR . '/marathon-pacing-calculator/marathon-pacing-calculator-widget.php' );

function add_custom_widgets() {  
  // register our custom widget..
  register_widget( 'Headline_Large_Widget' );
  register_widget( 'Headlines_Small_Widget' );
  register_widget( 'Adverts_Small_Widget' );
  register_widget( 'Section_Title_Widget' );
  register_widget( 'Explore_Marathon_Pacings_Widget' );
  register_widget( 'Explore_Marathon_Groups_Widget' );
  register_widget( 'Marathon_Pacing_Calculator_Widget' );
}

add_action( 'widgets_init', 'add_custom_widgets' );

add_action( 'genesis_after_entry_content', 'add_pacing_calculator_javascript' );


function add_pacing_calculator_javascript() {
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
		//wp_enqueue_style( 'fr-marathon-pacing-stylesheet', CHILD_URL . '/marathon-pacing-calculator/marathon-pacing.css', false, filemtime( CHILD_URL . '/marathon-pacing-calculator/marathon-pacing.css' ) );
		
		//Queue the Pocketgrid css
		//wp_enqueue_style( 'fr-pocketgrid', CHILD_URL . '/pocketgrid/pocketgrid.min.css', false, filemtime( CHILD_URL . '/pocketgrid/pocketgrid.min.css' ) );
	}
	
	if ( get_the_title() == "Explore Marathon Groups" )	{
		//Register the javascript file that contains client-side logic
		wp_register_script( 'explore-marathon-groups', CHILD_URL . '/marathon-pacing-calculator/explore-marathon-groups.js', array( 'jquery' ), '', true );
		//Queue the script to be included in the html
		wp_enqueue_script( 'explore-marathon-groups');
		
		//Register and queue the chartist.js script
		wp_register_script( 'fr-chartist', CHILD_URL . '/marathon-pacing-calculator/chartist.js/chartist.min.js', array( 'jquery' ), '', true );
		wp_enqueue_script( 'fr-chartist');
		//Register and queue the chartist.js default css 
		//wp_enqueue_style( 'fr-chartist-default-stylesheet', CHILD_URL . '/marathon-pacing-calculator/chartist.js/chartist.min.css', false, filemtime( CHILD_URL . '/marathon-pacing-calculator/chartist.js/chartist.min.css' ) );
		//Register and queue our css with chartist overrides etc.
		//wp_enqueue_style( 'fr-marathon-pacing-stylesheet', CHILD_URL . '/marathon-pacing-calculator/marathon-pacing.css', false, filemtime( CHILD_URL . '/marathon-pacing-calculator/marathon-pacing.css' ) );
	}
	
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
add_action( 'wp_enqueue_scripts', 'add_pacing_calculator_css' );
function add_pacing_calculator_css() {
	if ( get_the_title() == "Explore Marathon Research Data" )	{
		//Register and queue the chartist.js default css 
		wp_enqueue_style( 'fr-chartist-default-stylesheet', CHILD_URL . '/marathon-pacing-calculator/chartist.js/chartist.min.css', false, filemtime( get_stylesheet_directory() . '/marathon-pacing-calculator/chartist.js/chartist.min.css' ) );

		//Queue our css with chartist overrides etc.
		wp_enqueue_style( 'fr-marathon-pacing-stylesheet', CHILD_URL . '/marathon-pacing-calculator/marathon-pacing.css', false, filemtime( get_stylesheet_directory() . '/marathon-pacing-calculator/marathon-pacing.css' ) );
		
		//Queue the Pocketgrid css
		wp_enqueue_style( 'fr-pocketgrid', CHILD_URL . '/pocketgrid/pocketgrid.min.css', false, filemtime( get_stylesheet_directory() . '/pocketgrid/pocketgrid.min.css' ) );
	}
	
	if ( get_the_title() == "Explore Marathon Groups" )	{
		//Register and queue the chartist.js default css 
		wp_enqueue_style( 'fr-chartist-default-stylesheet', CHILD_URL . '/marathon-pacing-calculator/chartist.js/chartist.min.css', false, filemtime( get_stylesheet_directory() . '/marathon-pacing-calculator/chartist.js/chartist.min.css' ) );
		//Register and queue our css with chartist overrides etc.
		wp_enqueue_style( 'fr-marathon-pacing-stylesheet', CHILD_URL . '/marathon-pacing-calculator/marathon-pacing.css', false, filemtime( get_stylesheet_directory() . '/marathon-pacing-calculator/marathon-pacing.css' ) );
	}
	
	if ( get_the_title() == "Marathon Pacing Calculator" )	{
		//Queue our css with chartist overrides etc.
		wp_enqueue_style( 'fr-marathon-pacing-stylesheet', CHILD_URL . '/marathon-pacing-calculator/marathon-pacing.css', false, filemtime( get_stylesheet_directory() . '/marathon-pacing-calculator/marathon-pacing.css' ) );
		
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
genesis_register_sidebar( array(
	'id'		=> 'exploremarathongroupscontentarea',
	'name'		=> __( 'Flying Runner Explore Marathon Groups Content Area', 'Flying Runner' ),
	'description'	=> __( 'This is the widget area for the explore marathon groups tool.', 'Flying Runner' ),
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

	if ( $this_page_title == "Explore Marathon Research Data" )
	{	
		genesis_widget_area ('exploremarathonpacingscontentarea', array(
			'before' => '<div class="exploremarathonpacingscontentarea"><div class="wrap">',
			'after' => '</div></div>',
		) );		
	}
		
	if ( $this_page_title == "Explore Marathon Groups" )
	{	
		genesis_widget_area ('exploremarathongroupscontentarea', array(
			'before' => '<div class="exploremarathongroupscontentarea"><div class="wrap">',
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

//* Add support for 3-column footer widgets. Styled to full-width in style.css with .footer-widgets-4 class
add_theme_support( 'genesis-footer-widgets', 4 );

//* Add shortcode that lets us dynamically include the url of the site in text, widgets etc., e.g. <a href="[url]/pretend-page/">
add_shortcode('url','home_url');

add_action( 'wp_enqueue_scripts', 'custom_load_custom_style_sheet' );
function custom_load_custom_style_sheet() {
	wp_enqueue_style( 'fr-main-stylesheet', CHILD_URL . '/custom.css', false, filemtime( get_stylesheet_directory() . '/custom.css' ) );
	wp_enqueue_style( 'fr-headlines-stylesheet', CHILD_URL . '/headlines.css', false, filemtime( get_stylesheet_directory() . '/headlines.css' ) );
	wp_enqueue_style( 'fr-adverts-stylesheet', CHILD_URL . '/adverts.css', false, filemtime( get_stylesheet_directory() . '/adverts.css' ) );	
	
	if ( get_the_title() == "Marathon Pacing Calculator" || get_the_title() == "Explore Marathon Research Data" )	{
		wp_enqueue_style( 'fr-pacing-calculator-stylesheet', CHILD_URL . '/marathon-pacing-calculator/marathon-pacing-calculator.css', false, filemtime( get_stylesheet_directory() . '/marathon-pacing-calculator/marathon-pacing-calculator.css' ) );	
	}	
}

/** Dequeue style.css and enqueue it again with version number (for cache busting) */
/** For details of more reliable htaccess-based cache busting see https://wordimpress.com/wordpress-css-and-js-cache-busting/ */
wp_dequeue_style( 'lifestyle-pro-theme-css' );
if ( ! is_admin() )
{
  wp_enqueue_style( 'lifestyle-pro-theme', CHILD_URL . '/style.css', false, filemtime( get_stylesheet_directory() . '/style.css' ) );
}

wp_enqueue_style( 'fr-font-awesome', CHILD_URL . '/font-awesome-4.4.0/css/font-awesome.min.css', false, filemtime( get_stylesheet_directory() . '/font-awesome-4.4.0/css/font-awesome.min.css') );

$this_page_title = get_the_title();
if ($this_page_title != "Contact Us") {
add_filter( 'wpcf7_load_js', '__return_false' );
add_filter( 'wpcf7_load_css', '__return_false' );
}

//An open-sans style (which pulls a google font) is added by default by WP for admin pages
 if ( ! (is_admin() or is_user_logged_in() or in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) ) )) {
wp_deregister_style('open-sans');
} 

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

//* Remove the site title
remove_action( 'genesis_site_title', 'genesis_seo_site_title' );

//* Remove the site description
remove_action( 'genesis_site_description', 'genesis_seo_site_description' );

//* Display a custom favicon
add_filter( 'genesis_pre_load_favicon', 'sp_favicon_filter' );
function sp_favicon_filter( $favicon_url ) {
	return 'http://www.flyingrunner.co.uk/favicon.ico';}


/**Remove post meta info (Filed under: [category]   Tagged with: [tags]) from end of post **/
remove_action( 'genesis_entry_footer', 'genesis_post_meta' );

// Remove Post Info, Post Meta from Archive Pages
function themeprefix_remove_post_meta() {
	if (is_archive()) {
		remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
		remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
		}
}

/** genesis_before_content_sidebar_wrap is immediately after main menu */
//add_action( 'genesis_before_content_sidebar_wrap', 'custom_welcome_text' );


//Force IE to render using IE9 rendering engine, to fix crashes in IE10
add_action( 'wp_head', 'wc_add_IE_10_meta_tag' , 2 );
function wc_add_IE_10_meta_tag() {
  echo '<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE9" >' . "\n";
}

add_action( 'genesis_before_content_sidebar_wrap', 'add_top_banner' );
//add_action( 'genesis_before_footer', 'add_bottom_banner' );
//add_action( 'genesis_after_content_sidebar_wrap', 'add_bottom_banner' );

function add_top_banner() {
	//If we don't want to show a banner, we must include this so we put a margin between 
	//bottom of main menu and top of content 
	echo "<div class=\"banner-top-empty\"></div>";
	return;
	
	//if (is_shop() or is_product() or is_product_category() or is_cart() or is_checkout())
	//{
		$banner = "<div class=\"banner-top\">";
			$banner .= "<div class=\"banner-top-small\">";
				$banner .= "Free shipping on everything until Monday!";
				//$banner .= "<div><a href=\"" . esc_url( home_url( '/product-category/medal-displays' )) . "\">10% off our medal displays until Sunday!</div><div>Use coupon <span style=\"color:#CCC\">medal-madness-october</span> at Checkout</div></a>";
			$banner .= "</div>";
			$banner .= "<div class=\"banner-top-medium\">";
				$banner .= "Free shipping on everything until Monday!";
				//$banner .= "<a href=\"" . esc_url( home_url( '/product-category/medal-displays' )) . "\">10% off our race medal displays until Sunday! Use coupon <span style=\"color:#CCC\">medal-madness-october</span> at Checkout</a>";
			$banner .= "</div>";
		$banner .= "</div>";
		echo $banner;
	//}
	//else
	//{
		//banner-empty duplicates 32px margin on banner-top between nav and content that we need
	//	echo "<div class=\"banner-top-empty\"></div>";
	//}
}
function add_bottom_banner() {
	if (is_checkout()){
		echo "<div class=\"banner-bottom\">Can we help you? Just drop us an email at contact@flyingrunner.co.uk</div>";
	}
}
 
