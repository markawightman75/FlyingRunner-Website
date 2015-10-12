jQuery(document).ready(function(){
	jQuery('#predictor').on('click',function() {
		var target_time = jQuery('input#target-time').val();
		var age_category = jQuery('select#age-category').val();
		var previous_marathons = jQuery('select#previous-marathons').val();
		var ran_within_minutes_of_prediction = jQuery('select#ran-within-minutes-of-prediction').val();
		var ran_within_minutes_of_this_target_time = jQuery('select#ran-within-minutes-of-this-target-time').val();
		
		//alert("Age:" + age_category + " Marathons: " + previous_marathons + " Target time: " + target_time);
		//alert(ran_within_minutes_of_prediction);
		//TODO: Validate inputs
		//cumulative_splits = new Array();
		
		jQuery.ajax({
		url: ajax_parameters.ajaxurl,
		type: "GET",
		dataType: "JSON",
		data: {
			'action':'example_ajax_request',
			'target_time' : target_time,
			'age_category' : age_category,
			'previous_marathons' : previous_marathons,
			'ran_within_minutes_of_prediction' : ran_within_minutes_of_prediction,
			'ran_within_minutes_of_this_target_time' : ran_within_minutes_of_this_target_time
		},
		success:function(data) {
			jQuery('span#number-of-runners').text(data.number_of_runners);
						
			var icons_html = '';
			/*
			for (i = 0; i < data.number_of_runners; i++) { 
			icons_html = icons_html += "<i class=\"fa fa-female\"></i>";
			}
			*/
			jQuery('div#number-of-runners-icons').html(icons_html);
			
			var cumulative_splits = jQuery.parseJSON(data.splits_cumulative);
			jQuery('td#5k-split').text(cumulative_splits['5k']);
			jQuery('td#10k-split').text(cumulative_splits['10k']);
			jQuery('td#15k-split').text(cumulative_splits['15k']);
			jQuery('td#20k-split').text(cumulative_splits['20k']);
			jQuery('td#21k-split').text(cumulative_splits['21k']);
			jQuery('td#25k-split').text(cumulative_splits['25k']);
			jQuery('td#30k-split').text(cumulative_splits['30k']);
			jQuery('td#35k-split').text(cumulative_splits['35k']);
			jQuery('td#40k-split').text(cumulative_splits['40k']);
			
			var speeds_min_per_km = jQuery.parseJSON(data.speeds_min_per_km);
			jQuery('td#5k-min-per-km').text(speeds_min_per_km['5k']);
			jQuery('td#10k-min-per-km').text(speeds_min_per_km['10k']);
			jQuery('td#15k-min-per-km').text(speeds_min_per_km['15k']);
			jQuery('td#20k-min-per-km').text(speeds_min_per_km['20k']);
			jQuery('td#21k-min-per-km').text(speeds_min_per_km['21k']);
			jQuery('td#25k-min-per-km').text(speeds_min_per_km['25k']);
			jQuery('td#30k-min-per-km').text(speeds_min_per_km['30k']);
			jQuery('td#35k-min-per-km').text(speeds_min_per_km['35k']);
			jQuery('td#40k-min-per-km').text(speeds_min_per_km['40k']);
						
			var speeds_min_per_mile = jQuery.parseJSON(data.speeds_min_per_mile);			
			jQuery('td#5k-min-per-mile').text(speeds_min_per_mile['5k']);
			jQuery('td#10k-min-per-mile').text(speeds_min_per_mile['10k']);
			jQuery('td#15k-min-per-mile').text(speeds_min_per_mile['15k']);
			jQuery('td#20k-min-per-mile').text(speeds_min_per_mile['20k']);
			jQuery('td#21k-min-per-mile').text(speeds_min_per_mile['21k']);
			jQuery('td#25k-min-per-mile').text(speeds_min_per_mile['25k']);
			jQuery('td#30k-min-per-mile').text(speeds_min_per_mile['30k']);
			jQuery('td#35k-min-per-mile').text(speeds_min_per_mile['35k']);
			jQuery('td#40k-min-per-mile').text(speeds_min_per_mile['40k']);

			var split_means = jQuery.parseJSON(data.split_means);			
			var split_means = jQuery.parseJSON(data.split_means);			
			
			jQuery("#sparkline").sparkline([
				split_means['5k'],split_means['10k'],split_means['15k'],
				split_means['20k'], split_means['25k'], split_means['30k'],
				split_means['35k'], split_means['40k']
			], {
				type: 'bar', barWidth: '52', chartRangeMin: '900', barColor: '#A46497', height: '100px',
				tooltipFormat:  jQuery.spformat('<div style="font-size: 16px; padding-top: 0px; vertical-align: top"><span style="font-size: 16px; color: {{color}}">&#9679;</span></div> {{offset:names}} ({{value}}secs)','sparkline-tooltip-class'),
					tooltipValueLookups: {
						names: {
							0: '5km',
							1: '10km',
							2: '15km',
							3: '20km',
							4: '25km',
							5: '30km',
							6: '35km',
							7: '40km'
							// Add more here
						}
					}	
			});

			jQuery('span#pacing-target-time').text(target_time);		
			jQuery('td#finish').text(target_time);		
						
			//alert(data.runners_details);
			var runners_details = jQuery.parseJSON(data.runners_details);
			var runners_details_html = '';
			var initials = runners_details['initials'];
			var finish_times = runners_details['finish-time'];
			var predicted_times = runners_details['predicted-time'];
			var age_categories = runners_details['age-category'];
			var previous_marathons = runners_details['previous-marathons'];
			var fivek_splits = runners_details['5k_Split_s'];
			var tenk_splits = runners_details['10k_Split_s'];
			var fifteenk_splits = runners_details['15k_Split_s'];
			var twentyk_splits = runners_details['20k_Split_s'];
			var twentyfivek_splits = runners_details['25k_Split_s'];
			var thirtyk_splits = runners_details['30k_Split_s'];
			var thirtyfivek_splits = runners_details['35k_Split_s'];
			var fortyk_splits = runners_details['40k_Split_s'];
			
			for (i = 0; i < data.number_of_runners; i++) { 			
				
				//console.log(names);
				runners_details_html = runners_details_html += "<p style=\"margin-bottom: 0px;\">";
				runners_details_html = runners_details_html.concat(initials[i], " Predicted: ", predicted_times[i], " Finished: ", finish_times[i]);
				//runners_details_html = runners_details_html += finish_times[i];
				runners_details_html = runners_details_html += "</p>";
				runners_details_html = runners_details_html += "<p style=\"margin-bottom: 0px; color: #999;\">";
				runners_details_html = runners_details_html.concat(" Age category: ", age_categories[i], " Previous marathons: ", previous_marathons[i]);
				runners_details_html = runners_details_html += "</p>";
				runners_details_html = runners_details_html.concat("<div style=\"margin-bottom: 30px;\" id=\"", "sparkline-runner-", i, "\"", "></div>");
			}
			
			jQuery('div#runners-details').html(runners_details_html);
			
			for (i = 0; i < data.number_of_runners; i++) { 			
			
				jQuery("#sparkline-runner-".concat(i)).sparkline([
					fivek_splits[i],tenk_splits[i],fifteenk_splits[i],
					twentyk_splits[i], twentyfivek_splits[i], thirtyk_splits[i],
					thirtyfivek_splits[i], fortyk_splits[i]
				], {
					type: 'bar', barWidth: '40', chartRangeMin: '900', barColor: '#999', height: '50px',
					tooltipFormat:  jQuery.spformat('<div style="font-size: 16px; padding-top: 0px; vertical-align: top"><span style="font-size: 16px; color: {{color}}">&#9679;</span></div> {{offset:names}} ({{value}}secs)','sparkline-tooltip-class'),
						tooltipValueLookups: {
							names: {
								0: '5km',
								1: '10km',
								2: '15km',
								3: '20km',
								4: '25km',
								5: '30km',
								6: '35km',
								7: '40km'
								// Add more here
							}
						}	
				});
			}
			
			jQuery('div#debug').html(data.debug);		
			// This outputs the result of the ajax request
			//console.log(data);
			//alert(data);
		},
		error: function(data){
			console.log(data);
			//TODO: Log error to screen
			jQuery('div#debug').html(data);		
		}
		});   
		
		
		
	});
});

