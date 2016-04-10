
jQuery(document).ready(function(){
    //Runs when page loaded
    jQuery('#findOrderInProgress').hide(); //Spinny
    jQuery('#yourDetails').hide(); 
    jQuery('#printDetails').hide(); 
    
    //Get first name from query string
    var firstName = get_query_parameter('firstname');
    if (firstName != null) {
        jQuery("#helloFirstName").text(firstName);
        jQuery('#hello').show();
    }
    
    jQuery("#findOrderPostcode").focus();
    
    //FOR TESTING
   // retrieve_and_populate_details('A0123', 'CB223DD');
});

/*
jQuery.ajaxSetup({
    beforeSend: function() {
     jQuery('#loader').show();
  },
    complete: function(){
     jQuery('#loader').hide();
  },
  success: function() {}
});
*/

//Find my order button click
jQuery(document).ready(function(){
	jQuery("#findOrderButton").on('click',function(event) {
        findOrderButton_click();
	});
});

jQuery(document).ready(function(){
    jQuery("#findOrderPostcode").keydown(function(e) {
        if (e.keyCode == 13) {
            findOrderButton_click();
        }
    });
});

function findOrderButton_click() {
    var ID = get_query_parameter('id');
    if (ID === null) {
        jQuery('#findOrderError').html('<p>The URL of this page is incorrect. The query string should include an order ID but doesn\'t.</p>');            
        return;
    }

    var postcode = jQuery.trim(jQuery('input#findOrderPostcode').val());
    if (postcode.length === 0) {            
        jQuery('#findOrderError').html('<p>Please enter your postcode</p>');
        jQuery("#findOrderPostcode").focus();
        return;
    }
    retrieve_and_populate_details(ID, postcode);        
}
   
jQuery(document).ready(function(){
	jQuery("#yourDetailsBackButton").on('click',function(event) {
        jQuery("div#yourDetails").hide();
        jQuery("div#intro").show();
        jQuery("div#findOrder").show();
        jQuery("#findOrderInputs").show();
	});
});

jQuery(document).ready(function(){
	jQuery("#yourDetailsNextButton").on('click',function(event) {
        jQuery("div#yourDetails").hide();
        jQuery("div#printDetails").show();
        jQuery('#messageLine1').focus();
	});
});


jQuery(document).ready(function(){
	jQuery("#printDetailsBackButton").on('click',function(event) {
        jQuery("div#yourDetails").show();
        jQuery("div#printDetails").hide();
	});
});

jQuery(document).ready(function(){
	jQuery("#printDetailsNextButton").on('click',function(event) {

        var ID = get_query_parameter('id');
        if (ID === null) {
            jQuery('#findOrderError').html('<p>The URL of this page is incorrect. The query string should include an order ID but doesn\'t.</p>');            
            return;
        }

        var messageLine1 = jQuery('#messageLine1').val();
        if (messageLine1.length === 0) {
            jQuery('div#printDetailsError').html('<p>Please enter at least one line of text<\p>');
            jQuery('div#printDetailsError').show();
            jQuery('#messageLine1').focus();
            return;
        }

        jQuery('#confirmFirstName').text(jQuery('#firstName').text());
        jQuery('#confirmLastName').text(jQuery('#lastName').text());
        jQuery('#confirmAddress').text(jQuery('#address').text());
        jQuery('#confirmPostcode').text(jQuery('#postcode').text());
        jQuery('#confirmPhoneNumber').text(jQuery('#phoneNumber').text());
        jQuery('#confirmMessageLine1').text(jQuery('#messageLine1').val());
        jQuery('#confirmMessageLine2').text(jQuery('#messageLine2').val());
        jQuery('#confirmMessageLine3').text(jQuery('#messageLine3').val());
        if (jQuery('#confirmMessageLine2').text().length > 0) {jQuery('#confirmMessageLine2').show();}
        if (jQuery('#confirmMessageLine3').text().length > 0) {jQuery('#confirmMessageLine3').show();}
        jQuery("div#yourDetails").hide();
        jQuery("div#printDetails").hide();
        jQuery("div#confirmDetailsError").hide();
        jQuery("div#confirmDetails").show();
        jQuery("#submitDetailsButton").focus();

	});
});


jQuery(document).ready(function(){
	jQuery("#confirmDetailsBackButton").on('click',function(event) {
        jQuery("div#printDetails").show();
        jQuery("div#confirmDetails").hide();
        jQuery("div#printDetailsError").hide();
	});
});
   
jQuery(document).ready(function(){
	jQuery("#submitDetailsButton").on('click',function(event) {

        var ID = get_query_parameter('id');
        var postcode = jQuery('#confirmPostcode').text();
        submit_updated_details(ID, postcode);
	});
});

