<?php
/*
Plugin Name: WC Paymentsense Direct Gateway
Description: Paymentsense Direct Gateway is a plugin that extends WooCommerce, allowing you to take payments via Paymentsense.
Version: 2.0
Author: Paymentsense
Author URI: http://www.paymentsense.co.uk/
*/

add_action('plugins_loaded', 'woocommerce_paymentsense_direct_init', 0);

function woocommerce_paymentsense_direct_init() {

	if ( ! class_exists( 'Woocommerce' ) ) { return; }
	
	$here = plugin_dir_path(__FILE__);
	/**
 	 * Localication
	 */
	load_textdomain( 'woocommerce', $here . 'langs/paymentsense-direct-'.get_locale().'.mo' );
		
	require_once('gateway-paymentsense-direct.php');

	/**
 	* Add the Gateway to WooCommerce
 	**/
	function add_paymentsense_direct_gateway($methods) {
		$methods[] = 'WC_Gateway_Paymentsense_Direct';
		return $methods;
	}
	
	add_filter('woocommerce_payment_gateways', 'add_paymentsense_direct_gateway' );
} 
