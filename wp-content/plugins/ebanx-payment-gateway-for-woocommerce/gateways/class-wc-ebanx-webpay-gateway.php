<?php

use Ebanx\Benjamin\Models\Country;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_EBANX_Webpay_Gateway
 */
class WC_EBANX_Webpay_Gateway extends WC_EBANX_Flow_Gateway {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->id           = 'ebanx-webpay';
		$this->method_title = __( 'EBANX - Webpay', 'woocommerce-gateway-ebanx' );

		$this->title       = 'Webpay';
		$this->description = 'Paga con Webpay.';

		$this->template_file       = 'flow/webpay/payment-form.php';
		$this->flow_payment_method = 'webpay';

		parent::__construct();

		$this->ebanx_gateway = $this->ebanx->webpay();

	}

	/**
	 * Check if the method is available to show to the users
	 *
	 * @return boolean
	 * @throws Exception Throws missing param message.
	 */
	public function is_available() {
		$country = $this->get_transaction_address( 'country' );

		return parent::is_available() && $this->ebanx_gateway->isAvailableForCountry( Country::fromIso( $country ) );
	}
}
