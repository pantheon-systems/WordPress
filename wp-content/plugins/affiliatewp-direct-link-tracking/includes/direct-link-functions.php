<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Deletes a direct link
 *
 * @since 1.0.0
 * @param $delete_data bool
 * @return bool
 */
function affwp_dlt_delete_direct_link( $url_id ) {

	if ( is_numeric( $url_id ) ) {
		$url_id = absint( $url_id );
	} else {
		return false;
	}

	return affiliatewp_direct_link_tracking()->direct_links->delete( $url_id );

}

/**
 * Get a single direct link
 *
 * @since 1.1
 * @param $url_id The URL ID of the direct link
 * @return array $direct_link
 */
function affwp_dlt_get_direct_link( $url_id = 0 ) {

	if ( is_numeric( $url_id ) ) {
		$url_id = absint( $url_id );
	} else {
		return false;
	}

	return affiliatewp_direct_link_tracking()->direct_links->get( $url_id );
}

/**
 * Get direct links
 *
 * @since 1.1
 * @return array $direct_links, false otherwise
 */
function affwp_dlt_get_direct_links( $args = array() ) {

	$defaults = array(
		'affiliate_id' => '',
		'status'       => '',
		'number'       => -1
	);

	$args = wp_parse_args( $args, $defaults );

	$direct_links = affiliatewp_direct_link_tracking()->direct_links->get_direct_links( $args );

	if ( $direct_links ) {
		return $direct_links;
	}

	return false;

}

/**
 * Count the direct links
 *
 * @since 1.1
 * @return int
 */
function affwp_dlt_count_direct_links( $args = array() ) {
	return affiliatewp_direct_link_tracking()->direct_links->count( $args );
}

/**
 * Adds a direct link to the database
 *
 * @since  1.0.0
 * @uses   affwp_dlt_set_direct_link_status()
 *
 * @return bool true If direct link was added, false otherwise
 */
function affwp_dlt_add_direct_link( $data = array() ) {

	// If no affiliate is specified, add it to the currently logged in affiliate.
	if ( empty( $data['affiliate_id'] ) ) {
		$affiliate_id = affwp_get_affiliate_id();
	} else {
		$affiliate_id = $data['affiliate_id'];
	}

	// Status.
	if ( ! empty( $data['status'] ) ) {
		$status = $data['status'];
	} else {
		$status = 'pending';
	}

	$args = array(
		'url'          => ! empty( $data['url'] ) ? $data['url'] : '',
		'status'       => $status,
		'affiliate_id' => $affiliate_id,
	);

	$url_id = affiliatewp_direct_link_tracking()->direct_links->add( $args );

    if ( $url_id ) {

		// Not ideal since the status does not need to be set again but we need the affwp_direct_link_tracking_set_direct_link_status hook to be fired.
		if ( ! empty( $data['status'] ) ) {
			affwp_dlt_set_direct_link_status( $url_id, $data['status'] );
		}

        return true;
    }

    return false;

}

/**
 * Update a single direct link in the database
 *
 * @since  1.0.0
 * @param  int $url_id ID of URL
 * @param  array $data Array of data passed in
 * @uses   affwp_dlt_set_direct_link_status()
 *
 * @return bool
 */
function affwp_dlt_update_direct_link( $url_id, $data = array() ) {

	// Not ideal since the status does not need to be set again, but we need the affwp_direct_link_tracking_set_direct_link_status hook to be fired.
	// This needs to be run before $updated or the status will be overwritten and we won't be able to get the value of the old URL.
	if ( ! empty( $data['status'] ) ) {
		affwp_dlt_set_direct_link_status( $url_id, $data['status'] );
	}

	$updated = affiliatewp_direct_link_tracking()->direct_links->update( $url_id, $data, '', 'direct_link' );

	if ( (bool) true === $updated ) {
		return true;
	}

	return false;

}

/**
 * Sets a direct link's status
 *
 * @since  1.0
 * @return bool
 */
function affwp_dlt_set_direct_link_status( $direct_link, $status = '' ) {

	if ( is_object( $direct_link ) && isset( $direct_link->url_id ) ) {
		$url_id = $direct_link->$url_id;
	} elseif ( is_numeric( $direct_link ) ) {
		$url_id = absint( $direct_link );
	} else {
		return false;
	}

	$old_status = affiliatewp_direct_link_tracking()->direct_links->get_column( 'status', $url_id );

	do_action( 'affwp_direct_link_tracking_set_direct_link_status', $url_id, $status, $old_status );

	if ( affiliatewp_direct_link_tracking()->direct_links->update( $url_id, array( 'status' => $status ), '', 'direct_link' ) ) {
		return true;
	}

}

/**
 * Get a direct link's status.
 *
 * @since  1.1
 * @param  int $url_id url_id of the direct link
 * @return string $status, false otherwise
 */
function affwp_dlt_get_direct_link_status( $url_id = '' ) {

	if ( ! $url_id ) {
		return false;
	}

	$status = affiliatewp_direct_link_tracking()->direct_links->get_column( 'status', $url_id );

	if ( $status ) {
		return $status;
	}

	return false;
}
