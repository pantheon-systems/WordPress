<?php

/**
 * Class WPML_Troubleshoot_Action
 * @author onTheGoSystems
 */
class WPML_Troubleshoot_Action {

	const SYNC_POSTS_TAXONOMIES_SLUG = 'synchronize_posts_taxonomies';

	/**
	 * @return bool
	 */
	public function is_valid_request() {
		$response = false;

		if ( array_key_exists( 'nonce', $_POST ) && array_key_exists( 'debug_action', $_POST )
		     && self::SYNC_POSTS_TAXONOMIES_SLUG === $_POST['debug_action']
		) {
			$response = wp_verify_nonce( $_POST['nonce'], $_POST['debug_action'] );

			if ( ! $response ) {
				wp_send_json_error( array( 'message' => esc_html__( 'Invalid nonce.', 'sitepress' ) ) );
				return $response;
			}
		}

		return $response;
	}
}