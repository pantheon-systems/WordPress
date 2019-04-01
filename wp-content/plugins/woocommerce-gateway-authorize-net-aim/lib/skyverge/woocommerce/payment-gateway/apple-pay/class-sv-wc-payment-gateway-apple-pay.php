<?php
/**
 * WooCommerce Payment Gateway Framework
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the plugin to newer
 * versions in the future. If you wish to customize the plugin for your
 * needs please refer to http://www.skyverge.com
 *
 * @package   SkyVerge/WooCommerce/Payment-Gateway/Apple-Pay
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2016, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

if ( ! class_exists( 'SV_WC_Payment_Gateway_Apple_Pay' ) ) :

/**
 * Sets up Apple Pay support.
 *
 * @since 4.7.0
 */
class SV_WC_Payment_Gateway_Apple_Pay {


	/** @var \SV_WC_Payment_Gateway_Apple_Pay_Admin the admin instance */
	protected $admin;

	/** @var \SV_WC_Payment_Gateway_Apple_Pay_Frontend the frontend instance */
	protected $frontend;

	/** @var \SV_WC_Payment_Gateway_Apple_Pay_AJAX the AJAX instance */
	protected $ajax;

	/** @var \SV_WC_Payment_Gateway_Plugin the plugin instance */
	protected $plugin;

	/** @var \SV_WC_Payment_Gateway_Apple_Pay_API the Apple Pay API */
	protected $api;


	/**
	 * Constructs the class.
	 *
	 * @since 4.7.0
	 *
	 * @param \SV_WC_Payment_Gateway_Plugin $plugin the plugin instance
	 */
	public function __construct( SV_WC_Payment_Gateway_Plugin $plugin ) {

		$this->plugin = $plugin;

		$this->init();

		if ( $this->is_available() ) {
			add_filter( 'woocommerce_customer_taxable_address', array( $this, 'set_customer_taxable_address' ) );
		}
	}


	/**
	 * Initializes the Apple Pay handlers.
	 *
	 * @since 4.7.0
	 */
	protected function init() {

		if ( is_admin() && ! is_ajax() ) {
			$this->admin = new SV_WC_Payment_Gateway_Apple_Pay_Admin( $this );
		} else {
			$this->ajax     = new SV_WC_Payment_Gateway_Apple_Pay_AJAX( $this );
			$this->frontend = new SV_WC_Payment_Gateway_Apple_Pay_Frontend( $this->get_plugin(), $this );
		}
	}


	/**
	 * Processes the payment after an Apple Pay authorization.
	 *
	 * This method creates a new order and calls the gateway for processing.
	 *
	 * @since 4.7.0
	 *
	 * @return array
	 * @throws \SV_WC_Payment_Gateway_Exception
	 */
	public function process_payment() {

		$order = null;

		try {

			$payment_response = $this->get_stored_payment_response();

			if ( ! $payment_response ) {
				throw new SV_WC_Payment_Gateway_Exception( 'Invalid payment response data' );
			}

			$this->log( "Payment Response:\n" . $payment_response->to_string_safe() . "\n" );

			$order = SV_WC_Payment_Gateway_Apple_Pay_Orders::create_order( WC()->cart );

			$order->set_payment_method( $this->get_processing_gateway() );

			// if we got to this point, the payment was authorized by Apple Pay
			// from here on out, it's up to the gateway to not screw things up.
			$order->add_order_note( __( 'Apple Pay payment authorized.', 'woocommerce-plugin-framework' ) );

			$order->set_address( $payment_response->get_billing_address(),  'billing' );
			$order->set_address( $payment_response->get_shipping_address(), 'shipping' );

			if ( SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ) {
				$order->save();
			}

			// add Apple Pay response data to the order
			add_filter( 'wc_payment_gateway_' . $this->get_processing_gateway()->get_id() . '_get_order', array( $this, 'add_order_data' ) );

			if ( $this->is_test_mode() ) {
				$result = $this->process_test_payment( $order );
			} else {
				$result = $this->get_processing_gateway()->process_payment( SV_WC_Order_Compatibility::get_prop( $order, 'id' ) );
			}

			if ( isset( $result['result'] ) && 'success' !== $result['result'] ) {
				throw new SV_WC_Payment_Gateway_Exception( 'Gateway processing error.' );
			}

			if ( $user_id = $order->get_user_id() ) {
				$this->update_customer_addresses( $user_id, $payment_response );
			}

			$this->clear_payment_data();

			return $result;

		} catch ( SV_WC_Payment_Gateway_Exception $e ) {

			if ( $order ) {

				$order->add_order_note( sprintf(
					/** translators: Placeholders: %s - the error message */
					__( 'Apple Pay payment failed. %s', 'woocommerce-plugin-framework' ),
					$e->getMessage()
				) );
			}

			throw $e;
		}
	}


