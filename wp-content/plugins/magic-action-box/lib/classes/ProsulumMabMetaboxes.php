<?php
/**
 * Meta boxes for the Action Box post type
 **/
class ProsulumMabMetaBoxes{
	private $_post = null;
	
	function __construct( $post ){
		$this->_post = $post;

		/* Action hooks */
		add_action('mab_add_meta_boxes', array( $this, 'loadActionBoxGeneralMetaboxes'));
		add_action('mab_add_meta_boxes', array( $this, 'loadActionBoxSpecificMetaboxes'));
		add_action('mab_add_meta_boxes_semi', array( $this, 'loadCustomCssSettingsBox' ));

		do_action('mab_init_meta_boxes', $this, $post);
	}

	function initMetaboxes(){
		global $MabBase;
		$post = $this->_post;

		//get post type name
		$post_type = $MabBase->get_post_type();
		
		//remove unneeded metaboxes
		remove_meta_box( 'commentstatusdiv' , $MabBase->get_post_type() , 'normal' ); //removes comments status
		remove_meta_box( 'commentsdiv' , $MabBase->get_post_type() , 'normal' ); //removes comments
		
		//remove Publish metabox as we will be replacing this with out own
		remove_meta_box('submitdiv', null, 'side');
		add_meta_box('mab-publish', __('Save Action Box', MAB_DOMAIN), array($this,'saveBox'), $MabBase->get_post_type(), 'side', 'high' );
		
		add_meta_box('mab-duplicate', __('Duplicate Action Box', MAB_DOMAIN), array( $this, 'duplicateBox' ), $MabBase->get_post_type(), 'side', 'high' );
		
		//general design settings
		//add_meta_box( 'mab-general-design-settings', __('Design Setting: General', 'mab' ), array( &$this, 'generalDesignSettings' ), $post_type, 'advanced', 'high' );
		
		//heading design settings
		//add_meta_box( 'mab-heading-design-settings', __('Design Setting: Headings', 'mab' ), array(&$this, 'headingDesignSettings' ), $post_type, 'advanced', 'high' );
		
		//show metaboxes specific to action box type
		$type = $MabBase->get_actionbox_type( $post->ID );
		
		//load metaboxes specific to the action box type
		do_action( 'mab_add_meta_boxes', $type, $this );
		do_action( 'mab_add_meta_boxes-' . $type, $this );

		do_action( 'mab_add_meta_boxes_semi', $type, $this );
		do_action( 'mab_add_meta_boxes_advanced', $type, $this );
	}

	/**
	 * Load metabox for all action boxes
	 * 
	 * @param  string $type type of action box
	 * @return none
	 */
	function loadActionBoxGeneralMetaboxes( $type ){
		global $MabBase;
		$post_type = $MabBase->get_post_type();
		add_meta_box( 'mab-actionbox-settings', __('Action Box: General Settings', 'mab' ), array( $this, 'actionBoxSettings' ), $post_type, 'normal', 'high' );
	}
	
	/**
	 * Load metaboxes specific to an action box type
	 * @param  string $type type of action box
	 * @return none
	 */
	function loadActionBoxSpecificMetaboxes( $type ){
		global $MabBase;

		//get post type name
		$post_type = $MabBase->get_post_type();
		
		switch( $type ){
			case 'optin':
				//optin metaboxes
				add_meta_box( 'mab-optin-settings', __('Opt In Form Settings','mab' ), array( &$this, 'optInSettings' ), $post_type, 'normal', 'high' );
				add_meta_box( 'mab-optin', __( 'Opt In Copy', 'mab' ), array( &$this, 'optIn' ), $post_type, 'normal', 'high' );
				add_meta_box( 'mab-actionbox-aside', __('Action Box: Side Content', 'mab' ), array( &$this, 'actionBoxAside' ), $post_type, 'normal', 'high' );
				break; ##BREAK
			default:
				break;
		}

	}

	/**
	 * Loads Custom CSS Metabox
	 * 
	 * @return none
	 */
	function loadCustomCssSettingsBox(){
		global $MabBase;

		//get post type name
		$post_type = $MabBase->get_post_type();

		add_meta_box( 'mab-custom-css-settings', __('Design Settings: Custom CSS', 'mab' ), array( $this, 'customCssBox' ), $post_type, 'advanced', 'high' );
	}
	
