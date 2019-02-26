<?php
use AffWP\REST;

/**
 * Initializes a REST API for AffiliateWP.
 *
 * @since 1.9
 */
class Affiliate_WP_REST {

	/**
	 * REST Authentication.
	 *
	 * @access protected
	 * @since  1.9
	 * @var    \AffWP\REST\Authentication
	 */
	protected $auth;

	/**
	 * REST Consumers database layer.
	 *
	 * @access public
	 * @since  1.9
	 * @var    \AffWP\REST\Consumer\Database
	 */
	public $consumers;

	/**
	 * Constructor.
	 *
	 * @access public
	 * @since  1.9
	 */
	public function __construct() {
		$this->setup_objects();

		add_action( 'affwp_process_api_key', array( $this, 'process_api_key' ) );
	}

	/**
	 * Sets up REST components.
	 *
	 * @access private
	 * @since  1.9
	 */
	private function setup_objects() {
		$this->auth      = new REST\Authentication;
		$this->consumers = new REST\Consumer\Database;
	}

	/**
	 * Processes an API key generation/revocation.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @param array $args
	 */
	public function process_api_key( $args ) {
		if( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'affwp-api-nonce' ) ) {
			wp_die( __( 'Nonce verification failed', 'affiliate-wp' ), __( 'Error', 'affiliate-wp' ), array( 'response' => 403 ) );
		}

		$args = affiliate_wp()->utils->process_request_data( $args, 'user_name' );

		if ( empty( $args['user_id'] ) ) {
			wp_die( sprintf( __( 'User ID Required', 'affiliate-wp' ), $process ), __( 'Error', 'affiliate-wp' ), array( 'response' => 401 ) );
		}

		if ( is_numeric( $args['user_id'] ) ) {
			$user_id = isset( $args['user_id'] ) ? absint( $args['user_id'] ) : get_current_user_id();
		} else {
			$userdata = get_user_by( 'login', $args['user_id'] );
			$user_id  = $userdata->ID;
		}
		$process = isset( $args['affwp_api_process'] ) ? sanitize_key( $args['affwp_api_process'] ) : false;

		if ( $user_id == get_current_user_id() && ! current_user_can( 'manage_affiliates' ) ) {
			/* translators: 1: Generate, regenerate, or revoke */
			wp_die( sprintf( __( 'You do not have permission to %s API keys for this user.', 'affiliate-wp' ), $process ), __( 'Error', 'affiliate-wp' ), array( 'response' => 403 ) );
		} elseif( ! current_user_can( 'manage_affiliates' ) ) {
			/* translators: 1: Generate, regenerate, or revoke */
			wp_die( sprintf( __( 'You do not have permission to %s API keys for this user.', 'affiliate-wp' ), $process ), __( 'Error', 'affiliate-wp' ), array( 'response' => 403 ) );
		}

		$query_args = array( 'tab' => 'api_keys' );

		switch( $process ) {
			case 'generate':
				if ( $this->generate_api_keys( $user_id ) ) {
					delete_transient( 'affwp-total-api-keys' );
					$query_args['affwp_notice'] = 'api_key_generated';
				} else {
					$query_args['affwp_notice'] = 'api_key_failed';
				}
				break;
			case 'regenerate':
				$this->generate_api_keys( $user_id, true );
				delete_transient( 'affwp-total-api-keys' );

				$query_args['affwp_notice'] = 'api_key_regenerated';
				break;
			case 'revoke':
				$this->revoke_api_keys( $user_id );
				delete_transient( 'affwp-total-api-keys' );

				$query_args['affwp_notice'] = 'api_key_revoked';
				break;
			default;
				break;
		}

		wp_redirect( affwp_admin_url( 'tools', $query_args ) );