	/**
	 * Updates a customer's stored billing & shipping addresses based on the
	 * Apple Pay payment response.
	 *
	 * @since 4.7.0
	 *
	 * @param int $user_id WordPress user ID
	 * @param \SV_WC_Payment_Gateway_Apple_Pay_Payment_Response $payment_response payment response object
	 */
	protected function update_customer_addresses( $user_id, SV_WC_Payment_Gateway_Apple_Pay_Payment_Response $payment_response ) {

		foreach ( $payment_response->get_billing_address() as $key => $value ) {
			update_user_meta( $user_id, 'billing_' . $key, $value );
		}

		$shipping_address = $payment_response->get_shipping_address();

		if ( ! empty( $shipping_address['address_1'] ) ) {

			foreach ( $payment_response->get_shipping_address() as $key => $value ) {
				update_user_meta( $user_id, 'shipping_' . $key, $value );
			}
		}
	}


	/**
	 * Simulates a successful gateway payment response.
	 *
	 * This provides an easy way for merchants to test that their certificates
	 * and other settings are correctly configured and communicating with Apple
	 * without processing actual payments to test.
	 *
	 * @since 4.7.0
	 *
	 * @param \WC_Order $order order object
	 * @return array
	 */
	protected function process_test_payment( WC_Order $order ) {

		$order->payment_complete();

		WC()->cart->empty_cart();

		return array(
			'result'   => 'success',
			'redirect' => $this->get_processing_gateway()->get_return_url( $order ),
		);
	}


	/**
	 * Gets a single product payment request.
	 *
	 * @since 4.7.0
	 * @see \SV_WC_Payment_Gateway_Apple_Pay::build_payment_request()
	 *
	 * @param \WC_Product $product product object
	 * @param bool $in_cart whether to generate a cart for this request
	 * @return array
	 *
	 * @throws \SV_WC_Payment_Gateway_Exception
	 */
	public function get_product_payment_request( WC_Product $product, $in_cart = false ) {

		if ( ! is_user_logged_in() ) {
			WC()->session->set_customer_session_cookie( true );
		}

		// no subscription products
		if ( $this->get_plugin()->is_subscriptions_active() && WC_Subscriptions_Product::is_subscription( $product ) ) {
			throw new SV_WC_Payment_Gateway_Exception( 'Not available for subscription products.' );
		}

		// no pre-order "charge upon release" products
		if ( $this->get_plugin()->is_pre_orders_active() && WC_Pre_Orders_Product::product_is_charged_upon_release( $product ) ) {
			throw new SV_WC_Payment_Gateway_Exception( 'Not available for pre-order products that are set to charge upon release.' );
		}

		// only simple products
		if ( ! $product->is_type( 'simple' ) ) {
			throw new SV_WC_Payment_Gateway_Exception( 'Buy Now is only available for simple products' );
		}

		// if this product can't be purchased, bail
		if ( ! $product->is_purchasable() || ! $product->is_in_stock() || ! $product->has_enough_stock( 1 ) ) {
			throw new SV_WC_Payment_Gateway_Exception( 'Product is not available for purchase.' );
		}

		if ( $in_cart ) {

			WC()->cart->empty_cart();

			WC()->cart->add_to_cart( $product->get_id() );

			$request = $this->get_cart_payment_request( WC()->cart );

		} else {

			$request = $this->build_payment_request( $product->get_price(), array( 'needs_shipping' => $product->needs_shipping() ) );

			$stored_request = $this->get_stored_payment_request();

			$stored_request['product_id'] = $product->get_id();

			$this->store_payment_request( $stored_request );
		}

		/**
		 * Filters the Apple Pay Buy Now JS payment request.
		 *
		 * @since 4.7.0
		 * @param array $request request data
		 * @param \WC_Product $product product object
		 */
		return apply_filters( 'sv_wc_apple_pay_buy_now_payment_request', $request, $product );
	}


