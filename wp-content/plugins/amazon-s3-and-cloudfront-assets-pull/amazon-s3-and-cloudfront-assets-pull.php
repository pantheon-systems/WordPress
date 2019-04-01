<?php
/*
Plugin Name: WP Offload Media - Assets Pull Addon
Plugin URI: https://deliciousbrains.com/wp-offload-media/doc/assets-pull-addon/
Description: An addon for WP Offload Media to serve your site's JS, CSS, and other enqueued assets from Amazon CloudFront or another CDN.
Author: Delicious Brains
Version: 1.1
Author URI: https://deliciousbrains.com
Network: True

// Copyright (c) 2017 Delicious Brains. All rights reserved.
//
// Released under the GPL license
// http://www.opensource.org/licenses/gpl-license.php
//
// **********************************************************************
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// **********************************************************************
*/

require_once dirname( __FILE__ ) . '/version.php';
require_once dirname( __FILE__ ) . '/classes/as3cf-compatibility-check.php';
require_once dirname( __FILE__ ) . '/classes/addon-activation-data.php';

$as3cfpro_plugin_version_required = '2.0';

global $as3cf_assets_pull_compat_check;
$as3cf_assets_pull_compat_check = new AS3CF_Compatibility_Check(
	'WP Offload Media - Assets Pull Addon',
	'amazon-s3-and-cloudfront-assets-pull',
	__FILE__,
	'WP Offload Media',
	'amazon-s3-and-cloudfront-pro',
	$as3cfpro_plugin_version_required,
	null,
	false,
	'https://deliciousbrains.com/wp-offload-media/'
);

/**
 * @param Amazon_S3_And_CloudFront_Pro $as3cf
 */
function as3cf_assets_pull_init( $as3cf ) {
	global $as3cf_assets_pull_compat_check;
	if ( ! $as3cf_assets_pull_compat_check->is_compatible() ) {
		return;
	}

	global $as3cf_assets_pull;
	$abspath = dirname( __FILE__ );

	require_once $abspath . '/classes/amazon-s3-and-cloudfront-assets-pull.php';
	require_once $abspath . '/wp-offload-media-autoloader.php';

	new WP_Offload_Media_Autoloader( 'WP_Offload_Media_Assets_Pull', $abspath );
	$as3cf_assets_pull = new Amazon_S3_And_CloudFront_Assets_Pull( __FILE__ );
}

add_action( 'as3cf_pro_init', 'as3cf_assets_pull_init', 14 );

/**
 * Plugin activation handling.
 *
 * Must be registered before plugins_loaded.
 */
register_activation_hook( __FILE__, 'DeliciousBrains\\WP_Offload_Media_Assets_Pull\\Addon_Activation_Data::assets_pull_activated' );

/**
 * Deactivation handling.
 *
 * Must be registered before plugins_loaded.
 */
register_deactivation_hook( __FILE__, 'DeliciousBrains\\WP_Offload_Media_Assets_Pull\\Addon_Activation_Data::assets_pull_deactivated' );
