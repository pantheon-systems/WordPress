<?php
/*
Plugin Name: Google Tag Manager for Wordpress
Version: 1.9.2
Plugin URI: https://gtm4wp.com/
Description: The first Google Tag Manager plugin for WordPress with business goals in mind
Author: Thomas Geiger
Author URI: https://gtm4wp.com/
Text Domain: duracelltomi-google-tag-manager
Domain Path: /languages

WC requires at least: 2.6
WC tested up to: 3.5.4
*/

define( 'GTM4WP_VERSION',    '1.9.2' );
define( 'GTM4WP_PATH',       plugin_dir_path( __FILE__ ) );

$gtp4wp_plugin_url = plugin_dir_url( __FILE__ );
$gtp4wp_plugin_basename = plugin_basename( __FILE__ );
require_once( GTM4WP_PATH."/common/readoptions.php" );

function gtm4wp_init() {
	load_plugin_textdomain( 'duracelltomi-google-tag-manager', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	if ( is_admin() ) {
		require_once( GTM4WP_PATH."/admin/admin.php" );
	} else {
		require_once( GTM4WP_PATH."/public/frontend.php" );
	}
}
add_action('plugins_loaded', 'gtm4wp_init');