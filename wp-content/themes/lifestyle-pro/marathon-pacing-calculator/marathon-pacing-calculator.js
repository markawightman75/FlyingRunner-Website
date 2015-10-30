var selection_data_provided_in_query = false;

jQuery(document).ready(function(){

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
	
	jQuery('td#5k-passed-in-split-s').text(qs['km5']);
	jQuery('td#10k-passed-in-split-s').text(qs['km10']);
	jQuery('td#15k-passed-in-split-s').text(qs['km15']);
	jQuery('td#20k-passed-in-split-s').text(qs['km20']);
	jQuery('td#25k-passed-in-split-s').text(qs['km25']);
	jQuery('td#30k-passed-in-split-s').text(qs['km30']);
	jQuery('td#35k-passed-in-split-s').text(qs['km35']);
	jQuery('td#40k-passed-in-split-s').text(qs['km40']);
	jQuery('td#passed-in-finish-s').text(qs['finish']); //TOTAL finish time
	
	//TODO: Check all parameters are present	
	//alert(qs['km5']);
	if (typeof qs['km5'] != 'undefined')
	{
		selection_data_provided_in_query = true;
		Select_From_Selection_Tab();
	}
	else
	{
		Select_Even_Splits_Tab();
	}
});

//Calculate even splits
jQuery(document).ready(function(){
	jQuery("#calculate-even-splits").on('click',function(event) {
		//TODO: VALIDATE TIME INPUT
		//Get target time
		var hours = parseInt(jQuery('input#target-time-even-h').val());
		var minutes = parseInt(jQuery('input#target-time-even-m').val());
		var seconds = parseInt(jQuery('input#target-time-even-s').val());
		
		var totalSeconds = (hours * 3600) + (minutes * 60) + seconds;
		
		var secondsPerMile = Math.round(totalSeconds /26.21875);
		
		for (mile = 1; mile <= 26; mile++) {
			//Update the value in seconds
			jQuery('td#mile-' + mile + '-split-secs').text(secondsPerMile);
			//Update the user-visible value (in mm:ss)
			jQuery('td#mile-' + mile + '-split').text(seconds_to_hhmmss(secondsPerMile));
		}
		//alert(totalSeconds);
		
		//Update the halfway value in seconds
		jQuery('td#halfway-secs').text(totalSeconds/2);
		//Update the user-visible value (in mm:ss)
		jQuery('td#halfway-hhmmss').text(seconds_to_hhmmss(Math.round(totalSeconds/2)));
		
		//Update the finish value in seconds
		jQuery('td#finish-secs').text(totalSeconds);
		//Update the user-visible value (in mm:ss)
		jQuery('td#finish-hhmmss').text(seconds_to_hhmmss(totalSeconds));
		
		//Display the table and pacing band button
		jQuery('div#download-pacing-band-div').removeClass("hidden");
		jQuery('#pacing-table').removeClass("hidden");
	});
});

//Calculate negative splits
jQuery(document).ready(function(){
	jQuery("#calculate-negative-splits").on('click',function(event) {
		//TODO: VALIDATE TIME INPUT
		//alert(1);
		//Get target time
		var hours = parseInt(jQuery('input#target-time-negative-h').val());
		var minutes = parseInt(jQuery('input#target-time-negative-m').val());
		var seconds = parseInt(jQuery('input#target-time-negative-s').val());
		
		var second_half_faster_by_minutes = parseInt(jQuery('input#second-half-negative-m').val());
		var second_half_faster_by_seconds = parseInt(jQuery('input#second-half-negative-s').val());
				
		var totalSeconds = (hours * 3600) + (minutes * 60) + seconds;
		var totalSecondsFirstHalf = (totalSeconds/2) + (((second_half_faster_by_minutes * 60) + second_half_faster_by_seconds)/2);
		var totalSecondsSecondHalf = (totalSeconds/2) - (((second_half_faster_by_minutes * 60) + second_half_faster_by_seconds)/2);
		
		var secondsPerMileFirstHalf = Math.round(totalSecondsFirstHalf / 13.109375);
		var secondsPerMileSecondHalf = Math.round(totalSecondsSecondHalf /13.109375);
		
		for (mile = 1; mile <= 26; mile++) {
			var secondsPerMile = (mile <= 13) ? secondsPerMileFirstHalf : secondsPerMileSecondHalf;
			
			//Update the value in seconds
			jQuery('td#mile-' + mile + '-split-secs').text(secondsPerMile);
			//Update the user-visible value (in mm:ss)
			jQuery('td#mile-' + mile + '-split').text(seconds_to_hhmmss(secondsPerMile));
		}		
		//Update the halfway value in seconds
		jQuery('td#halfway-secs').text(totalSecondsFirstHalf);
		//Update the user-visible value (in mm:ss)
		jQuery('td#halfway-hhmmss').text(seconds_to_hhmmss(totalSecondsFirstHalf));
		
		//Update the finish value in seconds
		jQuery('td#finish-secs').text(totalSeconds);
		//Update the user-visible value (in mm:ss)
		jQuery('td#finish-hhmmss').text(seconds_to_hhmmss(totalSeconds));
		
		//Display the table and pacing band button
		jQuery('div#download-pacing-band-div').removeClass("hidden");
		jQuery('#pacing-table').removeClass("hidden");
	});
});

