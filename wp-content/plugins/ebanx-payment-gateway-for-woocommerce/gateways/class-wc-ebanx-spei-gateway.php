<?php

use Ebanx\Benjamin\Models\Country;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_EBANX_Spei_Gateway
 */
class WC_EBANX_Spei_Gateway extends WC_EBANX_New_Gateway {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->id           = 'ebanx-spei';
		$this->method_title = __( 'EBANX - SPEI', 'woocommerce-gateway-ebanx' );

		$this->api_name    = 'spei';
		$this->title       = 'SPEI';
		$this->description = 'Paga con SPEI.';

		parent::__construct();

		$this->ebanx_gateway = $this->ebanx->spei();

		$this->enabled = is_array( $this->configs->settings['mexico_payment_methods'] ) ? in_array( $this->id, $this->configs->settings['mexico_payment_methods'] ) ? 'yes' : false : false;
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
			'spei/payment-form.php',
			array(
				'id' => $this->id,
			),
			'woocommerce/ebanx/',
			WC_EBANX::get_templates_path()
		);

		parent::checkout_rate_conversion( WC_EBANX_Constants::CURRENCY_CODE_MXN );
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

		update_post_meta( $order->id, '_spei_url', $request->payment->spei_url );
		update_post_meta( $order->id, '_payment_due_date', $request->payment->due_date );
	}

	/**
	 * The page of order received, we call them as "Thank you pages"
	 *
	 * @param  WC_Order $order The order created.
	 * @return void
	 */
	public static function thankyou_page( $order ) {
		$spei_url       = get_post_meta( $order->id, '_spei_url', true );
		$spei_basic     = $spei_url . '&format=basic';
		$spei_pdf       = $spei_url . '&format=pdf';
		$spei_print     = $spei_url . '&format=print';
		$customer_email = get_post_meta( $order->id, '_ebanx_payment_customer_email', true );
		$customer_name  = $order->billing_first_name;
		$spei_due_date  = get_post_meta( $order->id, '_payment_due_date', true );
		$spei_hash = get_post_meta( $order->id, '_ebanx_payment_hash', true );

		$data       = array(
			'data'         => array(
				'url_basic'      => $spei_basic,
				'url_pdf'        => $spei_pdf,
				'url_print'      => $spei_print,
				'url_iframe'     => get_site_url() . '/?ebanx=order-received&hash=' . $spei_hash,
				'customer_email' => $customer_email,
				'customer_name'  => $customer_name,
				'due_date'       => $spei_due_date,
			),
			'order_status' => $order->get_status(),
			'method'       => 'spei',
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
	 * Mount the data to send to EBANX API
	 *
	 * @param  WC_Order $order
	 * @return array
	 */
	protected function request_data( $order ) {
		/*
		TODO: ? if (empty($_POST['ebanx_spei_rfc'])) {
		throw new Exception("Missing rfc.");
		}
		*/

		$data                                 = parent::request_data( $order );
		$data['payment']['payment_type_code'] = $this->api_name;

		return $data;
	}
}
