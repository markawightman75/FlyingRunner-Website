$(document).ready(function(){
$('#predictor').on('click',function() {
	
	var name = $('input#name').val();
	if ($.trim(name) != '') {
		//alert(1);
		var fruit = $.trim(name);
		var gender;
		
		if ($('#gender-male').is(':checked')) {
			gender = 'male';
		}
		else
		{
			gender = 'female';
		}
		alert(gender);
		
		//alert(male);
		$.ajax({
        url: MyAjax.ajaxurl,
		type: "GET",
		dataType: "JSON",
        data: {
            'action':'example_ajax_request',
            'gender' : gender
        },
        success:function(data) {
			$('div#name-data').text(data.time);		
            // This outputs the result of the ajax request
            //console.log(data);
			//alert(data);
        },
        error: function(errorThrown){
            console.log(errorThrown);
			//TODO: Log error to screen
        }
		});   
		
		//$.post('querydb.php', {name: name}, function(data) {
		//	$('div#name-data').text('hello');		
		//});		
	}
});
});
//$body = $("body");

//$(document).on({
//    ajaxStart: function() { $body.addClass("loading");    },
//     ajaxStop: function() { $body.removeClass("loading"); }    
//});