//Calculate positive splits
jQuery(document).ready(function(){
	jQuery("#calculate-positive-splits").on('click',function(event) {
		//TODO: VALIDATE TIME INPUT
		//Get target time
		var hours = parseInt(jQuery('input#target-time-positive-h').val());
		var minutes = parseInt(jQuery('input#target-time-positive-m').val());
		var seconds = parseInt(jQuery('input#target-time-positive-s').val());
		
		var second_half_faster_by_minutes = parseInt(jQuery('input#second-half-positive-m').val());
		var second_half_faster_by_seconds = parseInt(jQuery('input#second-half-positive-s').val());
				
		var totalSeconds = (hours * 3600) + (minutes * 60) + seconds;
		var totalSecondsFirstHalf = (totalSeconds/2) - (((second_half_faster_by_minutes * 60) + second_half_faster_by_seconds)/2);
		var totalSecondsSecondHalf = (totalSeconds/2) + (((second_half_faster_by_minutes * 60) + second_half_faster_by_seconds)/2);
		
		var secondsPerMileFirstHalf = Math.round(totalSecondsFirstHalf / 13.109375);
		var secondsPerMileSecondHalf = Math.round(totalSecondsSecondHalf /13.109375);
		
		for (mile = 1; mile <= 26; mile++) {
			var secondsPerMile = (mile <= 13) ? secondsPerMileFirstHalf : secondsPerMileSecondHalf;
			
			//Update the value in seconds
			jQuery('td#mile-' + mile + '-split-secs').text(secondsPerMile);
			//Update the user-visible value (in mm:ss)
			jQuery('td#mile-' + mile + '-split').text(seconds_to_hhmmss(secondsPerMile));
		}
		//alert(totalSeconds);
		
		//Update the halfway value in seconds
		jQuery('td#halfway-secs').text(totalSecondsFirstHalf);
		//Update the user-visible value (in mm:ss)
		jQuery('td#halfway-hhmmss').text(seconds_to_hhmmss(totalSecondsFirstHalf));
		
		//Update the finish value in seconds
		jQuery('td#finish-secs').text(totalSeconds);
		//Update the user-visible value (in mm:ss)
		jQuery('td#finish-hhmmss').text(seconds_to_hhmmss(totalSeconds));
		
		//Display the table and pacing band button
		jQuery('div#download-pacing-band-div').removeClass("hidden");
		jQuery('#pacing-table').removeClass("hidden");
	});
});

