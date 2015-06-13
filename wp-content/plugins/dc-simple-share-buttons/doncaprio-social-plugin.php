<?php
/*
Plugin Name: DC Simple Share Buttons
Plugin URI: http://geek.ng
Description: A set of very simple social sharing buttons that loads pretty fast (no javascript, no images).
Author: Don Caprio
Version: 1.0
Author URI: http://geek.ng
*/

// Initialize setting options on activation
register_activation_hook( __FILE__, 'dc_install_activate_default_values' );
function dc_install_activate_default_values() {
$dc_plugin_options = array(
'label' => 'Like the article? Please share on your social network. :)',
'post' => 'ok',
'page' => '',
);
update_option( 'doncaprio_social_plugin', $dc_plugin_options );
}


// get option value from the database
$option = get_option( 'doncaprio_social_plugin' );
$label = $option['label'];
	$displaypost = $option['post'];
	$displaypage = $option['page'];

// social share code obsolete
$plugin_code = '<!--Facebook-->
        <a style="color: #fff;" class="facebook" href="http://www.facebook.com/sharer.php?u='. get_permalink() .'&amp;t='. the_title('', '', FALSE) .'" rel="nofollow" title="Share this post on Facebook!" onclick="window.open(this.href); return false;">Facebook</a>		
		
        <!--Google Plus-->
        <a style="color: #fff;" class="google-plus" target="_blank" href="https://plus.google.com/share?url='. get_permalink() .'" rel="nofollow">Google+</a>
		
		<!--Twitter-->
        <a style="color: #fff;" class="twitter" href="http://twitter.com/home?status='. the_title('', '', FALSE) .': '. get_permalink() .'" title="Share this post on Twitter!" target="_blank" rel="nofollow">Twitter</a>	
				
		<!--Reddit-->
		<a style="color: #fff;" class="reddit" target="_blank" href="http://reddit.com/submit?url='. get_permalink() .'&amp;title='. the_title('', '', FALSE) .'" rel="nofollow">Reddit</a>
		
		<!--LinkedIn-->
		<a style="color: #fff;" class="linkedin" target="_blank" href="http://www.linkedin.com/shareArticle?mini=true&amp;url='. get_permalink() .'" rel="nofollow">LinkedIn</a>

		<!--Pinterst-->' .
		"<a class=\"pinterest\" href=\"javascript:void((function()%7Bvar%20e=document.createElement('script');e.setAttribute('type','text/javascript');e.setAttribute('charset','UTF-8');e.setAttribute('src','http://assets.pinterest.com/js/pinmarklet.js?r='+Math.random()*99999999);document.body.appendChild(e)%7D)());\">Pinterest</a>
</div>
</div>";


add_action( 'admin_menu', 'dc_social_share_plugin' );

// Adding Submenu to settings
function dc_social_share_plugin() {
	add_options_page( 'Don Caprio\'s Social Share Plugin', 'Don Caprio\'s Social Share',
'manage_options', 'doncaprio-social-plugin-dc', 'doncaprio_social_plugin_dc' );
}

