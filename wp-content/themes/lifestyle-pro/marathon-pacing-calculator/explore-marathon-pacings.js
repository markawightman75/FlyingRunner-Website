


jQuery(document).ready(function() {
	//On page load, default the hours to 4, focus and select the text
	jQuery("input#target-time-h").val("4");
	jQuery("input#target-time-h").focus();
	jQuery("input#target-time-h").select();
	
	//If query string includes 'debug', show debug info
	var qs = (function(a) {
		if (a == "") return {};
		var b = {};
		for (var i = 0; i < a.length; ++i)
		{
			var p=a[i].split('=', 2);
			if (p.length == 1)
				b[p[0]] = "";
			else
				b[p[0]] = decodeURIComponent(p[1].replace(/\+/g, " "));
		}
		return b;
	})(window.location.search.substr(1).split('&'));

	if (typeof qs['debug'] != 'undefined')
	{
		jQuery('div#debug').css('display','block');	
	}
	else
	{
		jQuery('div#debug').css('display','none');	
	}
	
	
	
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
		jQuery('div#runners-details-intro').html("<p style=\"margin-bottom: 0px;\">Finding runners...</p>");
		
		var finish_time_from_s = jQuery('select#finish-time-from').val();
		var finish_time_to_s = jQuery('select#finish-time-to').val();
		var age_category = jQuery('select#age-category').val();
		var previous_marathons = jQuery('select#previous-marathons').val();
		var gender = jQuery('select#gender').val();
		var prediction_accuracy_tag = jQuery('select#prediction-accuracy').val();
		
		/*
		if (target_time_h == "")
		{
			//alert("Please complete time");
			jQuery('p#time-validation-error').text("Please enter a number of hours");
			jQuery('input#target-time-h').addClass('input-empty');
			jQuery('input#target-time-h').focus();
			return;
		}
		
		var target_time = target_time_h + ":" + "00:00";
		*/
		jQuery.ajax({
		url: ajax_parameters.ajaxurl,
		type: "GET",
		dataType: "JSON",
		data: {
			'action':'example_ajax_request',
			'finish_time_from' : finish_time_from_s,
			'finish_time_to' : finish_time_to_s,
			'age_category' : age_category,
			'previous_marathons' : previous_marathons,
			'gender' : gender,
			'prediction_accuracy_tag' : prediction_accuracy_tag
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
				
				//Store split means so we can use them if user clicks build pacing button on average profile chart
				jQuery('td#5k-mean-split-s').text(split_means['5k']);
				jQuery('td#10k-mean-split-s').text(split_means['10k']);
				jQuery('td#15k-mean-split-s').text(split_means['15k']);
				jQuery('td#20k-mean-split-s').text(split_means['20k']);
				jQuery('td#25k-mean-split-s').text(split_means['25k']);
				jQuery('td#30k-mean-split-s').text(split_means['30k']);
				jQuery('td#35k-mean-split-s').text(split_means['35k']);
				jQuery('td#40k-mean-split-s').text(split_means['40k']);
								
				
				//jQuery('input#build-pacing-average').css('visibility','visible');
				
				var runners_details = jQuery.parseJSON(data.runners_details);
				var runners_details_html = '';
				var initials = runners_details['initials'];
				var finish_times = runners_details['finish-time'];
				var finish_times_secs = runners_details['finish-time-s'];
				var predicted_times = runners_details['predicted-time'];
				var prediction_accuracy_percents = runners_details['prediction-accuracy-percent'];
				
				var age_categories = runners_details['age-category'];
				var genders = runners_details['gender'];				
				var previous_marathons = runners_details['previous-marathons'];
				var fivek_splits = runners_details['5k_Split_s'];
				var tenk_splits = runners_details['10k_Split_s'];
				var fifteenk_splits = runners_details['15k_Split_s'];
				var twentyk_splits = runners_details['20k_Split_s'];
				var twentyfivek_splits = runners_details['25k_Split_s'];
				var thirtyk_splits = runners_details['30k_Split_s'];
				var thirtyfivek_splits = runners_details['35k_Split_s'];
				var fortyk_splits = runners_details['40k_Split_s'];

				var runner_detail_template = jQuery('#runner-detail-template').html();

				var num_predictions_excellent = 0;
				var num_predictions_good = 0;
				var num_predictions_ok = 0;
				var num_predictions_bad = 0;
				
				for (i = 0; i < data.number_of_runners; i++) { 			
					var runner_detail = runner_detail_template;					
					//Search and replace all the things we need to
					runner_detail = runner_detail.replace('[RUNNER-IMAGE]', 'runner-icon-' + ((genders[i] == 'Female') ? 'female' : 'male' ) + '-57x70.png');
					runner_detail = runner_detail.replace('[GENDER-AND-INITIALS]', 'Runner: ' + initials[i].toUpperCase() + ' (' + genders[i] + ')');
					runner_detail = runner_detail.replace('[AGE]', age_categories[i]);
					runner_detail = runner_detail.replace('[MARATHONS]', previous_marathons[i]);
					runner_detail = runner_detail.replace('[PREDICTED]', predicted_times[i]);
					runner_detail = runner_detail.replace('[ACTUAL]', finish_times[i]);			
					//alert(slower_than_predicted_by_percents[i]);
					var accuracy = Math.floor(parseFloat(prediction_accuracy_percents[i]));
					var accuracy_class = '';					
					runner_detail = runner_detail.replace('[ACCURACY%]', accuracy + '%');
						
					if (prediction_accuracy_percents[i] >= 99.0) {
						accuracy_class = 'runner-accuracy-excellent';
						num_predictions_excellent += 1;
					} 						
					else if (prediction_accuracy_percents[i] >= 95.0) {
						accuracy_class = 'runner-accuracy-good';
						num_predictions_good += 1;
					}
					else if (prediction_accuracy_percents[i] >= 90.0) {
						accuracy_class = 'runner-accuracy-ok';
						num_predictions_ok += 1;
					}					
					else
					{
						accuracy_class = 'runner-accuracy-bad';	
						num_predictions_bad += 1;
					}
					
					runner_detail = runner_detail.replace('[ACCURACY-CLASS]', accuracy_class);
					
					runner_detail = runner_detail.replace('[ID-SPARKLINE]','sparkline-runner-' + i);
					runner_detail = runner_detail.replace('[ID-BUILD-PACING-BUTTON]','build-pacing-button-runner-' + i);
										
					runner_detail = runner_detail.replace('[ID-VIEW-SPLITS-BUTTON]','view-splits-button-runner-' + i);
					runner_detail = runner_detail.replace('[ID-SPLITS-TABLE]','splits-table-runner-' + i);	
					
					//[SPLIT-5K]
					runner_detail = runner_detail.replace('[SPLIT-5K]',seconds_to_hhmmss(fivek_splits[i]));	
					runner_detail = runner_detail.replace('[SPLIT-10K]',seconds_to_hhmmss(tenk_splits[i]));	
					runner_detail = runner_detail.replace('[SPLIT-15K]',seconds_to_hhmmss(fifteenk_splits[i]));	
					runner_detail = runner_detail.replace('[SPLIT-20K]',seconds_to_hhmmss(twentyk_splits[i]));	
					runner_detail = runner_detail.replace('[SPLIT-25K]',seconds_to_hhmmss(twentyfivek_splits[i]));	
					runner_detail = runner_detail.replace('[SPLIT-30K]',seconds_to_hhmmss(thirtyk_splits[i]));	
					runner_detail = runner_detail.replace('[SPLIT-35K]',seconds_to_hhmmss(thirtyfivek_splits[i]));	
					runner_detail = runner_detail.replace('[SPLIT-40K]',seconds_to_hhmmss(fortyk_splits[i]));	
					runner_detail = runner_detail.replace('[FINISH-TIME-S]',finish_times_secs[i]);	
					
					runner_detail = runner_detail.replace('[PACE-5K]',seconds_to_hhmmss(Math.round(fivek_splits[i] / 3.10686)));	
					runner_detail = runner_detail.replace('[PACE-10K]',seconds_to_hhmmss(Math.round(tenk_splits[i] / 3.10686)));	
					runner_detail = runner_detail.replace('[PACE-15K]',seconds_to_hhmmss(Math.round(fifteenk_splits[i] / 3.10686)));	
					runner_detail = runner_detail.replace('[PACE-20K]',seconds_to_hhmmss(Math.round(twentyk_splits[i] / 3.10686)));	
					runner_detail = runner_detail.replace('[PACE-25K]',seconds_to_hhmmss(Math.round(twentyfivek_splits[i] / 3.10686)));	
					runner_detail = runner_detail.replace('[PACE-30K]',seconds_to_hhmmss(Math.round(thirtyk_splits[i] / 3.10686)));	
					runner_detail = runner_detail.replace('[PACE-35K]',seconds_to_hhmmss(Math.round(thirtyfivek_splits[i] / 3.10686)));	
					runner_detail = runner_detail.replace('[PACE-40K]',seconds_to_hhmmss(Math.round(fortyk_splits[i] / 3.10686)));	
					
					runners_details_html += runner_detail;				
				}
				
				//DON'T SHOW INDIVIDUAL RUNNER DETAILS
				//jQuery('div#runners-details').html(runners_details_html);

				//Update the text under the prediction accuracy chart with real numbers
				var accuracy_text_html = jQuery('div#prediction-accuracy-text-template').html();				
				accuracy_text_html = accuracy_text_html.replace('[NUMBER-EXCELLENT]',num_predictions_excellent);
				accuracy_text_html = accuracy_text_html.replace('[NUMBER-GOOD]',num_predictions_good);
				accuracy_text_html = accuracy_text_html.replace('[NUMBER-OK]',num_predictions_ok);
				accuracy_text_html = accuracy_text_html.replace('[NUMBER-BAD]',num_predictions_bad);
				jQuery('div#chart-group-prediction-accuracy-text').html(accuracy_text_html);
				
				//Show the div containing the accuracy and average pacing charts
				//Must happen before the sparkline is drawn
				jQuery('.group-charts').removeClass('hidden');
				
				//Build pacing accuracy distribution chart
				labelCount =0;
				new Chartist.Pie('#chart-group-prediction-accuracy', {
				  series: [{value: num_predictions_excellent, className: 'chart-group-prediction-accuracy-excellent'}, {value: num_predictions_good, className: 'chart-group-prediction-accuracy-good'}, {value: num_predictions_ok, className: 'chart-group-prediction-accuracy-ok'}, {value: num_predictions_bad, className: 'chart-group-prediction-accuracy-bad'}]
				}, {
				  donut: true,
				  donutWidth: 60,
				  startAngle: 270,
				  total: ((num_predictions_excellent + num_predictions_good + num_predictions_ok + num_predictions_bad)*2), /*Total must be 2x sum of values to get a half-circle donut*/
				  showLabel: true				  
				});
				
				//Build average pacing curve sparkline
				jQuery("#sparkline-mean").sparkline([
					split_means['5k'],split_means['10k'],split_means['15k'],
					split_means['20k'], split_means['25k'], split_means['30k'],
					split_means['35k'], split_means['40k']
				], {
					type: 'bar', barWidth: '35', chartRangeMin: '900', barColor: '#3a579a', height: '120px',
					tooltipFormat: "{{offset:names}}",
					tooltipValueLookups: {names: {0:'0-5km',1:'5-10km',2:'10-15km',3:'15-20km',4:'20-25km',5: '25-30km',6: '30-35km',7: '35-40km'}}	
				});
				
				
				//Pickup the media query we're using from the size of the div we've set the width of via css
				//TODO: Think this is getting made more complicated than necessary because table columns are varying in width by content
				//even though td width is set.
				var mediaQuery = jQuery('div#screen-size-tag').width();
				var barWidth = '44px';
				if (mediaQuery == 767) barWidth = '49px'; //iPhone 6
				if (mediaQuery == 768) barWidth = '48px'; //iPad portrait
				if (mediaQuery == 1024) barWidth = '40px'; //iPad portrait
				
				//Build the sparkline pacing charts for each runner
				/*
				for (i = 0; i < data.number_of_runners; i++) { 			
				
					jQuery("#sparkline-runner-".concat(i)).sparkline([
						fivek_splits[i],tenk_splits[i],fifteenk_splits[i],
						twentyk_splits[i], twentyfivek_splits[i], thirtyk_splits[i],
						thirtyfivek_splits[i], fortyk_splits[i]
					], {
						type: 'bar', barWidth: barWidth, chartRangeMin: '900', barColor: '#6D8ACD', height: '70px',
						tooltipFormat: "{{offset:names}}",
						tooltipValueLookups: {names: {0:'0-5km',1:'5-10km',2:'10-15km',3:'15-20km',4:'20-25km',5: '25-30km',6: '30-35km',7: '35-40km'}}	
					});
				}
				
				//Immediately hide the button and show the splits table for the first runner
				jQuery('#view-splits-button-runner-0').css('display','none');
				jQuery('#splits-table-runner-0').css('display','block');				
				*/
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
	//We have to hook into body, because the build pacing buttons are dynamically generated
	jQuery('body').on('click', '.build-pacing-button',function(event) {
		var params;
		var button_id = event.target.id;  //e.g. build-pacing-button-runner-0
		if (button_id == 'build-pacing-button-mean') {
			//Mean pacing splits are normalised t0 14400 seconds finish in ajax script
			params = {
			km5: jQuery('td#5k-mean-split-s').text(),
			km10: jQuery('td#10k-mean-split-s').text(),
			km15: jQuery('td#15k-mean-split-s').text(),
			km20: jQuery('td#20k-mean-split-s').text(),
			km25: jQuery('td#25k-mean-split-s').text(),
			km30: jQuery('td#30k-mean-split-s').text(),
			km35: jQuery('td#35k-mean-split-s').text(),
			km40: jQuery('td#40k-mean-split-s').text(),
			finish: 14400
			}; 
		}
		else {
		var splits_table_id = 'splits-table-runner-' + button_id.substring(27);				
		
		params = {
			km5: hhmmss_to_seconds(jQuery('table#' + splits_table_id).find('td#td-5k-split').text()),
			km10: hhmmss_to_seconds(jQuery('table#' + splits_table_id).find('td#td-10k-split').text()),
			km15: hhmmss_to_seconds(jQuery('table#' + splits_table_id).find('td#td-15k-split').text()),
			km20: hhmmss_to_seconds(jQuery('table#' + splits_table_id).find('td#td-20k-split').text()),
			km25: hhmmss_to_seconds(jQuery('table#' + splits_table_id).find('td#td-25k-split').text()),
			km30: hhmmss_to_seconds(jQuery('table#' + splits_table_id).find('td#td-30k-split').text()),
			km35: hhmmss_to_seconds(jQuery('table#' + splits_table_id).find('td#td-35k-split').text()),
			km40: hhmmss_to_seconds(jQuery('table#' + splits_table_id).find('td#td-40k-split').text()),
			finish: jQuery('table#' + splits_table_id).find('td#td-finish-time-s').text()
			}; 			
		}
				
		open_calculator_page(params);
	});
;});

function open_calculator_page(params) {
	var query_string;
	query_string = jQuery.param(params);		
	var root_url = window.location.protocol + '//' + window.location.host;
	if (root_url = "127.0.0.1:82") {
		root_url = root_url + "/wordpress";
	}
	url = window.location.href;
	calculator_page_url =  url.replace("explore-marathon-research-data","marathon-pacing-calculator");		
	calculator_page_url = calculator_page_url + "?" + query_string; //http:// + root_url + "/marathon-pacing-calculator?" + query_string;		
	window.open(calculator_page_url);		

}

jQuery(document).ready(function(){
	//We have to hook into body, because the view splits buttons are dynamically generated
	jQuery('body').on('click', '.view-splits-button',function(event) {
		var button_id = event.target.id;  //e.g. view-splits-button-runner-0
		var splits_table_id = 'splits-table-runner-' + button_id.substring(26);		
		jQuery('#' + button_id).css('display','none');
		jQuery('#' + splits_table_id).css('display','block');
	});
;});

/*Convert either mm:ss or hh:mm:ss into seconds*/
function hhmmss_to_seconds(hhmmss) {
	var firstColon = hhmmss.indexOf(":");
	if (firstColon == -1) return 0;
	var firstPart = hhmmss.substr(0,firstColon);
	var secondPart = hhmmss.substr(firstColon+1);
	var secondColon = secondPart.indexOf(":");
	if (secondColon > -1) {
		//We have hh:mm:ss
		var mm = secondPart.substr(0,secondColon);
		var ss = secondPart.substr(secondColon+1);
		//alert("hh: " + firstPart);
		//alert("mm: " + mm);
		//alert("ss: " + ss);
		return ((parseInt(firstPart) * 3600) + (parseInt(mm) * 60) + parseInt(ss));
	}	
	else {
		//We have mm:ss

		//alert("mm: " + firstPart);
		//alert("ss: " + secondPart);
		return ((parseInt(firstPart) * 60) + parseInt(secondPart));
	}
}

function seconds_to_hhmmss(totalSec) {
	var hours = parseInt( totalSec / 3600 ) % 24;
	var minutes = parseInt( totalSec / 60 ) % 60;
	var seconds = totalSec % 60;

	var result = "";
	if (hours > 0)
	{
		result = hours + ":" + (minutes < 10 ? "0" + minutes : minutes) + ":" + (seconds  < 10 ? "0" + seconds : seconds);
	}
	else
	{
		result = minutes + ":" + (seconds  < 10 ? "0" + seconds : seconds);
	}
	return result;
}
