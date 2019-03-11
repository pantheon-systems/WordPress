<?php

/**
 * Handles data being passed between different domains using WPML xDomain logic
 * https://wpml.org/?page_id=693147
 *
 */
class WCML_xDomain_Data {

	/**
	 * @var WPML_Cookie
	 */
	private $cookie_handler;

	/**
	 * WCML_xDomain_Data constructor.
	 *
	 * @param WPML_Cookie $cookie_handler
	 */
	public function __construct( WPML_Cookie $cookie_handler ) {
		$this->cookie_handler = $cookie_handler;
	}

	public function add_hooks() {
		add_filter( 'wpml_cross_domain_language_data', array( $this, 'pass_data_to_domain' ) );
		add_action( 'before_woocommerce_init', array( $this, 'check_request' ) );
	}

	/**
	 * @param $data
	 *
	 * @return mixed
	 */
	public function pass_data_to_domain( $data ) {

		$wcml_session_id = md5( microtime() . uniqid( mt_rand(), true ) );
		$data['wcsid']   = $wcml_session_id;
		$session_data    = array();

		if ( isset( $_COOKIE[ 'wp_woocommerce_session_' . COOKIEHASH ] ) ) {
			$session_data['session'] = $_COOKIE[ 'wp_woocommerce_session_' . COOKIEHASH ];
		}

		if ( isset( $_COOKIE['woocommerce_cart_hash'] ) ) {
			$session_data['hash']  = $_COOKIE['woocommerce_cart_hash'];
			$session_data['items'] = $_COOKIE['woocommerce_items_in_cart'];
		}

		if ( ! empty( $session_data ) ) {
			update_option( 'wcml_session_data_' . $wcml_session_id, $session_data );
		}

		return $data;
	}

	public function check_request() {

		if ( has_filter( 'wpml_get_cross_domain_language_data' ) ) { // After WPML 3.2.7
			$xdomain_data = apply_filters( 'wpml_get_cross_domain_language_data', array() );
		} elseif ( isset( $_GET['xdomain_data'] ) ) {
			$xdomain_data = json_decode( base64_decode( $_GET['xdomain_data'] ), true );
		}

		if ( isset( $xdomain_data['wcsid'] ) ) {
			$this->set_session_data( $xdomain_data['wcsid'] );
		}

	}

	/**
	 * @param string $wcml_session_id
	 */
	private function set_session_data( $wcml_session_id ) {

		$data = maybe_unserialize( get_option( 'wcml_session_data_' . $wcml_session_id ) );

		if ( ! empty( $data ) ) {

			$session_expiration = time() + (int) apply_filters( 'wc_session_expiration', 60 * 60 * 48 ); // 48 Hours
			$secure             = apply_filters( 'wc_session_use_secure_cookie', false );

			if ( isset( $data['session'] ) ) {
				$this->cookie_handler->set_cookie( 'wp_woocommerce_session_' . COOKIEHASH, $data['session'], $session_expiration, COOKIEPATH, COOKIE_DOMAIN, $secure );
				$_COOKIE[ 'wp_woocommerce_session_' . COOKIEHASH ] = $data['session'];
			}

			if ( isset( $data['hash'] ) ) {
				$this->cookie_handler->set_cookie( 'woocommerce_cart_hash', $data['hash'], $session_expiration, COOKIEPATH, COOKIE_DOMAIN, $secure );
				$this->cookie_handler->set_cookie( 'woocommerce_items_in_cart', $data['items'], $session_expiration, COOKIEPATH, COOKIE_DOMAIN, $secure );
				$_COOKIE['woocommerce_cart_hash']     = $data['hash'];
				$_COOKIE['woocommerce_items_in_cart'] = $data['items'];
			}

		}

		delete_option( 'wcml_session_data_' . $wcml_session_id );

	}

}