	/**
	 * Gets a payment request based on WooCommerce cart data.
	 *
	 * @since 4.7.0
	 * @see \SV_WC_Payment_Gateway_Apple_Pay::build_payment_request()
	 *
	 * @param \WC_Cart $cart cart object
	 * @return array
	 *
	 * @throws \SV_WC_Payment_Gateway_Exception
	 */
	public function get_cart_payment_request( WC_Cart $cart ) {

		if ( $this->get_plugin()->is_subscriptions_active() && WC_Subscriptions_Cart::cart_contains_subscription() ) {
			throw new SV_WC_Payment_Gateway_Exception( 'Cart contains subscriptions.' );
		}

		if ( $this->get_plugin()->is_pre_orders_active() && WC_Pre_Orders_Cart::cart_contains_pre_order() ) {
			throw new SV_WC_Payment_Gateway_Exception( 'Cart contains pre-orders.' );
		}

		// ensure totals are fully calculated by simulating checkout in WC 3.1 or lower
		// TODO: remove this when WC 3.2+ can be required {CW 2017-11-17}
		if ( SV_WC_Plugin_Compatibility::is_wc_version_lt( '3.2' ) && ! defined( 'WOOCOMMERCE_CHECKOUT' ) ) {
			define( 'WOOCOMMERCE_CHECKOUT', true );
		}

		$cart->calculate_totals();

		if ( count( WC()->shipping->get_packages() ) > 1 ) {
			throw new SV_WC_Payment_Gateway_Exception( 'Apple Pay cannot be used for multiple shipments.' );
		}

		$args = array(
			'line_totals'    => $this->get_cart_totals( $cart ),
			'needs_shipping' => $cart->needs_shipping(),
		);

		// build it!
		$request = $this->build_payment_request( $cart->total, $args );

		/**
		 * Filters the Apple Pay cart JS payment request.
		 *
		 * @since 4.7.0
		 * @param array $args the cart JS payment request
		 * @param \WC_Cart $cart the cart object
		 */
		return apply_filters( 'sv_wc_apple_pay_cart_payment_request', $request, $cart );
	}


	/**
	 * Recalculates the lines and totals for the current payment request.
	 *
	 * @since 4.7.0
	 *
	 * @return array
	 *
	 * @throws \SV_WC_Payment_Gateway_Exception
	 */
	public function recalculate_totals() {

		$payment_request = $this->get_stored_payment_request();

		if ( empty( $payment_request ) ){
			throw new SV_WC_Payment_Gateway_Exception( 'Payment request data is missing.' );
		}

		// if this is a single product request, make sure the cart gets populated
		if ( ! empty( $payment_request['product_id'] ) && $product = wc_get_product( $payment_request['product_id'] ) ) {
			$payment_request = $this->get_product_payment_request( $product, true );
		}

		if ( ! WC()->cart ) {
			throw new SV_WC_Payment_Gateway_Exception( 'Cart data is missing.' );
		}

		$totals = $this->get_cart_totals( WC()->cart );

		$payment_request['lineItems']       = $this->build_payment_request_lines( $totals );
		$payment_request['shippingMethods'] = array();

		$packages = WC()->shipping->get_packages();

		if ( ! empty( $packages ) ) {

			foreach ( $packages[0]['rates'] as $method ) {

				/**
				 * Filters a shipping method's description for the Apple Pay payment card.
				 *
				 * @since 4.7.0
				 *
				 * @param string $detail shipping method detail, such as delivery estimation
				 * @param object $method shipping method object
				 */
				$method_detail = apply_filters( 'wc_payment_gateway_apple_pay_shipping_method_detail', '', $method );

				$payment_request['shippingMethods'][] = array(
					'label'      => $method->get_label(),
					'detail'     => $method_detail,
					'amount'     => $this->format_price( $method->cost ),
					'identifier' => $method->id,
				);
			}
		}

		// reset the order total based on the new line items
		$payment_request['total']['amount'] = $this->format_price( array_sum( wp_list_pluck( $payment_request['lineItems'], 'amount' ) ) );

		// update the stored payment request session with the new line items & totals
		$this->store_payment_request( $payment_request );

		return $payment_request;
	}


