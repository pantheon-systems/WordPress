<?php

/**
 * @author OnTheGo Systems
 */
class WPML_TM_AJAX {
	/**
	 * @param string $action
	 *
	 * @return bool
	 */
	protected function is_valid_request( $action = '' ) {
		if ( ! $action ) {
			$action = array_key_exists( 'action', $_POST ) ? $_POST['action'] : '';
		}

		if ( ! array_key_exists( 'nonce', $_POST ) || ! $action
		     || ! wp_verify_nonce( sanitize_key( $_POST['nonce'] ), $action ) ) {

			wp_send_json_error( __( 'You have attempted to submit data in a not legit way.',
				'wpml-translation-management' ) );

			return false;
		}

		return true;
	}

}