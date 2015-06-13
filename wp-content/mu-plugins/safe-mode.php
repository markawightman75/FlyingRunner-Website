<?php
/*
	Plugin Name: Safe mode
	Plugin URI: http://1and1.com
	Description: Adds functionality for Safe mode
*/

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

class Safe_Mode_Install {
	private static $is_auto_updating = false;

	public function __construct() {
		add_action( 'init', array( $this, 'load_hooks' ) );
	}

	public function load_hooks() {
		if ( ! current_user_can('update_core') ) {
			return;
		}

		if ( preg_match( "/^app\d{5,15}$/", basename( ABSPATH ) ) || preg_match( "/^trial\d{9,15}$/", basename( ABSPATH ) ) ) {
			$this->dont_email_updates();

			add_filter( 'auto_update_core', '__return_false' );
			add_filter( 'user_has_cap', array( $this, 'block_core_updates' ) );

			add_action( 'admin_init', array( $this, 'admin_init' ), 1 );
			add_filter( 'pre_site_transient_update_core', array( $this, 'last_checked_now_core' ) );

			add_filter( 'pre_update_option_auto_updater.lock', array( $this, 'show_updates' ) );
		}
	}

	public function dont_email_updates() {
		// Don't send e-mails after an update
		add_filter( 'auto_core_update_send_email', '__return_false' );
		add_filter( 'automatic_updates_debug_email', '__return_false' );
	}

	public function block_core_updates( $allcaps ) {
		$allcaps['update_core'] = false;

		return $allcaps;
	}


	public function admin_init() {
		remove_action( 'admin_init', '_maybe_update_core' );
		remove_action( 'wp_version_check', 'wp_version_check' );

		remove_action( 'admin_notices', 'maintenance_nag' );
		remove_action( 'network_admin_notices', 'maintenance_nag' );
	}

	public function last_checked_now_core( $current ) {
		global $wp_version;

		if ( self::$is_auto_updating ) {
			return $current;
		}

		include ABSPATH . WPINC . '/version.php';

		$current                   = new stdClass;
		$current->updates         = array();
		$current->version_checked = $wp_version;
		$current->last_checked    = time();
		$current->response        = array();

		return $current;
	}

	public function show_updates( $return_value ) {
		self::$is_auto_updating = true;

		return $return_value;
	}

}

new Safe_Mode_Install;