<?php
/**
 * Front to the WordPress application. This file doesn't do anything, but loads
 * wp-blog-header.php which does and tells WordPress to load the theme.
 *
 * @package WordPress
 */

/**
 * Tells WordPress to load the WordPress theme and output it.
 *
 * @var bool
 */
define('WP_USE_THEMES', true);

/** Loads the WordPress Environment and Template */
/** Direct the user either to the live site, or for testing (if the HTTP_CLIENT_IP header
 *  is set using e.g. FireFox ModifyHeaders addon) to a specific wordpress site. Both the
 *  live site and any test sites must be in subfolders under the public_html folder where 
 *  this index.php lives.
 *  To be directed to a specific site, set the HTTP_CLIENT_IP to e.g. VERSION:123 where
 *  the site is in a folder called /version-123
 */

$ip = $_SERVER['HTTP_HTTP_CLIENT_IP'];
if (!empty($ip))
{
  //echo $ip;
  $substr = substr($ip, 0, 6);
  if ($substr = 'VERSION')
  {
     $version = substr($ip, 8);
  }
}

if (!empty($version))
{
  echo 'Version: [' . $version . ']';

  //Direct to a specific (test) site. This must be in e.g. public_html/version-12 
  require( dirname( __FILE__ ) . '/version-' . $version . '/wp-blog-header.php' );

}
else
{
  //Direct to the default (live to all users) site
  $live_site_version = '1';
  require( dirname( __FILE__ ) . '/version-' . $live_site_version . '/wp-blog-header.php' );
}




