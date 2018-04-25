<?php
/**
 * View Functions
 *
 * @package Strong_Testimonials
 */

/**
 * Return the default view settings.
 *
 * @param bool $unfiltered
 * @since 2.30.5
 *
 * @return array
 */
function wpmtst_get_view_default( $unfiltered = false ) {
	$default = get_option( 'wpmtst_view_default' );
	if ( ! $unfiltered ) {
		$default = apply_filters( 'wpmtst_view_default', $default );
	}

	return $default;
}

/**
 * @return array|mixed|null|object
 */
function wpmtst_get_views() {
	global $wpdb;
	$wpdb->show_errors();
	$table_name = $wpdb->prefix . 'strong_views';
	$results = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY id ASC", ARRAY_A );
	$wpdb->hide_errors();

	if ( $wpdb->last_error ) {
		deactivate_plugins( 'strong-testimonials/strong-testimonials.php' );
		$message = '<p><span style="color: #CD0000;">';
		$message .= __( 'An error occurred.', 'strong-testimonials' ) . '</span>&nbsp;';
		$message .= __( 'The plugin has been deactivated.', 'strong-testimonials' ) . '&nbsp;';
		$message .= sprintf( __( 'Please <a href="%s" target="_blank">open a support ticket</a>.', 'strong-testimonials' ), esc_url( 'https://support.strongplugins.com/new-ticket/' ) ) . '</p>';
		$message .= '<p>' . sprintf( __( '<a href="%s">Go back to Dashboard</a>', 'strong-testimonials' ), esc_url( admin_url() ) ) . '</p>';
		wp_die( sprintf( '<div class="error strong-view-error">%s</div>', $message ) );
	}

	return $results;
}

/**
 * @param $views
 *
 * @return mixed
 */
function wpmtst_unserialize_views( $views ) {
	foreach( $views as $key => $view ) {
		$views[$key]['data'] = unserialize( $view['value'] );
	}

	return $views;
}

/**
 * @param $id
 *
 * @return array
 */
function wpmtst_get_view( $id ) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'strong_views';
	$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", (int) $id ), ARRAY_A );

	return $row;
}

/**
 * Find the view for the single template.
 *
 * @return bool|array
 */
function wpmtst_find_single_template_view() {
	$views = wpmtst_get_views();
	/*
	 * [id] => 1
     * [name] => TEST
     * [value] => {serialized_array}
	 */

	foreach ( $views as $view ) {
		$view_data = maybe_unserialize( $view['value'] );
		if ( isset( $view_data['mode'] ) && 'single_template' == $view_data['mode'] ) {
			return $view_data;
		}
	}

	return false;
}

