<?php
/**
 * Based on http://www.aweber.com/faq/questions/396/Can+I+Use+My+Own+Form%3F
 */
$meta = $data;
$optinMeta = $data['optin'];
//$redirectUrl = !empty($meta['optin']['thank-you']) ? $meta['optin']['thank-you'] : 'http://www.aweber.com/thankyou-coi.htm?m=text';
//$trackingCode = !empty( $meta['optin']['mailchimp']['tracking-code'] ) ? $meta['optin']['mailchimp']['tracking-code'] : ''; 

//$actionUrl = !empty( $meta['optin']['mailchimp']['list-data']['subscribe_url_long'] ) ? $meta['optin']['mailchimp']['list-data']['subscribe_url_long'] : '';
$actionUrl = !empty( $meta['optin']['mailchimp']['form-action-url'] ) ? $meta['optin']['mailchimp']['form-action-url'] : '';
$submitValue = !empty( $meta['optin']['mailchimp']['submit-value'] ) ? $meta['optin']['mailchimp']['submit-value'] : 'Submit';
$submitImage = !empty( $meta['optin']['mailchimp']['submit-image'] ) ? $meta['optin']['mailchimp']['submit-image'] : '';

//labels
$fieldlabels = isset($optinMeta['field-labels']) && is_array( $optinMeta['field-labels'] ) ? $optinMeta['field-labels'] : array( 'email' => __('Email', MAB_DOMAIN), 'fname' => __('First Name', MAB_DOMAIN), 'lname' => __('Last Name', MAB_DOMAIN) );

$infieldlabels = isset($optinMeta['infield-labels']) && is_array( $optinMeta['infield-labels'] ) ? $optinMeta['infield-labels'] : array( 'email' => __('Enter your email', MAB_DOMAIN), 'fname' => __('Enter your name', MAB_DOMAIN), 'lname' => __('Enter your last name', MAB_DOMAIN) );
?>
<form method="POST" action="<?php echo $actionUrl; ?>">
	<div class="mab-field mab-field-name">
		<?php if( !empty( $fieldlabels['fname']) ) : ?>
		<label for="mab-name"><?php echo $fieldlabels['fname']; ?></label>
		<?php endif; ?>
		<input type="text" id="mab-name" placeholder="<?php echo $infieldlabels['fname']; ?>" name="FNAME" />
	</div>
	<div class="mab-field mab-field-email">
		<?php if( !empty( $fieldlabels['email']) ) : ?>
		<label for="mab-email"><?php echo $fieldlabels['email']; ?></label>
		<?php endif; ?>
		<input type="email" id="mab-email" placeholder="<?php echo $infieldlabels['email']; ?>" name="EMAIL" />
	</div>
	<div class="mab-field mab-field-submit">
		<?php
		if($submitImage):
		?>
		<input type="image" class="mab-optin-submit mab-submit" src="<?php echo $submitImage; ?>" alt="Submit">
		<?php else: ?>
		<input class="mab-submit" type="submit" value="<?php echo $submitValue; ?>" />
		<?php endif; ?>
	</div>
	<div class="clear"></div>
</form>
