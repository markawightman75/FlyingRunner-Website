<?php

//* Include our widgets
include_once( CHILD_DIR . '/lib/widgets/headline-large-widget.php' );
include_once( CHILD_DIR . '/lib/widgets/headlines-small-widget.php' );
include_once( CHILD_DIR . '/lib/widgets/adverts-small-widget.php' );
include_once( CHILD_DIR . '/lib/widgets/section-title-widget.php' );
include_once( CHILD_DIR . '/marathon-pacing-calculator/explore-marathon-pacings-widget.php' );
include_once( CHILD_DIR . '/marathon-pacing-calculator/explore-marathon-groups-widget.php' );
include_once( CHILD_DIR . '/marathon-pacing-calculator/marathon-pacing-calculator-widget.php' );

add_action( 'widgets_init', 'register_custom_widgets' );


// Create the custom areas (sidebars) that widgets can go in
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



// Add the relevant custom areas (sidebars) for this page
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


function register_custom_widgets() {  
  // register our custom widget..
  register_widget( 'Headline_Large_Widget' );
  register_widget( 'Headlines_Small_Widget' );
  register_widget( 'Adverts_Small_Widget' );
  register_widget( 'Section_Title_Widget' );
  register_widget( 'Explore_Marathon_Pacings_Widget' );
  register_widget( 'Explore_Marathon_Groups_Widget' );
  register_widget( 'Marathon_Pacing_Calculator_Widget' );
}

?>