	/**
	 * Gets the line totals for a cart.
	 *
	 * @since 4.7.0
	 * @see \SV_WC_Payment_Gateway_Apple_Pay::build_payment_request_lines()
	 *
	 * @param \WC_Cart $cart cart object
	 * @return array
	 */
	protected function get_cart_totals( WC_Cart $cart ) {

		// ensure totals are fully calculated by simulating checkout in WC 3.1 or lower
		// TODO: remove this when WC 3.2+ can be required {CW 2017-11-17}
		if ( SV_WC_Plugin_Compatibility::is_wc_version_lt( '3.2' ) && ! defined( 'WOOCOMMERCE_CHECKOUT' ) ) {
			define( 'WOOCOMMERCE_CHECKOUT', true );
		}

		$cart->calculate_totals();

		return array(
			'subtotal' => $cart->subtotal_ex_tax,
			'discount' => $cart->get_cart_discount_total(),
			'shipping' => $cart->shipping_total,
			'fees'     => $cart->fee_total,
			'taxes'    => $cart->tax_total + $cart->shipping_tax_total,
		);
	}


	/**
	 * Builds a payment request for the Apple Pay JS.
	 *
	 * This contains all of the data necessary to complete a payment.
	 *
	 * @since 4.7.0
	 *
	 * @param float|int $amount amount to be charged by Apple Pay
	 * @param array $args {
	 *     Optional. The payment request args.
	 *
	 *     @type string $currency_code         Payment currency code. Defaults to the shop currency.
	 *     @type string $country_code          Payment country code. Defaults to the shop base country.
	 *     @type string $merchant_name         Merchant name. Defaults to the shop name.
	 *     @type array  $merchant_capabilities merchant capabilities
	 *     @type array  $supported_networks    supported networks or card types
	 *     @type bool   $needs_shipping        whether the payment needs shipping
	 *     @type array  $line_totals           request line totals. @see \SV_WC_Payment_Gateway_Apple_Pay::build_payment_request_lines()
	 * }
	 *
	 * @return array
	 */
	public function build_payment_request( $amount, $args = array() ) {

		$args = wp_parse_args( $args, array(
			'currency_code'         => get_woocommerce_currency(),
			'country_code'          => get_option( 'woocommerce_default_country' ),
			'merchant_name'         => get_bloginfo( 'name', 'display' ),
			'merchant_capabilities' => $this->get_capabilities(),
			'supported_networks'    => $this->get_supported_networks(),
			'line_totals'           => array(),
			'needs_shipping'        => false,
		) );

		// set the base required defaults
		$request = array(
			'currencyCode'                  => $args['currency_code'],
			'countryCode'                   => substr( $args['country_code'], 0, 2 ),
			'merchantCapabilities'          => $args['merchant_capabilities'],
			'supportedNetworks'             => $args['supported_networks'],
			'requiredBillingContactFields'  => array( 'postalAddress' ),
			'requiredShippingContactFields' => array(
				'phone',
				'email',
				'name',
			),
		);

		if ( $args['needs_shipping'] ) {
			$request['requiredShippingContactFields'][] = 'postalAddress';
		}

		if ( is_array( $args['line_totals'] ) && ! empty( $args['line_totals'] ) ) {
			$request['lineItems'] = $this->build_payment_request_lines( $args['line_totals'] );
		}

		// order total
		$request['total'] = array(
			'type'   => 'final',
			'label'  => $args['merchant_name'],
			'amount' => $this->format_price( $amount ),
		);

		$this->store_payment_request( $request );

		// remove line item keys that are only useful for us later
		if ( ! empty( $request['lineItems'] ) ) {
			$request['lineItems'] = array_values( $request['lineItems'] );
		}

		return $request;
	}


