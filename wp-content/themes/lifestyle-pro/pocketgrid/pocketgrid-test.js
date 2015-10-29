jQuery(document).ready(function(){

	jQuery("#test-chart").sparkline([
	1100,1200,1300,1200,1200,1000,900,800	], {
		type: 'bar', barWidth: '44px', chartRangeMin: '900', barColor: '#6D8ACD', height: '70px',
			tooltipFormat: "{{offset:names}}",
			tooltipValueLookups: {
				names: {
					0: '0-5km',
					1: '5-10km',
					2: '10-15km',
					3: '15-20km',
					4: '20-25km',
					5: '25-30km',
					6: '30-35km',
					7: '35-40km'
				}
			}
	});
	
	jQuery("#test-chart2").sparkline([
	1100,1200,1300,1200,1200,1000,900,800	], {
		type: 'bar', barWidth: '44px', chartRangeMin: '900', barColor: '#6D8ACD', height: '70px'
	});
;});

jQuery(document).ready(function(){
	jQuery('.view-splits-button').on('click',function(event) {
		var button_id = event.target.id;  //e.g. view-splits-runner-1
		var splits_table_id = button_id.substring(5);		
		alert(splits_table_id);
		jQuery('#' + button_id).removeClass('visible');
		jQuery('#' + button_id).addClass('hidden');
		jQuery('#' + splits_table_id).removeClass('hidden');
		jQuery('#' + splits_table_id).addClass('visible');
	});
;});

jQuery(document).ready(function(){
	jQuery('a.build-pacing-button').on('click',function(event) {
		//alert(event.target.id);
		var button_id = event.target.id;  //e.g. build-pacing-average
		alert(button_id);
		
		alert(jQuery('#splits-runner-1').find('td#5k-split').text());
	});
});