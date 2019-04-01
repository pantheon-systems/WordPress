<?php

use Ebanx\Benjamin\Models\Country;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_EBANX_Eft_Gateway
 */
class WC_EBANX_Eft_Gateway extends WC_EBANX_Redirect_Gateway {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->id           = 'ebanx-eft';
		$this->method_title = __( 'EBANX - PSE', 'woocommerce-gateway-ebanx' );

		$this->api_name    = 'eft';
		$this->title       = 'PSE - Pago Seguros en Línea';
		$this->description = 'Paga con PSE - Pago Seguros en Línea.';

		parent::__construct();

		$this->ebanx_gateway = $this->ebanx->eft();

		$this->enabled = is_array( $this->configs->settings['colombia_payment_methods'] ) ? in_array( $this->id, $this->configs->settings['colombia_payment_methods'] ) ? 'yes' : false : false;
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
			'eft/payment-form.php',
			array(
				'title'       => $this->title,
				'description' => $this->description,
				'banks'       => WC_EBANX_Constants::$banks_eft_allowed[ WC_EBANX_Constants::COUNTRY_COLOMBIA ],
				'id'          => $this->id,
			),
			'woocommerce/ebanx/',
			WC_EBANX::get_templates_path()
		);

		parent::checkout_rate_conversion( WC_EBANX_Constants::CURRENCY_CODE_COP );
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
			'method'       => 'debit-card',
		);

		parent::thankyou_page( $data );
	}

	/**
	 *
	 * @param WC_Order $order
	 *
	 * @return \Ebanx\Benjamin\Models\Payment
	 * @throws Exception Throws missing parameter exception.
	 */
	protected function transform_payment_data( $order ) {
		if ( ! WC_EBANX_Request::has( 'eft' )
			|| ! array_key_exists( WC_EBANX_Request::read( 'eft' ), WC_EBANX_Constants::$banks_eft_allowed[ WC_EBANX_Constants::COUNTRY_COLOMBIA ] ) ) {
			throw new Exception( 'MISSING-BANK-NAME' );
		}

		$data = WC_EBANX_Payment_Adapter::transform( $order, $this->configs, $this->names, $this->id );

		// phpcs:ignore WordPress.NamingConventions.ValidVariableName
		$data->bankCode = WC_EBANX_Request::read( 'eft' );

		return $data;
	}
}
