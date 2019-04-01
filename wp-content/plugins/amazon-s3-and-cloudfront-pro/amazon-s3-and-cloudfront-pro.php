<?php
/*
Plugin Name: WP Offload Media
Plugin URI:  https://deliciousbrains.com/wp-offload-media/
Description: Speed up your WordPress site by offloading your media and assets to Amazon S3 or DigitalOcean Spaces and a CDN.
Author: Delicious Brains
Version: 2.0.2
Author URI: https://deliciousbrains.com/
Network: True
Text Domain: amazon-s3-and-cloudfront
Domain Path: /languages/

// Copyright (c) 2015 Delicious Brains. All rights reserved.
//
// Released under the GPL license
// http://www.opensource.org/licenses/gpl-license.php
//
// **********************************************************************
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// **********************************************************************
//
*/

require_once dirname( __FILE__ ) . '/version.php';
require_once dirname( __FILE__ ) . '/classes/as3cf-compatibility-check.php';
require_once dirname( __FILE__ ) . '/classes/pro/as3cf-pro-installer.php';
require_once dirname( __FILE__ ) . '/classes/pro/as3cf-pro-plugin-installer.php';

add_action( 'activated_plugin', array( 'AS3CF_Compatibility_Check', 'deactivate_other_instances' ) );

global $as3cfpro_compat_check;
$as3cfpro_compat_check = new AS3CF_Pro_Installer( __FILE__ );

/**
 * @throws Exception
 */
function as3cf_pro_init() {
	if ( class_exists( 'Amazon_S3_And_CloudFront_Pro' ) ) {
		return;
	}

	global $as3cfpro_compat_check, $as3cf_compat_check;
	$as3cf_compat_check = $as3cfpro_compat_check;

	if ( ! $as3cfpro_compat_check->is_compatible() ) {
		return;
	}

	if ( method_exists( 'AS3CF_Compatibility_Check', 'is_plugin_active' ) && $as3cfpro_compat_check->is_plugin_active( 'amazon-s3-and-cloudfront/wordpress-s3.php' ) ) {
		// Deactivate WP Offload Lite if activated
		AS3CF_Compatibility_Check::deactivate_other_instances( 'amazon-s3-and-cloudfront-pro/amazon-s3-and-cloudfront-pro.php' );
	}

	global $as3cf, $as3cfpro;
	$abspath = dirname( __FILE__ );

	// Autoloader.
	require_once $abspath . '/wp-offload-media-autoloader.php';

	// Lite files
	require_once $abspath . '/include/functions.php';
	require_once $abspath . '/classes/as3cf-utils.php';
	require_once $abspath . '/classes/as3cf-error.php';
	require_once $abspath . '/classes/as3cf-filter.php';
	require_once $abspath . '/classes/filters/as3cf-local-to-s3.php';
	require_once $abspath . '/classes/filters/as3cf-s3-to-local.php';
	require_once $abspath . '/classes/as3cf-notices.php';
	require_once $abspath . '/classes/as3cf-plugin-base.php';
	require_once $abspath . '/classes/as3cf-plugin-compatibility.php';
	require_once $abspath . '/classes/amazon-s3-and-cloudfront.php';
	// Pro files
	require_once $abspath . '/vendor/deliciousbrains/autoloader.php';
	require_once $abspath . '/classes/pro/as3cf-pro-licences-updates.php';
	require_once $abspath . '/classes/pro/amazon-s3-and-cloudfront-pro.php';
	require_once $abspath . '/classes/pro/as3cf-pro-plugin-compatibility.php';
	require_once $abspath . '/classes/pro/as3cf-pro-utils.php';
	require_once $abspath . '/classes/pro/as3cf-async-request.php';
	require_once $abspath . '/classes/pro/as3cf-background-process.php';

	new WP_Offload_Media_Autoloader( 'WP_Offload_Media', $abspath );

	$as3cf    = new Amazon_S3_And_CloudFront_Pro( __FILE__ );
	$as3cfpro = $as3cf; // Pro global alias

	do_action( 'as3cf_init', $as3cf );
	do_action( 'as3cf_pro_init', $as3cf );
}

add_action( 'init', 'as3cf_pro_init' );

// If AWS still active need to be around to satisfy addon version checks until upgraded.
add_action( 'aws_init', 'as3cf_pro_init', 11 );
