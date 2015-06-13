<?php
/**
 * Wysija Form
 */

/** TODO: stop if no lists selected? **/
$optinMeta = isset( $data['optin'] ) && is_array( $data['optin'] ) ? $data['optin'] : array();
$wysija = isset( $optinMeta['wysija'] ) && is_array( $optinMeta['wysija'] ) ? $optinMeta['wysija'] : array();
$formIdReal = 'mab-wysija-' . $data['mab-html-id'];
?>
<div id="msg-<?php echo $formIdReal; ?>" class="mab-wysija-msg ajax"></div>
<form id="<?php echo $formIdReal; ?>" method="post" action="#wysija" class="widget_wysija form-valid-sub">
	
	<?php /** Create the input fields **/ ?>
	<?php 
	$fields = isset( $wysija['fields'] ) && is_array( $wysija['fields'] ) ? $wysija['fields'] : array(); 

	$fieldlabels = isset($optinMeta['field-labels']) && is_array( $optinMeta['field-labels'] ) ? $optinMeta['field-labels'] : array( 'email' => __('Email', MAB_DOMAIN), 'fname' => __('First Name', MAB_DOMAIN), 'lname' => __('Last Name', MAB_DOMAIN) );

	$infieldlabels = isset($optinMeta['infield-labels']) && is_array( $optinMeta['infield-labels'] ) ? $optinMeta['infield-labels'] : array( 'email' => __('Enter your email', MAB_DOMAIN), 'fname' => __('Enter your name', MAB_DOMAIN), 'lname' => __('Enter your last name', MAB_DOMAIN) );

	?>
	
	<?php if( in_array( 'firstname', $fields ) ): ?>
	<div class="mab-field wysija-p-firstname mab-field-firstname">
		<?php if( !empty( $fieldlabels['fname']) ) : ?>
		<label for="<?php echo $formIdReal; ?>-firstname"><?php echo $fieldlabels['fname']; ?></label>
		<?php endif; ?>
		<input id="<?php echo $formIdReal; ?>-firstname" class="wysija-firstname validate[required]" type="text" name="wysija[user][firstname]" placeholder="<?php echo $infieldlabels['fname']; ?>" />
	</div>
	<?php endif; ?>
	
	<?php if( in_array( 'lastname', $fields ) ): ?>
	<div class="mab-field wysija-p-lastname mab-field-lastname">
		<?php if( !empty( $fieldlabels['lname']) ) : ?>
		<label for="<?php echo $formIdReal; ?>-lastname"><?php echo $fieldlabels['lname']; ?></label>
		<?php endif; ?>
		<input id="<?php echo $formIdReal; ?>-lastname" class="wysija-lastname validate[required]" type="text" name="wysija[user][lastname]" placeholder="<?php echo $infieldlabels['lname']; ?>" />
	</div>
	<?php endif; ?>
	
	<div class="mab-field wysija-p-email mab-field-email">
		<?php if( !empty( $fieldlabels['email']) ) : ?>
		<label for="<?php echo $formIdReal; ?>-wysija-to"><?php echo $fieldlabels['email']; ?></label>
		<?php endif; ?>
		<input id="<?php echo $formIdReal; ?>-wysija-to" class="wysija-email validate[required,custom[email]]" type="text" name="wysija[user][email]" placeholder="<?php echo $infieldlabels['email']; ?>" />
		<?php /** TODO: ask ben@wysija.com what validate[required,custom[email]] class is supposed to do **/ ?>
	</div>
	
	<?php 
	$submit = isset( $wysija['button-label'] ) ? $wysija['button-label'] : ''; 
	$submitImage = !empty( $wysija['submit-image'] ) ? $wysija['submit-image'] : '';
	?>
	<div class="mab-field mab-field-submit">
		<?php
		if($submitImage):
		?>
		<input type="image" class="mab-optin-submit mab-submit wysija-submit wysija-submit-field" src="<?php echo $submitImage; ?>" alt="Submit">
		<?php else: ?>
		<input class="mab-submit wysija-submit wysija-submit-field" type="submit" value="<?php echo esc_attr($submit); ?>" />
		<?php endif; ?>
	</div>
	
	<?php /** Email Lists field **/
	$listExplode = isset( $wysija['lists'] ) && is_array( $wysija['lists'] ) ? esc_attr( implode(', ', $wysija['lists'] ) ) : '';
	?>
	<input type="hidden" name="wysija[user_list][list_ids]" value="<?php echo $listExplode; ?>" />

	<?php /** Success Message **/
	$success = !empty( $wysija['success-message'] ) ? $wysija['success-message'] : ''; ?>
	<input type="hidden" name="message_success" value="<?php echo esc_attr( $success ); ?>" />
	
	<input type="hidden" name="formid" value="<?php echo $formIdReal; ?>" />
	<input type="hidden" name="action" value="save" />
	<input type="hidden" name="controller" value="subscribers" />
	<?php echo $data['subscriber-nonce']; ?>
	<input type="hidden" value="1" name="wysija-page" />
	<input type="hidden" value="<?php echo wp_create_nonce('wysija_ajax'); ?>" id="wysijax" />
	<?php /** TODO: ask ben@wysija why use id="wysijax" coz may have a problem validating when multiple wysija forms are used **/ ?>
	
</form>
