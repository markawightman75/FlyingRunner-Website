<?php

add_shortcode( 'magicactionbox', 'mab_get_actionbox_shortcode_wrap' );
add_shortcode( 'mab_button', 'mab_button_shortcode');

function mab_get_actionbox_shortcode_wrap( $atts = array(), $content = '', $code = '' ){
	
	$notice = '<div class="mab-shortcode-notice">Sorry, the <code>[magicactionbox]</code> shortcode is only available in the <a href="http://www.magicactionbox.com/pricing/?pk_campaign=LITE&pk_kwd=shortcode_notice">Pro version</a>.</div>';
	wp_enqueue_style('mab-extras');
	return $notice;
}

/**
 * Get an action box
 * @param int $actionBoxId - Post ID of action box
 * @param bool $loadAssets - whether to load action box assets or not
 * @param bool $forceShow - default FALSE. USed only if $actionBoxId is not set. 
                            If TRUE will show the action box even if the post
 * it is being shown in has not set the action box to be shown "Manually"
 * @param bool $fallbackToDefaults set to TRUE to let action box show default action box if any
 * @return string - HTML of action box. Or empty string
 */
function mab_get_actionbox( $actionBoxId = null, $loadAssets = true, $forceShow = false, $fallbackToDefaults = false ){
	global $post, $MabBase, $Mab;
	$actionBox = '';
	
	if( is_null( $actionBoxId ) || $actionBoxId === '' ){
		/** Get action box used for a post - if it is specified **/
		
		//get postmeta. NOTE: This is not postmeta from Action Box but
		//from a regular post/CPT where Action Box is to be shown
		$postmeta = $MabBase->get_mab_meta( $post->ID, 'post' );

		$post_action_box = isset($postmeta['post-action-box']) ? $postmeta['post-action-box'] : '';
		$post_action_box_placement = isset($postmeta['post-action-box-placement']) ? $postmeta['post-action-box-placement'] : '';
		
		//return nothing if action box is disabled or if placement is not set to 'manual'
		if( 'none' == $post_action_box ){
			/** Action box is "Disabled" **/
			return '';

		//if action box is not set or is set to "default"
		} elseif( !isset( $post_action_box ) || $post_action_box === '' || $post_action_box == 'default' ){
			if( $fallbackToDefaults && ($forceShow || $post_action_box_placement == 'manual' ) ){
				//get post type
				$post_type = get_post_type( $post );
				$post_type = ( $post_type == 'page' ) ? 'page' : 'single';
				$post_action_box = mab_get_action_box_id_from_context($post_type);
			} else {
				return '';
			}
		} elseif( !$forceShow && $post_action_box_placement != 'manual' ){
			/** Action box must be set to show "manually" **/
			return '';
		}
		
		$actionBoxId = $post_action_box;
	}
	
	$actionBoxObj = new MAB_ActionBox( $actionBoxId );
	
	$actionBox = $actionBoxObj->getActionBox(null, $loadAssets); //also loads assets
	
	return $actionBox;
}


/**
 * MAB Button Shortcode
 */
function mab_button_shortcode($atts = array(), $content = '', $code = ''){
	$notice = '<div class="mab-shortcode-notice">Sorry, the <code>[mab_button]</code> shortcode is only available in the <a href="http://www.magicactionbox.com/pricing/?pk_campaign=LITE&pk_kwd=shortcode_notice">Pro version</a>.</div>';
	wp_enqueue_style('mab-extras');
	return $notice;
}