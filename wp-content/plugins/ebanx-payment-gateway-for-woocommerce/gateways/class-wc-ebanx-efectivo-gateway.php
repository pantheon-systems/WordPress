<?php

use Ebanx\Benjamin\Models\Country;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_EBANX_Efectivo_Gateway
 */
class WC_EBANX_Efectivo_Gateway extends WC_EBANX_New_Gateway {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->id           = 'ebanx-efectivo';
		$this->method_title = __( 'EBANX - Efectivo', 'woocommerce-gateway-ebanx' );

		$this->api_name    = 'efectivo';
		$this->title       = 'Efectivo';
		$this->description = 'Paga con Efectivo.';

		parent::__construct();

		$this->ebanx_gateway = $this->ebanx->otrosCupones();

		$this->enabled = is_array( $this->configs->settings['argentina_payment_methods'] ) ? in_array( $this->id, $this->configs->settings['argentina_payment_methods'] ) ? 'yes' : false : false;
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
			'efectivo/payment-form.php',
			array(
				'id' => $this->id,
			),
			'woocommerce/ebanx/',
			WC_EBANX::get_templates_path()
		);

		parent::checkout_rate_conversion( WC_EBANX_Constants::CURRENCY_CODE_ARS );
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

		update_post_meta( $order->id, '_efectivo_url', $request->payment->voucher_url );
	}

	/**
	 * The page of order received, we call them as "Thank you pages"
	 *
	 * @param  WC_Order $order The order created.
	 * @return void
	 */
	public static function thankyou_page( $order ) {
		$efectivo_url   = get_post_meta( $order->id, '_efectivo_url', true );
		$efectivo_basic = $efectivo_url . '&format=basic';
		$efectivo_pdf   = $efectivo_url . '&format=pdf';
		$efectivo_print = $efectivo_url . '&format=print';
		$customer_email = get_post_meta( $order->id, '_ebanx_payment_customer_email', true );
		$efectivo_hash  = get_post_meta( $order->id, '_ebanx_payment_hash', true );

		$data = array(
			'data'         => array(
				'url_basic'      => $efectivo_basic,
				'url_pdf'        => $efectivo_pdf,
				'url_print'      => $efectivo_print,
				'url_iframe'     => get_site_url() . '/?ebanx=order-received&hash=' . $efectivo_hash,
				'customer_email' => $customer_email,
			),
			'order_status' => $order->get_status(),
			'method'       => 'efectivo',
		);

		parent::thankyou_page( $data );

		wp_enqueue_script( 'woocommerce_ebanx_clipboard', plugins_url( 'assets/js/vendor/clipboard.min.js', WC_EBANX::DIR, false, true ) );
		wp_enqueue_script( 'woocommerce_ebanx_order_received', plugins_url( 'assets/js/order-received.js', WC_EBANX::DIR, false, true ) );
	}

	/**
	 * Mount the data to send to EBANX API
	 *
	 * @param  WC_Order $order
	 * @return \Ebanx\Benjamin\Models\Payment
	 * @throws Exception Throws missing parameter exception.
	 */
	protected function transform_payment_data( $order ) {
		if ( ! WC_EBANX_Request::has( 'efectivo' )
			 || ! in_array( WC_EBANX_Request::read( 'efectivo' ), WC_EBANX_Constants::$vouchers_efectivo_allowed ) ) {
			throw new Exception( 'MISSING-VOUCHER' );
		}

		$data = WC_EBANX_Payment_Adapter::transform( $order, $this->configs, $this->names, $this->id );

		$data->person->documentType = WC_EBANX_Request::read( $this->names['ebanx_billing_argentina_document_type'], null );

		$efectivo_gateway    = WC_EBANX_Request::read( 'efectivo' );
		$this->ebanx_gateway = 'cupon' === $efectivo_gateway ? $this->ebanx->otrosCupones() : $this->ebanx->{$efectivo_gateway}();

		return $data;
	}
}
