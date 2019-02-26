<?php

use Ebanx\Benjamin\Models\Country;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_EBANX_Bank_Transfer_Gateway
 */
class WC_EBANX_Bank_Transfer_Gateway extends WC_EBANX_New_Gateway {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->id           = 'ebanx-banktransfer';
		$this->method_title = __( 'Transferência Bancária', 'woocommerce-gateway-ebanx' );

		$this->api_name = 'banktransfer';

		$this->title = 'Transferência Bancária';

		$this->description = 'Pague com transferência bancária usando o saldo da sua conta corrente. Importante: Depósitos em dinheiro não serão aceitos.';

		parent::__construct();

		$this->ebanx_gateway = $this->ebanx->banktransfer();

		$this->enabled = $this->is_gateway_enabled();
	}

	/**
	 * Check if the gateway is enabled or not
	 */
	private function is_gateway_enabled() {
		if ( is_array( $this->configs->settings['brazil_payment_methods'] ) ) {
			return in_array( $this->id, $this->configs->settings['brazil_payment_methods'] ) ? 'yes' : false;
		}

		return false;
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
			'banktransfer/checkout-instructions.php',
			array(
				'id' => $this->id,
			),
			'woocommerce/ebanx/',
			WC_EBANX::get_templates_path()
		);

		parent::checkout_rate_conversion( WC_EBANX_Constants::CURRENCY_CODE_BRL );
	}

	/**
	 * Mount the data to send to EBANX API
	 *
	 * @param  WC_Order $order
	 * @return array
	 */
	protected function request_data( $order ) {
		$data                                 = parent::request_data( $order );
		$data['payment']['payment_type_code'] = $this->api_name;

		return $data;
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

		update_post_meta( $order->id, '_payment_due_date', $request->payment->due_date );
		update_post_meta( $order->id, '_voucher_url', $request->payment->voucher_url );
	}

	/**
	 * The page of order received, we call them as "Thank you pages"
	 *
	 * @param  WC_Order $order The order created.
	 * @return void
	 */
	public static function thankyou_page( $order ) {
		$bank_transfer_url            = get_post_meta( $order->id, '_voucher_url', true );
		$bank_transfer_basic          = $bank_transfer_url . '&format=basic';
		$bank_transfer_pdf            = $bank_transfer_url . '&format=pdf';
		$bank_transfer_print          = $bank_transfer_url . '&format=print';
		$bank_transfer_mobile         = $bank_transfer_url . '&device_target=mobile';
		$customer_email     = get_post_meta( $order->id, '_billing_email', true );
		$customer_name      = get_post_meta( $order->id, '_billing_first_name', true );
		$bank_transfer_due_date       = get_post_meta( $order->id, '_payment_due_date', true );
		$bank_transfer_hash           = get_post_meta( $order->id, '_ebanx_payment_hash', true );

		$data = array(
			'data'         => array(
				'payment_hash'    => $bank_transfer_hash,
				'url_basic'      => $bank_transfer_basic,
				'url_pdf'        => $bank_transfer_pdf,
				'url_print'      => $bank_transfer_print,
				'url_mobile'     => $bank_transfer_mobile,
				'url_iframe'     => get_site_url() . '/?ebanx=order-received&hash=' . $bank_transfer_hash,
				'customer_email' => $customer_email,
				'customer_name'  => $customer_name,
				'due_date'       => $bank_transfer_due_date,
			),
			'order_status' => $order->get_status(),
			'method'       => 'banktransfer',
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
}
