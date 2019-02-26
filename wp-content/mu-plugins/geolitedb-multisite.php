<?php

/**
 * @wordpress-plugin
 * Plugin Name:       GeoLite2 DB Multisite
 * Description:       This plugin centralizes the GeoLite2-Country database used by WooCommerce so that all sites use the same database.
 * Version:           1.0.0
 * Author:            Christopher Cook
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( is_multisite() ) {

  // Define common path for GeoLite database so subsites do not need to duplicate it
  function gldbms_geolite_path( $path ) {
    return WP_CONTENT_DIR . '/GeoLite2-Country.mmdb';
  }
  add_filter( 'woocommerce_geolocation_local_database_path', 'gldbms_geolite_path', 1 );

}
