<?php

/**
 * Class WCML_Payment_Gateway_PayPal
 */
class WCML_Payment_Gateway_PayPal extends WCML_Payment_Gateway {

	const TEMPLATE = 'paypal.twig';

	protected function get_output_model() {
		$currencies_details = $this->get_currencies_details();

		return array(
			'strings'                 => array(
				'currency_label' => __( 'Currency', 'woocommerce-multilingual' ),
				'setting_label'  => __( 'PayPal Email', 'woocommerce-multilingual' ),
				'not_supported'  => sprintf( __( 'This gateway does not support %s. To show this gateway please select another currency.', 'woocommerce-multilingual' ), $this->current_currency )
			),
			'gateway_id'              => $this->get_id(),
			'gateway_title'           => $this->get_title(),
			'current_currency'        => $this->current_currency,
			'gateway_settings'        => $this->get_setting( $this->current_currency ),
			'currencies_details'      => array_intersect_key( $currencies_details, $this->get_active_currencies() ),
			'selected_currency_valid' => $this->is_valid_for_use( $currencies_details[ $this->current_currency ]['currency'] ),
			'current_currency_valid'  => $currencies_details[ $this->current_currency ]['is_valid']
		);
	}

	protected function get_output_template() {
		return self::TEMPLATE;
	}

	/**
	 * @param $currency
	 *
	 * @return bool
	 */
	public function is_valid_for_use( $currency ) {
		return in_array(
			$currency,
			apply_filters(
				'woocommerce_paypal_supported_currencies',
				array( 'AUD', 'BRL', 'CAD', 'MXN', 'NZD', 'HKD', 'SGD', 'USD', 'EUR', 'JPY', 'TRY', 'NOK', 'CZK', 'DKK', 'HUF', 'ILS', 'MYR', 'PHP', 'PLN', 'SEK', 'CHF', 'TWD', 'THB', 'GBP', 'RMB', 'RUB', 'INR' )
			),
			true
		);
	}

	/**
	 * @param array $active_currencies
	 *
	 * @return array
	 */
	public function get_currencies_details(){

		$currencies_details = array();
		$default_currency   = get_option( 'woocommerce_currency' );
		$woocommerce_currencies = get_woocommerce_currencies();

		foreach ( $woocommerce_currencies as $code => $currency ) {

			if ( $default_currency === $code ) {
				$currencies_details[ $code ]['value'] = $this->get_gateway()->settings['email'];
				$currencies_details[ $code ]['currency'] = $code;
				$currencies_details[ $code ]['is_valid'] = $this->is_valid_for_use( $default_currency );
			} else {
				$currency_gateway_setting    = $this->get_setting( $code );
				$currencies_details[ $code ]['value'] = $currency_gateway_setting ? $currency_gateway_setting['value'] : '';
				$currencies_details[ $code ]['currency'] = $currency_gateway_setting ? $currency_gateway_setting['currency'] : $code;
				$currencies_details[ $code ]['is_valid'] = $this->is_valid_for_use( $code );
			}
		}

		return $currencies_details;

	}

	public function add_hooks(){
		add_filter( 'woocommerce_paypal_args', array( $this, 'filter_paypal_args' ), 10, 2 );
	}

	/**
	 * @param array $args
	 * @param WC_Order $order
	 *
	 * @return array
	 */
	public function filter_paypal_args( $args, $order ) {

		$order_data      = $order->get_data();
		$client_currency = $order_data['currency'];
		$gateway_setting = $this->get_setting( $client_currency );

		if ( $gateway_setting ) {

			if ( $gateway_setting['value'] ) {
				$args['business'] = $gateway_setting['value'];
			}

			if ( $client_currency !== $gateway_setting['currency'] ) {
				$args['currency_code'] = $gateway_setting['currency'];
				$cart_items            = WC()->cart->get_cart_contents();
				$item_id               = 1;

				foreach ( $cart_items as $item ) {
					$args[ 'amount_' . $item_id ] = $this->woocommerce_wpml->multi_currency->prices->get_product_price_in_currency( $item['product_id'], $gateway_setting['currency'] );
					$item_id ++;
				}

				$args['shipping_1'] = $this->woocommerce_wpml->multi_currency->prices->convert_price_amount_by_currencies( WC()->cart->get_shipping_total(), $client_currency, $gateway_setting['currency'] );
			}
		}

		return $args;
	}

}