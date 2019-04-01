<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_EBANX_One_Click
 */
class WC_EBANX_One_Click {
	const CREATE_ORDER_ACTION = 'ebanx_create_order';

	/**
	 *
	 * @var array
	 */
	private $cards;

	/**
	 *
	 * @var int
	 */
	private $user_id;

	/**
	 *
	 * @var bool|WC_EBANX_Credit_Card_Gateway
	 */
	private $gateway;

	/**
	 *
	 * @var string
	 */
	private $user_country;

	/**
	 *
	 * @var array
	 */
	protected $instalment_rates = array();

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->user_id      = get_current_user_id();
		$this->user_country = trim( strtolower( get_user_meta( $this->user_id, 'billing_country', true ) ) );

		switch ( $this->user_country ) {
			case WC_EBANX_Constants::COUNTRY_ARGENTINA:
				$this->gateway = new WC_EBANX_Credit_Card_AR_Gateway();
				break;
			case WC_EBANX_Constants::COUNTRY_BRAZIL:
				$this->gateway = new WC_EBANX_Credit_Card_BR_Gateway();
				break;
			case WC_EBANX_Constants::COUNTRY_COLOMBIA:
				$this->gateway = new WC_EBANX_Credit_Card_CO_Gateway();
				break;
			case WC_EBANX_Constants::COUNTRY_MEXICO:
				$this->gateway = new WC_EBANX_Credit_Card_MX_Gateway();
				break;
			default:
				$this->gateway = false;
		}

		if ( ! $this->gateway
			|| $this->gateway->get_setting_or_default( 'one_click', 'no' ) !== 'yes'
			|| $this->gateway->get_setting_or_default( 'save_card_data', 'no' ) !== 'yes' ) {
			return;
		}