	/**
	 * Builds payment request lines for the Apple Pay JS.
	 *
	 * Apple guidelines prefer that the "lines" displayed on the Apple Pay card
	 * should be overall order totals, instead of listing actual product lines.
	 * This method standardizes the main breakdowns which are:
	 * + Subtotal
	 * + Discounts (represented as a single negative amount)
	 * + Shipping
	 * + Fees
	 * + Taxes
	 *
	 * @since 4.7.0
	 *
	 * @param array $totals {
	 *     Payment line totals.
	 *
	 *     @type float $subtotal items subtotal
	 *     @type float $discount discounts total
	 *     @type float $shipping shipping total
	 *     @type float $fees     fees total
	 *     @type float $taxes    tax total
	 * }
	 */
	public function build_payment_request_lines( $totals ) {

		$totals = wp_parse_args( $totals, array(
			'subtotal' => 0.00,
			'discount' => 0.00,
			'shipping' => 0.00,
			'fees'     => 0.00,
			'taxes'    => 0.00,
		) );

		$lines = array();

		// subtotal
		if ( $totals['subtotal'] > 0 ) {

			$lines['subtotal'] = array(
				'type'   => 'final',
				'label'  => __( 'Subtotal', 'woocommerce-plugin-framework' ),
				'amount' => $this->format_price( $totals['subtotal'] ),
			);
		}

		// discounts
		if ( $totals['discount'] > 0 ) {

			$lines['discount'] = array(
				'type'   => 'final',
				'label'  => __( 'Discount', 'woocommerce-plugin-framework' ),
				'amount' => abs( $this->format_price( $totals['discount'] ) ) * -1,
			);
		}

		// shipping
		if ( $totals['shipping'] > 0 ) {

			$lines['shipping'] = array(
				'type'   => 'final',
				'label'  => __( 'Shipping', 'woocommerce-plugin-framework' ),
				'amount' => $this->format_price( $totals['shipping'] ),
			);
		}

		// fees
		if ( $totals['fees'] > 0 ) {

			$lines['fees'] = array(
				'type'   => 'final',
				'label'  => __( 'Fees', 'woocommerce-plugin-framework' ),
				'amount' => $this->format_price( $totals['fees'] ),
			);
		}

		// taxes
		if ( $totals['taxes'] > 0 ) {

			$lines['taxes'] = array(
				'type'   => 'final',
				'label'  => __( 'Taxes', 'woocommerce-plugin-framework' ),
				'amount' => $this->format_price( $totals['taxes'] ),
			);
		}

		return $lines;
	}


	/**
	 * Formats a total price for use with Apple Pay JS.
	 *
	 * @since 4.7.0
	 *
	 * @param string|float $price the price to format
	 * @return string
	 */
	protected function format_price( $price ) {

		return wc_format_decimal( $price, 2 );
	}


	/**
	 * Gets the stored payment request data.
	 *
	 * @since 4.7.0
	 *
	 * @return array
	 */
	public function get_stored_payment_request() {

		return WC()->session->get( 'apple_pay_payment_request', array() );
	}


	/**
	 * Gets the stored payment response data.
	 *
	 * @since 4.7.0
	 *
	 * @return \SV_WC_Payment_Gateway_Apple_Pay_Payment_Response|false
	 */
	public function get_stored_payment_response() {

		$response_data = WC()->session->get( 'apple_pay_payment_response', array() );

		if ( ! empty( $response_data ) ) {
			return new SV_WC_Payment_Gateway_Apple_Pay_Payment_Response( $response_data );
		} else {
			return false;
		}
	}


	/**
	 * Stores payment request data for later use.
	 *
	 * @since 4.7.0
	 */
	public function store_payment_request( $data ) {

		WC()->session->set( 'apple_pay_payment_request', $data );
	}


	/**
	 * Stores payment response data for later use.
	 *
	 * @since 4.7.0
	 */
	public function store_payment_response( $data ) {

		WC()->session->set( 'apple_pay_payment_response', $data );
	}


	/**
	 * Clears all payment request & response data from the session.
	 *
	 * @since 4.7.0
	 */
	public function clear_payment_data() {

		unset( WC()->session->apple_pay_payment_request );
		unset( WC()->session->apple_pay_payment_response );
		unset( WC()->session->order_awaiting_payment );
	}


	/**
	 * Filters and sets the customer's taxable address.
	 *
	 * This is necessary because Apple Pay doesn't ever provide a billing
	 * address until after payment is complete. If the shop is set to calculate
	 * tax based on the billing address, we need to use the shipping address
	 * to at least get some rates for new customers.
	 *
	 * @internal
	 *
	 * @since 4.7.0
	 *
	 * @param array $address taxable address
	 * @return array
	 */
	public function set_customer_taxable_address( $address ) {

		$billing_country = SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ? WC()->customer->get_billing_country() : WC()->customer->get_country();

		// set to the shipping address provided by Apple Pay if:
		// 1. shipping is available
		// 2. billing is not available
		// 3. taxes aren't configured to use the shop base
		if ( WC()->customer->get_shipping_country() && ! $billing_country && $address[0] !== WC()->countries->get_base_country() ) {

			$address = array(
				WC()->customer->get_shipping_country(),
				WC()->customer->get_shipping_state(),
				WC()->customer->get_shipping_postcode(),
				WC()->customer->get_shipping_city(),
			);
		}

		return $address;
	}


