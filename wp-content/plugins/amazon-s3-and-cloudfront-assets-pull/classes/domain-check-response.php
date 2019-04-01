<?php

namespace DeliciousBrains\WP_Offload_Media_Assets_Pull;

use DeliciousBrains\WP_Offload_Media_Assets_Pull\Exceptions\Signature_Verification_Exception;

class Domain_Check_Response extends \WP_REST_Response {

	/**
	 * Verify that this response is valid for the given hashed signature.
	 *
	 * @param string $signature A hashed signature to verify this response against.
	 *
	 * @throws Signature_Verification_Exception
	 */
	public function verify_signature( $signature ) {
		if ( ! wp_check_password( $this->raw_signature(), $signature ) ) {
			throw new Signature_Verification_Exception( 'Invalid request signature.' );
		}
	}

	/**
	 * Get the hashed signature for this response.
	 *
	 * @return string
	 */
	public function hashed_signature() {
		return wp_hash_password( $this->raw_signature() );
	}

	/**
	 * Get the raw signature for this response.
	 *
	 * @return string
	 */
	protected function raw_signature() {
		return \AS3CF_Utils::reduce_url( network_home_url() ) . '|' . json_encode( $this->jsonSerialize() ) . '|' . AUTH_SALT;
	}
}