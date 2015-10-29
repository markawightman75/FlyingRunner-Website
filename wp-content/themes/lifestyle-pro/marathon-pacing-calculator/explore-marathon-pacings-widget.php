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
class Explore_Marathon_Pacings_Widget extends WP_Widget {

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
			'classname'   => 'explore-marathon-pacings',
			'description' => __( 'The marathon pacing exploration widget', 'custom' ),
		);

		$control_ops = array(
			'id_base' => 'explore-marathon-pacings',
			'width'   => 505,
			'height'  => 350,
		);

		//remove_filter( 'genesis_attr_entry', 'genesis_attributes_entry' );
		//add_filter( 'genesis_attr_entry', 'custom_add_entryclasses_attr' );
		
		parent::__construct( 'explore-marathon-pacings', __( 'Flying Runner - Explore Marathon Pacings', 'custom' ), $widget_ops, $control_ops );

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

		<p>Explore the pacing and prediction accuracy of the 778 runners who took part in our research at London Marathon 2015.</p>
		
		<div>
		<label style="display: block; margin-bottom: 0.5em;">Show me runners who finished in around this time:</label>
		<input type="text" id="target-time-h" style="width: 2em; margin-right: 0.5em; padding: 4px; height: 2.5em; text-align: center;" class="time-input input-empty" ><span style="font-size: 0.9em;">hours</span>
		<input type="text" id="target-time-m" style="width: 3em; margin-left: 0.5em; margin-right: 0.5em; padding: 4px; height: 2.5em; text-align: center;" class="time-input" value="00"><span style="font-size: 0.9em;">m</span>
		<input type="text" id="target-time-s" style="width: 3em; margin-left: 0.5em; margin-right: 0.5em; padding: 4px; height: 2.5em; text-align: center;" class="time-input" value="00"><span style="font-size: 0.9em;">s</span>
		<p id="time-validation-error" style="margin-top: 0.5em; margin-bottom: 0px; color: red; font-weight: bold; font-size: 0.9em;"></p>
		</div>
		<div class="wrap">
			<div id="selection-criteria" class="xone-half xfirst" >
				<div >
					<h2 style="font-size: 1em; margin-top: 0px; margin-bottom: 0.5em;">Speed</h2>
					<!--<span>I want to run the marathon in (hh:mm:ss):</span> -->

					<div>
					
						<span>Runners who finished within</span> 
						<select id="ran-within-minutes-of-this-target-time" style="width: 120px; margin-left: 0.5em; margin-right: 0.5em; padding: 4px">
							<option value="2">2 minutes</option>
							<option value="4">4 minutes</option>
							<option value="6">6 minutes</option>
							<option value="8">8 minutes</option>
							<option value="10">10 minutes</option>
							<option value="15">15 minutes</option>
							<option value="20">20 minutes</option>
							<option value="30">30 minutes</option>
							<option value="40">40 minutes</option>
						</select> 		
						
					</div>
					<div>
						<span>Runners who ran within</span> 
						<select id="ran-within-minutes-of-prediction" style="width: 120px; padding: 4px">
							<option value="1000">Any minutes</option>
							<option value="2">2 minutes</option>
							<option value="4">4 minutes</option>
							<option value="6">6 minutes</option>
							<option value="8">8 minutes</option>
							<option value="10">10 minutes</option>
							<option value="15">15 minutes</option>
							<option value="20">20 minutes</option>
							<option value="30">30 minutes</option>
							<option value="40">40 minutes</option>
						</select> 		
						<span>of their predicted time</span>
					</div>
					<div style="margin-top: 0.75em;">
						<span>Runners who had previously run</span>
						<select id="previous-marathons" style="width: 150px; padding: 4px">
							<option value="Any">Any number of</option>
							<option value="0">0</option>
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="3">4</option>
							<option value="3">5</option>
							<option value="6-10">6 to 10</option>
							<option value=">10">More than 10</option>
						</select> 		
						<span>marathons</span>
					</div>
					<div style="margin-top: 0.75em;">
						<span>Runners aged</span>
						<select id="age-category" style="width: 120px; padding: 4px" >
							<option value="Any" selected>Anything</option>
							<option value="18-39">18 to 39</option>
							<option value="40-49">40 to 49</option>
							<option value="50-59">50 to 59</option>
							<option value="60+">60 and over</option>
						</select> 		
					</div>
				</div>
			</div>
			<div class="xone-half" style="margin-top: 1em;">
				<input type="submit" name="predict" class="predictor-submit" id="find-runners" value="Find runners">
			</div>		

		</div>

		<!--
		<div class="wrap">
			<h2>Pacing for a <span id="pacing-target-time">????</span> marathon</h2>
			<p>These splits are based on the splits of the <span id="number-of-runners"></span> runners included in the calculations.</p> 
			<div id="number-of-runners-icons"></div>-->
			
		<!--
		</div>
		-->
		
		<div id="runners-details-intro"></div>
		<div id="sparkline-intro" style="height: 0px; visibility: collapse;">
			<p>This is the average pacing curve of all of these runners</p>
		</div>
		
		<div>
			<table>
				<tr>
					<td style="border-top: none;"><div id="sparkline"></div></td>
					<td style="border-top: none;"><input type="submit" name="" class="build-pacing" style="visibility: collapse;" id="build-pacing-average" value="Build pacing for me based on this"></td>
				</tr>
			</table>
			
		</div>
		
		<div id="runners-details" style="margin-top: 2em;"></div>
		
		<!--Hidden storage for the average splits, to use when building pacing from it-->
		<div id="splits-average-hidden" style="display: none;">
			<table style="width:100%">
			  <tr>
				<td id="5k-mean-split-s"></td>
				<td id="10k-mean-split-s"></td>
				<td id="15k-mean-split-s"></td>
				<td id="20k-mean-split-s"></td>
				<td id="25k-mean-split-s"></td>
				<td id="30k-mean-split-s"></td>
				<td id="35k-mean-split-s"></td>
				<td id="40k-mean-split-s"></td>				
			  </tr>
			</table> 
		</div>

		<div id="debug" style="background-color: #eee; display: none;">
		</div>		
		
		<div id="runner-detail-template" style="display: none;">
			<div class="runner-detail block-group">
			  <div class="runner-profile block-group">
				  <div class="block runner-image">
					  <img style="width:57px; height:auto;" src="http://images.flyingrunner.co.uk/marathon-pacing-research/[RUNNER-IMAGE]">
				  </div>
				  <div class="block runner-info"><strong>Age:</strong> [AGE]<br><strong>Marathons:</strong> [MARATHONS]</div>
				  <div class="block runner-accuracy-and-times">
					  <div class="block runner-accuracy">
						  <div class="runner-accuracy-circle [ACCURACY-CLASS]">
							  <div class="accuracy-percent">[ACCURACY%]</div>
							  <div class="accuracy-caption">Accurate</div>
						  </div>
					  </div>
					  <div class="block runner-times"><strong>Predicted:</strong> [PREDICTED]<br><strong>Actual:</strong> [ACTUAL]</div>
				  </div>
			  </div>
				
			  <div class="runner-pacing block-group">
				<div class="runner-chart block">
					<div id="[ID-SPARKLINE]" >
					</div>
					<a id="[ID-BUILD-PACING-BUTTON]" class="build-pacing-button" title="Open the pacing calculator with this pacing"></a>
				</div>
				<div class="runner-splits block">
					<input type="submit" name="" class="view-splits-button" id="[ID-VIEW-SPLITS-BUTTON]" value="View details..." style="display: block;" >
					<table class="splits-table hidden" id="[ID-SPLITS-TABLE]" style="display: none;">
						<tr class="splits-header">
							<td>5km</td>
							<td>10km</td>
							<td>15km</td>
							<td>20km</td>
							<td>25km</td>
							<td>30km</td>
							<td>35km</td>
							<td>40km</td>
							<td></td>
						</tr>
						<tr class="splits-time">
							<td id="td-5k-split">[SPLIT-5K]</td>
							<td id="td-10k-split">[SPLIT-10K]</td>
							<td id="td-15k-split">[SPLIT-15K]</td>
							<td id="td-20k-split">[SPLIT-20K]</td>
							<td id="td-25k-split">[SPLIT-25K]</td>
							<td id="td-30k-split">[SPLIT-30K]</td>
							<td id="td-35k-split">[SPLIT-35K]</td>
							<td id="td-40k-split">[SPLIT-40K]</td>
							<td>Time</td>
						</tr>
						<tr class="splits-pace">
							<td>[PACE-5K]</td>
							<td>[PACE-10K]</td>
							<td>[PACE-15K]</td>
							<td>[PACE-20K]</td>
							<td>[PACE-25K]</td>
							<td>[PACE-30K]</td>
							<td>[PACE-35K]</td>
							<td>[PACE-40K]</td>
							<td>Mile pace</td>                    
						</tr>
					</table>
				</div>        
			  </div>
			</div>
		</div>
		<!--Hidden div that we'll inject a tag indicating screen size in via media query in css, that we can pick up in chart buliding to ensure bars are the correct width-->
		<div id="screen-size-tag" style="display: none;"></div>
		
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
		
		
		<?php

	}

}