		/**
		 * Active the one click purchase when the settings is enabled
		 */
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 100 );
		add_action( 'woocommerce_after_add_to_cart_form', array( $this, 'print_button' ) );
		add_action( 'wp_loaded', array( $this, 'one_click_handler' ), 99 );

		$cards = get_user_meta( $this->user_id, '_ebanx_credit_card_token', true );

		$this->cards = is_array( $cards ) ? array_filter( $cards ) : array();

		$this->generate_instalments_rates();
	}

	/**
	 * Generate the properties for each interest rates
	 *
	 * @return void
	 */
	public function generate_instalments_rates() {
		if ( ! $this->gateway
			|| $this->gateway->get_setting_or_default( 'interest_rates_enabled', 'no' ) !== 'yes' ) {
			return;
		}

		$max_instalments = $this->gateway->configs->settings['credit_card_instalments'];

		for ( $i = 1; $i <= $max_instalments; $i++ ) {
			$field                        = 'interest_rates_' . sprintf( '%02d', $i );
			$this->instalment_rates[ $i ] = 0;
			if ( is_numeric( $this->gateway->configs->settings[ $field ] ) ) {
				$this->instalment_rates[ $i ] = $this->gateway->configs->settings[ $field ] / 100;
			}
		}
	}

	/**
	 * Process the one click request
	 *
	 * @throws Exception When reading with 'ebanx-action'.
	 *
	 * @return void
	 */
	public function one_click_handler() {
		ob_start();

		if ( is_admin()
			|| ! WC_EBANX_Request::has( 'ebanx-action' )
			|| ! WC_EBANX_Request::has( 'ebanx-nonce' )
			|| ! WC_EBANX_Request::has( 'ebanx-product-id' )
			|| ! WC_EBANX_Request::has( 'ebanx-cart-total' )
			|| WC_EBANX_Request::read( 'ebanx-action' ) !== self::CREATE_ORDER_ACTION
			|| ! wp_verify_nonce( WC_EBANX_Request::read( 'ebanx-nonce' ), self::CREATE_ORDER_ACTION )
			|| ! $this->customer_can()
			|| ! $this->customer_has_ebanx_required_data()
		) {
			return;
		}

		try {
			$product_id     = WC_EBANX_Request::read( 'ebanx-product-id' );
			$product_to_add = get_product( $product_id );

			$order_params = array(
				'status'      => 'pending',
				'customer_id' => $this->user_id,
			);

			if ( class_exists( 'WC_Subscription' ) && strpos( get_class( $product_to_add ), 'Subscription' ) !== false ) {
				$subscription        = new WC_Subscription( $order_params );
				$subscription->order = wc_create_order( $order_params );
				$subscription->save();

				update_post_meta( $subscription->id, '_customer_user', $this->user_id );

				$order = $subscription->order;
			} else {
				$order = wc_create_order( $order_params );
			}

			$order->add_product( $product_to_add, 1 );

			$user = array(
				'email'      => get_user_meta( $this->user_id, 'billing_email', true ),
				'country'    => get_user_meta( $this->user_id, 'billing_country', true ),
				'first_name' => get_user_meta( $this->user_id, 'billing_first_name', true ),
				'last_name'  => get_user_meta( $this->user_id, 'billing_last_name', true ),
				'company'    => get_user_meta( $this->user_id, 'billing_company', true ),
				'address_1'  => get_user_meta( $this->user_id, 'billing_address_1', true ),
				'address_2'  => get_user_meta( $this->user_id, 'billing_address_2', true ),
				'city'       => get_user_meta( $this->user_id, 'billing_city', true ),
				'state'      => get_user_meta( $this->user_id, 'billing_state', true ),
				'postcode'   => get_user_meta( $this->user_id, 'billing_postcode', true ),
				'phone'      => get_user_meta( $this->user_id, 'billing_phone', true ),
			);

			$order->set_payment_method( $this->gateway );

			$meta = array(
				'_billing_email'      => $user['email'],
				'_billing_country'    => $user['country'],
				'_billing_first_name' => $user['first_name'],
				'_billing_last_name'  => $user['last_name'],
				'_billing_company'    => $user['company'],
				'_billing_address_1'  => $user['address_1'],
				'_billing_address_2'  => $user['address_2'],
				'_billing_city'       => $user['city'],
				'_billing_state'      => $user['state'],
				'_billing_phone'      => $user['phone'],
				'_order_shipping'     => WC()->cart->shipping_total,
				'_cart_discount'      => WC()->cart->get_cart_discount_total(),
				'_cart_discount_tax'  => WC()->cart->get_cart_discount_tax_total(),
				'_order_tax'          => WC()->cart->tax_total,
				'_order_shipping_tax' => WC()->cart->shipping_tax_total,
				'_order_total'        => WC()->cart->total,
			);

			$order->billing_country    = $user['country'];
			$order->billing_first_name = $user['first_name'];
			$order->billing_last_name  = $user['last_name'];
			$order->billing_email      = $user['email'];
			$order->billing_phone      = $user['phone'];
			$order->save();

			foreach ( $meta as $meta_key => $meta_value ) {
				update_post_meta( $order->id, $meta_key, $meta_value );
			}

			$order->calculate_totals();

			$response = $this->gateway->process_payment( $order->id );

			if ( 'success' !== $response['result'] ) {
				$message = __( 'EBANX: Unable to create the payment via one click.', 'woocommerce-gateway-ebanx' );

				$order->add_order_note( $message );

				throw new Exception( $message );
			}

			if ( isset( $subscription ) ) {
				$subscription->save();
				$subscription->update_status( 'active' );
			}

			$this->restore_cart();

			wp_safe_redirect( $response['redirect'] );
			exit;
		} catch ( Exception $e ) {
			// TODO: Make a caught exception.
			WC_EBANX_Log::wp_write_log( 'Exception in one_click_handler.' );
		}

		$this->restore_cart();

		return;
	}

	/**
	 * Restore the items of the cart until the last request
	 *
	 * @return void
	 */
	public function restore_cart() {
		// delete current cart.
		WC()->cart->empty_cart( true );

		// update user meta with saved persistent.
		$saved_cart = get_user_meta( $this->user_id, '_ebanx_persistent_cart', true );

		// then reload cart.
		WC()->session->set( 'cart', $saved_cart );
		WC()->cart->get_cart_from_session();
	}

	/**
	 * It creates the user's billing data to process the one click response
	 *
	 * @return array
	 */
	public function get_user_billing_address() {
		// Formatted Addresses.
		$billing = array(
			'first_name' => get_user_meta( $this->user_id, 'billing_first_name', true ),
			'last_name'  => get_user_meta( $this->user_id, 'billing_last_name', true ),
			'company'    => get_user_meta( $this->user_id, 'billing_company', true ),
			'address_1'  => get_user_meta( $this->user_id, 'billing_address_1', true ),
			'address_2'  => get_user_meta( $this->user_id, 'billing_address_2', true ),
			'city'       => get_user_meta( $this->user_id, 'billing_city', true ),
			'state'      => get_user_meta( $this->user_id, 'billing_state', true ),
			'postcode'   => get_user_meta( $this->user_id, 'billing_postcode', true ),
			'country'    => get_user_meta( $this->user_id, 'billing_country', true ),
			'email'      => get_user_meta( $this->user_id, 'billing_email', true ),
			'phone'      => get_user_meta( $this->user_id, 'billing_phone', true ),
		);

		if ( ! empty( $billing['country'] ) ) {
			update_user_meta( $this->user_id, 'billing_country', $billing['country'] );
		}
		if ( ! empty( $billing['state'] ) ) {
			update_user_meta( $this->user_id, 'billing_state', $billing['state'] );
		}
		if ( ! empty( $billing['postcode'] ) ) {
			update_user_meta( $this->user_id, 'billing_postcode', $billing['postcode'] );
		}

		return apply_filters( 'ebanx_customer_billing', array_filter( $billing ) );
	}

	/**
	 * Set the assets necessary by one click works
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		wp_enqueue_script(
			'woocommerce_ebanx_one_click_script',
			plugins_url( 'assets/js/one-click.js', WC_EBANX::DIR ),
			array(),
			WC_EBANX::get_plugin_version(),
			true
		);

		wp_enqueue_style(
			'woocommerce_ebanx_one_click_style',
			plugins_url( 'assets/css/one-click.css', WC_EBANX::DIR )
		);
	}

	/**
	 * Check if the custom has all required data required by EBANX
	 *
	 * @return boolean If the user has all required data, return true
	 */
	protected function customer_has_ebanx_required_data() {
		$card = current(
			array_filter(
				(array) array_filter( get_user_meta( $this->user_id, '_ebanx_credit_card_token', true ) ), function ( $card ) {
					return WC_EBANX_Request::read( 'ebanx-one-click-token' ) == $card->token;
				}
			)
		);

		$names = $this->gateway->names;

		WC_EBANX_Request::set( 'ebanx_token', $card->token );
		WC_EBANX_Request::set( 'ebanx_masked_card_number', $card->masked_number );
		WC_EBANX_Request::set( 'ebanx_brand', $card->brand );
		WC_EBANX_Request::set( 'ebanx_billing_cvv', WC_EBANX_Request::read( 'ebanx-one-click-cvv' ) );
		WC_EBANX_Request::set( 'ebanx_is_one_click', true );
		WC_EBANX_Request::set( 'ebanx-credit-card-installments', WC_EBANX_Request::read( 'ebanx-credit-card-installments', 1 ) );
		WC_EBANX_Request::set( 'ebanx_billing_instalments', WC_EBANX_Request::read( 'ebanx-credit-card-installments' ) );

		WC_EBANX_Request::set( $names['ebanx_billing_brazil_document'], get_user_meta( $this->user_id, '_ebanx_billing_brazil_document', true ) );

		WC_EBANX_Request::set( $names['ebanx_billing_colombia_document'], get_user_meta( $this->user_id, '_ebanx_billing_colombia_document', true ) );

		WC_EBANX_Request::set( $names['ebanx_billing_argentina_document'], get_user_meta( $this->user_id, '_ebanx_billing_argentina_document', true ) );

		WC_EBANX_Request::set( 'billing_postcode', $this->get_user_billing_address()['postcode'] );
		WC_EBANX_Request::set( 'billing_address_1', $this->get_user_billing_address()['address_1'] );
		WC_EBANX_Request::set( 'billing_city', $this->get_user_billing_address()['city'] );
		WC_EBANX_Request::set( 'billing_state', $this->get_user_billing_address()['state'] );

		return ! empty( WC_EBANX_Request::read( 'ebanx-one-click-token', null ) )
			&& ! empty( WC_EBANX_Request::read( 'ebanx-credit-card-installments', null ) )
			&& ! empty( WC_EBANX_Request::read( 'ebanx-one-click-cvv', null ) )
			&& ( ( WC_EBANX_Request::has( $names['ebanx_billing_brazil_document'] )
			|| WC_EBANX_Request::has( $names['ebanx_billing_colombia_document'] )
			|| WC_EBANX_Request::has( $names['ebanx_billing_argentina_document'] ) )
			&& ( ! empty( WC_EBANX_Request::read( $names['ebanx_billing_brazil_document'], null ) )
			|| ! empty( WC_EBANX_Request::read( $names['ebanx_billing_colombia_document'], null ) )
			|| ! empty( WC_EBANX_Request::read( $names['ebanx_billing_argentina_document'], null ) ) )
			|| 'mx' === $this->user_country );
	}

	/**
	 * Check if the customer is ready
	 *
	 * @return boolean Returns if the customer has a minimal requirement
	 */
	public function customer_can() {
		return ! is_user_logged_in() || ! get_user_meta( $this->user_id, '_billing_email', true ) && ! empty( $this->cards );
	}

	/**
	 *
	 * @return bool
	 */
	public function should_show_button() {
		return $this->cards
			&& ( ! empty( get_user_meta( $this->user_id, '_ebanx_billing_brazil_document', true ) )
			|| ! empty( get_user_meta( $this->user_id, '_ebanx_billing_colombia_document', true ) )
			|| ! empty( get_user_meta( $this->user_id, '_ebanx_billing_argentina_document', true ) )
			|| WC_EBANX_Constants::COUNTRY_MEXICO === $this->user_country );
	}

	/**
	 * Render the button "One-Click Purchase" using a template
	 *
	 * @return void
	 * @throws Exception Throws missing parameter message.
	 */
	public function print_button() {
		if ( ! $this->user_country ) {
			return;
		}

		global $product;

		switch ( get_locale() ) {
			case 'pt_BR':
				$messages = array(
					'instalments' => 'Número de parcelas',
				);
				break;
			case 'es_ES':
			case 'es_CO':
			case 'es_CL':
			case 'es_PE':
			case 'es_MX':
			case 'es_AR':
				$messages = array(
					'instalments' => 'Meses sin intereses',
				);
				break;
			default:
				$messages = array(
					'instalments' => 'Number of instalments',
				);
				break;
		}

		$country = $this->gateway->get_transaction_address( 'country' );

		$cart_total = $product->price;

		$ebanx             = new WC_EBANX_New_Gateway();
		$currency          = WC_EBANX_Constants::$local_currencies[ $country ];
		$currency_rate = round( floatval( $ebanx->get_local_currency_rate_for_site( $currency ) ), 2 );
		$instalments_terms = $this->gateway->get_payment_terms( $country, $cart_total, $currency_rate );

		$args = apply_filters(
			'ebanx_template_args', array(
				'cards'              => $this->cards,
				'cart_total'         => $cart_total,
				'product_id'         => $product->id,
				'installment_taxes'  => $this->instalment_rates,
				'currency'           => $currency,
				'currency_rate'      => $currency_rate,
				'label'              => __( 'Pay with one click', 'woocommerce-gateway-ebanx' ),
				'instalments'        => $messages['instalments'],
				'instalments_terms'  => $instalments_terms,
				'nonce'              => wp_create_nonce( self::CREATE_ORDER_ACTION ),
				'action'             => self::CREATE_ORDER_ACTION,
				'permalink'          => get_permalink( $product->id ),
				'country'            => $country,
				'should_show_button' => $this->should_show_button(),
			)
		);

		wc_get_template( 'one-click.php', $args, '', WC_EBANX::get_templates_path() . 'one-click/' );
	}
}

new WC_EBANX_One_Click();
