<?php
	$meta = $data['meta'];
	$assets_url = $data['assets-url'];
	
?>
<div class="mab-option-box">
	<h4><label for="mab-post-action-box"><?php _e('Select Action Box','mab' ); ?></label></h4>
	<p><?php _e( sprintf('Select an Action Box to display. You can specity the default Action Box to use in the <a href="%1s">Main Settings page</a>.', admin_url('admin.php?page=mab-main') ),'mab' ); ?></p>
	<select id="mab-post-action-box" class="large-text" name="postmeta[post-action-box]" >
	
		<option value="none" <?php selected( $meta['post-action-box'], 'none' ); ?> >Disable</option>
		<option value="default" <?php selected( $meta['post-action-box'], 'default' ); ?> >Use Default</option>
		<?php //Get action boxes available
			global $MabBase;
			$action_boxes_obj = get_posts( array( 'numberposts' => -1, 'orderby' => 'title date', 'post_type' => $MabBase->get_post_type() ) );
			foreach( $action_boxes_obj as $action_box ):
		?>
			<option value="<?php echo $action_box->ID; ?>" <?php selected( $meta['post-action-box'], $action_box->ID ); ?> ><?php echo $action_box->post_title; ?></option>
		<?php endforeach; ?>
	</select>
</div>

<div class="mab-option-box">
	<h4>Action Box Placement</h4>
	<p>Where do you want the action box in relation to your post content.</p>

	<ul id="mab-post-action-box-placement-choices" class="mab-placement-choices">
		<li>
			<label for="mab-post-action-box-placement-bottom">
				<img alt="Action Box after the post" src="<?php echo $assets_url; ?>images/actionbox-bottom.png" />
				<br />
				<input id="mab-post-action-box-placement-bottom" value="bottom" type="radio" <?php checked( in_array( $meta['post-action-box-placement'], array( 'bottom', '') ), true ); ?> name="postmeta[post-action-box-placement]" />
				After Content
			</label>
		</li>
		<li>
			<label for="mab-post-action-box-placement-top">
				<img alt="Action Box before the post" src="<?php echo $assets_url; ?>images/actionbox-top.png" />
				<br />
				<input id="mab-post-action-box-placement-top" value="top" type="radio" <?php checked( $meta['post-action-box-placement'], 'top'); ?> name="postmeta[post-action-box-placement]" />
				Before Content
			</label>
		</li>
	</ul>
	You may also use the shortcode <span style="font-weight: bold; font-family: Consolas,Monaco,monospace;">[magicactionbox id="ACTIONBOX ID"]</span> to manually position your magic action box. You may also use the function <span style="font-weight: bold; font-family: Consolas,Monaco,monospace">mab_get_actionbox()</span> in your theme files to do the same thing.
</div>