//Calculate positive splits
jQuery(document).ready(function(){
	jQuery("#calculate-from-selection").on('click',function(event) {
		//TODO: VALIDATE TIME INPUT
		//Get target time
		var hours = parseInt(jQuery('input#target-time-from-selection-h').val());
		var minutes = parseInt(jQuery('input#target-time-from-selection-m').val());
		var seconds = parseInt(jQuery('input#target-time-from-selection-s').val());
		
		var targetSeconds = (hours * 3600) + (minutes * 60) + seconds;
		//alert('target seconds: ' + targetSeconds)
		
		var secondsPerMile = new Array();
		var split_5km = parseInt(jQuery('td#5k-passed-in-split-s').text());
		var split_10km = parseInt(jQuery('td#10k-passed-in-split-s').text());
		var split_15km = parseInt(jQuery('td#15k-passed-in-split-s').text());
		var split_20km = parseInt(jQuery('td#20k-passed-in-split-s').text());
		var split_25km = parseInt(jQuery('td#25k-passed-in-split-s').text());
		var split_30km = parseInt(jQuery('td#30k-passed-in-split-s').text());
		var split_35km = parseInt(jQuery('td#35k-passed-in-split-s').text());
		var split_40km = parseInt(jQuery('td#40k-passed-in-split-s').text());
		var passed_in_finish = parseInt(jQuery('td#passed-in-finish-s').text()); //TOTAL finish time
		
		//*******************************************************
		// Interpolate the pace for each mile from the 5km splits
		//*******************************************************
		var metres_per_mile = 1609.34;		
		var speed_m_per_s_5k = 5000 /split_5km;
		var speed_m_per_s_10k = 5000 /split_10km;
		var speed_m_per_s_15k = 5000 /split_15km;
		var speed_m_per_s_20k = 5000 /split_20km;
		var speed_m_per_s_25k = 5000 /split_25km;
		var speed_m_per_s_30k = 5000 /split_30km;
		var speed_m_per_s_35k = 5000 /split_35km;
		var speed_m_per_s_40k = 5000 /split_40km;
		
		secondsPerMile[1] = metres_per_mile / speed_m_per_s_5k;
		secondsPerMile[2] = metres_per_mile / speed_m_per_s_5k;
		secondsPerMile[3] = metres_per_mile / speed_m_per_s_5k;
		secondsPerMile[4] = ((0.10686 * metres_per_mile) / speed_m_per_s_5k ) + (((1-0.10686) * metres_per_mile) / speed_m_per_s_10k);
		secondsPerMile[5] = metres_per_mile / speed_m_per_s_10k;
		secondsPerMile[6] = metres_per_mile / speed_m_per_s_10k;
		//secondsPerMile[7] = (0.21371 * speed_m_per_s_10k) + ((1-0.21371) * speed_m_per_s_15k);
		secondsPerMile[7] = ((0.21371 * metres_per_mile) / speed_m_per_s_10k ) + (((1-0.21371) * metres_per_mile) / speed_m_per_s_15k);
		secondsPerMile[8] = metres_per_mile / speed_m_per_s_15k;
		secondsPerMile[9] = metres_per_mile / speed_m_per_s_15k;
		//secondsPerMile[10] = (0.32057 * speed_m_per_s_15k) + ((1-0.32057) * speed_m_per_s_20k);
		secondsPerMile[10] = ((0.21371 * metres_per_mile) / speed_m_per_s_15k ) + (((1-0.21371) * metres_per_mile) / speed_m_per_s_20k);
		secondsPerMile[11] = metres_per_mile / speed_m_per_s_20k;
		secondsPerMile[12] = metres_per_mile / speed_m_per_s_20k;
		//secondsPerMile[13] = (0.4274 * speed_m_per_s_20k) + ((1-0.4274) * speed_m_per_s_25k);
		secondsPerMile[13] = ((0.4274 * metres_per_mile) / speed_m_per_s_20k ) + (((1-0.4274) * metres_per_mile) / speed_m_per_s_25k);
		secondsPerMile[14] = metres_per_mile / speed_m_per_s_25k;
		secondsPerMile[15] = metres_per_mile / speed_m_per_s_25k;
		//secondsPerMile[16] = (0.5343 * speed_m_per_s_25k) + ((1-0.5343) * speed_m_per_s_30k);
		secondsPerMile[16] = ((0.5343 * metres_per_mile) / speed_m_per_s_25k ) + (((1-0.5343) * metres_per_mile) / speed_m_per_s_30k);
		secondsPerMile[17] = metres_per_mile / speed_m_per_s_30k;
		secondsPerMile[18] = metres_per_mile / speed_m_per_s_30k;
		//secondsPerMile[19] = (0.6411 * speed_m_per_s_30k) + ((1-0.6411) * speed_m_per_s_35k);
		secondsPerMile[19] = ((0.6411 * metres_per_mile) / speed_m_per_s_30k ) + (((1-0.6411) * metres_per_mile) / speed_m_per_s_35k);
		secondsPerMile[20] = metres_per_mile / speed_m_per_s_35k ;
		secondsPerMile[21] = metres_per_mile / speed_m_per_s_35k;
		//secondsPerMile[22] = (0.748 * speed_m_per_s_30k) + ((1-0.748) * speed_m_per_s_40k);
		secondsPerMile[22] = ((0.748 * metres_per_mile) / speed_m_per_s_35k ) + (((1-0.748) * metres_per_mile) / speed_m_per_s_40k);
		secondsPerMile[23] = metres_per_mile / speed_m_per_s_40k;
		secondsPerMile[24] = metres_per_mile / speed_m_per_s_40k;
		//Really we should be passing in the split for the final couple of kilometres but for now
		//just continue same pace as 35-40km
		secondsPerMile[25] = metres_per_mile / speed_m_per_s_40k;
		secondsPerMile[26] = metres_per_mile / speed_m_per_s_40k;

		//Adjust all mile times to reach our target time
		var adjustment_factor = targetSeconds / passed_in_finish; //Need to multiply all times by this
		for (mile = 1; mile <= 26; mile++) {
			secondsPerMile[mile] = Math.round(secondsPerMile[mile] * adjustment_factor);
		}
		
		var totalSecondsFirstHalf = 0;
		for (mile = 1; mile <= 13; mile++) {
			totalSecondsFirstHalf += secondsPerMile[mile];
		}
		totalSecondsFirstHalf +=  Math.round((secondsPerMile[14] * 0.109375));
		
		var totalSecondsSecondHalf = 0;
		for (mile = 14; mile <= 26; mile++) {
			totalSecondsSecondHalf += secondsPerMile[mile];
		}
		totalSecondsSecondHalf +=  Math.round((secondsPerMile[26] * 0.109375));
		//alert(totalSecondsSecondHalf);
		var totalSeconds = totalSecondsFirstHalf + totalSecondsSecondHalf;
		
		for (mile = 1; mile <= 26; mile++) {
			//var secondsPerMile = 600;
			
			//Update the value in seconds
			jQuery('td#mile-' + mile + '-split-secs').text(secondsPerMile[mile]);
			//Update the user-visible value (in mm:ss)
			jQuery('td#mile-' + mile + '-split').text(seconds_to_hhmmss(secondsPerMile[mile]));
		}
		//alert(totalSeconds);
		
		//Update the halfway value in seconds
		jQuery('td#halfway-secs').text(totalSecondsFirstHalf);
		//Update the user-visible value (in mm:ss)
		jQuery('td#halfway-hhmmss').text(seconds_to_hhmmss(totalSecondsFirstHalf));
		
		//Update the finish value in seconds
		jQuery('td#finish-secs').text(totalSeconds);
		//Update the user-visible value (in mm:ss)
		jQuery('td#finish-hhmmss').text(seconds_to_hhmmss(totalSeconds));
		
		//Display the table and pacing band button
		jQuery('div#download-pacing-band-div').removeClass("hidden");
		jQuery('#pacing-table').removeClass("hidden");
	});
});

