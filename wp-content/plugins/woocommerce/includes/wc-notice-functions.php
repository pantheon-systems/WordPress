<?php
/**
 * WooCommerce Message Functions
 *
 * Functions for error/message handling and display.
 *
 * @package WooCommerce/Functions
 * @version 2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the count of notices added, either for all notices (default) or for one.
 * particular notice type specified by $notice_type.
 *
 * @since  2.1
 * @param  string $notice_type Optional. The name of the notice type - either error, success or notice.
 * @return int
 */
function wc_notice_count( $notice_type = '' ) {
	if ( ! did_action( 'woocommerce_init' ) ) {
		wc_doing_it_wrong( __FUNCTION__, __( 'This function should not be called before woocommerce_init.', 'woocommerce' ), '2.3' );
		return;
	}

	$notice_count = 0;
	$all_notices  = WC()->session->get( 'wc_notices', array() );

	if ( isset( $all_notices[ $notice_type ] ) ) {

		$notice_count = count( $all_notices[ $notice_type ] );

	} elseif ( empty( $notice_type ) ) {

		foreach ( $all_notices as $notices ) {
			$notice_count += count( $notices );
		}
	}

	return $notice_count;
}

/**
 * Check if a notice has already been added.
 *
 * @since  2.1
 * @param  string $message The text to display in the notice.
 * @param  string $notice_type Optional. The name of the notice type - either error, success or notice.
 * @return bool
 */
function wc_has_notice( $message, $notice_type = 'success' ) {
	if ( ! did_action( 'woocommerce_init' ) ) {
		wc_doing_it_wrong( __FUNCTION__, __( 'This function should not be called before woocommerce_init.', 'woocommerce' ), '2.3' );
		return false;
	}

	$notices = WC()->session->get( 'wc_notices', array() );
	$notices = isset( $notices[ $notice_type ] ) ? $notices[ $notice_type ] : array();
	return array_search( $message, $notices, true ) !== false;
}

/**
 * Add and store a notice.
 *
 * @since 2.1
 * @param string $message The text to display in the notice.
 * @param string $notice_type Optional. The name of the notice type - either error, success or notice.
 */
function wc_add_notice( $message, $notice_type = 'success' ) {
	if ( ! did_action( 'woocommerce_init' ) ) {
		wc_doing_it_wrong( __FUNCTION__, __( 'This function should not be called before woocommerce_init.', 'woocommerce' ), '2.3' );
		return;
	}

	$notices = WC()->session->get( 'wc_notices', array() );

	// Backward compatibility.
	if ( 'success' === $notice_type ) {
		$message = apply_filters( 'woocommerce_add_message', $message );
	}

	$notices[ $notice_type ][] = apply_filters( 'woocommerce_add_' . $notice_type, $message );

	WC()->session->set( 'wc_notices', $notices );
}

/**
 * Set all notices at once.
 *
 * @since 2.6.0
 * @param mixed $notices Array of notices.
 */
function wc_set_notices( $notices ) {
	if ( ! did_action( 'woocommerce_init' ) ) {
		wc_doing_it_wrong( __FUNCTION__, __( 'This function should not be called before woocommerce_init.', 'woocommerce' ), '2.6' );
		return;
	}
	WC()->session->set( 'wc_notices', $notices );
}


/**
 * Unset all notices.
 *
 * @since 2.1
 */
function wc_clear_notices() {
	if ( ! did_action( 'woocommerce_init' ) ) {
		wc_doing_it_wrong( __FUNCTION__, __( 'This function should not be called before woocommerce_init.', 'woocommerce' ), '2.3' );
		return;
	}
	WC()->session->set( 'wc_notices', null );
}

/**
 * Prints messages and errors which are stored in the session, then clears them.
 *
 * @since 2.1
 * @param bool $return true to return rather than echo. @since 3.5.0.
 * @return string|null
 */
function wc_print_notices( $return = false ) {
	if ( ! did_action( 'woocommerce_init' ) ) {
		wc_doing_it_wrong( __FUNCTION__, __( 'This function should not be called before woocommerce_init.', 'woocommerce' ), '2.3' );
		return;
	}

	$all_notices  = WC()->session->get( 'wc_notices', array() );
	$notice_types = apply_filters( 'woocommerce_notice_types', array( 'error', 'success', 'notice' ) );

	// Buffer output.
	ob_start();

	foreach ( $notice_types as $notice_type ) {
		if ( wc_notice_count( $notice_type ) > 0 ) {
			wc_get_template( "notices/{$notice_type}.php", array(
				'messages' => array_filter( $all_notices[ $notice_type ] ),
			) );
		}
	}

	wc_clear_notices();

	$notices = wc_kses_notice( ob_get_clean() );

	if ( $return ) {
		return $notices;
	}

	echo $notices; // WPCS: XSS ok.
}

/**
 * Print a single notice immediately.
 *
 * @since 2.1
 * @param string $message The text to display in the notice.
 * @param string $notice_type Optional. The singular name of the notice type - either error, success or notice.
 */
function wc_print_notice( $message, $notice_type = 'success' ) {
	if ( 'success' === $notice_type ) {
		$message = apply_filters( 'woocommerce_add_message', $message );
	}

	wc_get_template( "notices/{$notice_type}.php", array(
		'messages' => array( apply_filters( 'woocommerce_add_' . $notice_type, $message ) ),
	) );
}

/**
 * Returns all queued notices, optionally filtered by a notice type.
 *
 * @since  2.1
 * @param  string $notice_type Optional. The singular name of the notice type - either error, success or notice.
 * @return array|mixed
 */
function wc_get_notices( $notice_type = '' ) {
	if ( ! did_action( 'woocommerce_init' ) ) {
		wc_doing_it_wrong( __FUNCTION__, __( 'This function should not be called before woocommerce_init.', 'woocommerce' ), '2.3' );
		return;
	}

	$all_notices = WC()->session->get( 'wc_notices', array() );

	if ( empty( $notice_type ) ) {
		$notices = $all_notices;
	} elseif ( isset( $all_notices[ $notice_type ] ) ) {
		$notices = $all_notices[ $notice_type ];
	} else {
		$notices = array();
	}

	return $notices;
}

/**
 * Add notices for WP Errors.
 *
 * @param WP_Error $errors Errors.
 */
function wc_add_wp_error_notices( $errors ) {
	if ( is_wp_error( $errors ) && $errors->get_error_messages() ) {
		foreach ( $errors->get_error_messages() as $error ) {
			wc_add_notice( $error, 'error' );
		}
	}
}

/**
 * Filters out the same tags as wp_kses_post, but allows tabindex for <a> element.
 *
 * @since 3.5.0
 * @param string $message Content to filter through kses.
 * @return string
 */
function wc_kses_notice( $message ) {
	return wp_kses( $message,
		array_replace_recursive( // phpcs:ignore PHPCompatibility.PHP.NewFunctions.array_replace_recursiveFound
			wp_kses_allowed_html( 'post' ),
			array(
				'a' => array(
					'tabindex' => true,
				),
			)
		)
	);
}
