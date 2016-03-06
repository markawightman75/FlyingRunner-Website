<?php

add_action( 'genesis_before_content_sidebar_wrap', 'add_top_banner' );
//add_action( 'genesis_before_footer', 'add_bottom_banner' );
//add_action( 'genesis_after_content_sidebar_wrap', 'add_bottom_banner' );

function add_top_banner() {
	//If we don't want to show a banner, we must include this so we put a margin between 
	//bottom of main menu and top of content 
	echo "<div class=\"banner-top-empty\"></div>";
	return;
	
	//if (is_shop() or is_product() or is_product_category() or is_cart() or is_checkout())
	//{
		$banner = "<div class=\"banner-top\">";
			$banner .= "<div class=\"banner-top-small\">";
				$banner .= "Free shipping on everything until Monday!";
				//$banner .= "<div><a href=\"" . esc_url( home_url( '/product-category/medal-displays' )) . "\">10% off our medal displays until Sunday!</div><div>Use coupon <span style=\"color:#CCC\">medal-madness-october</span> at Checkout</div></a>";
			$banner .= "</div>";
			$banner .= "<div class=\"banner-top-medium\">";
				$banner .= "Free shipping on everything until Monday!";
				//$banner .= "<a href=\"" . esc_url( home_url( '/product-category/medal-displays' )) . "\">10% off our race medal displays until Sunday! Use coupon <span style=\"color:#CCC\">medal-madness-october</span> at Checkout</a>";
			$banner .= "</div>";
		$banner .= "</div>";
		echo $banner;
	//}
	//else
	//{
		//banner-empty duplicates 32px margin on banner-top between nav and content that we need
	//	echo "<div class=\"banner-top-empty\"></div>";
	//}
}
function add_bottom_banner() {
	if (is_checkout()){
		echo "<div class=\"banner-bottom\">Can we help you? Just drop us an email at contact@flyingrunner.co.uk</div>";
	}
}

?>