// plugin settings form
function doncaprio_social_plugin_dc() {

	?>
<div class="wrap">
<?php screen_icon(); ?>
<h2>Doncaprio's social share buttons</h2>
<form action="options.php" method="post">
<table class="form-table">
<?php settings_fields('doncaprio_social_plugin'); ?>
<?php do_settings_sections('doncaprio-social-plugin-dc'); ?>
<div>
<input name="Submit" class="button-primary" type="submit" value="Save Changes" />
</div>

</form>
</table>

<br />
<h2>Documentation</h2>
<h3>Template Tags</h3>
<table><tr valign='top'>
<td scope='row'><p style="list-style:circle"></p>To include this plugin in any location of your theme, <br />
use this template tag</p></td>
<td style="margin: 5px"><textarea cols='30' rows='1'>&lt;?php dc_social_share_tag(); ?&gt;</textarea></td>
</tr>

<tr valign='top'>
<td scope='row'><p style="list-style:circle"></p>To add a <b>Share label</b> using the template tag,<br />
pass the text as the function argument like this: </p></td>
<td><textarea cols='45' rows='1'>&lt;?php dc_social_share_tag('Share this article'); ?&gt;</textarea></td>
</tr></table>

<h3>Shortcodes</h3>
<table><tr valign='top'>
<td scope='row'><p style="list-style:circle"></p>To include this plugin within a post or page,<br />
use this Shortcodes</p></td>
<td style="margin: 5px"><textarea cols='30' rows='1'>[dc-social]</textarea></td>
</tr>

<tr valign='top'>
<td scope='row'><p style="list-style:circle"></p>To add a <b>share label</b> using shortcodes,<br />
do it like this: </p></td>
<td><textarea cols='45' rows='1'>[dc-social label="Share this article"]</textarea></td>
</tr></table>
</div>

<?php }

	// Register and define the settings
	add_action('admin_init', 'doncaprio_social_plugin_init');
	function doncaprio_social_plugin_init(){
	register_setting(
	'doncaprio_social_plugin',
	'doncaprio_social_plugin',
	''
	);

	add_settings_section(
	'doncaprio-social-plugin-main',
	'',
	'',
	'doncaprio-social-plugin-dc'
	);
	add_settings_field(
	'doncaprio-social-plugin-dc',
	'Text to display before social buttons 	',
	'dc_social_share_plugin_setting_input',
	'doncaprio-social-plugin-dc',
	'doncaprio-social-plugin-main'
	);
	}

	// Display and fill the form field
	function dc_social_share_plugin_setting_input() {
	// Retrieve the settings values form DB and make them global
	global $label, $displaypost, $displaypage;
	echo "
	<tr valign='top'>
	<th scope='row'><label for='read_more'> <strong>Sharing label</strong></label></th>";
	echo "<td><textarea cols='60' rows='2' id='label' name='doncaprio_social_plugin[label]'>$label</textarea></td>
	</tr>"; ?>
	<tr valign='top'>
	<th scope='row'><label for='display'> <strong>Where to be shown?</strong></label></th>
	<td><b>Post</b><br/><input type='checkbox' name='doncaprio_social_plugin[post]' value="ok" <?php checked($displaypost, 'ok')?> /><br/>
	<br/><b>Page</b><br/><input type='checkbox' name='doncaprio_social_plugin[page]' value="ok" <?php checked($displaypage, 'ok')?> /><br/>
	</td><br/><br/>
	</tr>
	<?php
	}

	/**
	* Enqueue plugin font and css
	*/
	function dc_social_share_css() {
	wp_enqueue_style( 'dc-social-share', plugins_url('dc-social-share.css', __FILE__) );
	wp_enqueue_style( 'dc-social-font', 'http://fonts.googleapis.com/css?family=Pacifico' );
	}
	add_action( 'wp_enqueue_scripts', 'dc_social_share_css' );

	add_action('the_content', 'dc_social_share_plugin_display');

	/**
	* Displays social button function
	* @param string $content post content
	*/
	function dc_social_share_plugin_display($content) {
	global $plugin_code, $label, $displaypost, $displaypage;
	
	if ($displaypost == 'ok') {
	if (is_single()) {
	$content.= '<div class="myclass" align="center">
	<div class="social"><div class="thetext">'. $label . '</div>
        <!--Facebook-->
        <a style="color: #fff;" class="facebook" href="http://www.facebook.com/sharer.php?u='. get_permalink() .'&amp;t='. the_title('', '', FALSE) .'" rel="nofollow" title="Share this post on Facebook!" onclick="window.open(this.href); return false;">Facebook</a>		
		
        <!--Google Plus-->
        <a style="color: #fff;" class="google-plus" target="_blank" href="https://plus.google.com/share?url='. get_permalink() .'" rel="nofollow">Google+</a>
		
		<!--Twitter-->
        <a style="color: #fff;" class="twitter" href="http://twitter.com/home?status='. the_title('', '', FALSE) .': '. get_permalink() .'" title="Share this post on Twitter!" target="_blank" rel="nofollow">Twitter</a>	
				
		<!--Reddit-->
		<a style="color: #fff;" class="reddit" target="_blank" href="http://reddit.com/submit?url='. get_permalink() .'&amp;title='. the_title('', '', FALSE) .'" rel="nofollow">Reddit</a>
		
		<!--LinkedIn-->
		<a style="color: #fff;" class="linkedin" target="_blank" href="http://www.linkedin.com/shareArticle?mini=true&amp;url='. get_permalink() .'" rel="nofollow">LinkedIn</a>

		<!--Pinterst-->' .
		"<a class=\"pinterest\" href=\"javascript:void((function()%7Bvar%20e=document.createElement('script');e.setAttribute('type','text/javascript');e.setAttribute('charset','UTF-8');e.setAttribute('src','http://assets.pinterest.com/js/pinmarklet.js?r='+Math.random()*99999999);document.body.appendChild(e)%7D)());\">Pinterest</a>
</div>
</div>";
	}	
	}
if ($displaypage == 'ok') {
	if (is_page()) {
	$content.= '<div class="myclass" align="center">
	<div class="social"><div class="thetext">'. $label  . '</div>
        <!--Facebook-->
        <a style="color: #fff;" class="facebook" href="http://www.facebook.com/sharer.php?u='. get_permalink() .'&amp;t='. the_title('', '', FALSE) .'" rel="nofollow" title="Share this post on Facebook!" onclick="window.open(this.href); return false;">Facebook</a>		
		
        <!--Google Plus-->
        <a style="color: #fff;" class="google-plus" target="_blank" href="https://plus.google.com/share?url='. get_permalink() .'" rel="nofollow">Google+</a>
		
		<!--Twitter-->
        <a style="color: #fff;" class="twitter" href="http://twitter.com/home?status='. the_title('', '', FALSE) .': '. get_permalink() .'" title="Share this post on Twitter!" target="_blank" rel="nofollow">Twitter</a>	
				
		<!--Reddit-->
		<a style="color: #fff;" class="reddit" target="_blank" href="http://reddit.com/submit?url='. get_permalink() .'&amp;title='. the_title('', '', FALSE) .'" rel="nofollow">Reddit</a>
		
		<!--LinkedIn-->
		<a style="color: #fff;" class="linkedin" target="_blank" href="http://www.linkedin.com/shareArticle?mini=true&amp;url='. get_permalink() .'" rel="nofollow">LinkedIn</a>

		<!--Pinterst-->' .
		"<a class=\"pinterest\" href=\"javascript:void((function()%7Bvar%20e=document.createElement('script');e.setAttribute('type','text/javascript');e.setAttribute('charset','UTF-8');e.setAttribute('src','http://assets.pinterest.com/js/pinmarklet.js?r='+Math.random()*99999999);document.body.appendChild(e)%7D)());\">Pinterest</a>
</div>
</div>";
	}	
	}

	return $content;
	}

	/**
	* Shortcode function ish
	*/
	add_shortcode( 'dc-social', 'dc_social_share_plugin_shortcode' );
	function dc_social_share_plugin_shortcode( $attr ) {
	global $plugin_code;
	echo '<div class="myclass" align="center">';
	if (!empty($attr['label'])) {
	echo '<div class="social"><div class="thetext">'. $attr['label'] . '</div>';
	}
	else {
	echo '<div class="social">';
	}
	echo '<!--Facebook-->
        <a style="color: #fff;" class="facebook" href="http://www.facebook.com/sharer.php?u='. get_permalink() .'&amp;t='. the_title('', '', FALSE) .'" rel="nofollow" title="Share this post on Facebook!" onclick="window.open(this.href); return false;">Facebook</a>		
		
        <!--Google Plus-->
        <a style="color: #fff;" class="google-plus" target="_blank" href="https://plus.google.com/share?url='. get_permalink() .'" rel="nofollow">Google+</a>
		
		<!--Twitter-->
        <a style="color: #fff;" class="twitter" href="http://twitter.com/home?status='. the_title('', '', FALSE) .': '. get_permalink() .'" title="Share this post on Twitter!" target="_blank" rel="nofollow">Twitter</a>	
				
		<!--Reddit-->
		<a style="color: #fff;" class="reddit" target="_blank" href="http://reddit.com/submit?url='. get_permalink() .'&amp;title='. the_title('', '', FALSE) .'" rel="nofollow">Reddit</a>
		
		<!--LinkedIn-->
		<a style="color: #fff;" class="linkedin" target="_blank" href="http://www.linkedin.com/shareArticle?mini=true&amp;url='. get_permalink() .'" rel="nofollow">LinkedIn</a>

		<!--Pinterst-->' .
		"<a class=\"pinterest\" href=\"javascript:void((function()%7Bvar%20e=document.createElement('script');e.setAttribute('type','text/javascript');e.setAttribute('charset','UTF-8');e.setAttribute('src','http://assets.pinterest.com/js/pinmarklet.js?r='+Math.random()*99999999);document.body.appendChild(e)%7D)());\">Pinterest</a>
</div>
</div>";
	}

	/**
	* Template tag for adding plugin to templates
	* @param string $read_more Share plugin label
	*/
	function dc_social_share_tag( $read_more = '') {
	global $plugin_code;
	echo '<div class="myclass" align="center">
	<div class="social"><div class="thetext">'. $read_more . '</div>
        <!--Facebook-->
        <a style="color: #fff;" class="facebook" href="http://www.facebook.com/sharer.php?u='. get_permalink() .'&amp;t='. the_title('', '', FALSE) .'" rel="nofollow" title="Share this post on Facebook!" onclick="window.open(this.href); return false;">Facebook</a>		
		
        <!--Google Plus-->
        <a style="color: #fff;" class="google-plus" target="_blank" href="https://plus.google.com/share?url='. get_permalink() .'" rel="nofollow">Google+</a>
		
		<!--Twitter-->
        <a style="color: #fff;" class="twitter" href="http://twitter.com/home?status='. the_title('', '', FALSE) .': '. get_permalink() .'" title="Share this post on Twitter!" target="_blank" rel="nofollow">Twitter</a>	
				
		<!--Reddit-->
		<a style="color: #fff;" class="reddit" target="_blank" href="http://reddit.com/submit?url='. get_permalink() .'&amp;title='. the_title('', '', FALSE) .'" rel="nofollow">Reddit</a>
		
		<!--LinkedIn-->
		<a style="color: #fff;" class="linkedin" target="_blank" href="http://www.linkedin.com/shareArticle?mini=true&amp;url='. get_permalink() .'" rel="nofollow">LinkedIn</a>

		<!--Pinterst-->' .
		"<a class=\"pinterest\" href=\"javascript:void((function()%7Bvar%20e=document.createElement('script');e.setAttribute('type','text/javascript');e.setAttribute('charset','UTF-8');e.setAttribute('src','http://assets.pinterest.com/js/pinmarklet.js?r='+Math.random()*99999999);document.body.appendChild(e)%7D)());\">Pinterest</a>
</div>
</div>";

	}
