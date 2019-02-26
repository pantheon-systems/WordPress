<?php

class WPML_TP_Lock {

	private $lockable_endpoints = array(
		'/jobs/{job_id}/xliff.json',
	);

	/** @var WPML_WP_API $wp_api */
	private $wp_api;

	public function __construct( WPML_WP_API $wp_api ) {
		$this->wp_api = $wp_api;
	}

	/**
	 * @param string $url
	 *
	 * @return bool
	 */
	public function is_locked( $url ) {
		return $this->get_locker_reason() && $this->is_lockable( $url );
	}

	/**
	 * @return string|false
	 */
	public function get_locker_reason() {
		if ( 'test' === $this->wp_api->constant( 'WPML_ENVIRONMENT' ) ) {
			return __( 'The constant WPML_ENVIRONMENT is set to "Test".', 'wpml-translation-management' );
		}

		return false;
	}

	/**
	 * @param string $url
	 *
	 * @return bool
	 */
	private function is_lockable( $url ) {
		$endpoint = preg_replace( '#^' . OTG_TRANSLATION_PROXY_URL . '#', '', $url, 1 );
		return in_array( $endpoint, $this->lockable_endpoints, true );
	}
}