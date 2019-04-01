<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Determines if Affiliate Landing Pages is enabled or not
 *
 * @since 1.0
 *
 * @return bool true if enabled or false otherwise.
 */
function affwp_alp_is_enabled() {

	$is_enabled = affiliate_wp()->settings->get( 'affiliate-landing-pages' );

	if ( $is_enabled ) {
		return (bool) true;
	}

	return (bool) false;

}

/**
 * Get an array of an affiliate's landing page IDs
 *
 * @since  1.0
 *
 * @param  string $user_name The Affiliate's username
 * @return array $ids IDs of landing pages, empty array otherwise
 */
function affwp_alp_get_landing_page_ids( $user_name = '' ) {

	if ( empty( $user_name ) ) {
		return array();
	}

	global $wpdb;

	$key = 'affwp_landing_page_user_name';
	$ids = $wpdb->get_results( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta where meta_key = %s and meta_value = %s", $key, $user_name ), ARRAY_A );

	if ( $ids ) {
		return wp_list_pluck( $ids, 'post_id' );
	}

	return array();

}
