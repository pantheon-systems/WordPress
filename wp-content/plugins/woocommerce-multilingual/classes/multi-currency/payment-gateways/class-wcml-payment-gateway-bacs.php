<?php

/**
 * Class WCML_Payment_Gateway_Bacs
 */
class WCML_Payment_Gateway_Bacs extends WCML_Payment_Gateway {

	const TEMPLATE = 'bacs.twig';

	protected function get_output_model() {
		return array(
			'strings'           => array(
				'currency_label' => __( 'Currency', 'woocommerce-multilingual' ),
				'setting_label'  => __( 'Bank Account', 'woocommerce-multilingual' ),
				'all_label'      => __( 'All Accounts', 'woocommerce-multilingual' ),
				'all_in_label'   => __( 'All in selected currency', 'woocommerce-multilingual' ),
				'tooltip'        => __( 'Set the currency in which your customer will see the final price when they checkout. Choose which accounts they will see in their payment message.', 'woocommerce-multilingual' )
			),
			'gateway_id'        => $this->get_id(),
			'gateway_title'     => $this->get_title(),
			'current_currency'  => $this->current_currency,
			'gateway_settings'  => $this->get_setting( $this->current_currency ),
			'active_currencies' => $this->get_active_currencies(),
			'account_details'   => $this->get_gateway()->account_details,
		);
	}

	protected function get_output_template() {
		return self::TEMPLATE;
	}

	public function add_hooks(){
		add_filter( 'woocommerce_bacs_accounts', array( $this, 'filter_bacs_accounts' ) );
	}

	public function filter_bacs_accounts( $accounts ) {

		$client_currency = $this->woocommerce_wpml->multi_currency->get_client_currency();
		$gateway_setting = $this->get_setting( $client_currency );

		if ( $gateway_setting && 'all' !== $gateway_setting['value'] ) {

			if( 'all_in' === $gateway_setting['value'] ){
				$allowed_accounts = array();
				$bacs_accounts_currencies = get_option( WCML_WC_Gateways::WCML_BACS_ACCOUNTS_CURRENCIES_OPTION, array() );
				foreach ( $bacs_accounts_currencies as $account_key => $currency ){
					if( $gateway_setting['currency'] === $currency ){
						$allowed_accounts[] = $accounts[ $account_key ];
					}
				}
			}else{
				$allowed_accounts[] = $accounts[ $gateway_setting['value'] ];
			}
		}

		return $allowed_accounts ? $allowed_accounts : $accounts;
	}

}