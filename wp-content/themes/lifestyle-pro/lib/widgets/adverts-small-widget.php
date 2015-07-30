<?php
/**
 * Genesis Framework.
 *
 * WARNING: This file is part of the core Genesis Framework. DO NOT edit this file under any circumstances.
 * Please do all modifications in the form of a child theme.
 *
 * @package Genesis\Widgets
 * @author  StudioPress
 * @license GPL-2.0+
 * @link    http://my.studiopress.com/themes/genesis/
 */

/**
 * Genesis Featured Post widget class.
 *
 * @since 0.1.8
 *
 * @package Genesis\Widgets
 */
class Adverts_Small_Widget extends WP_Widget {

	/**
	 * Holds widget settings defaults, populated in constructor.
	 *
	 * @var array
	 */
	protected $defaults;

	/**
	 * Constructor. Set the default widget options and create widget.
	 *
	 * @since 0.1.8
	 */
	function __construct() {

		$this->defaults = array(
			'title'                   => '',
			'headline_1_id'           => '',
			'headline_2_id'           => '',
			'headline_3_id'           => '',
			'headline_4_id'           => '',
			'headline_5_id'           => '',
			'headline_6_id'           => ''
		);

		$widget_ops = array(
			'classname'   => 'sub-featured-content subfeaturedposts',
			'description' => __( 'Displays small adverts with thumbnails', 'custom' ),
		);

		$control_ops = array(
			'id_base' => 'adverts-small-posts',
			'width'   => 505,
			'height'  => 350,
		);

		//remove_filter( 'genesis_attr_entry', 'genesis_attributes_entry' );
		//add_filter( 'genesis_attr_entry', 'custom_add_entryclasses_attr' );
		
		parent::__construct( 'adverts-small-posts', __( 'Flying Runner - Small Adverts', 'custom' ), $widget_ops, $control_ops );

	}
	
	/**
	 * Echo the widget content.
	 *
	 * @since 0.1.8
	 *
	 * @param array $args Display arguments including before_title, after_title, before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget
	 */
	function widget( $args, $instance ) {

		global $wp_query, $_genesis_displayed_ids;

		extract( $args );

		//* Merge with defaults
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		echo $before_widget;

		//* Display the title if set
		if ( ! empty( $instance['title'] ) )
			echo $before_title . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $after_title;
		
		$advert_title = array();
		$advert_text = array();
		$advert_link = array();
		$advert_image = array();
		$advert_colour = array(); //'green', 'grey' or 'blue'
		
		$advert_title[1] = 'Stunning hand-made jewellery';
		$advert_text[1] = 'Running-themed jewellery for men &amp; women. Lovingly crafted exclusively for us in the UK.';
		$advert_link[1] = esc_url( home_url( '/product-category/jewellery' ));
		$advert_image[1] = 'http://dev.flyingrunner.co.uk.gridhosted.co.uk/wp-content/uploads/2015/04/FriendshipBracelet_LLR_LoveToRun_650x499px-300x230.jpg';
		$advert_colour[1] = 'grey';
		
		$advert_title[2] = 'Race medal displays';
		$advert_text[2] = 'Get your hard-earned medals out of the drawer and give them pride of place  with our beautiful displays.';
		$advert_link[2] = esc_url( home_url( '/product-category/medal-displays' ));
		$advert_image[2] = 'http://127.0.0.1:82/wordpress/wp-content/themes/lifestyle-pro/test_pages/medal_display.png';
		$advert_colour[2] = 'green';
		
		$advert_title[3] = 'Personalised London Marathon Art Print';
		$advert_text[3] = 'Celebrate your achievement with this stunning art print exclusively created for us by artist Kate Molloy.';
		$advert_link[3] = esc_url( home_url( '/product-category/art-prints' ));
		$advert_image[3] = 'http://dev.flyingrunner.co.uk.gridhosted.co.uk/wp-content/uploads/2015/04/FriendshipBracelet_LLR_LoveToRun_650x499px-300x230.jpg';
		$advert_colour[3] = 'blue';
		
		for ($index = 1; $index <= 3; $index++) {
			$extra_classes = 'advert-' . $advert_colour[$index];
			$extra_classes .= ($index == 1) ? ' first':'';
			
		?>
		
		 <div class="advert-small one-third xdebug-borders <?php echo $extra_classes; ?>">
		  <div class="advert-small-image">
			<a href="<?php echo $advert_link[$index] ?>" title="<?php echo $advert_title[$index]; ?>">
			<img src="<?php echo $advert_image[$index]; ?>"/></a>		
		  </div>
		  <div class="advert-small-content"> 
			  <div class="advert-small-header">
				  <h2><a href="<?php echo $advert_link[$index]; ?>" title="<?php echo $advert_title[$index]; ?>"><?php echo $advert_title[$index]; ?></a></h2>
			  </div>
			  <div class="advert-small-text">
				  <p><a href="<?php echo $advert_link[$index]; ?>" title="<?php echo $advert_title[$index]; ?>">
				  <?php echo $advert_text[$index]; ?></a>
				  </p>
					<div class="advert-small-arrow-button">
						<a href="<?php echo $advert_link[$index]; ?>" title="<?php echo $advert_title[$index]; ?>"><i class="fa fa-arrow-circle-right"></i></a>
						
					</div>
			  </div>
		  </div>
		</div>  
		
		<?php
		} //end of for loop
		echo $after_widget;

	}

	/**
	 * Update a particular instance.
	 *
	 * This function should check that $new_instance is set correctly.
	 * The newly calculated value of $instance should be returned.
	 * If "false" is returned, the instance won't be saved/updated.
	 *
	 * @since 0.1.8
	 *
	 * @param array $new_instance New settings for this instance as input by the user via form()
	 * @param array $old_instance Old settings for this instance
	 * @return array Settings to save or bool false to cancel saving
	 */
	function update( $new_instance, $old_instance ) {

		$new_instance['title']     = strip_tags( $new_instance['title'] );
		$new_instance['more_text'] = strip_tags( $new_instance['more_text'] );
		$new_instance['post_info'] = wp_kses_post( $new_instance['post_info'] );
		return $new_instance;

	}

	/**
	 * Echo the settings update form.
	 *
	 * @since 0.1.8
	 *
	 * @param array $instance Current settings
	 */
	function form( $instance ) {

		//* Merge with defaults
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'genesis' ); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat" />
		</p>

		<p>Advert text, images and links are set in lifestyle-pro\lib\widgets\adverts-small-widget.php
		</p>

		<?php

	}

}
