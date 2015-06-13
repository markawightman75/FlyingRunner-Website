<?php
global $MabAdmin;
?>
<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php _e('Add New Action Box', 'mab' ); ?></h2>

	<h3>Select Action Box Type</h3>

	<p>
		<?php _e('Choose the type of Magic Action Box you wish to create. You cannot change the type of an Action Box after creating it, so choose wisely.', 'mab' )?>
	</p>
	<ul class="mab-type-choice">
		<?php
		global $post, $MabBase;
		$types = $MabBase->get_registered_action_box_types('all');
		foreach($types as $type => $info) {
			$enabled = !empty( $info['status'] ) && $info['status'] == 'enabled' ? true : false;
			
			if( $enabled ){ ?>
			<li>
				<?php
				$query_args = array( 'action' => 'edit', 'post' => $post->ID, 'action_box_set' => 1, 'action_box_type' => $type );
				$url = add_query_arg( $query_args, admin_url('post.php') );
				$url = wp_nonce_url( $url, 'action_box_set' );
				?>

				<?php if( $type === 'optin' || $type == 'gforms' ) : ?>
					<a class="mab-type-title" href="<?php echo esc_attr($url); ?>"><?php esc_html_e($info['name'], 'mab' ); ?></a><br />
					<?php esc_html_e($info['description'], 'mab' ); ?>
				<?php else: ?>
					<span class="disabled-type"><?php esc_html_e($info['name'], 'mab' ); ?></span><br />
					<em><small>(Available in <a href="http://www.magicactionbox.com/features/?pk_campaign=LITE&pk_kwd=addScreen" target="_blank">Magic Action Box Pro</a>)</small></em><br />
					<?php esc_html_e($info['description'], 'mab' ); ?><br />
					
				<?php endif; ?>
			</li>
			<?php } else { ?>
			<li>
				<span class="disabled-type"><?php esc_html_e($info['name'], 'mab' ); ?><?php echo apply_filters('mab_disabled_type_text', ' (Disabled)' ); ?></span><br />
				<?php echo $info['description']; ?>
			</li>
			<?php }//endif
		}
		?>
	</ul>
<?php /*
	<h3>Coming Soon</h3>
	<ul class="mab-type-choice">
		<li>
			<span class="disabled-type">Gravity Forms (New)</span><br />
			<!--em><small>Coming Soon</small></em><br /-->

			Combine Gravity Forms with Magic Action Box's slick designs.<br />
		</li>
	</ul>
*/ ?>

	<div class="mab-ad updated">
		<a href="http://www.magicactionbox.com/features/?pk_campaign=LITE&pk_kwd=addScreen" target="_blank"><img src="<?php echo MAB_ASSETS_URL . 'images/adbox.png'; ?>" alt="" class="mab-ad-img" width="200"/></a>
		<div class="mab-ad-content">
			<p>Get additional action box types by getting <a href="http://www.magicactionbox.com/features/?pk_campaign=LITE&pk_kwd=addScreen" target="_blank">Magic Action Box Pro</a>.</p>
			<h3>Some of the features available in the full version:</h3>
			<ul>
				<li>Use your own custom image for submit button in Opt-In Form action box type.</li>
				<li>Display random action boxes on every page load.</li>
				<li>Show a video (not just images) with your action boxes</li>
				<li>Use Contact Form action box type with Contact Form 7.</li>
				<li>Use Sales Box action box type.</li>
				<li>Use Share Box action box type.</li>
				<li>Future action box types not available in the lite version.</li>
				<li>Use shortcodes and template tags.</li>
				<li>VIP Customer Support</li>
				<li>More pre-designed styles</li>
				<li>Create and upload your own buttons (for Sales Boxes)</li>
				<li><a href="http://www.magicactionbox.com/features/?pk_campaign=LITE&pk_kwd=addScreen" target="_blank">And more...</a></li>
			</ul>

			<h3>Upcoming Features</h3>
			<ul>
				<li>Use FaceBook Connect to allow visitors to subscribe to your mailing list.</li>
			</ul>

		</div>
	</div><!-- .mab-ad -->

	<h3>Upcoming Features</h3>
	<ul>
		<li>Use FaceBook Connect to allow visitors to subscribe to your mailing list.</li>
	</ul>

</div><!-- .wrap -->
