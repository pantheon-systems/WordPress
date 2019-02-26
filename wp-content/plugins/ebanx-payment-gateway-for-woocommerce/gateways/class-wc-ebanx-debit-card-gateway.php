<?php

use Ebanx\Benjamin\Models\Country;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_EBANX_Debit_Card_Gateway
 */
class WC_EBANX_Debit_Card_Gateway extends WC_EBANX_New_Gateway {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->id           = 'ebanx-debit-card';
		$this->method_title = __( 'EBANX - Debit Card', 'woocommerce-gateway-ebanx' );

		$this->api_name    = 'debitcard';
		$this->title       = 'Tarjeta de Débito';
		$this->description = 'Paga con tarjeta de débito.';

		parent::__construct();

		$this->ebanx_gateway = $this->ebanx->debitCard();

		$this->enabled = is_array( $this->configs->settings['mexico_payment_methods'] ) ? in_array( $this->id, $this->configs->settings['mexico_payment_methods'] ) ? 'yes' : false : false;
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
	 * Insert the necessary assets on checkout page
	 *
	 * @return void
	 */
	public function checkout_assets() {
		if ( is_checkout() ) {
			wp_enqueue_script( 'wc-debit-card-form' );
			wp_enqueue_script( 'woocommerce_ebanx_debit', plugins_url( 'assets/js/debit-card.js', WC_EBANX::DIR ), array( 'jquery-payment' ), WC_EBANX::get_plugin_version(), true );
		}

		parent::checkout_assets();
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
			'debit-card/payment-form.php',
			array(
				'cart_total' => $this->get_order_total(),
				'id'         => $this->id,
			),
			'woocommerce/ebanx/',
			WC_EBANX::get_templates_path()
		);

		parent::checkout_rate_conversion( WC_EBANX_Constants::CURRENCY_CODE_MXN );
	}


	/**
	 *
	 * @param WC_Order $order
	 *
	 * @return \Ebanx\Benjamin\Models\Payment
	 * @throws Exception Throws missing parameter exception.
	 */
	protected function transform_payment_data( $order ) {
		if ( empty( WC_EBANX_Request::read( 'ebanx_debit_token', null ) )
			|| empty( WC_EBANX_Request::read( 'ebanx_billing_cvv', null ) ) ) {
			throw new Exception( 'Missing ebanx card params.' );
		}

		return WC_EBANX_Payment_Adapter::transform_card( $order, $this->configs, $this->names, $this->id );
	}

	/**
	 *
	 * @param array    $request
	 * @param WC_Order $order
	 *
	 * @throws Exception Throws missing parameter exception.
	 * @throws WC_EBANX_Payment_Exception Throws error message.
	 */
	protected function process_response( $request, $order ) {
		if ( 'SUCCESS' !== $request['status'] || ! $request['payment']['pre_approved'] ) {
			$this->process_response_error( $request, $order );
		}

		parent::process_response( $request, $order );
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

		update_post_meta( $order->id, '_cards_brand_name', $request->payment->payment_type_code );
		update_post_meta( $order->id, '_masked_card_number', WC_EBANX_Request::read( 'ebanx_masked_card_number' ) );
	}

	/**
	 * The page of order received, we call them as "Thank you pages"
	 *
	 * @param  WC_Order $order The order created.
	 * @return void
	 */
	public static function thankyou_page( $order ) {
		$order_amount = $order->get_total();

		$data = array(
			'data'         => array(
				'card_brand_name' => get_post_meta( $order->id, '_cards_brand_name', true ),
				'order_amount'    => $order_amount,
				'masked_card'     => substr( get_post_meta( $order->id, '_masked_card_number', true ), -4 ),
				'customer_email'  => $order->billing_email,
				'customer_name'   => $order->billing_first_name,
			),
			'order_status' => $order->get_status(),
			'method'       => 'debit-card',
		);

		parent::thankyou_page( $data );
	}
}