jQuery(document).ready(function(){
	jQuery(".time-m, .time-s").on('blur',function(event) {		
		
		if (!jQuery(this).val()) {
			jQuery(this).val("00");
		}
	});
});

function Select_Even_Splits_Tab() {
	jQuery("#tab-even").addClass('tab-selected');
	jQuery("#tab-from-selection").removeClass('tab-selected');
	jQuery("#tab-negative").removeClass('tab-selected');
	jQuery("#tab-positive").removeClass('tab-selected');
	jQuery("#tab-dans-pacing").removeClass('tab-selected');	

	jQuery("#tab-content-even").addClass('tab-content-active');
	jQuery("#tab-content-from-selection").removeClass('tab-content-active');
	jQuery("#tab-content-from-selection-none-provided").removeClass('tab-content-active');
	jQuery("#tab-content-negative").removeClass('tab-content-active');
	jQuery("#tab-content-positive").removeClass('tab-content-active');
	jQuery("#tab-content-dans-pacing").removeClass('tab-content-active');
		
	jQuery("#pacing-example-sparkline-even").sparkline([
	1200,1200,1200,1200,1200,1200,1200,1200	], {
		type: 'bar', barWidth: '40', chartRangeMin: '900', barColor: '#6D8ACD', height: '70px',
		tooltipFormat: "{{offset:names}}",
		tooltipValueLookups: {names: {0:'0-5km',1:'5-10km',2:'10-15km',3:'15-20km',4:'20-25km',5: '25-30km',6: '30-35km',7: '35-40km'}}	
	});
};

