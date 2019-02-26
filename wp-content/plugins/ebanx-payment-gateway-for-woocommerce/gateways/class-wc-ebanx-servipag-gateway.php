<?php

use Ebanx\Benjamin\Models\Country;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_EBANX_Servipag_Gateway
 */
class WC_EBANX_Servipag_Gateway extends WC_EBANX_Redirect_Gateway {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->id           = 'ebanx-servipag';
		$this->method_title = __( 'EBANX - Servipag', 'woocommerce-gateway-ebanx' );

		$this->api_name    = 'servipag';
		$this->title       = 'ServiPag';
		$this->description = 'Paga con Servipag.';

		parent::__construct();

		$this->ebanx_gateway = $this->ebanx->servipag();

		$this->enabled = is_array( $this->configs->settings['chile_payment_methods'] ) ? in_array( $this->id, $this->configs->settings['chile_payment_methods'] ) ? 'yes' : false : false;
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

	/**
	 * The HTML structure on checkout page
	 */
	public function payment_fields() {
		$message = $this->get_sandbox_form_message( $this->get_transaction_address( 'country' ) );
		wc_get_template(
			'sandbox-checkout-alert.php',
			array(
				'is_sandbox_mode' => $this->is_sandbox_mode,
				'message'         => $message,
			),
			'woocommerce/ebanx/',
			WC_EBANX::get_templates_path()
		);

		$description = $this->get_description();
		if ( isset( $description ) ) {
			echo wp_kses_post( wpautop( wptexturize( $description ) ) );
		}

		wc_get_template(
			'servipag/payment-form.php',
			array(
				'id' => $this->id,
			),
			'woocommerce/ebanx/',
			WC_EBANX::get_templates_path()
		);

		parent::checkout_rate_conversion( WC_EBANX_Constants::CURRENCY_CODE_CLP );
	}

	/**
	 * The page of order received, we call them as "Thank you pages"
	 *
	 * @param  WC_Order $order The order created.
	 * @return void
	 */
	public static function thankyou_page( $order ) {
		$data = array(
			'data'         => array(),
			'order_status' => $order->get_status(),
			'method'       => 'servipag',
		);

		parent::thankyou_page( $data );
	}

	/**
	 * Mount the data to send to EBANX API
	 *
	 * @param  WC_Order $order
	 * @return array
	 */
	protected function request_data( $order ) {
		/*
		TODO: ? if (empty($_POST['ebanx_servipag_rut'])) {
		throw new Exception("Missing rut.");
		}
		*/

		$data = parent::request_data( $order );

		$data['payment']['payment_type_code'] = $this->api_name;

		return $data;
	}
}
