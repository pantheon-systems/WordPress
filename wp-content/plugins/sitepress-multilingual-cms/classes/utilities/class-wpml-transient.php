<?php

/**
 * Class WPML_Transient
 *
 * Due to some conflicts between cached environments (e.g. using W3TC) and the normal
 * WP Transients API, we've added this class which should behaves almost like the normal
 * transients API. Except for the fact that it is stored as normal options, so WP won't
 * recognize/treat it as a transient.
 */
class WPML_Transient {

	const WPML_TRANSIENT_PREFIX = '_wpml_transient_';

	/**
	 * @param string $name
	 * @param string $value
	 * @param string $expiration
	 */
	public function set( $name, $value, $expiration = '' ) {
		$data = array(
			'value'      => $value,
			'expiration' => $expiration ? time() + (int) $expiration : '',
		);

		update_option( self::WPML_TRANSIENT_PREFIX . $name, $data );
	}

	/**
	 * @param string $name
	 *
	 * @return string
	 */
	public function get( $name ) {
		$data = get_option( self::WPML_TRANSIENT_PREFIX . $name );

		if ( $data ) {
			if ( (int) $data['expiration'] < time() ) {
				delete_option( self::WPML_TRANSIENT_PREFIX . $name );

				return '';
			}

			return $data['value'];
		}

		return '';
	}

	/**
	 * @param string $name
	 */
	public function delete( $name ) {
		delete_option( self::WPML_TRANSIENT_PREFIX . $name );
	}
}