function Select_From_Selection_Tab() {
	jQuery("#tab-even").removeClass('tab-selected');
	jQuery("#tab-from-selection").addClass('tab-selected');
	jQuery("#tab-negative").removeClass('tab-selected');
	jQuery("#tab-positive").removeClass('tab-selected');
	jQuery("#tab-dans-pacing").removeClass('tab-selected');
	
	jQuery("#tab-content-even").removeClass('tab-content-active');
	if (selection_data_provided_in_query)
	{
		jQuery("#tab-content-from-selection").addClass('tab-content-active');
	}
	else
	{
		jQuery("#tab-content-from-selection-none-provided").addClass('tab-content-active');
	}
	jQuery("#tab-content-negative").removeClass('tab-content-active');
	jQuery("#tab-content-positive").removeClass('tab-content-active');
	jQuery("#tab-content-dans-pacing").removeClass('tab-content-active');
			
	
	
	jQuery("#selection-sparkline").sparkline([
	jQuery('td#5k-passed-in-split-s').text(), jQuery('td#10k-passed-in-split-s').text(), jQuery('td#15k-passed-in-split-s').text(), jQuery('td#20k-passed-in-split-s').text(),
	jQuery('td#25k-passed-in-split-s').text(), jQuery('td#30k-passed-in-split-s').text(), jQuery('td#35k-passed-in-split-s').text(), jQuery('td#40k-passed-in-split-s').text()
	], {
		type: 'bar', barWidth: '40', chartRangeMin: '900', barColor: '#6D8ACD', height: '70px',
		tooltipFormat: "{{offset:names}}",
		tooltipValueLookups: {names: {0:'0-5km',1:'5-10km',2:'10-15km',3:'15-20km',4:'20-25km',5: '25-30km',6: '30-35km',7: '35-40km'}}	
	});

};

jQuery(document).ready(function(){
	jQuery("#tab-even").on('click',function(event) {
		Select_Even_Splits_Tab();
	});
});

jQuery(document).ready(function(){
	jQuery("#tab-from-selection").on('click',function(event) {

		Select_From_Selection_Tab();
	});
});

jQuery(document).ready(function(){
	jQuery("#tab-negative").on('click',function(event) {
		jQuery("#tab-even").removeClass('tab-selected');
		jQuery("#tab-from-selection").removeClass('tab-selected');
		jQuery("#tab-negative").addClass('tab-selected');
		jQuery("#tab-positive").removeClass('tab-selected');
		jQuery("#tab-dans-pacing").removeClass('tab-selected');
		
		jQuery("#tab-content-even").removeClass('tab-content-active');
		jQuery("#tab-content-from-selection").removeClass('tab-content-active');
		jQuery("#tab-content-from-selection-none-provided").removeClass('tab-content-active');
		jQuery("#tab-content-negative").addClass('tab-content-active');
		jQuery("#tab-content-positive").removeClass('tab-content-active');
		jQuery("#tab-content-dans-pacing").removeClass('tab-content-active');
		
		jQuery("#pacing-example-sparkline-negative").sparkline([
		1200,1200,1200,1200,1150,1150,1150,1150	], {
		type: 'bar', barWidth: '40', chartRangeMin: '900', barColor: '#6D8ACD', height: '70px',
		tooltipFormat: "{{offset:names}}",
		tooltipValueLookups: {names: {0:'0-5km',1:'5-10km',2:'10-15km',3:'15-20km',4:'20-25km',5: '25-30km',6: '30-35km',7: '35-40km'}}	
		});

	});
});

