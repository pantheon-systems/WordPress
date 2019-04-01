<?php

use Ebanx\Benjamin\Models\Country;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_EBANX_Tef_Gateway
 */
class WC_EBANX_Tef_Gateway extends WC_EBANX_Redirect_Gateway {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->id           = 'ebanx-tef';
		$this->method_title = __( 'EBANX - TEF', 'woocommerce-gateway-ebanx' );

		$this->api_name    = 'tef';
		$this->title       = 'Débito Online';
		$this->description = 'Selecione o seu banco. A seguir, você será redirecionado para concluir o pagamento pelo seu internet banking.';

		parent::__construct();

		$this->ebanx_gateway = $this->ebanx->tef();

		$this->enabled = is_array( $this->configs->settings['brazil_payment_methods'] ) ? in_array( $this->id, $this->configs->settings['brazil_payment_methods'] ) ? 'yes' : false : false;
	}

	/**
	 * Check if the method is available to show to the users
	 *
	 * @return boolean
	 * @throws Exception Throws missing parameter message.
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
			'tef/payment-form.php',
			array(
				'title'       => $this->title,
				'description' => $this->description,
				'id'          => $this->id,
			),
			'woocommerce/ebanx/',
			WC_EBANX::get_templates_path()
		);

		parent::checkout_rate_conversion( WC_EBANX_Constants::CURRENCY_CODE_BRL );
	}

	/**
	 * The page of order received, we call them as "Thank you pages"
	 *
	 * @param  WC_Order $order The order created.
	 * @return void
	 */
	public static function thankyou_page( $order ) {
		$data = array(
			'data'         => array(
				'bank_name'     => get_post_meta( $order->id, '_ebanx_tef_bank', true ),
				'customer_name' => get_post_meta( $order->id, '_billing_first_name', true ),
			),
			'order_status' => $order->get_status(),
			'method'       => 'tef',
		);

		parent::thankyou_page( $data );
	}

	/**
	 * Save order's meta fields for future use
	 *
	 * @param  WC_Order $order The order created.
	 * @param  Object   $request The request from EBANX success response.
	 *
	 * @return void
	 * @throws Exception Throw parameter missing exception.
	 */
	protected function save_order_meta_fields( $order, $request ) {
		update_post_meta( $order->id, '_ebanx_tef_bank', sanitize_text_field( WC_EBANX_Request::read( 'tef' ) ) );

		parent::save_order_meta_fields( $order, $request );
	}

	/**
	 * Mount the data to send to EBANX API
	 *
	 * @param  WC_Order $order
	 *
	 * @return \Ebanx\Benjamin\Models\Payment
	 * @throws Exception Throw parameter missing exception.
	 */
	protected function transform_payment_data( $order ) {
		if ( ! WC_EBANX_Request::has( 'tef' )
			|| ! in_array( WC_EBANX_Request::read( 'tef' ), WC_EBANX_Constants::$banks_tef_allowed[ WC_EBANX_Constants::COUNTRY_BRAZIL ] ) ) {
			throw new Exception( 'MISSING-BANK-NAME' );
		}

		$data = parent::transform_payment_data( $order );

		// phpcs:ignore WordPress.NamingConventions.ValidVariableName
		$data->bankCode = WC_EBANX_Request::read( 'tef' );

		return $data;
	}
}
