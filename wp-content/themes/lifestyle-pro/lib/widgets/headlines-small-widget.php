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
class Headlines_Small_Widget extends WP_Widget {

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
			'description' => __( 'Displays small headlines with thumbnails', 'custom' ),
		);

		$control_ops = array(
			'id_base' => 'headlines-small-posts',
			'width'   => 505,
			'height'  => 350,
		);

		//remove_filter( 'genesis_attr_entry', 'genesis_attributes_entry' );
		//add_filter( 'genesis_attr_entry', 'custom_add_entryclasses_attr' );
		
		parent::__construct( 'headlines-small-posts', __( 'Flying Runner - Small Headlines', 'custom' ), $widget_ops, $control_ops );

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
		
		$post_ids = array();
		
		if (! empty ($instance['headline_1_id'])) $post_ids[] = $instance['headline_1_id'];
		if (! empty ($instance['headline_2_id'])) $post_ids[] = $instance['headline_2_id'];
		if (! empty ($instance['headline_3_id'])) $post_ids[] = $instance['headline_3_id'];
		if (! empty ($instance['headline_4_id'])) $post_ids[] = $instance['headline_4_id'];
		if (! empty ($instance['headline_5_id'])) $post_ids[] = $instance['headline_5_id'];
		if (! empty ($instance['headline_6_id'])) $post_ids[] = $instance['headline_6_id'];

		//To make the ordering work, we would need to remove a filter that seems to have been applied,
		//apparently by the Post Types Order plugin. So the ordering here doesn't actually work at the moment.
		//remove_all_filters('posts_orderby');
		$query_args = array(
			'post__in' => $post_ids,
			'orderby'  => 'post__in'
		);

		$wp_query = new WP_Query( $query_args );

		$headline_index = 0;
		if ( have_posts() ) : while ( have_posts() ) : the_post();
			
			$_genesis_displayed_ids[] = get_the_ID();

			// If a custom field called "short_title" is defined on the post, use that for the entry title instead of the main post title
			$short_title = get_post_meta(get_the_ID(), "short_title", true);			
			if ( ! empty ($short_title))
			{
				$title = $short_title;
				$title_clean = strip_tags($short_title);
				$title_clean = esc_attr($title_clean);	
			}						
			else
			{
				$title = get_the_title();
				$title_clean = the_title_attribute( 'echo=0' );
			}
			
			if( 0 == $headline_index || 0 == $headline_index % 3 ) {
				echo '<div class="headline-small xdebug-borders one-third first">';				
			}
			else {
				echo '<div class="headline-small xdebug-borders one-third">';
			}
				/** Image */
				echo '<div class="headline-small-image">';
					//Get the url of the featured image
					$thumb_id = get_post_thumbnail_id();
					$thumb_url_array = wp_get_attachment_image_src($thumb_id, 'thumbnail-size', true);
					$thumb_url = $thumb_url_array[0];
					
					if (! empty ($thumb_url)) {						
						$imagetag = sprintf( '<img src="%s"/>', $thumb_url);
						printf( '<a href="%s" title="%s">%s</a>', get_permalink(), the_title_attribute( 'echo=0' ), $imagetag );
					}
				echo '</div>';

				/** Content */
				echo '<div class="headline-small-content">';
					/** Header */
					echo '<div class="headline-small-header">';
						printf( '<h2><a href="%s" title="%s">%s</a></h2>', get_permalink(), $title_clean, $title);								
					echo '</div>';
					
					/** Text */
					echo '<div class="headline-small-text">';
						the_excerpt();
					echo '</div>';
				echo '</div>';
			echo '</div>';			

			$headline_index = $headline_index +1;								
		endwhile; endif;

		//* Restore original query
		wp_reset_query();
		
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

		<p>
			<label for="<?php echo $this->get_field_id( 'headline_1_id' ); ?>"><?php _e( 'Headline 1 post ID', 'genesis' ); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'headline_1_id' ); ?>" name="<?php echo $this->get_field_name( 'headline_1_id' ); ?>" value="<?php echo esc_attr( $instance['headline_1_id'] ); ?>" size="5" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'headline_2_id' ); ?>"><?php _e( 'Headline 2 post ID', 'genesis' ); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'headline_2_id' ); ?>" name="<?php echo $this->get_field_name( 'headline_2_id' ); ?>" value="<?php echo esc_attr( $instance['headline_2_id'] ); ?>" size="5" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'headline_3_id' ); ?>"><?php _e( 'Headline 3 post ID', 'genesis' ); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'headline_3_id' ); ?>" name="<?php echo $this->get_field_name( 'headline_3_id' ); ?>" value="<?php echo esc_attr( $instance['headline_3_id'] ); ?>" size="5" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'headline_4_id' ); ?>"><?php _e( 'Headline 4 post ID', 'genesis' ); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'headline_4_id' ); ?>" name="<?php echo $this->get_field_name( 'headline_4_id' ); ?>" value="<?php echo esc_attr( $instance['headline_4_id'] ); ?>" size="5" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'headline_5_id' ); ?>"><?php _e( 'Headline 5 post ID', 'genesis' ); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'headline_5_id' ); ?>" name="<?php echo $this->get_field_name( 'headline_5_id' ); ?>" value="<?php echo esc_attr( $instance['headline_5_id'] ); ?>" size="5" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'headline_6_id' ); ?>"><?php _e( 'Headline 6 post ID', 'genesis' ); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'headline_6_id' ); ?>" name="<?php echo $this->get_field_name( 'headline_6_id' ); ?>" value="<?php echo esc_attr( $instance['headline_6_id'] ); ?>" size="5" />
		</p>

		<?php

	}

}
