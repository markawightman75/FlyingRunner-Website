<?php

/**
 * Register action box
 * 
 * Form the array $box like this:
 * $box = array(
 *    'type'        => 'action-box-type', //use letters, numbers, underscore, dash only
 *    'name'        => 'Human readable name',
 *    'description' => 'Short description about the action box type'
 * );
 * 
 * @param  array  $box required
 * @return none
 */
function mab_register_action_box( $box ){
	global $MabBase;
	$MabBase->register_action_box_type( $box );
}

/**
 * @param string $context - single | page | default
 * @return int|bool Post ID of Action Box or FALSE if actionbox is not specified for a context
 */
function mab_get_action_box_id_from_context( $context = 'default' ){
	global $MabBase;
	
	$settings = MAB_Utils::getSettings();
	$globalMab = $settings['global-mab'];
	
	$actionBoxId = '';
	
	$default = ( isset( $globalMab['default']['actionbox'] ) && $globalMab['default']['actionbox'] != 'none' ) ? $globalMab['default']['actionbox'] : '';
	
	$defaultPlacement = $placement = isset( $globalMab['default']['placement'] ) ? $globalMab['default']['placement'] : 'bottom';

	switch( $context ){
		case 'single':
			global $post;
			if( !is_object( $post ) ){
				//something's wrong
				$actionBoxId = '';
				break;
			}
			
			/** Single Post Setting **/
			$postmeta = $MabBase->get_mab_meta( $post->ID, 'post' );
			$singleActionBoxId = isset($postmeta['post-action-box']) ? $postmeta['post-action-box'] : '';
			
			//if $pageActionBoxId is empty string, then action box is not yet set
			if( '' !== $singleActionBoxId && 'default' != $singleActionBoxId ){
				//specific action box set for Post
				$actionBoxId = $singleActionBoxId;
				$placement = isset( $postmeta['post-action-box-placement'] ) ? $postmeta['post-action-box-placement'] : $defaultPlacement;
				
			} else {
				//use global settings
				
				/** Global Single Post Setting **/
				$actionBoxId = isset( $globalMab['post']['actionbox'] ) ? $globalMab['post']['actionbox'] : 'default';
				$placement = isset( $globalMab['post']['placement'] ) ? $globalMab['post']['placement'] : $defaultPlacement;
				
				/** Global Category Setting - will override Global Single Post Setting **/
				$terms = get_the_terms( $post->ID, 'category' );
				if( $terms && !is_wp_error( $terms ) ){
					foreach( $terms as $term ){
						//catch the first category set
						if( isset( $globalMab['category'][$term->term_id]['actionbox'] ) AND $globalMab['category'][$term->term_id]['actionbox'] != 'default' ){
							$actionBoxId = $globalMab['category'][$term->term_id]['actionbox'];
							$placement = $globalMab['category'][$term->term_id]['placement'];
							break; //break out of foreach loop
						}//endif
					}//endforeach
				}//endif
				
			}//endif
			
			break;
		
		case 'page':
			global $post;
			if( !is_object( $post ) ){
				//something's wrong
				$actionBoxId = '';
			}
			
			$postmeta = $MabBase->get_mab_meta( $post->ID, 'post' );
			$pageActionBoxId = isset($postmeta['post-action-box']) ? $postmeta['post-action-box'] : '';
			
			//if $pageActionBoxId is empty string, then action box is not yet set
			if( '' !== $pageActionBoxId && 'default' != $pageActionBoxId ){
				//specific action box set for Page
				$actionBoxId = $pageActionBoxId;
				$placement = isset( $postmeta['post-action-box-placement'] ) ? $postmeta['post-action-box-placement'] : $defaultPlacement;
				
			} else {
				//use global setting
				$actionBoxId = isset( $globalMab['page']['actionbox'] ) ? $globalMab['page']['actionbox'] : 'default';
				$placement = isset( $globalMab['page']['placement'] ) ? $globalMab['page']['placement'] : $defaultPlacement;
				
			}
			
			break;
		case 'front_page':
			global $wp_query;
			
			//make sure that the front page is set to display a static page
			if( !is_page() ){
				break;
			}
			
			$post = $wp_query->get_queried_object();
			if( !is_object( $post ) ){
				//something's wrong
				$actionBoxId = '';
			}
			
			$postmeta = $MabBase->get_mab_meta( $post->ID, 'post' );
			$pageActionBoxId = isset($postmeta['post-action-box']) ? $postmeta['post-action-box'] : '';
			
			//if $pageActionBoxId is empty string, then action box is not yet set
			if( '' !== $pageActionBoxId && 'default' != $pageActionBoxId ){
				//specific action box set for Page
				$actionBoxId = $pageActionBoxId;
				$placement = isset( $postmeta['post-action-box-placement'] ) ? $postmeta['post-action-box-placement'] : $defaultPlacement;
				
			} else {
				//use global setting
				$actionBoxId = isset( $globalMab['page']['actionbox'] ) ? $globalMab['page']['actionbox'] : 'default';
				$placement = isset( $globalMab['page']['placement'] ) ? $globalMab['page']['placement'] : $defaultPlacement;
				
			}
			
			break;
		case 'tag':
		case 'archive':
		case 'blog':
		case 'category':
		default: 
			$actionBoxId = '';
			break;
	}//endswitch
	
	if( $actionBoxId == 'default' ){
		$actionBoxId = $default;
	} elseif( $actionBoxId == 'none' ){
		$actionBoxId = '';
	}
	
	//return array( 'id' => $actionBoxId, 'placement' => $placement );
	return $actionBoxId;
}


/**
 * Returns the value of an array index from a key-value array. Will check if an array index is set. 
 * By default, will return an empty string if not set.
 *
 * @param array $array array to get value from
 * @param string $index key index
 * @param string $empty_value value to return if array key is not set
 * @return mixed value of array index
 */
function mab_array_index_value($array, $index, $empty_value = ''){
	return isset($array[$index]) ? $array[$index] : $empty_value;
}