jQuery(document).ready(function(){
	jQuery("#tab-positive").on('click',function(event) {
		jQuery("#tab-even").removeClass('tab-selected');
		jQuery("#tab-from-selection").removeClass('tab-selected');
		jQuery("#tab-negative").removeClass('tab-selected');
		jQuery("#tab-positive").addClass('tab-selected');
		jQuery("#tab-dans-pacing").removeClass('tab-selected');
		
		jQuery("#tab-content-even").removeClass('tab-content-active');
		jQuery("#tab-content-from-selection").removeClass('tab-content-active');
		jQuery("#tab-content-from-selection-none-provided").removeClass('tab-content-active');
		jQuery("#tab-content-negative").removeClass('tab-content-active');
		jQuery("#tab-content-positive").addClass('tab-content-active');
		jQuery("#tab-content-dans-pacing").removeClass('tab-content-active');
		
		jQuery("#pacing-example-sparkline-positive").sparkline([
		1150,1150,1150,1150,1200,1200,1200,1200	], {
		type: 'bar', barWidth: '40', chartRangeMin: '900', barColor: '#6D8ACD', height: '70px',
		tooltipFormat: "{{offset:names}}",
		tooltipValueLookups: {names: {0:'0-5km',1:'5-10km',2:'10-15km',3:'15-20km',4:'20-25km',5: '25-30km',6: '30-35km',7: '35-40km'}}	
		});

	});
});

jQuery(document).ready(function(){
	jQuery(".increment-time").on('click',function(event) {
		
		//alert(event.target.id);
		var button_id = event.target.id;  //e.g. increment+01-mile-1
		
		var increment_type = button_id.substring(9,12); // '+15' '+01' '-15' '-01'
		//alert(increment_type);
		//Get the id of the td containing the time (in mm:ss) visible to the user
		var time_td_id = button_id.substring(13) + "-split"; //e.g. mile-1-split
		
		//Get the id of the td containing the time (in seconds) which is invisible to the user
		var time_secs_td_id = button_id.substring(13) + "-split-secs";  //e.g. mile-1-split-secs
		
		//Get the current value in seconds
		var time_secs = parseInt(jQuery('td#' + time_secs_td_id).text());		
		
		//Increment it		
		
		var new_time_secs = 0;
		switch(increment_type) {
			case "+15":
				new_time_secs = time_secs + 15;
				break;
			case "+01":
				new_time_secs = time_secs + 1;
				break;
			case "-15":
				new_time_secs = time_secs - 15;
				break;
			case "-01":
				new_time_secs = time_secs - 1;
				break;				
			default:
				new_time_secs = time_secs; //Should never happen
		}
		
		//Update the value in seconds
		jQuery('td#' + time_secs_td_id).text(new_time_secs);
		//Update the user-visible value (in mm:ss)
		jQuery('td#' + time_td_id).text(seconds_to_hhmmss(new_time_secs));
		
		//Recalculate finish time
		/*
		var totalSeconds = 0;
		var halfwaySeconds = 0;
		for (mile = 1; mile <= 26; mile++) {
			var seconds = parseInt(jQuery('td#mile-' + mile + '-split-secs').text());
			totalSeconds = totalSeconds + seconds;;		
			if (mile <= 13)	{
				halfwaySeconds = halfwaySeconds + seconds;
			}
		}*/

		//Update the halfway value in seconds
		var halfwaySeconds = parseInt(jQuery('td#halfway-secs').text());
		//alert(halfwaySeconds);
		switch(increment_type) {
			case "+15":
				halfwaySeconds = halfwaySeconds + 15;
				break;
			case "+01":
				halfwaySeconds = halfwaySeconds + 1;
				break;
			case "-15":
				halfwaySeconds = halfwaySeconds - 15;
				break;
			case "-01":
				halfwaySeconds = halfwaySeconds - 1;
				break;				
			default:
				halfwaySeconds = halfwaySeconds; //Should never happen
		}
		
		//alert(halfwaySeconds);
		//alert(seconds_to_hhmmss(halfwaySeconds));
		jQuery('td#halfway-secs').text(halfwaySeconds);
		//Update the user-visible value (in mm:ss)
		jQuery('td#halfway-hhmmss').text(seconds_to_hhmmss(halfwaySeconds));
		
		//Update the finish value in seconds
		var finishSeconds = parseInt(jQuery('td#finish-secs').text());
		switch(increment_type) {
			case "+15":
				finishSeconds = finishSeconds + 15;
				break;
			case "+01":
				finishSeconds = finishSeconds + 1;
				break;
			case "-15":
				finishSeconds = finishSeconds - 15;
				break;
			case "-01":
				finishSeconds = finishSeconds - 1;
				break;				
			default:
				finishSeconds = finishSeconds; //Should never happen
		}
		
		jQuery('td#finish-secs').text(finishSeconds);
		//Update the user-visible value (in mm:ss)
		jQuery('td#finish-hhmmss').text(seconds_to_hhmmss(finishSeconds));
		
	});
});

