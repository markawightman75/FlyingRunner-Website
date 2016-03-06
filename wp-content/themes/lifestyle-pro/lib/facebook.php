<?php

// Add OpenGraph tags (for Facebook etc. to head) - turn off Yoast settings that do this
add_filter('language_attributes', 'fb_doctype_opengraph');
add_action('wp_head', 'fb_opengraph_meta_properties', 5);

function fb_doctype_opengraph($output) {
    return $output . '
    xmlns:og="http://opengraphprotocol.org/schema/"
    xmlns:fb="http://www.facebook.com/2008/fbml"';
}

function fb_opengraph_meta_properties() {
    global $post;
 
    if(is_single()) {
        $thumb_url = '';
		$using_large_featured_image = false;
		if( class_exists('Dynamic_Featured_Image') ) {
			global $dynamic_featured_image;
			$featured_images = $dynamic_featured_image->get_featured_images( );
			$featured_image = $featured_images[0];
			$thumb_url = $featured_image['full'];
			$using_large_featured_image = true;
		   //You can now loop through the image to display them as required
		}
		else
		{
			//Get the url of the featured image
			$thumb_id = get_post_thumbnail_id();
			$thumb_url_array = wp_get_attachment_image_src($thumb_id, 'thumbnail-size', true);
			$thumb_url = $thumb_url_array[0];
		}
		
		
		if (substr($thumb_url,0, 19) != "http://127.0.0.1:82" )
		{
			//Redirect to http://images.
			$firstdot = strpos($thumb_url, ".");
			$url_secondpart = substr($thumb_url, $firstdot+1);
			$thumb_url = "http://images." . $url_secondpart;
			$thumb_url = str_replace("2/wp-content/uploads/", "",$thumb_url);
			$thumb_url = str_replace("wp-content/uploads/", "",$thumb_url);
			$thumb_url = str_replace("images.flyingrunner.co.uk/2/", "images.flyingrunner.co.uk/",$thumb_url);
		}
		
		if (! empty ($thumb_url)) {						
			$img_src = $thumb_url;
		} else {
			$img_src = "http://images.flyingrunner.co.uk/2014/06/FlyingRunner-main-purple-crop.jpg";
		}
		
        if($excerpt = $post->post_excerpt) {
            $excerpt = strip_tags($post->post_excerpt);
			$excerpt = esc_html($excerpt);
            $excerpt = str_replace("", "'", $excerpt);
        } else {
            $excerpt = get_bloginfo('description');
        }
        ?>
        <meta property="og:locale" content="en_GB"/>
        <meta property="og:title" content="<?php echo the_title(); ?>"/>
        <meta property="og:description" content="<?php echo $excerpt; ?>"/>
        <meta property="og:type" content="article"/>
        <meta property="og:url" content="<?php echo the_permalink(); ?>"/>
        <meta property="og:site_name" content="<?php echo get_bloginfo(); ?>"/>
        <meta property="og:image" content="<?php echo $img_src; ?>"/>
        <meta property="og:image:type" content="image/jpeg" />
        <link rel="image_src" type="image/jpeg" href="<?php echo $img_src; ?>" />
        <?php
        if ($using_large_featured_image)
        {		
            ?>
            <meta property="og:image:width" content="400"/>
            <meta property="og:image:height" content="267"/>
            <?php

        }
    } else {
        //Everything that isn't an article
        ?>
        <meta property="og:locale" content="en_GB"/>
        <meta property="og:title" content="<?php echo the_title(); ?>"/>
        <meta property="og:description" content="Welcome to The Flying Runner. Thought-provoking articles, latest news, helpful resources and beautiful gifts for runners. Please come in and explore..."/>
        <meta property="og:type" content="website"/>
        <meta property="og:url" content="<?php echo the_permalink(); ?>"/>
        <meta property="og:site_name" content="<?php echo get_bloginfo(); ?>"/>
        <meta property="og:image" content="http://images.flyingrunner.co.uk/2014/06/FlyingRunner-main-purple-crop.jpg"/>

        <?php
        return;
    }
}
?>