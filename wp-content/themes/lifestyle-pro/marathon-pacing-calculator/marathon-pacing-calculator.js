jQuery(document).ready(function(){
	jQuery('#predictor').on('click',function() {
		
		var age_category = jQuery('select#age-category').val();
		var previous_marathons = jQuery('select#previous-marathons').val();
		var target_time = jQuery('input#target-time').val();
		//alert("Age:" + age_category + " Marathons: " + previous_marathons + " Target time: " + target_time);
		
		//TODO: Validate inputs
		//cumulative_splits = new Array();
		
		jQuery.ajax({
		url: ajax_parameters.ajaxurl,
		type: "GET",
		dataType: "JSON",
		data: {
			'action':'example_ajax_request',
			'age_category' : age_category,
			'previous_marathons' : previous_marathons,
			'target_time' : target_time			
		},
		success:function(data) {
			jQuery('span#number-of-runners').text(data.number_of_runners);
			
			var icons_html = '';
			for (i = 0; i < data.number_of_runners; i++) { 
			icons_html = icons_html += "<i class=\"fa fa-female\"></i>";
			}
			jQuery('div#number-of-runners-icons').html(icons_html);
			
			var cumulative_splits = jQuery.parseJSON(data.splits_cumulative);
			//cumulative_splits = data.splits_cumulative;
			jQuery('td#5k-split').text(cumulative_splits['5k']);
			jQuery('td#10k-split').text(cumulative_splits['10k']);
			jQuery('td#15k-split').text(cumulative_splits['15k']);
			jQuery('td#20k-split').text(cumulative_splits['20k']);
			jQuery('td#21k-split').text(cumulative_splits['21k']);
			jQuery('td#25k-split').text(cumulative_splits['25k']);
			jQuery('td#30k-split').text(cumulative_splits['30k']);
			jQuery('td#35k-split').text(cumulative_splits['35k']);
			jQuery('td#40k-split').text(cumulative_splits['40k']);
			
			jQuery('td#finish').text(target_time);		
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
