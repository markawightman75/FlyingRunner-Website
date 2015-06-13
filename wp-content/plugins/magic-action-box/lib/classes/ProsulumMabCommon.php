<?php
/**
 * TODO:
 *
 * - complete getMetaBoxViewTemplate() function
 */

class ProsulumMabCommon{
	
	/**
	 * @param string $filename reference a file relative to the MAB_VIEWS_DIR
	 * @param mixed $data data to pass on to the view file
	 * @return string contents of the file
	 */
	public static function getView( $filename, $data = null ){
		
		if( empty( $filename ) ) return '';
		
		//TODO: allow custom views to override standard views
		
		
		$filename = trailingslashit( MAB_VIEWS_DIR ) . $filename;
		
		//check if file exists
		if( !file_exists( $filename ) ) return '';
		
		ob_start();
		include( $filename );
		$contents = ob_get_contents();
		ob_end_clean();
		
		return $contents;
		
	}
	
	/**
	 * Get View Template for MetaBoxes
	 */
	public static function getMetaBoxViewTemplate( $template = '' ){
		
		$filenames = array(
			
		);
	}
	
	/**
	 * Get Settings
	 *
	 * Wrapper function for ProsulumMabBase->get_settings() function
	 *
	 * @return array Magic Action Box settings
	 */
	public static function getSettings(){
		global $MabBase;
		return $MabBase->get_settings();
	}
	
	/**
	 * Get Action Box Types
	 * @param string $type - unique name/type of action box
	 * @return array|bool - return array of data for action box depending specified through $type or all action boxes if no parameter is passed. Will return FALSE if specified $type is not found
	 */
	public static function getActionBox( $type = null ){
		$boxes = array();
		
		/** TODO: make a buildActionBoxArray() function **/
		
		//Optin
		$boxes['optin'] = array( 'type' => 'optin', 'name' => __('Optin Form', 'mab' ), 'description' => __('An opt in form is used to build your email list.','mab'), 'template' => 'optin' );
		
		//Sales Box
		$boxes['sales-box'] = array( 'type' => 'sales-box', 'name' => __('Sales Box', 'mab' ), 'description' => __('A simple sales box. Use it to lead visitors to your sales page.','mab'), 'template' => 'sales-box' );
		
		//Social Media
		$boxes['share-box'] = array( 'type' => 'share-box', 'name' => __('Share Box', 'mab' ), 'description' => __('Action box made for sharing your content','mab'), 'template' => 'share-box' );
		
		if( !is_null( $type ) ){
			if( isset( $boxes[$type] ) ){
				//return specified action box
				return $boxes[$type];
			} else {
				//specified action box type does not exist
				return false;
			}
		} else {
			//return all action boxes
			return $boxes;
		}
	}
	
	/**
	 * Get Action Box Styles
	 */
	public static function getStyles(){
		$styles = array(
			'default' => array( 'id' => 'default', 'name' => 'Default', 'description' => 'Starter style for your action box.' ),
			'dark' => array( 'id' => 'dark', 'name' => 'Dark', 'description' => '' ),
			'royalty' => array( 'id' => 'royalty', 'name' => 'Royalty', 'description' => ''),
			'pink' => array( 'id' => 'pink', 'name' => 'Pink', 'description' => ''),
			'none' => array( 'id' => 'none', 'name' => 'None', 'description' => 'Don\'t use any style designs. Useful if you wish to roll out your own design.')
		);
		
		return $styles;
	}
	
	/**
	 * Get a single action box style
	 */
	public static function getSingleStyle( $id ){
		$styles = self::getStyles();
		if( isset( $styles[$id] ) ){
			return $styles[$id];
		}
	}
	
	/**
	 * Get Single Action Box Style Resource
	 * @param string $id unique id for the action box style
	 * @param string $type thumb | css
	 * @return url - url to the resource
	 */
	public static function getStyleResource( $id, $type ){
		$style = self::getSingleStyle( $id );
		
		$styleUrl = trailingslashit( MAB_STYLES_URL . "{$id}/" );
		$styleDir = trailingslashit( MAB_STYLES_DIR . "{$id}/" );
		
		/* TODO: Allow to override location of resources */
		switch( $type ){
			case 'css':
			case 'stylesheet':
				if( file_exists( $styleDir . 'style.css' ) ){
					return $styleUrl . 'style.css';
				} else {
					return '';
				}
				break;
			case 'thumb':
			case 'image':
				if( file_exists( $styleDir . 'thumb.png' ) ){
					return $styleUrl . 'thumb.png';
				} else {
					return '';
				}
				break;
			default:
				return '';
				break;
		}
	}
	
	/**
	 * Deletes all the files in the temp directory under 
	 * wp-content/uploads/magic-action-box/
	 */
	public static function clearCacheDir( $ext = '*' ){
		mab_make_stylesheet_path_writable();
		$dir = mab_get_stylesheet_location('path');
		foreach( glob( $dir . "*.{$ext}" ) as $file ){
			unlink( $file );
		}
	}
}
