<?php

if ( ! function_exists( 'get_preview_post_link' ) ) :

/**
 * Retrieves the URL used for the post preview.
 *
 * Allows additional query args to be appended.
 *
 * @since 4.4.0
 *
 * @param int|WP_Post $post         Optional. Post ID or `WP_Post` object. Defaults to global `$post`.
 * @param array       $query_args   Optional. Array of additional query args to be appended to the link.
 *                                  Default empty array.
 * @param string      $preview_link Optional. Base preview link to be used if it should differ from the
 *                                  post permalink. Default empty.
 * @return string|null URL used for the post preview, or null if the post does not exist.
 */
function get_preview_post_link( $post = null, $query_args = array(), $preview_link = '' ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return;
	}

	$post_type_object = get_post_type_object( $post->post_type );
	if ( is_post_type_viewable( $post_type_object ) ) {
		if ( ! $preview_link ) {
			$preview_link = set_url_scheme( get_permalink( $post ) );
		}

		$query_args['preview'] = 'true';
		$preview_link = add_query_arg( $query_args, $preview_link );
	}

	/**
	 * Filters the URL used for a post preview.
	 *
	 * @since 2.0.5
	 * @since 4.0.0 Added the `$post` parameter.
	 *
	 * @param string  $preview_link URL used for the post preview.
	 * @param WP_Post $post         Post object.
	 */
	return apply_filters( 'preview_post_link', $preview_link, $post );
}

endif;


if ( ! function_exists( 'is_post_type_viewable' ) ) :

/**
 * Determines whether a post type is considered "viewable".
 *
 * For built-in post types such as posts and pages, the 'public' value will be evaluated.
 * For all others, the 'publicly_queryable' value will be used.
 *
 * @since 4.4.0
 * @since 4.5.0 Added the ability to pass a post type name in addition to object.
 * @since 4.6.0 Converted the `$post_type` parameter to accept a WP_Post_Type object.
 *
 * @param string|WP_Post_Type $post_type Post type name or object.
 * @return bool Whether the post type should be considered viewable.
 */
function is_post_type_viewable( $post_type ) {
	if ( is_scalar( $post_type ) ) {
		$post_type = get_post_type_object( $post_type );
		if ( ! $post_type ) {
			return false;
		}
	}

	return $post_type->publicly_queryable || ( $post_type->_builtin && $post_type->public );
}

endif;


/**
 * wp_make_content_images_responsive in WP 4.4.0+
 */
if ( ! function_exists( 'wp_make_content_images_responsive' ) ) :

function wp_make_content_images_responsive( $content ) {
	return $content;
}

endif;


if ( ! function_exists( 'wp_doing_ajax' ) ) :

/**
 * Determines whether the current request is a WordPress Ajax request.
 *
 * @since 4.7.0
 *
 * @return bool True if it's a WordPress Ajax request, false otherwise.
 */
function wp_doing_ajax() {
	/**
	 * Filters whether the current request is a WordPress Ajax request.
	 *
	 * @since 4.7.0
	 *
	 * @param bool $wp_doing_ajax Whether the current request is a WordPress Ajax request.
	 */
	return apply_filters( 'wp_doing_ajax', defined( 'DOING_AJAX' ) && DOING_AJAX );
}

endif;


if ( ! function_exists( 'sanitize_hex_color' ) ) :

/**
 * Sanitizes a hex color.
 *
 * Returns either '', a 3 or 6 digit hex color (with #), or nothing.
 * For sanitizing values without a #, see sanitize_hex_color_no_hash().
 *
 * @since 3.4.0
 *
 * @param string $color
 * @return string|void
 */
function sanitize_hex_color( $color ) {
	if ( '' === $color ) {
		return '';
	}

	// 3 or 6 hex digits, or the empty string.
	if ( preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) ) {
		return $color;
	}
}

endif;
