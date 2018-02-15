<?php

/**
 * Prevent other post ordering plugins, in admin_menu hook.
 *
 * @since 1.16.0
 */
function wpmtst_deny_plugins_menu() {

	/**
	 * Post Types Order
	 */
	if ( is_plugin_active( 'post-types-order/post-types-order.php' ) ) {
		remove_submenu_page( 'edit.php?post_type=wpm-testimonial', 'order-post-types-wpm-testimonial' );
	}

}
add_action( 'admin_menu', 'wpmtst_deny_plugins_menu', 200 );


/**
 * Plugin and theme compatibility in admin.
 *
 * @since 2.4.0
 */
function wpmtst_compat_admin_init() {
	$theme = wp_get_theme();

	/* ------------------------------------------------------------
	 * Theme Name: Mercury
	 * Theme URI: http://themes.themegoods2.com/mercury
	 * Description: Premium Template for Photography Portfolio
	 * Version: 1.7.5
	 * Author: Peerapong Pulpipatnan
	 * Author URI: http://themeforest.net/user/peerapong
	 * ------------------------------------------------------------
	 * Mercury enqueues its scripts and styles poorly.
	 * 1. on the `admin_init` hook
	 * 2. UNconditionally
	 */
	if ( 'Mercury' == $theme->get( 'Name' ) && 'http://themes.themegoods2.com/mercury' == $theme->get( 'ThemeURI' )	) {

		/** Screen information is not available yet. */
		//$screen = get_current_screen();
		//if ( $screen && 'wpm-testimonial' == $screen->post_type ) {

		if ( false !== strpos( $_SERVER['QUERY_STRING'], 'post_type=wpm-testimonial' ) ) {
			if ( function_exists( 'pp_add_init' ) ) {
				remove_action( 'admin_init', 'pp_add_init' );
			}
		}

	}
}
add_action( 'admin_init', 'wpmtst_compat_admin_init', 1 );


/**
 * Prevent other post ordering plugins, in admin_init hook.
 *
 * @since 1.16.0
 */
function wpmtst_deny_plugins_init() {

	/**
	 * Intuitive Custom Post Order
	 */
	if ( is_plugin_active( 'intuitive-custom-post-order/intuitive-custom-post-order.php' ) ) {
		$options = get_option( 'hicpo_options' );
		$update = false;

		if ( isset( $options['objects'] ) && is_array( $options['objects'] ) ) {
			if ( in_array( 'wpm-testimonial', $options['objects'] ) ) {
				$options['objects'] = array_diff( $options['objects'], array( 'wpm-testimonial' ) );
				$update = true;
			}
		}

		if ( isset( $options['tags'] ) && is_array( $options['tags'] ) ) {
			if ( in_array( 'wpm-testimonial-category', $options['tags'] ) ) {
				$options['tags'] = array_diff( $options['tags'], array( 'wpm-testimonial-category' ) );
				$update = true;
			}
		}

		if ( $update )
			update_option( 'hicpo_options', $options );
	}

	/**
	 * Simple Custom Post Order
	 */
	if ( is_plugin_active( 'simple-custom-post-order/simple-custom-post-order.php' ) ) {
		$options = get_option( 'scporder_options' );
		$update = false;

		if ( isset( $options['objects'] ) && is_array( $options['objects'] ) ) {
			if ( in_array( 'wpm-testimonial', $options['objects'] ) ) {
				$options['objects'] = array_diff( $options['objects'], array( 'wpm-testimonial' ) );
				$update = true;
			}
		}

		if ( isset( $options['tags'] ) && is_array( $options['tags'] ) ) {
			if ( in_array( 'wpm-testimonial-category', $options['tags'] ) ) {
				$options['tags'] = array_diff( $options['tags'], array( 'wpm-testimonial-category' ) );
				$update = true;
			}
		}

		if ( $update )
			update_option( 'scporder_options', $options );
	}

}
add_action( 'admin_init', 'wpmtst_deny_plugins_init', 200 );
