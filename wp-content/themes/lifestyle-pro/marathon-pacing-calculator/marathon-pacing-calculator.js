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

	/*alert (qs['km5']);
	alert (qs['km10']);
	alert (qs['km15']);
	alert (qs['km20']);*/
	
	jQuery('td#5k-passed-in-split-s').text(qs['km5']);
	jQuery('td#10k-passed-in-split-s').text(qs['km10']);
	jQuery('td#15k-passed-in-split-s').text(qs['km15']);
	jQuery('td#20k-passed-in-split-s').text(qs['km20']);
	jQuery('td#25k-passed-in-split-s').text(qs['km25']);
	jQuery('td#30k-passed-in-split-s').text(qs['km30']);
	jQuery('td#35k-passed-in-split-s').text(qs['km35']);
	jQuery('td#40k-passed-in-split-s').text(qs['km40']);

	//TODO: Check all parameters are present	
	//alert(qs['km5']);
	if (typeof qs['km5'] != 'undefined')
	{
		Select_From_Selection_Tab();
	}
	else
	{
		Select_Even_Splits_Tab();
	}
});

function Select_Even_Splits_Tab() {
	jQuery("#tab-even").addClass('tab-selected');
		jQuery("#tab-from-selection").removeClass('tab-selected');
		jQuery("#tab-negative").removeClass('tab-selected');
		jQuery("#tab-positive").removeClass('tab-selected');
		
		jQuery("#tab-content-even").addClass('tab-content-active');
		jQuery("#tab-content-from-selection").removeClass('tab-content-active');
		jQuery("#tab-content-negative").removeClass('tab-content-active');
		jQuery("#tab-content-positive").removeClass('tab-content-active');
};

function Select_From_Selection_Tab() {
	jQuery("#tab-even").removeClass('tab-selected');
	jQuery("#tab-from-selection").addClass('tab-selected');
	jQuery("#tab-negative").removeClass('tab-selected');
	jQuery("#tab-positive").removeClass('tab-selected');
	
	jQuery("#tab-content-even").removeClass('tab-content-active');
	jQuery("#tab-content-from-selection").addClass('tab-content-active');
	jQuery("#tab-content-negative").removeClass('tab-content-active');
	jQuery("#tab-content-positive").removeClass('tab-content-active');
	
			
	
	
	jQuery("#selection-sparkline").sparkline([
	jQuery('td#5k-passed-in-split-s').text(), jQuery('td#10k-passed-in-split-s').text(), jQuery('td#15k-passed-in-split-s').text(), jQuery('td#20k-passed-in-split-s').text(),
	jQuery('td#25k-passed-in-split-s').text(), jQuery('td#30k-passed-in-split-s').text(), jQuery('td#35k-passed-in-split-s').text(), jQuery('td#40k-passed-in-split-s').text()
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
		
		jQuery("#tab-content-even").removeClass('tab-content-active');
		jQuery("#tab-content-from-selection").removeClass('tab-content-active');
		jQuery("#tab-content-negative").addClass('tab-content-active');
		jQuery("#tab-content-positive").removeClass('tab-content-active');
	});
});

jQuery(document).ready(function(){
	jQuery("#tab-positive").on('click',function(event) {
		jQuery("#tab-even").removeClass('tab-selected');
		jQuery("#tab-from-selection").removeClass('tab-selected');
		jQuery("#tab-negative").removeClass('tab-selected');
		jQuery("#tab-positive").addClass('tab-selected');
		
		jQuery("#tab-content-even").removeClass('tab-content-active');
		jQuery("#tab-content-from-selection").removeClass('tab-content-active');
		jQuery("#tab-content-negative").removeClass('tab-content-active');
		jQuery("#tab-content-positive").addClass('tab-content-active');
	});
});





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
		jQuery('td#halfway-secs').text(secondsPerMile * 13);
		//Update the user-visible value (in mm:ss)
		jQuery('td#halfway-hhmmss').text(seconds_to_hhmmss((secondsPerMile * 13)));
		
		//Update the finish value in seconds
		jQuery('td#finish-secs').text(totalSeconds);
		//Update the user-visible value (in mm:ss)
		jQuery('td#finish-hhmmss').text(seconds_to_hhmmss(totalSeconds));
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
		var totalSeconds = 0;
		var halfwaySeconds = 0;
		for (mile = 1; mile <= 26; mile++) {
			//alert(mile + " " + jQuery('td#mile-' + mile + '-split-secs').text());
			var seconds = parseInt(jQuery('td#mile-' + mile + '-split-secs').text());
			totalSeconds = totalSeconds + seconds;;		
			if (mile <= 13)	{
				halfwaySeconds = halfwaySeconds + seconds;
			}
		}

		//Update the halfway value in seconds
		jQuery('td#halfway-secs').text(halfwaySeconds);
		//Update the user-visible value (in mm:ss)
		jQuery('td#halfway-hhmmss').text(seconds_to_hhmmss(halfwaySeconds));
		
		//Update the finish value in seconds
		jQuery('td#finish-secs').text(totalSeconds);
		//Update the user-visible value (in mm:ss)
		jQuery('td#finish-hhmmss').text(seconds_to_hhmmss(totalSeconds));
		
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

