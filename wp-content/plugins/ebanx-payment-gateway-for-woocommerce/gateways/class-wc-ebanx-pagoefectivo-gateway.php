<?php

use Ebanx\Benjamin\Models\Country;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_EBANX_Pagoefectivo_Gateway
 */
class WC_EBANX_Pagoefectivo_Gateway extends WC_EBANX_New_Gateway {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->id           = 'ebanx-pagoefectivo';
		$this->method_title = __( 'EBANX - Pagoefectivo', 'woocommerce-gateway-ebanx' );

		$this->api_name    = 'pagoefectivo';
		$this->title       = 'PagoEfectivo';
		$this->description = 'Paga con PagoEfectivo.';

		parent::__construct();

		$this->ebanx_gateway = $this->ebanx->pagoEfectivo();

		$this->enabled = is_array( $this->configs->settings['peru_payment_methods'] ) ? in_array( $this->id, $this->configs->settings['peru_payment_methods'] ) ? 'yes' : false : false;
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
			'pagoefectivo/payment-form.php',
			array(
				'id' => $this->id,
			),
			'woocommerce/ebanx/',
			WC_EBANX::get_templates_path()
		);

		parent::checkout_rate_conversion( WC_EBANX_Constants::CURRENCY_CODE_PEN );
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

		update_post_meta( $order->id, '_pagoefectivo_url', $request->redirect_url );
	}

	/**
	 * The page of order received, we call them as "Thank you pages"
	 *
	 * @param  WC_Order $order The order created.
	 * @return void
	 */
	public static function thankyou_page( $order ) {
		$pagoefectivo_url  = get_post_meta( $order->id, '_pagoefectivo_url', true );
		$pagoefectivo_hash = get_post_meta( $order->id, '_ebanx_payment_hash', true );

		$data = array(
			'data'         => array(
				'url_basic'      => $pagoefectivo_url,
				'url_iframe'     => get_site_url() . '/?ebanx=order-received&hash=' . $pagoefectivo_hash,
				'customer_email' => $order->billing_email,
			),
			'order_status' => $order->get_status(),
			'method'       => 'pagoefectivo',
		);

		parent::thankyou_page( $data );

		wp_enqueue_script(
			'woocommerce_ebanx_clipboard',
			plugins_url( 'assets/js/vendor/clipboard.min.js', WC_EBANX::DIR ),
			array(),
			WC_EBANX::get_plugin_version(),
			true
		);
		wp_enqueue_script(
			'woocommerce_ebanx_order_received',
			plugins_url( 'assets/js/order-received.js', WC_EBANX::DIR ),
			array( 'jquery' ),
			WC_EBANX::get_plugin_version(),
			true
		);
	}


	/**
	 *
	 * @param array    $response
	 * @param WC_Order $order
	 *
	 * @throws Exception Throws parameter missing exception.
	 * @throws WC_EBANX_Payment_Exception Throws error message.
	 */
	protected function process_response( $response, $order ) {
		if ( 'SUCCESS' !== $response['status'] || ! $response['payment']['cip_url'] ) {
			$this->process_response_error( $response, $order );
		}
		$response['redirect_url'] = $response['payment']['cip_url'];

		parent::process_response( $response, $order );
	}
}
