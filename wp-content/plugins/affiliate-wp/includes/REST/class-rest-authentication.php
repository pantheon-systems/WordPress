<?php
namespace AffWP\REST;

/**
 * Implements API key authentication for AffiliateWP REST endpoints.
 *
 * @since 1.9
 */
final class Authentication {

	/**
	 * Constructor.
	 *
	 * @access public
	 * @since  1.9
	 */
	public function __construct() {
		add_filter( 'determine_current_user', array( $this, 'authenticate' ), 20 );
	}

	/**
	 * Authenticates a user using Basic Auth.
	 *
	 * User is the public key, password is the token.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @param int $user ID for the current user.
	 * @return int API consumer user ID if authenticated.
	 */
	public function authenticate( $user_id ) {

		if ( ! empty( $user_id ) || empty( $_SERVER['PHP_AUTH_USER'] ) ) {
			return $user_id;
		}

		$public_key = $_SERVER['PHP_AUTH_USER'];
		$token      = $_SERVER['PHP_AUTH_PW'];

		// Prevent recursion.
		remove_filter( 'determine_current_user', array( $this, 'authenticate' ), 20 );

		if ( $consumer = affiliate_wp()->REST->consumers->get_by( 'public_key', $public_key ) ) {
			if ( hash_equals( affwp_auth_hash( $public_key, $consumer->secret_key, false ), $token ) ) {
				return $consumer->user_id;
			}
		}
		return $user_id;
	}
}
