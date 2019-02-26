<?php

use Ebanx\Benjamin\Models\Country;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_EBANX_Credit_Card_AR_Gateway
 */
class WC_EBANX_Credit_Card_AR_Gateway extends WC_EBANX_Credit_Card_Gateway {
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->id            = 'ebanx-credit-card-ar';
		$this->method_title  = __( 'EBANX - Credit Card Argentina', 'woocommerce-gateway-ebanx' );
		$this->currency_code = WC_EBANX_Constants::CURRENCY_CODE_ARS;

		$this->title       = 'Tarjeta de Crédito';
		$this->description = 'Pague con tarjeta de crédito.';

		parent::__construct();

		$this->enabled = is_array( $this->configs->settings['argentina_payment_methods'] )
			&& in_array( $this->id, $this->configs->settings['argentina_payment_methods'] )
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
			&& Country::fromIso( $country ) === Country::ARGENTINA
			&& $this->ebanx_gateway->isAvailableForCountry( Country::fromIso( $country ) );
	}

	/**
	 * The HTML structure on checkout page
	 */
	public function payment_fields() {
		parent::payment_fields();

		parent::checkout_rate_conversion(
			WC_EBANX_Constants::CURRENCY_CODE_ARS,
			true,
			null,
			1
		);
	}

	/**
	 *
	 * @param WC_Order $order
	 *
	 * @return \Ebanx\Benjamin\Models\Payment
	 * @throws Exception Throws missing parameter exception.
	 */
	protected function transform_payment_data( $order ) {
		$data = parent::transform_payment_data( $order );

		$data->person->documentType = WC_EBANX_Request::read( $this->names['ebanx_billing_argentina_document_type'], null );

		return $data;
	}
}
