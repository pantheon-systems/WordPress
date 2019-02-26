<?php

use Ebanx\Benjamin\Models\Country;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_EBANX_Multicaja_Gateway
 */
class WC_EBANX_Multicaja_Gateway extends WC_EBANX_Flow_Gateway {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->id           = 'ebanx-multicaja';
		$this->method_title = __( 'EBANX - Multicaja', 'woocommerce-gateway-ebanx' );

		$this->title       = 'Multicaja';
		$this->description = 'Paga con multicaja.';

		$this->template_file       = 'flow/multicaja/payment-form.php';
		$this->flow_payment_method = 'multicaja';

		parent::__construct();

		$this->ebanx_gateway = $this->ebanx->multicaja();
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