jQuery(document).ready(function(){
	jQuery(".increment-time").on('click',function(event) {
		//alert(event.target.id);
		var button_id = event.target.id;  //e.g. increment-mile-1
		//alert(button_id);
		//Get the id of the td containing the time (in mm:ss) visible to the user
		var time_td_id = button_id.substring(10) + "-split"; //e.g. mile-1-split
		//alert(time_td_id);
		//Get the id of the td containing the time (in seconds) which is invisible to the user
		var time_secs_td_id = button_id.substring(10) + "-split-secs";  //e.g. mile-1-split-secs
		
		//Get the current value in seconds
		var time_secs = parseInt(jQuery('td#' + time_secs_td_id).text());		
		
		//Increment it		
		var new_time_secs = time_secs + 15;
		//Update the value in seconds
		jQuery('td#' + time_secs_td_id).text(new_time_secs);
		//Update the user-visible value (in mm:ss)
		jQuery('td#' + time_td_id).text(seconds_to_hhmmss(new_time_secs));
		
		//Recalculate finish time
		var totalSeconds = 0;
		for (mile = 1; mile <= 13; mile++) {
			//alert(mile + " " + jQuery('td#mile-' + mile + '-split-secs').text());
			totalSeconds = totalSeconds + parseInt(jQuery('td#mile-' + mile + '-split-secs').text());		
		}
		//alert(totalSeconds);
		//Update the finish value in seconds
		jQuery('td#finish-secs').text(totalSeconds);
		//Update the user-visible value (in mm:ss)
		var total_hhmmss = seconds_to_hhmmss(totalSeconds);
		//alert(total_hhmmss);
		jQuery('td#finish-hhmmss').text(total_hhmmss);
		
	});
});

function seconds_to_hhmmss(seconds) {
	var minutes = Math.floor(seconds / 60);
	var seconds = seconds - minutes * 60;
	var hours = Math.floor(seconds / 3600);
	seconds = seconds - hours * 3600;
	var finalTime = str_pad_left(minutes,'0',2)+':'+str_pad_left(seconds,'0',2);
	return finalTime;
}

function str_pad_left(string,pad,length) {
    return (new Array(length+1).join(pad)+string).slice(-length);
}

