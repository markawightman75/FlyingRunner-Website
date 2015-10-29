


jQuery(document).ready(function() {	
	////////////////////////////////////////////////////
	//See https://gionkunz.github.io/chartist-js/api-documentation.html#chartistbar-declaration-defaultoptions for options
	
	new Chartist.Bar('#chart-by-experience', {
		labels: ['No marathons', '1 marathon', '2 marathons', '3 marathons', '4 marathons', '5 marathons', '6-10 marathons', 'More than 10 marathons'],
		series: [
					[0.7738, 0.8065, 0.9288, 0.9329, 0.8957, 0.8521, 0.7339, 0.9593],
				]
		}, {
		  seriesBarDistance: 19, //Only relevant when there is more than one bar in a group 
		  reverseData: true,
		  horizontalBars: true,
		  axisX: {			  
			  showLabel: true,
			  showGrid: false,
			  labelInterpolationFnc: function(value) {
				return value;
			  },
			  scaleMinSpace: 80, //Force only a few x axis grid lines and labels
		  },
		  axisY: {
			offset: 110,
			showLabel: true,
			position: 'start', 
			showGrid: false
		  },
		 low: 0.0,
		 high: 1.0,
		 chartPadding: {
			top: -11,
			right: 10,
			bottom: 5,
			left: 0
		  },
	});
	
	new Chartist.Bar('#chart-by-experience-and-gender', {
		labels: ['No marathons', '1 marathon', '2 marathons', '3 marathons', '4 marathons', '5 marathons', '6-10 marathons', 'More than 10 marathons'],
		series: [
					[0.7248, 0.7507, 0.9205, 0.9267, 0.9797, 0.727, 0.8045, 0.9759], //Female by experience
					[0.7993, 0.8536, 0.9267, 0.9336, 0.8493, 0.8632, 0.6959, 0.9532]  //Male by experience
				]
		}, {
		  seriesBarDistance: 19,
		  reverseData: true,
		  horizontalBars: true,
		  axisX: {			  
			  showLabel: true,
			  showGrid: false,
			  labelInterpolationFnc: function(value) {
				return value;
			  },
			  scaleMinSpace: 80, //Force only a few x axis grid lines and labels
		  },
		  axisY: {
			offset: 110,
			showLabel: true,
			position: 'start', 
			showGrid: false
		  },
		 low: 0.0,
		 high: 1.0,
		 chartPadding: {
			top: -11,
			right: 10,
			bottom: 5,
			left: 0
		  },
	});
	
	/*new Chartist.Bar('.ct-chart', {
		labels: ['No marathons', '1 marathon', '2 marathons', '3 marathons', '4 marathons', '5 marathons', '6-10 marathons', 'More than 10 marathons'],
		series: [
					[5, 4, 3, 7, 5, 10, 3],
					[3, 2, 9, 5, 4, 6, 4]
				]
		}, {
		  seriesBarDistance: 10,
		  reverseData: true,
		  horizontalBars: true,
		  axisY: {
			offset: 70
		  }
	});*/
	/////////////////////////////
});

jQuery(document).ready(function() {
	jQuery('input#target-time-h').on('blur',function() {
		var target_time_h = jQuery('input#target-time-h').val();
		if (target_time_h == "")
		{
			jQuery('p#time-validation-error').text("Please enter a number of hours");
			jQuery('input#target-time-h').addClass('input-empty');
		}
		else
		{
			jQuery('p#time-validation-error').text("");
			jQuery('input#target-time-h').removeClass('input-empty');
		}
	});	
});

jQuery(document).ready(function() {
	jQuery('input#target-time-m').on('blur',function() {
		if (jQuery('input#target-time-m').val() == "")
		{
			jQuery('input#target-time-m').val('00');
		}
	});	
});

jQuery(document).ready(function() {
	jQuery('input#target-time-s').on('blur',function() {
		if (jQuery('input#target-time-s').val() == "")
		{
			jQuery('input#target-time-s').val('00');
		}
	});	
});