	/**
	 * Allows the processing gateway to add Apple Pay details to the payment data.
	 *
	 * @internal
	 *
	 * @since 4.7.0
	 *
	 * @param \WC_Order $order the order object
	 * @return \WC_Order
	 */
	public function add_order_data( $order ) {

		if ( $response = $this->get_stored_payment_response() ) {
			$order = $this->get_processing_gateway()->get_order_for_apple_pay( $order, $response );
		}

		return $order;
	}


	/**
	 * Gets the Apple Pay API.
	 *
	 * @since 4.7.0
	 *
	 * @return \SV_WC_Payment_Gateway_Apple_Pay_API
	 */
	public function get_api() {

		if ( ! $this->api instanceof SV_WC_Payment_Gateway_Apple_Pay_API ) {

			require_once( $this->get_plugin()->get_payment_gateway_framework_path() . '/apple-pay/api/class-sv-wc-payment-gateway-apple-pay-api.php');
			require_once( $this->get_plugin()->get_payment_gateway_framework_path() . '/apple-pay/api/class-sv-wc-payment-gateway-apple-pay-api-request.php');
			require_once( $this->get_plugin()->get_payment_gateway_framework_path() . '/apple-pay/api/class-sv-wc-payment-gateway-apple-pay-api-response.php');

			$this->api = new SV_WC_Payment_Gateway_Apple_Pay_API( $this->get_processing_gateway() );
		}

		return $this->api;
	}


	/**
	 * Adds a log entry to the gateway's debug log.
	 *
	 * @since 4.7.0
	 *
	 * @param string $message the log message to add
	 */
	public function log( $message ) {

		$gateway = $this->get_processing_gateway();

		if ( ! $gateway ) {
			return;
		}

		if ( $gateway->debug_log() ) {
			$gateway->get_plugin()->log( '[Apple Pay] ' . $message, $gateway->get_id() );
		}
	}


	/**
	 * Determines if Apple Pay is available.
	 *
	 * This does not indicate browser support or a user's ability, but rather
	 * that Apple Pay is properly configured and ready to be initiated by the
	 * Apple Pay JS.
	 *
	 * @since 4.7.0
	 *
	 * @return bool
	 */
	public function is_available() {

		$is_available = wc_site_is_https() && $this->is_configured();

		$is_available = $is_available && in_array( get_woocommerce_currency(), $this->get_accepted_currencies(), true );

		/**
		 * Filters whether Apple Pay should be made available to users.
		 *
		 * @since 4.7.0
		 * @param bool $is_available
		 */
		return apply_filters( 'sv_wc_apple_pay_is_available', $is_available );
	}


	/**
	 * Determines if Apple Pay settings are properly configured.
	 *
	 * @since 4.7.0
	 *
	 * @return bool
	 */
	public function is_configured() {

		if ( ! $this->get_processing_gateway() ) {
			return false;
		}

		$is_configured = $this->is_enabled() && $this->get_merchant_id() && $this->get_processing_gateway()->is_enabled();

		$is_configured = $is_configured && $this->is_cert_configured();

		return $is_configured;
	}


	/**
	 * Determines if the certification path is set and valid.
	 *
	 * @since 4.7.0
	 *
	 * @return bool
	 */
	public function is_cert_configured() {

		return is_readable( $this->get_cert_path() );
	}


	/**
	 * Determines if Apple Pay is enabled.
	 *
	 * @since 4.7.0
	 *
	 * @return bool
	 */
	public function is_enabled() {

		return 'yes' === get_option( 'sv_wc_apple_pay_enabled' );
	}


	/**
	 * Determines if test mode is enabled.
	 *
	 * @since 4.7.0
	 *
	 * @return bool
	 */
	public function is_test_mode() {

		return 'yes' === get_option( 'sv_wc_apple_pay_test_mode' );
	}


	/**
	 * Gets the configured Apple merchant ID.
	 *
	 * @since 4.7.0
	 * @return string
	 */
	public function get_merchant_id() {

		return get_option( 'sv_wc_apple_pay_merchant_id' );
	}


	/**
	 * Gets the certificate file path.
	 *
	 * @since 4.7.0
	 *
	 * @return string
	 */
	public function get_cert_path() {

		return get_option( 'sv_wc_apple_pay_cert_path' );
	}


