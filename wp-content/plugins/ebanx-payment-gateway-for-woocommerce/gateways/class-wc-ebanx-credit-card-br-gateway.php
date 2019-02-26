<?php

use Ebanx\Benjamin\Models\Country;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_EBANX_Credit_Card_BR_Gateway
 */
class WC_EBANX_Credit_Card_BR_Gateway extends WC_EBANX_Credit_Card_Gateway {
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->id            = 'ebanx-credit-card-br';
		$this->method_title  = __( 'EBANX - Credit Card Brazil', 'woocommerce-gateway-ebanx' );
		$this->currency_code = WC_EBANX_Constants::CURRENCY_CODE_BRL;

		$this->title       = 'Cartão de Crédito';
		$this->description = 'Pague com cartão de crédito.';

		parent::__construct();

		$this->enabled = is_array( $this->configs->settings['brazil_payment_methods'] )
			&& in_array( $this->id, $this->configs->settings['brazil_payment_methods'] )
			? 'yes'
			: false;
	}

	/**
	 * Check if the method is available to show to the users
	 *
	 * @return boolean
	 * @throws Exception Throws missing param message.
	 */
	public function is_available() {
		$country = $this->get_transaction_address( 'country' );

		return parent::is_available()
			&& Country::fromIso( $country ) === Country::BRAZIL
			&& $this->ebanx_gateway->isAvailableForCountry( Country::fromIso( $country ) );
	}

	/**
	 * The HTML structure on checkout page
	 */
	public function payment_fields() {
		parent::payment_fields();

		parent::checkout_rate_conversion(
			WC_EBANX_Constants::CURRENCY_CODE_BRL,
			true,
			null,
			1
		);
	}
}