jQuery(document).ready(function(){
	jQuery('#find-runners').on('click',function() {
		var target_time_h = jQuery('input#target-time-h').val();
		var age_category = jQuery('select#age-category').val();
		var previous_marathons = jQuery('select#previous-marathons').val();
		var ran_within_minutes_of_prediction = jQuery('select#ran-within-minutes-of-prediction').val();
		var ran_within_minutes_of_this_target_time = jQuery('select#ran-within-minutes-of-this-target-time').val();
		
		if (target_time_h == "")
		{
			//alert("Please complete time");
			jQuery('p#time-validation-error').text("Please enter a number of hours");
			jQuery('input#target-time-h').addClass('input-empty');
			jQuery('input#target-time-h').focus();
			return;
		}
		
		var target_time = target_time_h + ":" + "00:00";
		
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
					
			//TODO - really we should be throwing an error back in the ajax response
			jQuery('div#debug').html(data.debug);
			
			if (data.number_of_runners == 0)
			{
				var intro_html = "";
				intro_html = intro_html + "<p style=\"margin-bottom: 0px;\">";
				intro_html = intro_html + "No runners found. Please try broadening your search criteria.";
				intro_html = intro_html + "<\p>";
				jQuery('div#runners-details-intro').html(intro_html);
			}
			else
			{
				var intro_html = "";
				intro_html = intro_html + "<p style=\"margin-bottom: 0px;\">";
				intro_html = intro_html + "There were ";
				intro_html = intro_html + data.number_of_runners;
				intro_html = intro_html + " runners who matched this profile.";
				intro_html = intro_html + "<\p>";
				jQuery('div#runners-details-intro').html(intro_html);
				jQuery('div#sparkline-intro').css('height','auto');
				jQuery('div#sparkline-intro').css('visibility','visible');
				
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
				
				//Store split means so we can use them if user clicks "build pacing from this"
				jQuery('td#5k-mean-split-s').text(split_means['5k']);
				jQuery('td#10k-mean-split-s').text(split_means['10k']);
				jQuery('td#15k-mean-split-s').text(split_means['15k']);
				jQuery('td#20k-mean-split-s').text(split_means['20k']);
				jQuery('td#25k-mean-split-s').text(split_means['25k']);
				jQuery('td#30k-mean-split-s').text(split_means['30k']);
				jQuery('td#35k-mean-split-s').text(split_means['35k']);
				jQuery('td#40k-mean-split-s').text(split_means['40k']);
				
				
				jQuery("#sparkline").sparkline([
					split_means['5k'],split_means['10k'],split_means['15k'],
					split_means['20k'], split_means['25k'], split_means['30k'],
					split_means['35k'], split_means['40k']
				], {
					type: 'bar', barWidth: '40', chartRangeMin: '900', barColor: '#A46497', height: '50px',
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

				jQuery('input#build-pacing-average').css('visibility','visible');
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
			jQuery('div#debug').css('display', 'block');
		}
		});   
		
		
		
	});
});


jQuery(document).ready(function(){
	jQuery(".build-pacing").on('click',function(event) {
		//alert(event.target.id);
		var button_id = event.target.id;  //e.g. build-pacing-average
		//alert(button_id);
		
		var params = {
			km5: jQuery('td#5k-mean-split-s').text(),
			km10: jQuery('td#10k-mean-split-s').text(),
			km15: jQuery('td#15k-mean-split-s').text(),
			km20: jQuery('td#20k-mean-split-s').text(),
			km25: jQuery('td#25k-mean-split-s').text(),
			km30: jQuery('td#30k-mean-split-s').text(),
			km35: jQuery('td#35k-mean-split-s').text(),
			km40: jQuery('td#40k-mean-split-s').text(),
		}; 
				
		var query_string;
		query_string = jQuery.param(params);
		
		var root_url = window.location.protocol + '//' + window.location.host;
		if (root_url = "127.0.0.1:82") {
			root_url = root_url + "/wordpress";
		}
		//alert(root_url);
		//alert(window.location);
		url = window.location.href;
		calculator_page_url =  url.replace("explore-marathon-pacings","marathon-pacing-calculator");		
		calculator_page_url = calculator_page_url + "?" + query_string; //http:// + root_url + "/marathon-pacing-calculator?" + query_string;		
		window.open(calculator_page_url);
		
		/*
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
		*/
	});
});