	/**
	 * Gets the currencies accepted by the gateway's Apple Pay integration.
	 *
	 * @since 4.7.0
	 *
	 * @return array
	 */
	public function get_accepted_currencies() {

		$currencies = ( $this->get_processing_gateway() ) ? $this->get_processing_gateway()->get_apple_pay_currencies() : array();

		/**
		 * Filters the currencies accepted by the gateway's Apple Pay integration.
		 *
		 * @since 4.7.0
		 * @return array
		 */
		return apply_filters( 'sv_wc_apple_pay_accepted_currencies', $currencies );
	}


	/**
	 * Gets the gateway's Apple Pay capabilities.
	 *
	 * @since 4.7.0
	 *
	 * @return array
	 */
	public function get_capabilities() {

		$valid_capabilities = array(
			'supports3DS',
			'supportsEMV',
			'supportsCredit',
			'supportsDebit',
		);

		$gateway_capabilities = ( $this->get_processing_gateway() ) ? $this->get_processing_gateway()->get_apple_pay_capabilities() : array();

		$capabilities = array_intersect( $valid_capabilities, $gateway_capabilities );

		/**
		 * Filters the gateway's Apple Pay capabilities.
		 *
		 * @since 4.7.0
		 *
		 * @param array $capabilities the gateway capabilities
		 * @param \SV_WC_Payment_Gateway_Apple_Pay $handler the Apple Pay handler
		 */
		return apply_filters( 'sv_wc_apple_pay_capabilities', array_values( $capabilities ), $this );
	}


	/**
	 * Gets the supported networks for Apple Pay.
	 *
	 * @since 4.7.0
	 *
	 * @return array
	 */
	public function get_supported_networks() {

		$accepted_card_types = ( $this->get_processing_gateway() ) ? $this->get_processing_gateway()->get_card_types() : array();

		$accepted_card_types = array_map( 'SV_WC_Payment_Gateway_Helper::normalize_card_type', $accepted_card_types );

		$valid_networks = array(
			SV_WC_Payment_Gateway_Helper::CARD_TYPE_AMEX       => 'amex',
			SV_WC_Payment_Gateway_Helper::CARD_TYPE_DISCOVER   => 'discover',
			SV_WC_Payment_Gateway_Helper::CARD_TYPE_MASTERCARD => 'masterCard',
			SV_WC_Payment_Gateway_Helper::CARD_TYPE_VISA       => 'visa',
			'privateLabel' => 'privateLabel', // ?
		);

		$networks = array_intersect_key( $valid_networks, array_flip( $accepted_card_types ) );

		/**
		 * Filters the supported Apple Pay networks (card types).
		 *
		 * @since 4.7.0
		 *
		 * @param array $networks the supported networks
		 * @param \SV_WC_Payment_Gateway_Apple_Pay $handler the Apple Pay handler
		 */
		return apply_filters( 'sv_wc_apple_pay_supported_networks', array_values( $networks ), $this );
	}


	/**
	 * Gets the gateways that declare Apple Pay support.
	 *
	 * @since 4.7.0
	 *
	 * @return array the supporting gateways as `$gateway_id => \SV_WC_Payment_Gateway`
	 */
	public function get_supporting_gateways() {

		$available_gateways  = $this->get_plugin()->get_gateways();
		$supporting_gateways = array();

		foreach ( $available_gateways as $key => $gateway ) {

			if ( $gateway->supports_apple_pay() ) {
				$supporting_gateways[ $gateway->get_id() ] = $gateway;
			}
		}

		return $supporting_gateways;
	}


	/**
	 * Gets the gateway set to process Apple Pay transactions.
	 *
	 * @since 4.7.0
	 *
	 * @return \SV_WC_Payment_Gateway|null
	 */
	public function get_processing_gateway() {

		$gateways = $this->get_supporting_gateways();

		$gateway_id = get_option( 'sv_wc_apple_pay_payment_gateway' );

		return isset( $gateways[ $gateway_id ] ) ? $gateways[ $gateway_id ] : null;
	}


	/**
	 * Gets the Apple Pay button style.
	 *
	 * @since 4.7.0
	 *
	 * @return string
	 */
	public function get_button_style() {

		return get_option( 'sv_wc_apple_pay_button_style', 'black' );
	}


	/**
	 * Gets the gateway plugin instance.
	 *
	 * @since 4.7.0
	 *
	 * @return \SV_WC_Payment_Gateway_Plugin
	 */
	public function get_plugin() {

		return $this->plugin;
	}


}

endif;
