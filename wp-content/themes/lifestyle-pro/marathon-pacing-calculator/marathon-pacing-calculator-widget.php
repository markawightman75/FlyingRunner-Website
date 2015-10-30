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
class Marathon_Pacing_Calculator_Widget extends WP_Widget {

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
			'classname'   => 'marathon-pacing-calculator',
			'description' => __( 'The marathon pacing calculator widget', 'custom' ),
		);

		$control_ops = array(
			'id_base' => 'marathon-pacing-calculator',
			'width'   => 505,
			'height'  => 350,
		);

		//remove_filter( 'genesis_attr_entry', 'genesis_attributes_entry' );
		//add_filter( 'genesis_attr_entry', 'custom_add_entryclasses_attr' );
		
		parent::__construct( 'marathon-pacing-calculator', __( 'Flying Runner - Marathon Pacing Calculator', 'custom' ), $widget_ops, $control_ops );

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
		
		<div>
		<div class="" style="">
			<!-- See http://themefoundation.com/two-column-css-layout/ to implement this properly -->
			<div class="" style="width: 20%; float:left; height: 205px;">
			<!--
				<ul class="tabs vertical" data-tab>
				  <li class="tab-title active"><a href="#panel11">Tab 1</a></li>
				  <li class="tab-title"><a href="#panel21">Tab 2</a></li>
				  <li class="tab-title"><a href="#panel31">Tab 3</a></li>
				  <li class="tab-title"><a href="#panel41">Tab 4</a></li>
				</ul>
				-->
				<div>
				<input type="submit" name="" class="tab" id="tab-even" value="Even pacing">
				</div>
				<div>
				<input type="submit" name="" class="tab" id="tab-negative" value="Negative pacing">
				</div>
				<div>
				<input type="submit" name="" class="tab" id="tab-positive" value="Positive pacing">
				</div>
				<div style="display: none;">
				<input type="submit" name="" class="tab" id="tab-dans-pacing" value="Dan Gordon pacing">
				</div>
				<div>
				<input type="submit" name="" class="tab" id="tab-from-selection" value="From research data">
				</div>

			</div>
			<div class="tabs-content" style="background-color: #fff; height: 300px; width: 70%; float: left;">
				  <div class="tab-content" id="tab-content-even" style="height: 205px;">
					<p style="font-weight: bold; margin-bottom: 0.5em;">Calculate pacing that is even across the whole race.</p>
					<div id="pacing-example-sparkline-even" style="margin-bottom: 1em;"></div>
					<span>My target time:</span><input type="text" class="time-h" id="target-time-even-h" value="4"><span>h</span>
					<input type="text" class="time-m" id="target-time-even-m" value="00"><span>m</span>
					<input type="text" class="time-h" id="target-time-even-s" value="00"><span>s</span>
					<input type="submit" name="" class="calculate-button" id="calculate-even-splits" value="Calculate" style="display: block;">
				  </div>
				  <div class="tab-content" id="tab-content-negative" style="height: 205px;">
					<p style="font-weight: bold; margin-bottom: 0.5em;">Calculate pacing that is faster in the second half than first half</p>
					<div id="pacing-example-sparkline-negative" style="margin-bottom: 1em;"></div>
					<span>My target time:</span><input type="text" class="time-h" id="target-time-negative-h" value="4"><span>h</span>
					<input type="text" class="time-m" id="target-time-negative-m" value="00"><span>m</span>
					<input type="text" class="time-s" id="target-time-negative-s" value="00"><span>s</span>
					<div style="margin-top: 1em;">
					<span>Run second half this much faster than first half:</span><input type="text" class="time-m" id="second-half-negative-m" value="1"><span>m</span>
					<input type="text" class="time-s" id="second-half-negative-s" value="00"><span>s</span>
					</div>
					<input type="submit" name="" class="calculate-button" id="calculate-negative-splits" value="Calculate" style="display: block;">
					
				  </div>

 				  <div class="tab-content" id="tab-content-positive" style="height: 205px;">
					<p style="font-weight: bold; margin-bottom: 0.5em;">Calculate pacing that is slower in the second half than first half</p>
					<div id="pacing-example-sparkline-positive" style="margin-bottom: 1em;"></div>
					<span>My target time:</span><input type="text" class="time-h" id="target-time-positive-h" value="4"><span>h</span>
					<input type="text" class="time-m" id="target-time-positive-m" value="00"><span>m</span>
					<input type="text" class="time-s" id="target-time-positive-s" value="00"><span>s</span>
					<div style="margin-top: 1em;">
					<span>Run second half this much slower than first half:</span><input type="text" class="time-m" id="second-half-positive-m" value="1"><span>m</span>
					<input type="text" class="time-s" id="second-half-positive-s" value="00"><span>s</span>
					</div>
					<input type="submit" name="" class="calculate-button" id="calculate-positive-splits" value="Calculate" style="display: block;">
				  </div>

				  <div class="tab-content" id="tab-content-dans-pacing" style="height: 205px;">
					<p style="font-weight: bold; margin-bottom: 0.5em;">Calculate pacing according to the profile proposed by Dan Gordon's research.</p>
					<div id="pacing-example-sparkline-dan" style="margin-bottom: 1em;"></div>
					<p>(Link to article, seminar video etc. here)</p>
					<span>My target time:</span><input type="text" id="target-time-dan-h" style="width: 50px; margin-left: 0.5em; padding: 4px; height: 2.5em;" value="4"><span>h</span>
					<input type="text" id="target-time-dan-m" style="width: 50px; margin-left: 0.5em; padding: 4px; height: 2.5em;" value="00"><span>m</span>
					<input type="text" id="target-time-dan-s" style="width: 50px; margin-left: 0.5em; padding: 4px; height: 2.5em;" value="00"><span>s</span>
					<p>Not implemented yet</p>
					<!--<input type="submit" name="" class="calculate-button" id="calculate-dan-splits" value="Calculate" style="display: block;">-->
				  </div>

				  <div class="tab-content" id="tab-content-from-selection" style="height: 205px;">
					<p style="font-weight: bold; margin-bottom: 0.5em;">Calculate pacing from the profile you've selected, adjusted to your target time.</p>
					<div id="selection-sparkline" style="margin-bottom: 20px"></div>
					<span>My target time:</span><input type="text" id="target-time-from-selection-h" style="width: 50px; margin-left: 0.5em; padding: 4px; height: 2.5em;" value="4"><span>h</span>
					<input type="text" id="target-time-from-selection-m" style="width: 50px; margin-left: 0.5em; padding: 4px; height: 2.5em;" value="00"><span>m</span>
					<input type="text" id="target-time-from-selection-s" style="width: 50px; margin-left: 0.5em; padding: 4px; height: 2.5em;" value="00"><span>s</span>
					
					<input type="submit" name="" class="calculate-button" id="calculate-from-selection" value="Calculate" style="display: block;">
				  </div>

				  <div class="tab-content" id="tab-content-from-selection-none-provided" style="height: 205px;">
					<p style="font-weight: bold;">Calculate pacing from our research data</p>
					<p>You can build pacing based on the pacing of a particular runner in our research data from London Marathon 2015, or an average of a set of runners.</p>
					<p>You can find out more <a href="explore-marathon-pacings" target="_blank">here</a></p>
				  </div>
				</div>
			</div>
		</div>
		 
		
		
		<div class="wrap" style="margin-top: 10px;">
		
			
		</div>
		<table id="pacing-table" style="width: auto; margin-top: 20px">
		  <tr>
			<th style="width: 150px;">Distance</th>
			<th style="width: 60px;">Time</th>
			<th style="width: 140px;"></th> <!--Buttons-->
			<th style="width: 60px;"></th>
			<th style="width: 150px;">Distance</th>
			<th style="width: 60px;">Time</th>
			<th style="width: 140px;"></th> <!--Buttons-->
		  </tr>
		  <tr>
			<td>Mile 1</td>
			<td id="mile-1-split"></td>
			<td>
				<input type="submit" name="increment" class="increment-time" id="increment-15-mile-1" value="-15">
				<input type="submit" name="increment" class="increment-time" id="increment-01-mile-1" value="-1">
				<input type="submit" name="increment" class="increment-time" id="increment+01-mile-1" value="+1">
				<input type="submit" name="increment" class="increment-time" id="increment+15-mile-1" value="+15">
			</td>
			<td class="pacing-table-middle-margin"></td>
			<td>Mile 14</td>
			<td id="mile-14-split"></td>
			<td>
				<input type="submit" name="increment" class="increment-time" id="increment-15-mile-14" value="-15">
				<input type="submit" name="increment" class="increment-time" id="increment-01-mile-14" value="-1">
				<input type="submit" name="increment" class="increment-time" id="increment+01-mile-14" value="+1">
				<input type="submit" name="increment" class="increment-time" id="increment+15-mile-14" value="+15">
			</td>
		  </tr>
		  <tr>
			<td>Mile 2</td>
			<td id="mile-2-split"></td>
			<td>
				<input type="submit" name="increment" class="increment-time" id="increment-15-mile-2" value="-15">
				<input type="submit" name="increment" class="increment-time" id="increment-01-mile-2" value="-1">
				<input type="submit" name="increment" class="increment-time" id="increment+01-mile-2" value="+1">
				<input type="submit" name="increment" class="increment-time" id="increment+15-mile-2" value="+15">
			</td>
			<td class="pacing-table-middle-margin"></td>
			<td>Mile 15</td>
			<td id="mile-15-split"></td>
			<td>
				<input type="submit" name="increment" class="increment-time" id="increment-15-mile-15" value="-15">
				<input type="submit" name="increment" class="increment-time" id="increment-01-mile-15" value="-1">
				<input type="submit" name="increment" class="increment-time" id="increment+01-mile-15" value="+1">
				<input type="submit" name="increment" class="increment-time" id="increment+15-mile-15" value="+15">
			</td>
		  </tr>
		  <tr>
			<td>Mile 3</td>
			<td id="mile-3-split"></td>
			<td>
				<input type="submit" name="increment" class="increment-time" id="increment-15-mile-3" value="-15">
				<input type="submit" name="increment" class="increment-time" id="increment-01-mile-3" value="-1">
				<input type="submit" name="increment" class="increment-time" id="increment+01-mile-3" value="+1">
				<input type="submit" name="increment" class="increment-time" id="increment+15-mile-3" value="+15">
			</td>
			<td></td>
			<td>Mile 16</td>
			<td id="mile-16-split"></td>
			<td>
				<input type="submit" name="increment" class="increment-time" id="increment-15-mile-16" value="-15">
				<input type="submit" name="increment" class="increment-time" id="increment-01-mile-16" value="-1">
				<input type="submit" name="increment" class="increment-time" id="increment+01-mile-16" value="+1">
				<input type="submit" name="increment" class="increment-time" id="increment+15-mile-16" value="+15">
			</td>
		  </tr>
		  <tr>
			<td>Mile 4</td>
			<td id="mile-4-split"></td>
			<td>
				<input type="submit" name="increment" class="increment-time" id="increment-15-mile-4" value="-15">
				<input type="submit" name="increment" class="increment-time" id="increment-01-mile-4" value="-1">
				<input type="submit" name="increment" class="increment-time" id="increment+01-mile-4" value="+1">
				<input type="submit" name="increment" class="increment-time" id="increment+15-mile-4" value="+15">
			</td>
			<td></td>
			<td>Mile 17</td>
			<td id="mile-17-split"></td>
			<td>
				<input type="submit" name="increment" class="increment-time" id="increment-15-mile-17" value="-15">
				<input type="submit" name="increment" class="increment-time" id="increment-01-mile-17" value="-1">
				<input type="submit" name="increment" class="increment-time" id="increment+01-mile-17" value="+1">
				<input type="submit" name="increment" class="increment-time" id="increment+15-mile-17" value="+15">
			</td>
		  </tr>
		  <tr>
			<td>Mile 5</td>
			<td id="mile-5-split"></td>
			<td>
				<input type="submit" name="increment" class="increment-time" id="increment-15-mile-5" value="-15">
				<input type="submit" name="increment" class="increment-time" id="increment-01-mile-5" value="-1">
				<input type="submit" name="increment" class="increment-time" id="increment+01-mile-5" value="+1">
				<input type="submit" name="increment" class="increment-time" id="increment+15-mile-5" value="+15">
			</td>
			<td></td>
			<td>Mile 18</td>
			<td id="mile-18-split"></td>
			<td>
				<input type="submit" name="increment" class="increment-time" id="increment-15-mile-18" value="-15">
				<input type="submit" name="increment" class="increment-time" id="increment-01-mile-18" value="-1">
				<input type="submit" name="increment" class="increment-time" id="increment+01-mile-18" value="+1">
				<input type="submit" name="increment" class="increment-time" id="increment+15-mile-18" value="+15">
			</td>
		  </tr>
		  <tr>
			<td>Mile 6</td>
			<td id="mile-6-split"></td>
			<td>
				<input type="submit" name="increment" class="increment-time" id="increment-15-mile-6" value="-15">
				<input type="submit" name="increment" class="increment-time" id="increment-01-mile-6" value="-1">
				<input type="submit" name="increment" class="increment-time" id="increment+01-mile-6" value="+1">
				<input type="submit" name="increment" class="increment-time" id="increment+15-mile-6" value="+15">
			</td>
			<td></td>
			<td>Mile 19</td>
			<td id="mile-19-split"></td>
			<td>
				<input type="submit" name="increment" class="increment-time" id="increment-15-mile-19" value="-15">
				<input type="submit" name="increment" class="increment-time" id="increment-01-mile-19" value="-1">
				<input type="submit" name="increment" class="increment-time" id="increment+01-mile-19" value="+1">
				<input type="submit" name="increment" class="increment-time" id="increment+15-mile-19" value="+15">
			</td>
		  </tr>
		  <tr>
			<td>Mile 7</td>
			<td id="mile-7-split"></td>
			<td>
				<input type="submit" name="increment" class="increment-time" id="increment-15-mile-7" value="-15">
				<input type="submit" name="increment" class="increment-time" id="increment-01-mile-7" value="-1">
				<input type="submit" name="increment" class="increment-time" id="increment+01-mile-7" value="+1">
				<input type="submit" name="increment" class="increment-time" id="increment+15-mile-7" value="+15">
			</td>
			<td></td>
			<td>Mile 20</td>
			<td id="mile-20-split"></td>
			<td>
				<input type="submit" name="increment" class="increment-time" id="increment-15-mile-20" value="-15">
				<input type="submit" name="increment" class="increment-time" id="increment-01-mile-20" value="-1">
				<input type="submit" name="increment" class="increment-time" id="increment+01-mile-20" value="+1">
				<input type="submit" name="increment" class="increment-time" id="increment+15-mile-20" value="+15">
			</td>
		  </tr>
		  <tr>
			<td>Mile 8</td>
			<td id="mile-8-split"></td>
			<td>
				<input type="submit" name="increment" class="increment-time" id="increment-15-mile-8" value="-15">
				<input type="submit" name="increment" class="increment-time" id="increment-01-mile-8" value="-1">
				<input type="submit" name="increment" class="increment-time" id="increment+01-mile-8" value="+1">
				<input type="submit" name="increment" class="increment-time" id="increment+15-mile-8" value="+15">
			</td>
			<td></td>
			<td>Mile 21</td>
			<td id="mile-21-split"></td>
			<td>
				<input type="submit" name="increment" class="increment-time" id="increment-15-mile-21" value="-15">
				<input type="submit" name="increment" class="increment-time" id="increment-01-mile-21" value="-1">
				<input type="submit" name="increment" class="increment-time" id="increment+01-mile-21" value="+1">
				<input type="submit" name="increment" class="increment-time" id="increment+15-mile-21" value="+15">
			</td>
		  </tr>
		  <tr>
			<td>Mile 9</td>
			<td id="mile-9-split"></td>
			<td>
				<input type="submit" name="increment" class="increment-time" id="increment-15-mile-9" value="-15">
				<input type="submit" name="increment" class="increment-time" id="increment-01-mile-9" value="-1">
				<input type="submit" name="increment" class="increment-time" id="increment+01-mile-9" value="+1">
				<input type="submit" name="increment" class="increment-time" id="increment+15-mile-9" value="+15">
			</td>
			<td></td>
			<td>Mile 22</td>
			<td id="mile-22-split"></td>
			<td>
				<input type="submit" name="increment" class="increment-time" id="increment-15-mile-22" value="-15">
				<input type="submit" name="increment" class="increment-time" id="increment-01-mile-22" value="-1">
				<input type="submit" name="increment" class="increment-time" id="increment+01-mile-22" value="+1">
				<input type="submit" name="increment" class="increment-time" id="increment+15-mile-22" value="+15">
			</td>
		  </tr>
		  <tr>
			<td>Mile 10</td>
			<td id="mile-10-split"></td>
			<td>
				<input type="submit" name="increment" class="increment-time" id="increment-15-mile-10" value="-15">
				<input type="submit" name="increment" class="increment-time" id="increment-01-mile-10" value="-1">
				<input type="submit" name="increment" class="increment-time" id="increment+01-mile-10" value="+1">
				<input type="submit" name="increment" class="increment-time" id="increment+15-mile-10" value="+15">
			</td>
			<td></td>
			<td>Mile 23</td>
			<td id="mile-23-split"></td>
			<td>
				<input type="submit" name="increment" class="increment-time" id="increment-15-mile-23" value="-15">
				<input type="submit" name="increment" class="increment-time" id="increment-01-mile-23" value="-1">
				<input type="submit" name="increment" class="increment-time" id="increment+01-mile-23" value="+1">
				<input type="submit" name="increment" class="increment-time" id="increment+15-mile-23" value="+15">
			</td>
		  </tr>
		  <tr>
			<td>Mile 11</td>
			<td id="mile-11-split"></td>
			<td>
				<input type="submit" name="increment" class="increment-time" id="increment-15-mile-11" value="-15">
				<input type="submit" name="increment" class="increment-time" id="increment-01-mile-11" value="-1">
				<input type="submit" name="increment" class="increment-time" id="increment+01-mile-11" value="+1">
				<input type="submit" name="increment" class="increment-time" id="increment+15-mile-11" value="+15">
			</td>
			<td></td>
			<td>Mile 24</td>
			<td id="mile-24-split"></td>
			<td>
				<input type="submit" name="increment" class="increment-time" id="increment-15-mile-24" value="-15">
				<input type="submit" name="increment" class="increment-time" id="increment-01-mile-24" value="-1">
				<input type="submit" name="increment" class="increment-time" id="increment+01-mile-24" value="+1">
				<input type="submit" name="increment" class="increment-time" id="increment+15-mile-24" value="+15">
			</td>
		  </tr>
		  <tr>
			<td>Mile 12</td>
			<td id="mile-12-split"></td>
			<td>
				<input type="submit" name="increment" class="increment-time" id="increment-15-mile-12" value="-15">
				<input type="submit" name="increment" class="increment-time" id="increment-01-mile-12" value="-1">
				<input type="submit" name="increment" class="increment-time" id="increment+01-mile-12" value="+1">
				<input type="submit" name="increment" class="increment-time" id="increment+15-mile-12" value="+15">
			</td>
			<td></td>
			<td>Mile 25</td>
			<td id="mile-25-split"></td>
			<td>
				<input type="submit" name="increment" class="increment-time" id="increment-15-mile-25" value="-15">
				<input type="submit" name="increment" class="increment-time" id="increment-01-mile-25" value="-1">
				<input type="submit" name="increment" class="increment-time" id="increment+01-mile-25" value="+1">
				<input type="submit" name="increment" class="increment-time" id="increment+15-mile-25" value="+15">
			</td>
		  </tr>
		  <tr>
			<td>Mile 13</td>
			<td id="mile-13-split"></td>
			<td>
				<input type="submit" name="increment" class="increment-time" id="increment-15-mile-13" value="-15">
				<input type="submit" name="increment" class="increment-time" id="increment-01-mile-13" value="-1">
				<input type="submit" name="increment" class="increment-time" id="increment+01-mile-13" value="+1">
				<input type="submit" name="increment" class="increment-time" id="increment+15-mile-13" value="+15">
			</td>
			<td></td>
			<td>Mile 26</td>
			<td id="mile-26-split"></td>
			<td>
				<input type="submit" name="increment" class="increment-time" id="increment-15-mile-26" value="-15">
				<input type="submit" name="increment" class="increment-time" id="increment-01-mile-26" value="-1">
				<input type="submit" name="increment" class="increment-time" id="increment+01-mile-26" value="+1">
				<input type="submit" name="increment" class="increment-time" id="increment+15-mile-26" value="+15">
			</td>
		  </tr>

		  <tr>
			<td style="font-weight: bold;">Halfway</td>
			<td id="halfway-hhmmss" style="font-weight: bold;"></td>
			<td></td>
			<td></td>
			<td style="font-weight: bold;">Finish</td>
			<td id="finish-hhmmss" style="font-weight: bold"></td>
		  </tr>
		 </table>
		 
		 <input type="submit" name="" class="" id="download-pacing-band" value="Download Pacing Band" style="display: block;">
		 
		 <table id="pacing-table-seconds-hidden" style="visibility: collapse;">
		  <tr>
			  <td>Mile 1 (s)<td>
			  <td id="mile-1-split-secs">300</td>
		  </tr>
		  <tr>
			  <td>Mile 2 (s)<td>
			  <td id="mile-2-split-secs">300</td>
		  </tr>
		  		  <tr>
			  <td>Mile 3 (s)<td>
			  <td id="mile-3-split-secs">300</td>
		  </tr>
		  <tr>
			  <td>Mile 4 (s)<td>
			  <td id="mile-4-split-secs">300</td>
		  </tr>
		  <tr>
			  <td>Mile 5 (s)<td>
			  <td id="mile-5-split-secs">300</td>
		  </tr>
		  <tr>
			  <td>Mile 6 (s)<td>
			  <td id="mile-6-split-secs">300</td>
		  </tr>
		  <tr>
			  <td>Mile 7 (s)<td>
			  <td id="mile-7-split-secs">300</td>
		  </tr>
		  <tr>
			  <td>Mile 8 (s)<td>
			  <td id="mile-8-split-secs">300</td>
		  </tr>
		  <tr>
			  <td>Mile 9 (s)<td>
			  <td id="mile-9-split-secs">300</td>
		  </tr>
		  <tr>
			  <td>Mile 10 (s)<td>
			  <td id="mile-10-split-secs">300</td>
		  </tr>
		  <tr>
			  <td>Mile 11 (s)<td>
			  <td id="mile-11-split-secs">300</td>
		  </tr>
		  <tr>
			  <td>Mile 12 (s)<td>
			  <td id="mile-12-split-secs">300</td>
		  </tr>
		  <tr>
			  <td>Mile 13 (s)<td>
			  <td id="mile-13-split-secs">300</td>
		  </tr> 
		  <tr>
			  <td>Mile 14 (s)<td>
			  <td id="mile-14-split-secs">300</td>
		  </tr>
		  <tr>
			  <td>Mile 15 (s)<td>
			  <td id="mile-15-split-secs">300</td>
		  </tr>
		  		  <tr>
			  <td>Mile 16 (s)<td>
			  <td id="mile-16-split-secs">300</td>
		  </tr>
		  <tr>
			  <td>Mile 17 (s)<td>
			  <td id="mile-17-split-secs">300</td>
		  </tr>
		  <tr>
			  <td>Mile 18 (s)<td>
			  <td id="mile-18-split-secs">300</td>
		  </tr>
		  <tr>
			  <td>Mile 19 (s)<td>
			  <td id="mile-19-split-secs">300</td>
		  </tr>
		  <tr>
			  <td>Mile 20 (s)<td>
			  <td id="mile-20-split-secs">300</td>
		  </tr>
		  <tr>
			  <td>Mile 21 (s)<td>
			  <td id="mile-21-split-secs">300</td>
		  </tr>
		  <tr>
			  <td>Mile 22 (s)<td>
			  <td id="mile-22-split-secs">300</td>
		  </tr>
		  <tr>
			  <td>Mile 23 (s)<td>
			  <td id="mile-23-split-secs">300</td>
		  </tr>
		  <tr>
			  <td>Mile 24 (s)<td>
			  <td id="mile-24-split-secs">300</td>
		  </tr>
		  <tr>
			  <td>Mile 25 (s)<td>
			  <td id="mile-25-split-secs">300</td>
		  </tr>
		  <tr>
			  <td>Mile 26 (s)<td>
			  <td id="mile-26-split-secs">300</td>
		  </tr> 
		  <tr>
			  <td>Halfways (s)<td>
			  <td id="halfway-secs">7800</td>
		  </tr>
		  <tr>
			  <td>Finish (s)<td>
			  <td id="finish-secs">7800</td>
		  </tr>
		</table>

		<!--Hidden storage for any pacing values passed in through query string-->
		<div id="splits-passed-in-hidden" style="visibility: collapse;">
			<table style="width:100%">
			  <tr>
				<td id="5k-passed-in-split-s"></td>
				<td id="10k-passed-in-split-s"></td>
				<td id="15k-passed-in-split-s"></td>
				<td id="20k-passed-in-split-s"></td>
				<td id="25k-passed-in-split-s"></td>
				<td id="30k-passed-in-split-s"></td>
				<td id="35k-passed-in-split-s"></td>
				<td id="40k-passed-in-split-s"></td>				
				<td id="passed-in-finish-s"></td>	
			  </tr>
			</table> 
		</div>

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
