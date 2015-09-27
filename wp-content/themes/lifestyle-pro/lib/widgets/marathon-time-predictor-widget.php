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
class Marathon_Time_Predictor_Widget extends WP_Widget {

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
			'classname'   => 'marathon-time-predictor',
			'description' => __( 'Displays small adverts with thumbnails', 'custom' ),
		);

		$control_ops = array(
			'id_base' => 'marathon-time-predictor',
			'width'   => 505,
			'height'  => 350,
		);

		//remove_filter( 'genesis_attr_entry', 'genesis_attributes_entry' );
		//add_filter( 'genesis_attr_entry', 'custom_add_entryclasses_attr' );
		
		parent::__construct( 'marathon-time-predictor', __( 'Flying Runner - Time Predictor', 'custom' ), $widget_ops, $control_ops );

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

			
		?>
		
		
		Name: <input type="text" id="name">
		<input type="radio" id="gender-male" name="gender" value="male" checked> Male
		<br>
		<input type="radio" id="gender-female" name="gender" value="female"> Female
		Predicted time: <div id="name-data">NONE YET</div>
		<input type="submit" name="predict" class="predictor-submit" id="predictor" value="Predict">
		
				
		<?php
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
