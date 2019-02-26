<?php
/**
 * WPML helpers
 *
 * @package Salient WordPress Theme
 * @subpackage helpers
 * @version 9.0.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( defined( 'ICL_LANGUAGE_CODE' ) ) {

	add_filter( 'icl_ls_languages', 'nectar_wmpl_duplicate_content_fix' );
	function nectar_wmpl_duplicate_content_fix( $languages ) {
		wp_reset_query();
		return $languages;
	}

	add_filter( 'wpml_pb_shortcode_content_for_translation', 'nectar_wpml_filter_content_for_translation', 10, 2 );

	function nectar_wpml_filter_content_for_translation( $content, $post_id ) {

		if ( 'portfolio' === get_post_type( $post_id ) ) {
			$content = get_post_meta( $post_id, '_nectar_portfolio_extra_content', true );
		}
		return $content;
	}

	add_filter( 'wpml_pb_shortcodes_save_translation', 'nectar_wpml_filter_save_translation', 10, 3 );

	function nectar_wpml_filter_save_translation( $saved, $post_id, $new_content ) {

		if ( 'portfolio' === get_post_type( $post_id ) ) {
			update_post_meta( $post_id, '_nectar_portfolio_extra_content', $new_content );
			$saved = true;
		}
		return $saved;
	}
}
