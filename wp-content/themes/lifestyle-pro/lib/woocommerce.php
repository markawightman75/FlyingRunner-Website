<?php

//Using the Genesis Connect for WooCommerce plugin, replace WC's built-in templates with its Genesis-ready versions
add_theme_support( 'genesis-connect-woocommerce' );

// Remove WC generated styles & scripts from non-shop pages (for loading speed)
// and load our custom WC styles on shop pages
add_action( 'wp_enqueue_scripts', 'load_and_unload_woocommerce_styles', 99 );

// Change number or products per row to 3
add_filter('loop_shop_columns', 'set_columns_per_row_to_3');

// Display 24 products per page. 
add_filter( 'loop_shop_per_page', create_function( '$cols', 'return 24;' ), 20 );

// Override the message displayed when you add a product to the cart, to use "Basket" not "Cart"
add_filter( 'wc_add_to_cart_message', 'custom_add_to_cart_message' ,10,2);

// Add 'continue shopping' button to cart
add_action( 'woocommerce_before_cart_table', 'woo_add_continue_shopping_button_to_cart' );

add_image_size( 'cart_item_image_size',200, 200, true );
add_filter( 'woocommerce_cart_item_thumbnail', 'cart_item_thumbnail', 10, 3 );

// Hide shipping rates when free shipping is available
//add_filter( 'woocommerce_package_rates', 'hide_shipping_when_free_is_available', 10, 2 );

// Remove WC generated styles & scripts from non-shop pages (for loading speed)
// and load our custom WC styles on shop pages
// See: https://wordimpress.com/how-to-load-woocommerce-scripts-and-styles-only-in-shop/
function load_and_unload_woocommerce_styles() {	
    //remove generator meta tag
	remove_action( 'wp_head', array( $GLOBALS['woocommerce'], 'generator' ) );

	//first check that woo exists to prevent fatal errors
	if ( function_exists( 'is_woocommerce' ) ) {
		//If this isn't a shop page, dequeue scripts and styles that are queued by default
		if ( ! is_woocommerce() && ! is_cart() && ! is_checkout() ) {
			wp_dequeue_style( 'woocommerce_frontend_styles' );
			wp_dequeue_style( 'woocommerce_fancybox_styles' );
			wp_dequeue_style( 'woocommerce_chosen_styles' );
			wp_dequeue_style( 'woocommerce_prettyPhoto_css' );
			wp_dequeue_style( 'woocommerce' );
			wp_dequeue_style( 'woocommerce-layout' );
			wp_dequeue_style( 'woocommerce-smallscreen' );
			wp_dequeue_style( 'woocommerce-general' );
			wp_dequeue_style( 'pac-styles' );
			wp_dequeue_style( 'pac-layout-styles' );
			wp_dequeue_script( 'wc_price_slider' );
			wp_dequeue_script( 'wc-single-product' );
			wp_dequeue_script( 'wc-add-to-cart' );
			wp_dequeue_script( 'wc-cart-fragments' );
			wp_dequeue_script( 'wc-checkout' );
			wp_dequeue_script( 'wc-add-to-cart-variation' );
			wp_dequeue_script( 'wc-single-product' );
			wp_dequeue_script( 'wc-cart' );
			wp_dequeue_script( 'wc-chosen' );
			wp_dequeue_script( 'woocommerce' );
			wp_dequeue_script( 'prettyPhoto' );
			wp_dequeue_script( 'prettyPhoto-init' );
			wp_dequeue_script( 'jquery-blockui' );
			wp_dequeue_script( 'jquery-placeholder' );
			wp_dequeue_script( 'fancybox' );
			wp_dequeue_script( 'jqueryui' );
		}
		else
		{
            //If this is a shop page, also load our custom WC styles
			wp_enqueue_style( 'fr-woocommerce-overrides-stylesheet', CHILD_URL . '/woocommerce-overrides.css', false, filemtime( get_stylesheet_directory() . '/woocommerce-overrides.css' ) );
		}
	}

}

if (!function_exists('set_columns_per_row_to_3')) {
	function set_columns_per_row_to_3() {
		return 3; // 3 products per row
	}
}

//* Override the message displayed when you add a product to the cart, to use "Basket" not "Cart"
function custom_add_to_cart_message($message, $product_id) {
     if ( is_array( $product_id ) ) {
          $titles = array();
  
          foreach ( $product_id as $id ) {
              $titles[] = get_the_title( $id );
          }
 
          $added_text = sprintf( __( 'Added %s to your basket.', 'woocommerce' ), wc_format_list_of_items( $titles ) );
  
      } else {
          $added_text = sprintf( __( '&quot;%s&quot; was successfully added to your basket.', 'woocommerce' ), get_the_title( $product_id ) );
      }
 
      // Output success messages
      if ( get_option( 'woocommerce_cart_redirect_after_add' ) == 'yes' ) :
  
          $return_to  = apply_filters( 'woocommerce_continue_shopping_redirect', wp_get_referer() ? wp_get_referer() : home_url() );
  
          $message    = sprintf('<a href="%s" class="button wc-forward">%s</a> %s', $return_to, __( 'Continue Shopping', 'woocommerce' ), $added_text );
  
      else :
  
          $message    = sprintf('<a href="%s" class="button wc-forward">%s</a> %s', get_permalink( wc_get_page_id( 'cart' ) ), __( 'View Cart', 'woocommerce' ), $added_text );
  
      endif;
  
	return $message; 

}


function woo_add_continue_shopping_button_to_cart() {
    $shop_page_url = get_permalink( woocommerce_get_page_id( 'shop' ) );
    echo '<div class="woocommerce-message">';
    echo ' <a href="'.$shop_page_url.'" class="button">Continue Shopping ?</a> Need some more products?';
    echo '</div>';
}

// Set the image size to use for the product thumbnail
function cart_item_thumbnail( $thumb, $cart_item, $cart_item_key ) { 
 // create the product object 
 $product = get_product( $cart_item['product_id'] );
 return $product->get_image( 'cart_item_image_size' ); 
} 

/**
 * 
 *
 * @param array $rates Array of rates found for the package
 * @param array $package The package array/object being shipped
 * @return array of modified rates
 */
function hide_shipping_when_free_is_available( $rates, $package ) {
 	
 	// Only modify rates if free_shipping is present
  	if ( isset( $rates['free_shipping'] ) ) {
  	
  		// To unset a single rate/method, do the following. This example unsets flat_rate shipping
  		unset( $rates['flat_rate'] );
  		
  		// To unset all methods except for free_shipping, do the following
  		$free_shipping          = $rates['free_shipping'];
  		$rates                  = array();
  		$rates['free_shipping'] = $free_shipping;
	}
	
	return $rates;
}




?>