<?php
/**
 * Based on http://www.aweber.com/faq/questions/396/Can+I+Use+My+Own+Form%3F
 */
$meta = $data;
$redirectUrl = !empty($meta['optin']['thank-you']) ? $meta['optin']['thank-you'] : 'http://www.aweber.com/thankyou-coi.htm?m=text';
$trackingCode = !empty( $meta['optin']['aweber']['tracking-code'] ) ? $meta['optin']['aweber']['tracking-code'] : ''; 

?>
<form method="POST" action="http://www.aweber.com/scripts/addlead.pl">
	<p class="mab-field">
		<label for="mab-name">Name</label>
		<input type="text" id="mab-name" placeholder="Enter your name" name="name" />
	</p>
	<p class="mab-field">
		<label for="mab-email">Email Address</label>
		<input type="email" id="mab-email" placeholder="Enter your email" name="email" />
	</p>
	<input class="mab-submit" type="submit" value="Submit" />

	<?php 
	//might be usefule later
	//<input type="hidden" name="meta_split_id" value="" />
	//<input type="hidden" name="meta_redirect_onlist" value="http://pogidude.com" /> //redirect to url if user is already subscribed to the list 
	//<input type="hidden" name="meta_message" value="1" /> //the follow up message subscribers will first receive when signing up to the list. In most cases, this is set to "1".
	//<input type="hidden" name="meta_forward_vars" value="1" /> set to "1" to forward all submitted fields to the redirect/thank you page using GET method. ?>
	
	<input type="hidden" name="listname" value="<?php echo $meta['optin']['aweber']['list']; ?>" />
	<input type="hidden" name="redirect" value="<?php echo $redirectUrl; ?>" />
	<input type="hidden" name="meta_adtracking" value="<?php echo $meta['optin']['aweber']['tracking-code']; ?>" />
	<input type="hidden" name="meta_message" value="1" />
	
	<?php //maybe have a way to set which fields are required? ?>
	<input type="hidden" name="meta_required" value="name,email" />
	
</form>
