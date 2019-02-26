<?php

/**
 * Class WPML_Media_Attachments_Query
 */
class WPML_Media_Attachments_Query implements IWPML_Action {


	public function add_hooks(){
		add_action( 'pre_get_posts', array( $this, 'adjust_attachment_query' ), 10 );
	}

	/**
	 * Set `suppress_filters` to false if attachment is displayed.
	 *
	 * @param WP_Query $query
	 *
	 * @return WP_Query
	 */
	public function adjust_attachment_query( $query ) {
		if ( isset( $query->query['post_type'] ) && 'attachment' === $query->query['post_type'] ) {
			$query->set( 'suppress_filters', false );
		}
		return $query;
	}
}