	/**
	 * The Meta Boxes
	 * ========================== */
	
	/* Save Action Box */
	function saveBox( $post ){
		$filename = 'metaboxes/save-box.php';
		$data = $post;
		$box = MAB_Utils::getView( $filename, $data );
		echo $box;
	}
	
	/* Duplicate Action Box */
	function duplicateBox( $post ){
		$data = array('post_id' => $post->ID );
		$filename = 'metaboxes/duplicate.php';
		$box = MAB_Utils::getView( $filename, $data );
		echo $box;
	}
	
	/*  Custom CSS Settings */
	function customCssBox( $post ){
		global $MabBase;
		$meta = $MabBase->get_mab_meta( $post->ID, 'design' );
		$data['custom_css'] = isset( $meta['mab_custom_css'] ) ? $meta['mab_custom_css'] : '';
		$filename = 'metaboxes/metabox-customcss.php';
		$box = MAB_Utils::getView( $filename, $data );
		echo $box;
	}
	
	/** ALL ACTION BOXES **/
	/* General Settings */
	function actionBoxSettings( $post ){
		global $MabBase, $MabAdmin;

		$data['meta'] = $MabBase->get_mab_meta( $post->ID );
		$data['assets-url'] = MAB_ASSETS_URL;
		
		//get available action box styles
		$data['styles'] = MAB_Utils::getStyles();
		
		$filename = 'metaboxes/type/actionbox-settings.php';
		$box = MAB_Utils::getView( $filename, $data );
		echo $box;
	}
	
	/* Content */
	function actionBoxContent( $post ){
		global $MabBase, $MabAdmin;

		$data['meta'] = $MabBase->get_mab_meta( $post->ID );
		$data['assets-url'] = MAB_ASSETS_URL;
		$filename = 'metaboxes/type/actionbox-content.php';
		$box = MAB_Utils::getView( $filename, $data );
		echo $box;
	}
	
	/* Aside */
	function actionBoxAside( $post ){
		global $MabBase, $MabAdmin;

		$data['meta'] = $MabBase->get_mab_meta( $post->ID );
		$data['assets-url'] = MAB_ASSETS_URL;		
		$filename = 'metaboxes/type/actionbox-aside.php';
		$box = MAB_Utils::getView( $filename, $data );
		echo $box;
	}
	
	/** OPT IN SPECIFIC **/
	function optIn( $post ){
		global $MabBase, $MabAdmin;

		$data['meta'] = $MabBase->get_mab_meta( $post->ID );
		$data['assets-url'] = MAB_ASSETS_URL;
		$filename = 'metaboxes/type/optin.php';
		$box = MAB_Utils::getView( $filename, $data );
		echo $box;
	}
	
	function optInSettings( $post ){
		global $MabBase, $MabAdmin, $MabButton;

		$data['meta'] = $MabBase->get_mab_meta( $post->ID );
		$data['assets-url'] = MAB_ASSETS_URL;
		
		//get allowed optin providers
		$data['optin-providers'] = $MabAdmin->getAllowedOptinProviders();
		
		//get available action box styles
		$data['styles'] = MAB_Utils::getStyles();

		//get buttons
		$data['buttons'] = $MabButton->getConfiguredButtons();
		
		$filename = 'metaboxes/type/optin-settings.php';
		$box = MAB_Utils::getView( $filename, $data );
		echo $box;
	}
	
	function optinFormDesignSettings( $post ){
		mab_form_design_settings( $post );
	}
	
	/** OTHER CUSTOM POST TYPES METABOX **/
	//put metabox in selected content types
	static function postActionBox( $post ){
		global $MabBase;
		
		$data['meta'] = $MabBase->get_mab_meta( $post->ID, 'post' );
		$data['assets-url'] = MAB_ASSETS_URL;
		$filename = 'metaboxes/post-select-actionbox.php';
		$box = MAB_Utils::getView( $filename, $data );
		echo $box;
	}
	

}
