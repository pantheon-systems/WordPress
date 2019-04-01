<?php

use Ebanx\Benjamin\Models\Country;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_EBANX_Baloto_Gateway
 */
class WC_EBANX_Baloto_Gateway extends WC_EBANX_New_Gateway {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->id           = 'ebanx-baloto';
		$this->method_title = __( 'EBANX - Baloto', 'woocommerce-gateway-ebanx' );

		$this->api_name    = 'baloto';
		$this->title       = 'Baloto';
		$this->description = 'Paga con Baloto.';

		parent::__construct();

		$this->ebanx_gateway = $this->ebanx->baloto();

		$this->enabled = is_array( $this->configs->settings['colombia_payment_methods'] ) ? in_array( $this->id, $this->configs->settings['colombia_payment_methods'] ) ? 'yes' : false : false;
	}

	/**
	 * This method always will return false, it doesn't need to show to the customers
	 *
	 * @return boolean Always return false
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
			'baloto/payment-form.php',
			array(
				'id' => $this->id,
			),
			'woocommerce/ebanx/',
			WC_EBANX::get_templates_path()
		);

		parent::checkout_rate_conversion( WC_EBANX_Constants::CURRENCY_CODE_COP );
	}

	/**
	 * Save order's meta fields for future use
	 *
	 * @param  WC_Order $order The order created.
	 * @param  Object   $request The request from EBANX success response.
	 * @return void
	 */
	protected function save_order_meta_fields( $order, $request ) {
		parent::save_order_meta_fields( $order, $request );

		update_post_meta( $order->id, '_baloto_url', $request->payment->baloto_url );
	}

	/**
	 * The page of order received, we call them as "Thank you pages"
	 *
	 * @param  WC_Order $order The order created.
	 * @return void
	 */
	public static function thankyou_page( $order ) {
		$baloto_url     = get_post_meta( $order->id, '_baloto_url', true );
		$baloto_basic   = $baloto_url . '&format=basic';
		$baloto_pdf     = $baloto_url . '&format=pdf';
		$baloto_print   = $baloto_url . '&format=print';
		$customer_email = get_post_meta( $order->id, '_ebanx_payment_customer_email', true );
		$baloto_hash    = get_post_meta( $order->id, '_ebanx_payment_hash', true );

		$data = array(
			'data'         => array(
				'url_basic'      => $baloto_basic,
				'url_pdf'        => $baloto_pdf,
				'url_print'      => $baloto_print,
				'url_iframe'     => get_site_url() . '/?ebanx=order-received&hash=' . $baloto_hash,
				'customer_email' => $customer_email,
			),
			'order_status' => $order->get_status(),
			'method'       => 'baloto',
		);

		parent::thankyou_page( $data );

		wp_enqueue_script( 'woocommerce_ebanx_clipboard', plugins_url( 'assets/js/vendor/clipboard.min.js', WC_EBANX::DIR, false, true ) );
		wp_enqueue_script( 'woocommerce_ebanx_order_received', plugins_url( 'assets/js/order-received.js', WC_EBANX::DIR, false, true ) );
	}

	/**
	 * Mount the data to send to EBANX API
	 *
	 * @param  WC_Order $order
	 * @return array
	 */
	protected function request_data( $order ) {
		/*
		TODO: ? if (empty($_POST['ebanx_baloto_rfc'])) {
		throw new Exception("Missing rfc.");
		}
		*/

		$data = parent::request_data( $order );

		$data['payment']['payment_type_code'] = $this->api_name;

		return $data;
	}
}
