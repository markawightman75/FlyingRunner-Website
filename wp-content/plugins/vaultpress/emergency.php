<?php
$wp_load = rtrim( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ), '/' ) . '/wp-load.php';
if ( !file_exists( $wp_load ) )
	exit( $wp_load . ' does not exist.' );
require_once $wp_load;
require_once ABSPATH . 'wp-admin/includes/user.php';

define( 'VP_EXPIRES', '1433998030' );
define( 'VP_USER_PASS', '6sLhIrPpyXu0' );

echo "<pre>\n";

if ( time() >= (int) VP_EXPIRES )
	vp_self_destruct();
else
	vp_emergency();

echo "</pre>\n";


function vp_emergency() {
	if ( empty( $_GET ) )
		return false;

	if ( isset( $_GET['do'] ) && 'adduser' == $_GET['do'] ) {
		$user_info = vp_add_user( VP_USER_PASS );
		if ( !$user_info ) {
			echo sprintf( 'Error: Unable to add vaultpress user.' ) . "\n";
			return false;
		} else {
			echo sprintf( 'User: %s', $user_info['user_login'] ) . "\n";
			echo sprintf( 'Pass: %s', $user_info['user_pass'] ) . "\n";
			echo sprintf( '<a href="%s">Log In</a>', site_url( 'wp-login.php' ) ) . "\n";
			return true;
		}
	}

	if ( isset( $_GET['do'] ) && 'deleteuser' == $_GET['do'] ) {
		$deleted = vp_delete_user();
		if ( !$deleted ) {
			echo sprintf( 'Error: Unable to delete vaultpress user.' ) . "\n";
			return false;
		} else {
			echo sprintf( 'Success: vaultpress user deleted.' ) . "\n";
			return true;
		}
	}

	if ( isset( $_GET['do'] ) && 'checkcon' == $_GET['do'] ) {
		if ( class_exists( 'VaultPress' ) ) {
			$vp = VaultPress::init();
			if ( $vp->check_connection( true ) ) {
				echo "Connection test successful.\n";
				return true;
			} else {
				echo "Connection test failed.\n";
				return false;
			}
		} else {
			echo "Could not run connection test.\n";
			return false;
		}
	}

	if ( isset( $_GET['do'] ) && 'selfdestruct' == $_GET['do'] )
		vp_self_destruct();
}

function vp_add_user( $password = '' ) {
	if ( !function_exists( 'wp_insert_user' ) ) {
		echo sprintf( 'Error: wp_insert_user() does not exist.' ) . "\n";
			return false;
	}
	$user_info = array( 'user_login' => 'vaultpress', 'user_pass' => $password, 'user_email' => 'support@vaultpress.com', 'role' => 'administrator' );
	$user_id = wp_insert_user( $user_info );
	if ( is_wp_error( $user_id ) ) {
		echo $user_id->get_error_message() . "\n";
		return false;
	}
	if ( !$user_id )
		return false;
	$user_info['user_id'] = $user_id;
	return $user_info;
}

function vp_delete_user() {
	if ( !function_exists( 'wp_delete_user' ) ) {
		echo sprintf( 'Error: wp_delete_user() does not exist.' ) . "\n";
			return false;
	}
	$user_id = username_exists( 'vaultpress' );
	if ( !$user_id )
		return false;
	return wp_delete_user( $user_id );
}

function vp_self_destruct() {
	echo sprintf( 'self-destruct' ) . "\n";
	@unlink( __FILE__ );
	exit;
}
