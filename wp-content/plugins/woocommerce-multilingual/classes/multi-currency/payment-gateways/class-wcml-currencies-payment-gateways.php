<?php

/**
 * Class WCML_Currencies_Payment_Gateways
 */
class WCML_Currencies_Payment_Gateways {

	const OPTION_KEY = 'wcml_custom_payment_gateways_for_currencies';
	const TEMPLATE_FOLDER = '/templates/multi-currency/payment-gateways/';

	private $available_gateways = array();
	private $supported_gateways = array();
	private $payment_gateways = array();

	/** @var woocommerce_wpml */
	private $woocommerce_wpml;
	/** @var WPML_WP_API */
	private $wp_api;

	/**
	 * @param woocommerce_wpml $woocommerce_wpml
	 * @param WPML_WP_API $wp_api
	 */
	public function __construct( woocommerce_wpml $woocommerce_wpml, WPML_WP_API $wp_api ) {
		$this->woocommerce_wpml = $woocommerce_wpml;
		$this->wp_api           = $wp_api;
	}

	public function add_hooks(){
		add_action( 'init', array( $this, 'init_gateways' ) );

		add_filter( 'woocommerce_gateway_description', array( $this, 'filter_gateway_description' ), 10, 2 );
		add_filter( 'option_woocommerce_stripe_settings', array( 'WCML_Payment_Gateway_Stripe', 'filter_stripe_settings' ) );
	}

	/**
	 * @param string $currency
	 *
	 * @return bool
	 */
	public function is_enabled( $currency ) {

		$gateway_enabled_settings = $this->get_settings();

		if( isset( $gateway_enabled_settings[ $currency ] ) ) {
			return $gateway_enabled_settings[ $currency ];
		}

		return false;
	}

	/**
	 * @param string $currency
	 * @param bool $value
	 */
	public function set_enabled( $currency, $value ) {

		$gateway_enabled_settings              = $this->get_settings();
		$gateway_enabled_settings[ $currency ] = $value;

		update_option( self::OPTION_KEY, $gateway_enabled_settings );
	}

	/**
	 * @return array
	 */
	private function get_settings() {
		return get_option( self::OPTION_KEY, array() );
	}

	public function init_gateways(){

		do_action( 'wcml_before_init_currency_payment_gateways' );

		$this->available_gateways = $this->get_available_payment_gateways();

		$this->supported_gateways = array(
			'bacs' => 'WCML_Payment_Gateway_Bacs',
			'paypal' => 'WCML_Payment_Gateway_PayPal',
			'stripe' => 'WCML_Payment_Gateway_Stripe'
		);
		$this->supported_gateways = apply_filters( 'wcml_supported_currency_payment_gateways', $this->supported_gateways );

		$this->store_supported_gateways();
		$this->store_non_supported_gateways();
	}

	/**
	 * @return array
	 */
	public function get_gateways() {

		return $this->payment_gateways;
	}

	/**
	 * @return array
	 */
	public function get_supported_gateways() {

		return $this->supported_gateways;
	}

	/**
	 * @param string $description
	 * @param string $id
	 *
	 * @return string
	 */
	public function filter_gateway_description( $description, $id ) {

		if ( in_array( $id, array_keys( $this->supported_gateways ), true ) ) {

			$client_currency   = $this->woocommerce_wpml->multi_currency->get_client_currency();
			$gateway_setting   = $this->payment_gateways[ $id ]->get_setting( $client_currency );
			$active_currencies = $this->woocommerce_wpml->multi_currency->get_currency_codes();

			if (
				$this->is_enabled( $client_currency ) &&
				$gateway_setting &&
				$client_currency !== $gateway_setting['currency'] &&
				in_array( $gateway_setting['currency'], $active_currencies )
			) {
				$cart_total = $this->woocommerce_wpml->cart->get_formatted_cart_total_in_currency( $gateway_setting['currency'] );

				$description .= '<p>';
				$description .= sprintf( __( 'Please note that the payment will be made in %1$s. %2$s will be debited from your account.', 'woocommerce-multilingual' ), $gateway_setting['currency'], $cart_total );
				$description .= '</p>';
			}
		}

		return $description;
	}

	/**
	 * @param string $id
	 * @param object $supported_gateway
	 *
	 * @return bool
	 */
	private function is_a_valid_gateway( $id, $supported_gateway ) {
		return is_subclass_of( $supported_gateway, 'WCML_Payment_Gateway' ) && array_key_exists( $id, $this->available_gateways );
	}

	private function store_supported_gateways() {
		if ( is_array( $this->supported_gateways ) ) {
			/** @var \WCML_Payment_Gateway $supported_gateway */
			$client_currency = $this->woocommerce_wpml->multi_currency->get_client_currency();
			foreach ( $this->supported_gateways as $id => $supported_gateway ) {
				if ( $this->is_a_valid_gateway( $id, $supported_gateway ) ) {
					$this->payment_gateways[ $id ] = new $supported_gateway( $this->available_gateways[ $id ], $this->get_template_service(), $this->woocommerce_wpml );
					if ( $this->is_enabled( $client_currency ) ) {
						$this->payment_gateways[ $id ]->add_hooks();
					}
				}
			}
		}
	}

	private function store_non_supported_gateways() {
		$non_supported_gateways = array_diff( array_keys( $this->available_gateways ), array_keys( $this->payment_gateways ) );

		/** @var int $non_supported_gateway */
		foreach ( $non_supported_gateways as $non_supported_gateway ) {
			$this->payment_gateways[ $non_supported_gateway ] = new WCML_Not_Supported_Payment_Gateway( $this->available_gateways[ $non_supported_gateway ], $this->get_template_service(), $this->woocommerce_wpml );
		}
	}

	/**
	 * @return \WPML_Twig_Template
	 */
	private function get_template_service() {
		$twig_loader = new WPML_Twig_Template_Loader( array( $this->wp_api->constant( 'WCML_PLUGIN_PATH' ) . self::TEMPLATE_FOLDER ) );

		return $twig_loader->get_template();
	}

	/**
	 * @return array
	 */
	private function get_available_payment_gateways() {
		return WC()->payment_gateways()->get_available_payment_gateways();
	}

}