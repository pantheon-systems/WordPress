<?php

/**
 * Misc. functions for backward-compatibility.
 */

if ( ! function_exists( 'wp_get_attachment_image_url' ) ) {
	/**
	 * Get the URL of an image attachment.
	 *
	 */
	function wp_get_attachment_image_url( $attachment_id, $size = 'thumbnail', $icon = false ) {
		$image = wp_get_attachment_image_src( $attachment_id, $size, $icon );
		return isset( $image['0'] ) ? $image['0'] : false;
	}
}
