<?php

/* PHP 5.3 - start */

if ( false === function_exists( 'lcfirst' ) ) {
	/**
	 * Make a string's first character lowercase
	 *
	 * @param string $str
	 *
	 * @return string the resulting string.
	 */
	function lcfirst( $str ) {
		$str[0] = strtolower( $str[0] );

		return (string) $str;
	}
}

if ( get_magic_quotes_gpc() ) {
	if ( ! function_exists( 'stripslashes_deep' ) ) {
		function stripslashes_deep( $value ) {
			$value = is_array( $value ) ?
				array_map( 'stripslashes_deep', $value ) :
				stripslashes( $value );

			return $value;
		}
	}

	$_POST   = array_map( 'stripslashes_deep', $_POST );
	$_GET    = array_map( 'stripslashes_deep', $_GET );
	$_COOKIE = array_map( 'stripslashes_deep', $_COOKIE );
}
/* PHP 5.3 - end */

add_action( 'plugins_loaded', 'wcml_check_wpml_functions' );

function wcml_check_wpml_functions() {

	if ( ! has_filter( 'translate_object_id' ) ) {
		add_filter( 'translate_object_id', 'icl_object_id', 10, 4 );
	}

	if ( ! has_action( 'wpml_register_single_string' ) ) {
		if ( function_exists( 'wpml_register_single_string_action' ) ) {
			add_action( 'wpml_register_single_string', 'wpml_register_single_string_action', 10, 4 );
		} elseif ( function_exists( 'icl_register_string' ) ) {
			add_action( 'wpml_register_single_string', 'icl_register_string', 10, 4 );
		}
	}

}

// two WordPress functions that were added in 4.4.0
if ( version_compare( $GLOBALS['wp_version'], '4.4.0', '<' ) ) {

	if ( ! function_exists( 'get_the_post_thumbnail_url' ) ) {
		function get_the_post_thumbnail_url( $post = null, $size = 'post-thumbnail' ) {
			$post_thumbnail_id = get_post_thumbnail_id( $post );
			if ( ! $post_thumbnail_id ) {
				return false;
			}

			return wp_get_attachment_image_url( $post_thumbnail_id, $size );
		}
	}

	if ( ! function_exists( 'wp_get_attachment_image_url' ) ) {
		function wp_get_attachment_image_url( $attachment_id, $size = 'thumbnail', $icon = false ) {
			$image = wp_get_attachment_image_src( $attachment_id, $size, $icon );

			return isset( $image['0'] ) ? $image['0'] : false;
		}
	}

	if ( ! function_exists( 'rest_get_url_prefix' ) ) {
		function rest_get_url_prefix() {
			/**
			 * Filters the REST URL prefix.
			 *
			 * @param string $prefix URL prefix. Default 'wp-json'.
			 */
			return apply_filters( 'rest_url_prefix', 'wp-json' );
		}
	}

}