function retrieve_and_populate_details(ID, postcode) { 
    jQuery("#findOrderInProgress").show();
    jQuery("#findOrderInputs").hide();
    jQuery.ajax({
		url: ajax_parameters.ajaxurl,
		type: "GET",
		dataType: "JSON",
		data: {
			'action':'retrieve_personalised_print_details_from_ID',
            'ID':ID,
            'postcode':postcode
		},
		success:function(data) {
			switch (data.status) {
                case 'success':
                    jQuery('#firstName').text(data.firstName);
                    jQuery('#lastName').text(data.lastName);
                    jQuery('#address').text(data.address);
                    jQuery('#postcode').text(data.postcode);
                    jQuery('#phoneNumber').text(data.phoneNumber);
                    jQuery('#messageLine1').val('');
                    jQuery('#messageLine2').val('');
                    jQuery('#messageLine3').val('');
                    jQuery('#messageLine4').val('');
                    jQuery('#messageLine5').val('');
                    jQuery('#messageLine6').val('');

                    jQuery('div#yourDetails').show();
                    //jQuery('div#printDetails').show();
                    jQuery('div#findOrder').hide();
                    jQuery('div#intro').hide();
                    jQuery("#messageLine1").focus();
                    jQuery("#findOrderInProgress").hide();
                    jQuery("#yourDetailsNextButton").focus();
                    
                    break;
                case 'error':
                    //An expected error (e.g. postcode didn't match the one in the db) or an unexpected error (e.g. db connection error)                    
                    var userFriendlyMessage = '';
                    switch (data.message) {
                        case 'Incorrect postcode':
                            userFriendlyMessage = 'The postcode you entered doesn\'t match the one we have recorded.'; //TODO: Improve this message
                            break;
                        case 'No matching order':
                            userFriendlyMessage = 'We don\'t have an order recorded with the ID specified in the page URL'; //TODO: Improve this message
                            break;
                        default:
                            userFriendlyMessage = data.message;
                    }
                    jQuery('#findOrderError').html('<p>' + userFriendlyMessage + '</p>');
                    jQuery("#findOrderInProgress").hide();
                    jQuery("#findOrderInputs").show();
                    jQuery("#findOrderPostcode").focus();
                    break; 
            }
            
			
		},
		error: function(data){
            //Ajax call failed, e.g. network problem (test with network disconnected)
			//TODO: Make it clear this is an error            
			jQuery('#findOrderError').html('<p>We are sorry. We could not contact our servers to find your order. Please check you have an internet connection and try again.<\p>');          
            jQuery("#findOrderInProgress").hide();
            jQuery("#findOrderInputs").show();
            jQuery("#findOrderPostcode").focus();
        }
		});
}

function submit_updated_details(ID, postcode) {
    //Get details from inputs
    var messageLine1 = jQuery('#confirmMessageLine1').text();
    var messageLine2 = jQuery('#confirmMessageLine2').text();
    var messageLine3 = jQuery('#confirmMessageLine3').text();
    
    jQuery.ajax({
        url: ajax_parameters.ajaxurl,
        type: "GET",
        dataType: "JSON",
        data: {
            'action':'update_personalised_print_details',
            'ID':ID,
            'postcode':postcode,
            'messageLine1':messageLine1,
            'messageLine2':messageLine2,
            'messageLine3':messageLine3,
        },
        success:function(data) {
            switch (data.status) {
                case 'success':
                    jQuery('div#orderSubmitted').show();
                    jQuery('div#confirmDetails').hide();
                    //jQuery('div#detailsMessage').html('<p>Your details have been recorded.</p>');
                    break;
                case 'error':
                    //An expected error (e.g. postcode didn't match the one in the db) or an unexpected error (e.g. db connection error)                    
                    var userFriendlyMessage = '';
                    switch (data.message) {
                        case 'xxx':
                            userFriendlyMessage = 'The xxx'; //TODO: Improve this message
                            break;
                        default:
                            userFriendlyMessage = data.message;
                    }
                    //TODO: Make it clear this is an error
                    jQuery('div#confirmDetailsError').html('<p>' + userFriendlyMessage + '<\p>');
                    jQuery('div#confirmDetailsError').show();
                    break; 
            }
        },
        error: function(data){
            //Ajax call failed, e.g. network problem (test with network disconnected)
            //TODO: Make it clear this is an error
            jQuery('div#confirmDetailsError').html('<p>We are sorry. We could not contact our servers to submit your details. Please check you have an internet connection and try again.<\p>');
            jQuery('div#confirmDetailsError').show();

        }
    });				                
}

//Returns string containing parameter value or null
function get_query_parameter(key) {
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
		
	if (typeof qs[key] != 'undefined')
	{
		return qs[key];
	}
	else
	{
		return null;
	}        

}