jQuery(document).ready(function(){
	jQuery("#download-pacing-band-button").on('click',function(event) {		
		 var params = {
			mile1: jQuery('td#mile-1-split-secs').text(),
			mile2: jQuery('td#mile-2-split-secs').text(),
			mile3: jQuery('td#mile-3-split-secs').text(),
			mile4: jQuery('td#mile-4-split-secs').text(),
			mile5: jQuery('td#mile-5-split-secs').text(),
			mile6: jQuery('td#mile-6-split-secs').text(),
			mile7: jQuery('td#mile-7-split-secs').text(),
			mile8: jQuery('td#mile-8-split-secs').text(),
			mile9: jQuery('td#mile-9-split-secs').text(),
			mile10: jQuery('td#mile-10-split-secs').text(),
			mile11: jQuery('td#mile-11-split-secs').text(),
			mile12: jQuery('td#mile-12-split-secs').text(),
			mile13: jQuery('td#mile-13-split-secs').text(),
			mile14: jQuery('td#mile-14-split-secs').text(),
			mile15: jQuery('td#mile-15-split-secs').text(),
			mile16: jQuery('td#mile-16-split-secs').text(),
			mile17: jQuery('td#mile-17-split-secs').text(),
			mile18: jQuery('td#mile-18-split-secs').text(),
			mile19: jQuery('td#mile-19-split-secs').text(),
			mile20: jQuery('td#mile-20-split-secs').text(),
			mile21: jQuery('td#mile-21-split-secs').text(),
			mile22: jQuery('td#mile-22-split-secs').text(),
			mile23: jQuery('td#mile-23-split-secs').text(),
			mile24: jQuery('td#mile-24-split-secs').text(),
			mile25: jQuery('td#mile-25-split-secs').text(),
			mile26: jQuery('td#mile-26-split-secs').text(),
			halfway: jQuery('td#halfway-hhmmss').text(),
			finish: jQuery('td#finish-hhmmss').text(),
		}; 
				
		var query_string;
		query_string = jQuery.param(params);
		//alert(query_string);
		url = window.location.href;
		download_pacing_band_url =  url.replace("marathon-pacing-calculator","download-pacing-band.php");		
		download_pacing_band_url = download_pacing_band_url + "?" + query_string; //http:// + root_url + "/marathon-pacing-calculator?" + query_string;		
		//window.open(download_pacing_band_url); 
		window.location.href = download_pacing_band_url;
	});
});

function seconds_to_hhmmss(totalSec) {
	/*
	var minutes = Math.floor(seconds / 60);
	var seconds = seconds - minutes * 60;
	var hours = Math.floor(seconds / 3600);
	seconds = seconds - hours * 3600;
	var finalTime = str_pad_left(hours,'0',2)+':'+str_pad_left(minutes,'0',2)+':'+str_pad_left(seconds,'0',2);
	return finalTime;*/
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

function str_pad_left(string,pad,length) {
    return (new Array(length+1).join(pad)+string).slice(-length);
}