		exit();
	}

	/**
	 * Generates new API keys for a consumer.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @param int  $user_id    User ID for the consumer the key is being generated for.
	 * @param bool $regenerate Whether to regenerate the key for the user.
	 * @return bool True if (re)generated successfully, otherwise false.
	 */
	public function generate_api_keys( $user_id, $regenerate = false ) {

		if ( ! $user = get_userdata( $user_id ) ) {
			return false;
		}

		$public_key = $this->get_consumer_public_key( $user_id );

		if ( empty( $public_key ) || true === $regenerate ) {
			$new_public_key = $this->generate_public_key( $user->ID );
			$new_secret_key = $this->generate_secret_key( $user->ID );
		} else {
			return false;
		}

		$added = false;

		if ( true === $regenerate ) {
			$this->revoke_api_keys( $user->ID );
		}

		$added = $this->consumers->add( array(
			'user_id'    => $user->ID,
			'token'      => affwp_auth_hash( $new_public_key, $new_secret_key, false ),
			'public_key' => $new_public_key,
			'secret_key' => $new_secret_key,
		) );

		return true;
	}

	/**
	 * Revokes a consumer's API keys.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @param int $user_id User ID of consumer to revoke keys for.
	 * @return bool True if the consumer was successfully deleted, otherwise false.
	 */
	public function revoke_api_keys( $user_id ) {
		$consumer_id = $this->consumers->get_column_by( 'consumer_id', 'user_id', $user_id );

		if ( ! $consumer_id ) {
			return false;
		}

		if ( ! $key = wp_cache_get( md5( "affwp_consumer{$user_id}_public_key" ), 'affwp-rest' ) ) {
			$key = '';
		}

		$deleted = $this->consumers->delete( $consumer_id );

		if ( $deleted ) {
			// Dump cached values.
			$cache_keys = array(
				md5( "affwp_consumer_user_id_{$key}" ),
				md5( "affwp_consumer_{$user_id}_public_key" ),
				md5( "affwp_consumer_{$user_id}_secret_key" )
			);

			foreach ( $cache_keys as $cache_key ) {
				wp_cache_delete( $cache_key, 'affwp-rest' );
			}

			return true;
		}

		return false;
	}

	/**
	 * Generates the public key for a consumer.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @param int $user_id Consumer user ID.
	 * @return string The consumer's public key.
	 */
	public function generate_public_key( $user_id ) {
		$public = '';

		if ( $user = get_user_by( 'id', $user_id ) ) {
			$public = affwp_auth_hash( $user->data->user_email, date( 'U' ) );
		}

		return $public;
	}

	/**
	 * Generates the secret key for a consumer.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @param int $user_id Consumer user ID.
	 * @return string The consumer's secret key.
	 */
	public function generate_secret_key( $user_id ) {
		$secret = '';

		if ( $user = get_user_by( 'id', $user_id ) ) {
			$secret = affwp_auth_hash( $user_id, date( 'U' ) );
		}

		return $secret;
	}

	/**
	 * Retrieves the consumer token.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @param int $user_id Consumer user ID.
	 * @return string The consumer's token.
	 */
	public function get_token( $user_id ) {
		return affwp_auth_hash( $this->get_consumer_secret_key( $user_id ), $this->get_consumer_public_key( $user_id ), false );
	}

	/**
	 * Retrieves the user ID based on the provided public key.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @param string $key Public Key.
	 * @return int|false User ID if found, otherwise false.
	 */
	public function get_consumer_user( $key ) {

		$cache_key = md5( "affwp_consumer_user_id_{$key}" );
		$user_id   = wp_cache_get( $cache_key, 'affwp-rest' );

		if ( false === $user_id ) {
			$user_id = $this->consumers->get_column_by( 'user_id', 'public_key', $key );
		}

		wp_cache_add( $cache_key, $user_id, 'affwp-rest', DAY_IN_SECONDS );

		if ( $user_id ) {
			return $user_id;
		}

		return false;
	}

	/**
	 * Retrieves a consumer's public key based on a given user ID.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @param int $user_id User ID.
	 * @return string|false The consumer's public key if found, otherwise false.
	 */
	public function get_consumer_public_key( $user_id ) {

		$cache_key  = md5( "affwp_consumer_{$user_id}_public_key" );
		$public_key = wp_cache_get( $cache_key, 'affwp-rest' );

		if ( false === $public_key ) {
			$public_key = $this->consumers->get_column_by( 'public_key', 'user_id', $user_id );
		}

		wp_cache_add( $cache_key, $public_key, 'affwp-rest', DAY_IN_SECONDS );

		if ( $public_key ) {
			return $public_key;
		}

		return false;
	}

	/**
	 * Retrieves a consumer's secret key based on a given user ID.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @param int $user_id User ID.
	 * @return string|false The consumer's secret key if found, otherwise false.
	 */
	public function get_consumer_secret_key( $user_id ) {

		$cache_key = md5( "affwp_consumer_{$user_id}_secret_key" );
		$secret_key = wp_cache_get( $cache_key, 'affwp-rest' );

		if ( false === $secret_key ) {
			$secret_key = $this->consumers->get_column_by( 'secret_key', 'user_id', $user_id );
		}

		wp_cache_add( $cache_key, $secret_key, 'affwp-rest', DAY_IN_SECONDS );

		if ( $secret_key ) {
			return $secret_key;
		}

		return false;
	}


}
