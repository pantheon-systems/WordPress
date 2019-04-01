<?php

/**
 * Class WPML_Attachments_Urls_With_Identical_Slugs
 *
 * @link https://onthegosystems.myjetbrains.com/youtrack/issue/wpmlcore-4700
 */
class WPML_Attachments_Urls_With_Identical_Slugs implements IWPML_Action {

	public function add_hooks() {
		add_filter( 'parse_query', array( $this, 'translate_attachment_id' ), PHP_INT_MAX );
	}

	/**
	 * Translate the attachment id in the $wp_query during parse_query
	 *
	 * @param WP_Query $wp_query
	 *
	 * @return WP_Query
	 */
	public function translate_attachment_id( $wp_query ) {

		if ( isset( $wp_query->query['pagename'] ) && false !== strpos( $wp_query->query['pagename'], '/' ) ) {

			if ( ! empty( $wp_query->queried_object_id ) ) {
				$post_type = get_post_field( 'post_type', $wp_query->queried_object_id );
				if ( $post_type === 'attachment' ) {
					$wp_query->queried_object_id = apply_filters( 'wpml_object_id', $wp_query->queried_object_id, 'attachment', true );
					$wp_query->queried_object    = get_post( $wp_query->queried_object_id );
				}

			}

		}

		return $wp_query;
	}

}