<?php

/**
 * Class WCML_Payment_Gateway_Stripe
 */
class WCML_Payment_Gateway_Stripe extends WCML_Payment_Gateway {

	const TEMPLATE = 'stripe.twig';
	const ID = 'stripe';

	protected function get_output_model() {
		return array(
			'strings'            => array(
				'currency_label'    => __( 'Currency', 'woocommerce-multilingual' ),
				'publishable_label' => __( 'Live Publishable Key', 'woocommerce-multilingual' ),
				'secret_label'      => __( 'Live Secret Key', 'woocommerce-multilingual' ),
			),
			'gateway_id'         => $this->get_id(),
			'gateway_title'      => $this->get_title(),
			'current_currency'   => $this->current_currency,
			'gateway_settings'   => $this->get_setting( $this->current_currency ),
			'currencies_details' => $this->get_currencies_details( $this->get_active_currencies() )
		);
	}

	protected function get_output_template() {
		return self::TEMPLATE;
	}

	public function add_hooks(){
		add_filter( 'woocommerce_stripe_request_body', array( $this, 'filter_request_body' ) );
	}

	public function filter_request_body( $request ) {

		$client_currency = $this->woocommerce_wpml->multi_currency->get_client_currency();
		$gateway_setting = $this->get_setting( strtoupper( $request['currency'] ) );

		if ( $gateway_setting ) {

			if ( $client_currency !== $gateway_setting['currency'] ) {
				$request['currency'] = strtolower( $gateway_setting['currency'] );
				$request['amount']   = WC_Stripe_Helper::get_stripe_amount( $this->woocommerce_wpml->cart->get_cart_total_in_currency( $gateway_setting['currency'] ), $gateway_setting['currency'] );
			}
		}

		return $request;
	}

	/**
	 * @param array $active_currencies
	 *
	 * @return array
	 */
	public function get_currencies_details( $active_currencies ){

		$currencies_details = array();
		$default_currency   = get_option( 'woocommerce_currency' );

		foreach ( $active_currencies as $code => $currency ) {

			if ( $default_currency === $code ) {
				$currencies_details[ $code ]['publishable_key'] = $this->get_gateway()->settings['publishable_key'];
				$currencies_details[ $code ]['secret_key'] = $this->get_gateway()->settings['secret_key'];
			} else {
				$currency_gateway_setting    = $this->get_setting( $code );
				$currencies_details[ $code ]['publishable_key'] = $currency_gateway_setting ? $currency_gateway_setting['publishable_key'] : '';
				$currencies_details[ $code ]['secret_key'] = $currency_gateway_setting ? $currency_gateway_setting['secret_key'] : '';
			}
		}

		return $currencies_details;

	}

	/**
	 * Filter Stripe settings before WC initialized them
	 *
	 * @param array $settings
	 *
	 * @return array
	 */
	public static function filter_stripe_settings( $settings ) {
		if ( is_admin() ){
			return $settings;
		}

		global $woocommerce_wpml;

		$client_currency  = $woocommerce_wpml->multi_currency->get_client_currency();
		$gateway_settings = get_option( self::OPTION_KEY . self::ID, array() );

		if( $gateway_settings && isset( $gateway_settings[ $client_currency ] ) ){
			$gateway_setting  = $gateway_settings[ $client_currency ];
			if( $gateway_setting['publishable_key'] && $gateway_setting['secret_key'] ){
				if ( 'yes' === $settings['testmode'] ) {
					$settings['test_publishable_key'] = $gateway_setting['publishable_key'];
					$settings['test_secret_key']      = $gateway_setting['secret_key'];
				} else {
					$settings['publishable_key'] = $gateway_setting['publishable_key'];
					$settings['secret_key']      = $gateway_setting['secret_key'];
				}
			}
		}

		return $settings;
	}

}