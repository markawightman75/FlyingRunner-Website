function hex2rgb(hex) {
  var c, o = [], k = 0, m = hex.match(
    /^#(([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})|([0-9a-f])([0-9a-f])([0-9a-f]))$/i);
      
  if (!m) return {r:0,g:0,b:0};
  for (var i = 2, s = m.length; i < s; i++) {
    if (undefined === m[i]) continue;
    c = parseInt(m[i], 16);
    o[k++] = c + (i > 4 ? c * 16 : 0);
  }
  var obj = {r:o[0], g:o[1], b:o[2]};
  return '' + obj.r + ',' + obj.g + ',' + obj.b + '';
}

jQuery(document).ready(function(){
	
	/**
	 * Sales Box Button Type Select
	 */
	jQuery( '#mab-option-button-type-select input' ).change('keyup keydown change', function(event){
		var $selected_button_type = jQuery( '#mab-option-button-type-select input[type=radio]:checked' ).val();
		
		if( $selected_button_type == 'css3' ){
			jQuery( '.mab-option-box.mab-used-for-image-button' ).hide();
			jQuery( '.mab-option-box.mab-used-for-css3-button:hidden' ).show();
		} else if ( $selected_button_type = 'image' ){
			jQuery( '.mab-option-box.mab-used-for-image-button:hidden' ).show();
			jQuery( '.mab-option-box.mab-used-for-css3-button' ).hide();
		}
		
	}).change();
	
	/**
	 * Button preview for CSS3 button design dropdown
	 */
	jQuery( '#mab-button-select' ).bind('keyup keydown change', function(event){
		var $this = jQuery( this );
		var $key = $this.val();
		var $example = jQuery( '#mab-example-button' );
		//remove all classes
		$example.removeClass();
		//recreate classes
		$example.addClass('mab-example-button mab-button-' + $key );
	}).change();
	
	/**
	 * Button preview for CSS3 Button text
	 */
	jQuery( '#mab-button-text' ).bind('blur keyup', function(event){
		var $button = jQuery( '#mab-example-button' );
		var $this = jQuery( this );
		$button.text( $this.val() );
	}).blur();
	
	/**
	 * Optin Settings
	 */
	//show list for selected email provider
	jQuery( '#mab-optin-provider' ).bind('keyup keydown change', function(event){
		
		//get selected value
		var $this = jQuery( this );
		var val = $this.val();
		
		var $div = jQuery( '#mab-' + val + '-settings' );
		
		//hide all dependent containers
		jQuery( '.mab-optin-list-dependent-container' ).hide();
		
		//show related dependent container
		$div.show('fast');

		$optionBoxes = $div.data('option-box');
		if($optionBoxes){
			$optionBoxesArr = $optionBoxes.split(',');
			$hidableBoxes = jQuery('.mab-hidable');
			$toShow = jQuery(); // we'll add elements to show here
			jQuery.each($optionBoxesArr, function(index, className){
				jQuery('.mab-option-'+className).addClass('mab-toshow');
			});

			$hidableBoxes.not(jQuery('.mab-toshow')).hide();
			jQuery('.mab-toshow').show().removeClass('mab-toshow');
		} else {
			// hide all .mab-hidable
			jQuery('.mab-hidable').hide();
		}
		
		//Hide Field Labels options if "Manual" is selected as email provider
		var $fieldLabels = jQuery('.mab-option-field-labels');
		if( val == 'manual'){
			$fieldLabels.hide();
			$fieldLabels.addClass('hide');
		} else {
			if( $fieldLabels.hasClass('hide') ){
				$fieldLabels.removeClass('hide');
				$fieldLabels.show('fast');
			}
		}

	}).change();
	
	//Force Update Optin Lists from Email Provider servers
	jQuery( '.mab-optin-get-list').click( function(){
	
		var $provider = jQuery( '#mab-optin-provider' );
		var $list = jQuery( this ).siblings( '.mab-optin-list' );
		var $feedback = jQuery( this ).siblings( '.ajax-feedback' );
		
		var val = $provider.val();
		$list.attr('disabled','disabled');
		$feedback.css('visibility','visible');
		
		jQuery.post(
			ajaxurl,
			{
				action: 'mab_optin_get_lists',
				provider: val
			},
			function( data, status){
				$list.removeAttr('disabled');
				$list.empty();
				jQuery.each( data, function( i, val ){
					$item = jQuery( '<option value="' +this.id+ '">' +this.name+ '</option>' );
					$list.append( $item );
				});
				$feedback.css('visibility','hidden');
			},
			'json'
		);

		return false;
	});
	
	//Process manual opt in form code into useable format
	jQuery( '#mab-process-manual-optin-code' ).click( function(){
		var $theCode = jQuery( '#mab-optin-manual-code' ).val();
		var $submitValue = jQuery( '#mab-optin-manual-submit-value' ).val();
		var $submitImage = jQuery( '#mab-optin-manual-submit-image' ).val();
		
		jQuery('#mab-optin-process-manual-feedback').css('visibility','visible');
		
		jQuery.post(
			ajaxurl,
			{
				action: 'mab_optin_process_manual_code',
				optinFormCode: $theCode,
				submitValue: $submitValue,
				submitImage : $submitImage
			},
			function( data, status ){
				jQuery('#mab-optin-process-manual-feedback').css('visibility','hidden');
				jQuery('#mab-optin-manual-processed-code').val(data);
			},
			'json'
			
		);
		
		return false;
	});
	
	//Show preview for action Box style on change of dropdown
	jQuery( '#mab-style' ).bind('keyup keydown change', function(){
		var $this = jQuery( this );
		var $selectedStyle = $this.val();
		
		if( $selectedStyle == 'none' ){
			jQuery('#mab-style-preview-heading').hide();
			jQuery('#mab-user-style').hide();
		} else if( $selectedStyle == 'user'){
			jQuery('#mab-style-preview-heading').hide();
			jQuery('#mab-user-style').show();
		} else {
			jQuery('#mab-style-preview-heading').show();
			jQuery('#mab-user-style').hide();
		}
		
		//hide all thumbs
		jQuery( '#mab-style-preview li' ).hide();
		//show corresponding thumb
		jQuery( '#mab-style-' + $selectedStyle + '-preview' ).show();
	}).change();
	
	/**
	 * Side Item Type selection
	 */
	jQuery( '.mab-side-item-type-control input:radio' ).change( function(){

		var $this = jQuery(this);
		var $type = $this.val();
		var $selected_class = '.mab-aside-settings-' + $type;
		var $selected = jQuery( $selected_class );

		if( $this.is(':not(:checked)') )
			return;

		jQuery( '.mab-aside-settings:not(' + $selected_class + ')' ).fadeOut('fast');

		//if selected type is hidden, then show it
		if( $selected.is(':hidden') ){
			$selected.fadeIn('fast');
		}

		if( $type == 'none' )
			jQuery('.mab-aside-settings-general').fadeOut('fast');
		else
			jQuery('.mab-aside-settings-general:hidden').fadeIn('fast');

	}).change();

	/**
	 * Side item Youtube Video Embed Code Creator
	 */
	//when the Generate Embed Code button is clicked
	jQuery( '.mab-embed-code-creator-wrap a.button-secondary' ).click( function(e){
		var $this = jQuery(this);

		var $show_notice = function(){
			$this.siblings('.mab-notice').fadeIn('fast');
		};

		var $url = jQuery('#mab-aside-yt-url').val();

		if( $url == '' ){
			$show_notice();
			return false;
		}

		var $size_select_field = jQuery('.mab-aside-yt-size-select');
		var $size = $size_select_field.val();
		var $height = '';
		var $width = '';

		if( $size == 'custom' ){
			$width = jQuery('#mab-aside-yt-width').val();
			$height = jQuery('#mab-aside-yt-height').val();
		} else {
			var $size_data = $size_select_field.find('option:selected').data();
			$width = $size_data.width;
			$height = $size_data.height;
		}

		//make sure $width and $height is not empty
		if( $width == '' || $height == '' ){
			$show_notice();
			return false;
		}

		var $yt_id = mab_get_youtube_id( $url );
		var $embed_code = mab_create_youtube_embed_code($yt_id, $width, $height);
		
		//add the entries to the fields
		jQuery('#mab-aside-video-embed-code').val($embed_code);
		// jQuery('#mab-aside-width').val($width);
		// jQuery('#mab-aside-height').val($height);

		return false;
	});

	//selecting Youtube Video Size
	jQuery( 'select.mab-aside-yt-size-select' ).bind('keyup keydown change', function(){
		var $this = jQuery(this);
		var $val = $this.val();
		var $fields = jQuery('.mab-aside-yt-size-fields');

		if( $val == 'custom' ){
			//show Youtube Video Size fields
			$fields.fadeIn('fast');
		} else {
			$fields.fadeOut('fast');
		}
	}).change();
	
	/**
	 * Other Stuff
	 */
	//remove the "Preview" button. we don't need it and only confuses other users.
	jQuery( '#post-preview' ).remove();

});
