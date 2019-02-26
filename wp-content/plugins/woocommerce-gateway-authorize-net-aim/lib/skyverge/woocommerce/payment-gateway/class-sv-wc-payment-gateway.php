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
 * @package   SkyVerge/WooCommerce/Payment-Gateway/Classes
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2018, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

if ( ! class_exists( 'SV_WC_Payment_Gateway' ) ) :

/**
 * WooCommerce Payment Gateway Framework
 *
 * @since 1.0.0
 */
abstract class SV_WC_Payment_Gateway extends WC_Payment_Gateway {


	/** Sends through sale and request for funds to be charged to cardholder's credit card. */
	const TRANSACTION_TYPE_CHARGE = 'charge';

	/** Sends through a request for funds to be "reserved" on the cardholder's credit card. A standard authorization is reserved for 2-5 days. Reservation times are determined by cardholder's bank. */
	const TRANSACTION_TYPE_AUTHORIZATION = 'authorization';

	/** The production environment identifier */
	const ENVIRONMENT_PRODUCTION = 'production';

	/** The test environment identifier */
	const ENVIRONMENT_TEST = 'test';

	/** Debug mode log to file */
	const DEBUG_MODE_LOG = 'log';

	/** Debug mode display on checkout */
	const DEBUG_MODE_CHECKOUT = 'checkout';

	/** Debug mode log to file and display on checkout */
	const DEBUG_MODE_BOTH = 'both';

	/** Debug mode disabled */
	const DEBUG_MODE_OFF = 'off';

	/** Gateway which supports direct (XML, REST, SOAP, custom, etc) communication */
	const GATEWAY_TYPE_DIRECT = 'direct';

	/** Gateway which supports redirecting to a gateway server for payment collection, or embedding an iframe on checkout */
	const GATEWAY_TYPE_HOSTED = 'hosted';

	/** Credit card payment type */
	const PAYMENT_TYPE_CREDIT_CARD = 'credit-card';

	/** eCheck payment type */
	const PAYMENT_TYPE_ECHECK = 'echeck';

	/** Gateway with multiple payment options */
	const PAYMENT_TYPE_MULTIPLE = 'multiple';

	/** Bank transfer gateway */
	const PAYMENT_TYPE_BANK_TRANSFER = 'bank_transfer';

	/** Products feature */
	const FEATURE_PRODUCTS = 'products';

	/** Credit card types feature */
	const FEATURE_CARD_TYPES = 'card_types';

	/** Tokenization feature */
	const FEATURE_TOKENIZATION = 'tokenization';

	/** Credit Card charge transaction feature */
	const FEATURE_CREDIT_CARD_CHARGE = 'charge';

	/** Credit Card authorization transaction feature */
	const FEATURE_CREDIT_CARD_AUTHORIZATION = 'authorization';

	/** Credit Card charge virtual-only orders feature */
	const FEATURE_CREDIT_CARD_CHARGE_VIRTUAL = 'charge-virtual';

	/** Credit Card capture charge transaction feature */
	const FEATURE_CREDIT_CARD_CAPTURE = 'capture_charge';

	/** Display detailed customer decline messages on checkout */
	const FEATURE_DETAILED_CUSTOMER_DECLINE_MESSAGES = 'customer_decline_messages';

	/** Refunds feature */
	const FEATURE_REFUNDS = 'refunds';

	/** Voids feature */
	const FEATURE_VOIDS = 'voids';

	/** Payment Form feature */
	const FEATURE_PAYMENT_FORM = 'payment_form';

	/** Customer ID feature */
	const FEATURE_CUSTOMER_ID = 'customer_id';

	/** Apple Pay feature */
	const FEATURE_APPLE_PAY = 'apple_pay';

	/** @var SV_WC_Payment_Gateway_Plugin the parent plugin class */
	private $plugin;

	/** @var string payment type, one of 'credit-card' or 'echeck' */
	private $payment_type;

	/** @var array associative array of environment id to display name, defaults to 'production' => 'Production' */
	private $environments;

	/** @var array associative array of card type to display name */
	private $available_card_types;

	/** @var array optional array of currency codes this gateway is allowed for */
	protected $currencies;

	/** @var string configuration option: the transaction environment, one of $this->environments keys */
	private $environment;

	/** @var string configuration option: the type of transaction, whether purchase or authorization, defaults to 'charge' */
	private $transaction_type;

	/** @var string configuration option: whether transactions should always be charged if the order is virtual-only, defaults to 'no' */
	private $charge_virtual_orders;

	/** @var array configuration option: card types to show images for */
	private $card_types;

	/** @var string configuration option: indicates whether a Card Security Code field will be presented on checkout, either 'yes' or 'no' */
	private $enable_csc;

	/** @var array configuration option: supported echeck fields, one of 'check_number', 'account_type' */
	private $supported_check_fields;

	/** @var string configuration option: indicates whether tokenization is enabled, either 'yes' or 'no' */
	private $tokenization;

	/** @var string configuration option: indicates whether detailed customer decline messages should be displayed at checkout, either 'yes' or 'no' */
	private $enable_customer_decline_messages;

	/** @var string configuration option: 4 options for debug mode - off, checkout, log, both */
	private $debug_mode;

	/** @var string configuration option: whether to use a sibling gateway's connection/authentication settings */
	private $inherit_settings;

	/** @var array of shared setting names, if any.  This can be used for instance when a single plugin supports both credit card and echeck payments, and the same credentials can be used for both gateways */
	private $shared_settings = array();


	/**
	 * Initialize the gateway
	 *
	 * Args:
	 *
	 * + `method_title` - string admin method title, ie 'Intuit QBMS', defaults to 'Settings'
	 * + `method_description` - string admin method description, defaults to ''
	 * + `supports` - array  list of supported gateway features, possible values include:
	 *   'products', 'card_types', 'tokenziation', 'charge', 'authorization', 'subscriptions',
	 *   'subscription_suspension', 'subscription_cancellation', 'subscription_reactivation',
	 *   'subscription_amount_changes', 'subscription_date_changes', 'subscription_payment_method_change',
	 *   'customer_decline_messages'
	 *   Defaults to 'products', 'charge' (credit-card gateways only)
	 * + `payment_type` - string one of 'credit-card' or 'echeck', defaults to 'credit-card'
	 * + `card_types` - array  associative array of card type to display name, used if the payment_type is 'credit-card' and the 'card_types' feature is supported.  Defaults to:
	 *   'VISA' => 'Visa', 'MC' => 'MasterCard', 'AMEX' => 'American Express', 'DISC' => 'Discover', 'DINERS' => 'Diners', 'JCB' => 'JCB'
	 * + `echeck_fields` - array of supported echeck fields, including 'check_number', 'account_type'
	 * + `environments` - associative array of environment id to display name, merged with default of 'production' => 'Production'
	 * + `currencies` -  array of currency codes this gateway is allowed for, defaults to plugin accepted currencies
	 * + `countries` -  array of two-letter country codes this gateway is allowed for, defaults to all
	 * + `shared_settings` - array of shared setting names, if any.  This can be used for instance when a single plugin supports both credit card and echeck payments, and the same credentials can be used for both gateways
	 *
	 * @since 1.0.0
	 * @param string $id the gateway id
	 * @param SV_WC_Payment_Gateway_Plugin $plugin the parent plugin class
	 * @param array $args gateway arguments
	 */
	public function __construct( $id, $plugin, $args ) {

		// first setup the gateway and payment type for this gateway
		$this->payment_type = isset( $args['payment_type'] ) ? $args['payment_type'] : self::PAYMENT_TYPE_CREDIT_CARD;

		// default credit card gateways to supporting 'charge' transaction type, this could be overridden by the 'supports' constructor parameter to include (or only support) authorization
		if ( $this->is_credit_card_gateway() ) {
			$this->add_support( self::FEATURE_CREDIT_CARD_CHARGE );
		}

		// required fields
		$this->id          = $id;  // @see WC_Payment_Gateway::$id

		$this->plugin      = $plugin;
		// kind of sucks, but we need to register back to the plugin because
		//  there's no other way of grabbing existing gateways so as to avoid
		//  double-instantiation errors (esp for shared settings)
		$this->get_plugin()->set_gateway( $id, $this );

		// optional parameters
		if ( isset( $args['method_title'] ) ) {
			$this->method_title = $args['method_title'];        // @see WC_Settings_API::$method_title
		}
		if ( isset( $args['method_description'] ) ) {
			$this->method_description = $args['method_description'];  // @see WC_Settings_API::$method_description
		}
		if ( isset( $args['supports'] ) ) {
			$this->set_supports( $args['supports'] );
		}
		if ( isset( $args['card_types'] ) ) {
			$this->available_card_types = $args['card_types'];
		}
		if ( isset( $args['echeck_fields'] ) ) {
			$this->supported_check_fields = $args['echeck_fields'];
		}
		if ( isset( $args['environments'] ) ) {
			$this->environments = array_merge( $this->get_environments(), $args['environments'] );
		}
		if ( isset( $args['countries'] ) ) {
			$this->countries = $args['countries'];  // @see WC_Payment_Gateway::$countries
		}
		if ( isset( $args['shared_settings'] ) ) {
			$this->shared_settings = $args['shared_settings'];
		}
		if ( isset( $args['currencies'] ) ) {
			$this->currencies = $args['currencies'];
		} else {
			$this->currencies = $this->get_plugin()->get_accepted_currencies();
		}
		if ( isset( $args['order_button_text'] ) ) {
			$this->order_button_text = $args['order_button_text'];
		} else {
			$this->order_button_text = $this->get_order_button_text();
		}

		// always want to render the field area, even for gateways with no fields, so we can display messages  @see WC_Payment_Gateway::$has_fields
		$this->has_fields = true;

		// default icon filter  @see WC_Payment_Gateway::$icon
		$this->icon = apply_filters( 'wc_' . $this->get_id() . '_icon', '' );

		// Load the form fields
		$this->init_form_fields();

		// initialize and load the settings
		$this->init_settings();

		$this->load_settings();

		// pay page fallback
		$this->add_pay_page_handler();

		// filter order received text for held orders
		add_filter( 'woocommerce_thankyou_order_received_text', array( $this, 'maybe_render_held_order_received_text' ), 10, 2 );

		// admin only
		if ( is_admin() ) {

			// save settings
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->get_id(), array( $this, 'process_admin_options' ) );
		}

		// Enqueue the necessary scripts & styles
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// add API request logging
		$this->add_api_request_logging();
	}


	/**
	 * Loads the plugin configuration settings
	 *
	 * @since 1.0.0
	 */
	protected function load_settings() {

		// define user set variables
		foreach ( $this->settings as $setting_key => $setting ) {
			$this->$setting_key = $setting;
		}

		// inherit settings from sibling gateway(s)
		if ( $this->inherit_settings() ) {
			$this->load_shared_settings();
		}
	}


	/**
	 * Loads any shared settings from sibling gateways.
	 *
	 * @since 4.5.0
	 */
	protected function load_shared_settings() {

		// get any other sibling gateways
		$other_gateway_ids = array_diff( $this->get_plugin()->get_gateway_ids(), array( $this->get_id() ) );

		// determine if any sibling gateways have any configured shared settings
		foreach ( $other_gateway_ids as $other_gateway_id ) {

			$other_gateway_settings = $this->get_plugin()->get_gateway_settings( $other_gateway_id );

			// if the other gateway isn't also trying to inherit settings...
			if ( ! isset( $other_gateway_settings['inherit_settings'] ) || 'no' === $other_gateway_settings['inherit_settings'] ) {

				// load the other gateway so we can access the shared settings properly
				$other_gateway = $this->get_plugin()->get_gateway( $other_gateway_id );

				// skip this gateway if it isn't meant to share its settings
				if ( ! $other_gateway->share_settings() ) {
					continue;
				}

				foreach ( $this->shared_settings as $setting_key ) {
					$this->$setting_key = $other_gateway->$setting_key;
				}
			}
		}
	}


	/**
	 * Enqueue the necessary scripts & styles for the gateway, including the
	 * payment form assets (if supported) and any gateway-specific assets.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {

		if ( ! $this->is_available() ) {
			return;
		}

		// payment form assets
		if ( $this->supports_payment_form() ) {

			$this->enqueue_payment_form_assets();
		}

		// gateway-specific assets
		$this->enqueue_gateway_assets();
	}


	/**
	 * Enqueue the payment form JS, CSS, and localized
	 * JS params
	 *
	 * @since 4.3.0
	 */
	protected function enqueue_payment_form_assets() {

		// bail if on my account page and *not* on add payment method page
		if ( is_account_page() && ! is_add_payment_method_page() ) {
			return;
		}

		$handle = 'sv-wc-payment-gateway-payment-form';

		// Frontend JS
		wp_enqueue_script( $handle, $this->get_plugin()->get_payment_gateway_framework_assets_url() . '/js/frontend/' . $handle . '.min.js', array( 'jquery-payment' ), SV_WC_Plugin::VERSION, true );

		// Frontend CSS
		wp_enqueue_style( $handle, $this->get_plugin()->get_payment_gateway_framework_assets_url() . '/css/frontend/' . $handle . '.min.css', array(), SV_WC_Plugin::VERSION );

		// localized JS params
		$this->localize_script( $handle, $this->get_payment_form_js_localized_script_params() );
	}


	/**
	 * Returns an array of JS script params to localize for the
	 * payment form JS. Generally used for i18n purposes.
	 *
	 * @since 4.3.0
	 * @return array associative array of param name to value
	 */
	protected function get_payment_form_js_localized_script_params() {

		/**
		 * Payment Form JS Localized Script Params Filter.
		 *
		 * Allow actors to modify the JS localized script params for the
		 * payment form.
		 *
		 * @since 4.3.0
		 * @param array $params
		 * @return array
		 */
		return apply_filters( 'sv_wc_payment_gateway_payment_form_js_localized_script_params', array(
			'card_number_missing'            => esc_html__( 'Card number is missing', 'woocommerce-plugin-framework' ),
			'card_number_invalid'            => esc_html__( 'Card number is invalid', 'woocommerce-plugin-framework' ),
			'card_number_digits_invalid'     => esc_html__( 'Card number is invalid (only digits allowed)', 'woocommerce-plugin-framework' ),
			'card_number_length_invalid'     => esc_html__( 'Card number is invalid (wrong length)', 'woocommerce-plugin-framework' ),
			'cvv_missing'                    => esc_html__( 'Card security code is missing', 'woocommerce-plugin-framework' ),
			'cvv_digits_invalid'             => esc_html__( 'Card security code is invalid (only digits are allowed)', 'woocommerce-plugin-framework' ),
			'cvv_length_invalid'             => esc_html__( 'Card security code is invalid (must be 3 or 4 digits)', 'woocommerce-plugin-framework' ),
			'card_exp_date_invalid'          => esc_html__( 'Card expiration date is invalid', 'woocommerce-plugin-framework' ),
			'check_number_digits_invalid'    => esc_html__( 'Check Number is invalid (only digits are allowed)', 'woocommerce-plugin-framework' ),
			'check_number_missing'           => esc_html__( 'Check Number is missing', 'woocommerce-plugin-framework' ),
			'drivers_license_state_missing'  => esc_html__( 'Drivers license state is missing', 'woocommerce-plugin-framework' ),
			'drivers_license_number_missing' => esc_html__( 'Drivers license number is missing', 'woocommerce-plugin-framework' ),
			'drivers_license_number_invalid' => esc_html__( 'Drivers license number is invalid', 'woocommerce-plugin-framework' ),
			'account_number_missing'         => esc_html__( 'Account Number is missing', 'woocommerce-plugin-framework' ),
			'account_number_invalid'         => esc_html__( 'Account Number is invalid (only digits are allowed)', 'woocommerce-plugin-framework' ),
			'account_number_length_invalid'  => esc_html__( 'Account number is invalid (must be between 5 and 17 digits)', 'woocommerce-plugin-framework' ),
			'routing_number_missing'         => esc_html__( 'Routing Number is missing', 'woocommerce-plugin-framework' ),
			'routing_number_digits_invalid'  => esc_html__( 'Routing Number is invalid (only digits are allowed)', 'woocommerce-plugin-framework' ),
			'routing_number_length_invalid'  => esc_html__( 'Routing number is invalid (must be 9 digits)', 'woocommerce-plugin-framework' ),
		) );
	}


	/**
	 * Enqueue the gateway-specific assets if present, including JS, CSS, and
	 * localized script params
	 *
	 * @since 4.3.0
	 */
	protected function enqueue_gateway_assets() {

		$handle = $this->get_gateway_js_handle();
		$js_path   = $this->get_plugin()->get_plugin_path() . '/assets/js/frontend/' . $handle . '.min.js';
		$css_path  = $this->get_plugin()->get_plugin_path() . '/assets/css/frontend/' . $handle . '.min.css';

		// JS
		if ( is_readable( $js_path ) ) {

			$js_url = $this->get_plugin()->get_plugin_url() . '/assets/js/frontend/' . $handle . '.min.js';

			/**
			 * Concrete Payment Gateway JS URL
			 *
			 * Allow actors to modify the URL used when loading a concrete
			 * payment gateway's javascript.
			 *
			 * @since 2.0.0
			 * @param string $js_url JS asset URL
			 * @return string
			 */
			$js_url = apply_filters( 'wc_payment_gateway_' . $this->get_plugin()->get_id() . '_javascript_url', $js_url );

			wp_enqueue_script( $handle, $js_url, array(), $this->get_plugin()->get_version(), true );
		}

		// CSS
		if ( is_readable( $css_path ) ) {

			$css_url = $this->get_plugin()->get_plugin_url() . '/assets/css/frontend/' . $handle . '.min.css';

			/**
			 * Concrete Payment Gateway CSS URL
			 *
			 * Allow actors to modify the URL used when loading a concrete payment
			 * gateway's CSS.
			 *
			 * @since 4.3.0
			 * @param string $css_url CSS asset URL
			 * @return string
			 */
			$css_url = apply_filters( 'wc_payment_gateway_' . $this->get_plugin()->get_id() . '_css_url', $css_url );

			wp_enqueue_style( $handle, $css_url, array(), $this->get_plugin()->get_version() );
		}

		// localized JS script params
		if ( $params = $this->get_gateway_js_localized_script_params() ) {

			/**
			 * Payment Gateway Localized JS Script Params Filter.
			 *
			 * Allow actors to modify the localized script params passed to the
			 * frontend for the concrete payment gateway's JS.
			 *
			 * @since 2.2.0
			 * @param $params array
			 * @return array
			 */
			$params = apply_filters( 'wc_gateway_' . $this->get_plugin()->get_id() . '_js_localize_script_params', $this->get_gateway_js_localized_script_params() );

			$this->localize_script( $handle, $params );
		}
	}


	/**
	 * Return the gateway-specifics JS script handle. This is used for:
	 *
	 * + enqueuing the script
	 * + the localized JS script param object name
	 *
	 * Defaults to 'wc-<plugin ID dasherized>'.
	 *
	 * @since 4.3.0
	 * @return string
	 */
	protected function get_gateway_js_handle() {

		return 'wc-' . $this->get_plugin()->get_id_dasherized();
	}


	/**
	 * Returns an array of JS script params to localize for the gateway-specific
	 * JS. Concrete classes must override this as needed.
	 *
	 * @since 4.3.0
	 * @return array
	 */
	protected function get_gateway_js_localized_script_params() {

		// stub method
	}


	/**
	 * Localize a script once. Gateway plugins that have multiple gateways should
	 * only have their params localized once.
	 *
	 * @since 4.3.0
	 * @param string $handle script handle to localize
	 * @param array $params script params to localize
	 */
	protected function localize_script( $handle, $params ) {

		// If the script isn't loaded, bail
		if ( ! wp_script_is( $handle, 'enqueued' ) ) {
			return;
		}

		global $wp_scripts;

		$object_name = str_replace( '-', '_', $handle ) . '_params';

		// If the plugin's JS params already exists in the localized data, bail
		if ( $wp_scripts instanceof WP_Scripts && strpos( $wp_scripts->get_data( $handle, 'data' ), $object_name ) ) {
			return;
		}

		wp_localize_script( $handle, $object_name, $params );
	}


	/**
	 * Returns true if on the pay page and this is the currently selected gateway
	 *
	 * @since 1.0.0
	 * @return mixed true if on pay page and is currently selected gateways, false if on pay page and not the selected gateway, null otherwise
	 */
	public function is_pay_page_gateway() {

		if ( is_checkout_pay_page() ) {

			$order_id  = $this->get_checkout_pay_page_order_id();

			if ( $order_id ) {
				$order = wc_get_order( $order_id );

				return SV_WC_Order_Compatibility::get_prop( $order, 'payment_method' ) === $this->get_id();
			}

		}

		return null;
	}


	/**
	 * Gets the order button text:
	 *
	 * Direct gateway: "Place order"
	 * Redirect/Hosted gateway: "Continue"
	 *
	 * @since 4.0.0
	 */
	protected function get_order_button_text() {

		$text = $this->is_hosted_gateway() ? esc_html__( 'Continue', 'woocommerce-plugin-framework' ) : esc_html__( 'Place order', 'woocommerce-plugin-framework' );

		/**
		 * Payment Gateway Place Order Button Text Filter.
		 *
		 * Allow actors to modify the "place order" button text.
		 *
		 * @since 4.0.0
		 * @param string $text button text
		 * @param \SV_WC_Payment_Gateway $this instance
		 */
		return apply_filters( 'wc_payment_gateway_' . $this->get_id() . '_order_button_text', $text, $this );
	}


	/**
	 * Adds a default simple pay page handler
	 *
	 * @since 1.0.0
	 */
	protected function add_pay_page_handler() {
		add_action( 'woocommerce_receipt_' . $this->get_id(), array( $this, 'payment_page' ) );
	}


	/**
	 * Render a simple payment page
	 *
	 * @since 2.1.0
	 * @param int $order_id identifies the order
	 */
	public function payment_page( $order_id ) {
		echo '<p>' . esc_html__( 'Thank you for your order.', 'woocommerce-plugin-framework' ) . '</p>';
	}


	/** Payment Form Feature **************************************************/


	/**
	 * Returns true if the gateway supports the payment form feature
	 *
	 * @since 4.0.0
	 * @return bool
	 */
	public function supports_payment_form() {

		return $this->supports( self::FEATURE_PAYMENT_FORM );
	}


	/**
	 * Render the payment fields
	 *
	 * @since 4.0.0
	 * @see WC_Payment_Gateway::payment_fields()
	 * @see SV_WC_Payment_Gateway_Payment_Form class
	 */
	public function payment_fields() {

		if ( $this->supports_payment_form() ) {

			$this->get_payment_form_instance()->render();

		} else {

			parent::payment_fields();
		}
	}


	/**
	 * Get the payment form class instance
	 *
	 * @since 4.1.2
	 * @return \SV_WC_Payment_Gateway_Payment_Form
	 */
	public function get_payment_form_instance() {

		return new SV_WC_Payment_Gateway_Payment_Form( $this );
	}


	/**
	 * Get the payment form field defaults, primarily for gateways to override
	 * and set dummy credit card/eCheck info when in the test environment
	 *
	 * @since 4.0.0
	 * @return array
	 */
	public function get_payment_method_defaults() {

		assert( $this->supports_payment_form() );

		$defaults = array(
			'account-number' => '',
			'routing-number' => '',
			'expiry'         => '',
			'csc'            => '',
		);

		if ( $this->is_test_environment() ) {
			$defaults['expiry'] = '01/' . ( date( 'y' ) + 1 );
			$defaults['csc'] = '123';
		}

		return $defaults;
	}


	/** Apple Pay Feature *****************************************************/


	/**
	 * Determines whether this gateway supports Apple Pay.
	 *
	 * @since 4.7.0
	 *
	 * @return bool
	 */
	public function supports_apple_pay() {

		return $this->supports( self::FEATURE_APPLE_PAY );
	}


	/**
	 * Gets the Apple Pay gateway capabilities.
	 *
	 * Gateways should override this if they have more or less capabilities than
	 * the default. See https://developer.apple.com/reference/applepayjs/paymentrequest/1916123-merchantcapabilities
	 * for valid values.
	 *
	 * @since 4.7.0
	 *
	 * @return array
	 */
	public function get_apple_pay_capabilities() {

		return array(
			'supports3DS',
			'supportsCredit',
			'supportsDebit',
		);
	}


	/**
	 * Gets the currencies supported by Apple Pay.
	 *
	 * @since 4.7.0
	 *
	 * @return array
	 */
	public function get_apple_pay_currencies() {

		return array( 'USD' );
	}


	/**
	 * Adds the Apple Pay payment data to the order object.
	 *
	 * Gateways should override this to set the appropriate values depending on
	 * how their processing API needs to handle the data.
	 *
	 * @since 4.7.0
	 *
	 * @param \WC_Order the order object
	 * @param \SV_WC_Payment_Gateway_Apple_Pay_Payment_Response authorized payment response
	 * @return \WC_Order
	 */
	public function get_order_for_apple_pay( WC_Order $order, SV_WC_Payment_Gateway_Apple_Pay_Payment_Response $response ) {

		$order->payment->account_number = $response->get_last_four();
		$order->payment->last_four      = $response->get_last_four();
		$order->payment->card_type      = $response->get_card_type();

		return $order;
	}


	/**
	 * Get the default payment method title, which is configurable within the
	 * admin and displayed on checkout
	 *
	 * @since 2.1.0
	 * @return string payment method title to show on checkout
	 */
	protected function get_default_title() {

		// defaults for credit card and echeck, override for others
		if ( $this->is_credit_card_gateway() ) {
			return esc_html__( 'Credit Card', 'woocommerce-plugin-framework' );
		} elseif ( $this->is_echeck_gateway() ) {
			return esc_html__( 'eCheck', 'woocommerce-plugin-framework' );
		}

		return '';
	}


	/**
	 * Get the default payment method description, which is configurable
	 * within the admin and displayed on checkout
	 *
	 * @since 2.1.0
	 * @return string payment method description to show on checkout
	 */
	protected function get_default_description() {

		// defaults for credit card and echeck, override for others
		if ( $this->is_credit_card_gateway() ) {
			return esc_html__( 'Pay securely using your credit card.', 'woocommerce-plugin-framework' );
		} elseif ( $this->is_echeck_gateway() ) {
			return esc_html__( 'Pay securely using your checking account.', 'woocommerce-plugin-framework' );
		}

		return '';
	}


	/**
	 * Initialize payment gateway settings fields
	 *
	 * @since 1.0.0
	 * @see WC_Settings_API::init_form_fields()
	 */
	public function init_form_fields() {

		// common top form fields
		$this->form_fields = array(

			'enabled' => array(
				'title'   => esc_html__( 'Enable / Disable', 'woocommerce-plugin-framework' ),
				'label'   => esc_html__( 'Enable this gateway', 'woocommerce-plugin-framework' ),
				'type'    => 'checkbox',
				'default' => 'no',
			),

			'title' => array(
				'title'    => esc_html__( 'Title', 'woocommerce-plugin-framework' ),
				'type'     => 'text',
				'desc_tip' => esc_html__( 'Payment method title that the customer will see during checkout.', 'woocommerce-plugin-framework' ),
				'default'  => $this->get_default_title(),
			),

			'description' => array(
				'title'    => esc_html__( 'Description', 'woocommerce-plugin-framework' ),
				'type'     => 'textarea',
				'desc_tip' => esc_html__( 'Payment method description that the customer will see during checkout.', 'woocommerce-plugin-framework' ),
				'default'  => $this->get_default_description(),
			),

		);

		// Card Security Code (CVV) field
		if ( $this->is_credit_card_gateway() ) {
			$this->form_fields = $this->add_csc_form_fields( $this->form_fields );
		}

		// both credit card authorization & charge supported
		if ( $this->supports_credit_card_authorization() && $this->supports_credit_card_charge() ) {
			$this->form_fields = $this->add_authorization_charge_form_fields( $this->form_fields );
		}

		// card types support
		if ( $this->supports_card_types() ) {
			$this->form_fields = $this->add_card_types_form_fields( $this->form_fields );
		}

		// tokenization support
		if ( $this->supports_tokenization() ) {
			$this->form_fields = $this->add_tokenization_form_fields( $this->form_fields );
		}

		// add "detailed customer decline messages" option if the feature is supported
		if ( $this->supports( self::FEATURE_DETAILED_CUSTOMER_DECLINE_MESSAGES ) ) {
			$this->form_fields['enable_customer_decline_messages'] = array(
				'title'   => esc_html__( 'Detailed Decline Messages', 'woocommerce-plugin-framework' ),
				'type'    => 'checkbox',
				'label'   => esc_html__( 'Check to enable detailed decline messages to the customer during checkout when possible, rather than a generic decline message.', 'woocommerce-plugin-framework' ),
				'default' => 'no',
			);
		}

		// debug mode
		$this->form_fields['debug_mode'] = array(
			'title'   => esc_html__( 'Debug Mode', 'woocommerce-plugin-framework' ),
			'type'    => 'select',
			/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
			'desc'    => sprintf( esc_html__( 'Show Detailed Error Messages and API requests/responses on the checkout page and/or save them to the %1$sdebug log%2$s', 'woocommerce-plugin-framework' ), '<a href="' . SV_WC_Helper::get_wc_log_file_url( $this->get_id() ) . '">', '</a>' ),
			'default' => self::DEBUG_MODE_OFF,
			'options' => array(
				self::DEBUG_MODE_OFF      => esc_html__( 'Off', 'woocommerce-plugin-framework' ),
				self::DEBUG_MODE_CHECKOUT => esc_html__( 'Show on Checkout Page', 'woocommerce-plugin-framework' ),
				self::DEBUG_MODE_LOG      => esc_html__( 'Save to Log', 'woocommerce-plugin-framework' ),
				/* translators: show debugging information on both checkout page and in the log */
				self::DEBUG_MODE_BOTH     => esc_html__( 'Both', 'woocommerce-plugin-framework' )
			),
		);

		// if there is more than just the production environment available
		if ( count( $this->get_environments() ) > 1 ) {
			$this->form_fields = $this->add_environment_form_fields( $this->form_fields );
		}

		// add the "inherit settings" toggle if there are settings shared with a sibling gateway
		if ( count( $this->shared_settings ) ) {
			$this->form_fields = $this->add_shared_settings_form_fields( $this->form_fields );
		}

		// add unique method fields added by concrete gateway class
		$gateway_form_fields = $this->get_method_form_fields();
		$this->form_fields = array_merge( $this->form_fields, $gateway_form_fields );

		// add the special 'shared-settings-field' class name to any shared settings fields
		foreach ( $this->shared_settings as $field_name ) {
			$this->form_fields[ $field_name ]['class'] = trim( isset( $this->form_fields[ $field_name ]['class'] ) ? $this->form_fields[ $field_name ]['class'] : '' ) . ' shared-settings-field';
		}

		/**
		 * Payment Gateway Form Fields Filter.
		 *
		 * Actors can use this to add, remove, or tweak gateway form fields
		 *
		 * @since 4.0.0
		 * @param array $form_fields array of form fields in format required by WC_Settings_API
		 * @param \SV_WC_Payment_Gateway $this gateway instance
		 */
		$this->form_fields = apply_filters( 'wc_payment_gateway_' . $this->get_id() . '_form_fields', $this->form_fields, $this );
	}


	/**
	 * Returns an array of form fields specific for this method.
	 *
	 * To add environment-dependent fields, include the 'class' form field argument
	 * with 'environment-field production-field' where "production" matches a
	 * key from the environments member
	 *
	 * @since 1.0.0
	 * @return array of form fields
	 */
	abstract protected function get_method_form_fields();


	/**
	 * Adds the gateway environment form fields
	 *
	 * @since 1.0.0
	 * @param array $form_fields gateway form fields
	 * @return array $form_fields gateway form fields
	 */
	protected function add_environment_form_fields( $form_fields ) {

		$form_fields['environment'] = array(
			/* translators: environment as in a software environment (test/production) */
			'title'    => esc_html__( 'Environment', 'woocommerce-plugin-framework' ),
			'type'     => 'select',
			'default'  => key( $this->get_environments() ),  // default to first defined environment
			'desc_tip' => esc_html__( 'Select the gateway environment to use for transactions.', 'woocommerce-plugin-framework' ),
			'options'  => $this->get_environments(),
		);

		return $form_fields;
	}


	/**
	 * Adds the optional shared settings toggle element.  The 'shared_settings'
	 * optional constructor parameter must have been used in order for shared
	 * settings to be supported.
	 *
	 * @since 1.0.0
	 * @see SV_WC_Payment_Gateway::$shared_settings
	 * @see SV_WC_Payment_Gateway::$inherit_settings
	 * @param array $form_fields gateway form fields
	 * @return array $form_fields gateway form fields
	 */
	protected function add_shared_settings_form_fields( $form_fields ) {

		// get any sibling gateways
		$other_gateway_ids                  = array_diff( $this->get_plugin()->get_gateway_ids(), array( $this->get_id() ) );
		$configured_other_gateway_ids       = array();
		$inherit_settings_other_gateway_ids = array();

		// determine if any sibling gateways have any configured shared settings
		foreach ( $other_gateway_ids as $other_gateway_id ) {

			$other_gateway_settings = $this->get_plugin()->get_gateway_settings( $other_gateway_id );

			// if the other gateway isn't also trying to inherit settings...
			if ( isset( $other_gateway_settings['inherit_settings'] ) && 'yes' == $other_gateway_settings['inherit_settings'] ) {
				$inherit_settings_other_gateway_ids[] = $other_gateway_id;
			}

			foreach ( $this->shared_settings as $setting_name ) {

				// if at least one shared setting is configured in the other gateway
				if ( isset( $other_gateway_settings[ $setting_name ] ) && $other_gateway_settings[ $setting_name ] ) {

					$configured_other_gateway_ids[] = $other_gateway_id;
					break;
				}
			}
		}

		$form_fields['connection_settings'] = array(
			'title' => esc_html__( 'Connection Settings', 'woocommerce-plugin-framework' ),
			'type'  => 'title',
		);

		// disable the field if the sibling gateway is already inheriting settings
		$form_fields['inherit_settings'] = array(
			'title'       => esc_html__( 'Share connection settings', 'woocommerce-plugin-framework' ),
			'type'        => 'checkbox',
			'label'       => esc_html__( 'Use connection/authentication settings from other gateway', 'woocommerce-plugin-framework' ),
			'default'     => count( $configured_other_gateway_ids ) > 0 ? 'yes' : 'no',
			'disabled'    => count( $inherit_settings_other_gateway_ids ) > 0 ? true : false,
			'description' => count( $inherit_settings_other_gateway_ids ) > 0 ? esc_html__( 'Disabled because the other gateway is using these settings', 'woocommerce-plugin-framework' ) : '',
		);

		return $form_fields;
	}


	/**
	 * Adds the enable Card Security Code form fields
	 *
	 * @since 1.0.0
	 * @param array $form_fields gateway form fields
	 * @return array $form_fields gateway form fields
	 */
	protected function add_csc_form_fields( $form_fields ) {

		$form_fields['enable_csc'] = array(
			'title'   => esc_html__( 'Card Verification (CSC)', 'woocommerce-plugin-framework' ),
			'label'   => esc_html__( 'Display the Card Security Code (CV2) field on checkout', 'woocommerce-plugin-framework' ),
			'type'    => 'checkbox',
			'default' => 'yes',
		);

		return $form_fields;
	}


	/**
	 * Display settings page with some additional javascript for hiding conditional fields
	 *
	 * @since 1.0.0
	 * @see WC_Settings_API::admin_options()
	 */
	public function admin_options() {

		parent::admin_options();

		?>
		<style type="text/css">.nowrap { white-space: nowrap; }</style>
		<?php

		// if transaction types are supported, show/hide the "charge virtual-only" setting
		if ( isset( $this->form_fields['transaction_type'] ) ) {

			// add inline javascript
			ob_start();
			?>
				$( '#woocommerce_<?php echo esc_js( $this->get_id() ); ?>_transaction_type' ).change( function() {

					var transaction_type = $( this ).val();
					var hidden_setting   = $( '#woocommerce_<?php echo $this->get_id(); ?>_charge_virtual_orders' ).closest( 'tr' );

					if ( '<?php echo esc_js( self::TRANSACTION_TYPE_AUTHORIZATION ); ?>' === transaction_type ) {
						$( hidden_setting ).show();
					} else {
						$( hidden_setting ).hide();
					}

				} ).change();
			<?php

			wc_enqueue_js( ob_get_clean() );
		}

		// if there's more than one environment include the environment settings switcher code
		if ( count( $this->get_environments() ) > 1 ) {

			// add inline javascript
			ob_start();
			?>
				$( '#woocommerce_<?php echo esc_js( $this->get_id() ); ?>_environment' ).change( function() {

					// inherit settings from other gateway?
					var inheritSettings = $( '#woocommerce_<?php echo $this->get_id(); ?>_inherit_settings' ).is( ':checked' );

					var environment = $( this ).val();

					// hide all environment-dependant fields
					$( '.environment-field' ).closest( 'tr' ).hide();

					// show the currently configured environment fields that are not also being hidden as any shared settings
					var $environmentFields = $( '.' + environment + '-field' );
					if ( inheritSettings ) {
						$environmentFields = $environmentFields.not( '.shared-settings-field' );
					}

					$environmentFields.not( '.hidden' ).closest( 'tr' ).show();

				} ).change();
			<?php

			wc_enqueue_js( ob_get_clean() );

		}

		if ( ! empty( $this->shared_settings ) ) {

			// add inline javascript to show/hide any shared settings fields as needed
			ob_start();
			?>
				$( '#woocommerce_<?php echo $this->get_id(); ?>_inherit_settings' ).change( function() {

					var enabled = $( this ).is( ':checked' );

					if ( enabled ) {
						$( '.shared-settings-field' ).closest( 'tr' ).hide();
					} else {
						// show the fields
						$( '.shared-settings-field' ).closest( 'tr' ).show();

						// hide any that may not be available for the currently selected environment
						$( '#woocommerce_<?php echo $this->get_id(); ?>_environment' ).change();
					}

				} ).change();
			<?php

			wc_enqueue_js( ob_get_clean() );

		}

	}


	/**
	 * Checks for proper gateway configuration including:
	 *
	 * + gateway enabled
	 * + correct configuration (gateway specific)
	 * + any dependencies met
	 * + required currency
	 * + required country
	 *
	 * @since 1.0.0
	 * @see WC_Payment_Gateway::is_available()
	 * @return true if this gateway is available for checkout, false otherwise
	 */
	public function is_available() {

		// is enabled check
		$is_available = parent::is_available();

		// proper configuration
		if ( ! $this->is_configured() ) {
			$is_available = false;
		}

		// all plugin dependencies met
		if ( count( $this->get_plugin()->get_missing_extension_dependencies() ) > 0 ) {
			$is_available = false;
		}

		// any required currencies?
		if ( ! $this->currency_is_accepted() ) {
			$is_available = false;
		}

		// any required countries?
		if ( $this->countries && WC()->customer ) {

			$customer_country = ( SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ) ? WC()->customer->get_billing_country() : WC()->customer->get_country();

			if ( $customer_country && ! in_array( $customer_country, $this->countries, true ) ) {
				$is_available = false;
			}
		}

		/**
		 * Payment Gateway Is Available Filter.
		 *
		 * Allow actors to modify whether the gateway is available or not.
		 *
		 * @since 1.0.0
		 * @param bool $is_available
		 */
		return apply_filters( 'wc_gateway_' . $this->get_id() . '_is_available', $is_available );
	}


	/**
	 * Returns true if the gateway is properly configured to perform transactions
	 *
	 * @since 1.0.0
	 * @see SV_WC_Payment_Gateway::is_configured()
	 * @return boolean true if the gateway is properly configured
	 */
	protected function is_configured() {
		// override this to check for gateway-specific required settings (user names, passwords, secret keys, etc)
		return true;
	}


	/**
	 * Returns the gateway icon markup
	 *
	 * @since 1.0.0
	 * @see WC_Payment_Gateway::get_icon()
	 * @return string icon markup
	 */
	public function get_icon() {

		$icon = '';

		// specific icon
		if ( $this->icon ) {

			// use icon provided by filter
			$icon = sprintf( '<img src="%s" alt="%s" class="sv-wc-payment-gateway-icon wc-%s-payment-gateway-icon" />', esc_url( WC_HTTPS::force_https_url( $this->icon ) ), esc_attr( $this->get_title() ), esc_attr( $this->get_id_dasherized() ) );
		}

		// credit card images
		if ( ! $icon && $this->supports_card_types() && $this->get_card_types() ) {

			// display icons for the selected card types
			foreach ( $this->get_card_types() as $card_type ) {

				$card_type = SV_WC_Payment_Gateway_Helper::normalize_card_type( $card_type );

				if ( $url = $this->get_payment_method_image_url( $card_type ) ) {
					$icon .= sprintf( '<img src="%s" alt="%s" class="sv-wc-payment-gateway-icon wc-%s-payment-gateway-icon" width="40" height="25" style="width: 40px; height: 25px;" />', esc_url( $url ), esc_attr( $card_type ), esc_attr( $this->get_id_dasherized() ) );
				}
			}
		}

		// echeck image
		if ( ! $icon && $this->is_echeck_gateway() ) {

			if ( $url = $this->get_payment_method_image_url( 'echeck' ) ) {
				$icon .= sprintf( '<img src="%s" alt="%s" class="sv-wc-payment-gateway-icon wc-%s-payment-gateway-icon" width="40" height="25" style="width: 40px; height: 25px;" />', esc_url( $url ), esc_attr( 'echeck' ), esc_attr( $this->get_id_dasherized() ) );
			}
		}

		/* This filter is documented in WC core */
		return apply_filters( 'woocommerce_gateway_icon', $icon, $this->get_id() );
	}


	/**
	 * Returns the payment method image URL (if any) for the given $type, ie
	 * if $type is 'amex' a URL to the american express card icon will be
	 * returned.  If $type is 'echeck', a URL to the echeck icon will be
	 * returned.
	 *
	 * @since 1.0.0
	 * @param string $type the payment method cc type or name
	 * @return string the image URL or null
	 */
	public function get_payment_method_image_url( $type ) {

		$image_type = strtolower( $type );

		if ( 'card' === $type ) {
			$image_type = 'cc-plain';
		}

		/**
		 * Payment Gateway Fallback to PNG Filter.
		 *
		 * Allow actors to enable the use of PNGs over SVGs for payment icon images.
		 *
		 * @since 4.0.0
		 * @param bool $use_svg true by default, false to use PNGs
		 */
		$image_extension = apply_filters( 'wc_payment_gateway_' . $this->get_plugin()->get_id() . '_use_svg', true ) ? '.svg' : '.png';

		// first, is the card image available within the plugin?
		if ( is_readable( $this->get_plugin()->get_payment_gateway_framework_assets_path() . '/images/card-' . $image_type . $image_extension ) ) {
			return WC_HTTPS::force_https_url( $this->get_plugin()->get_payment_gateway_framework_assets_url() . '/images/card-' . $image_type . $image_extension );
		}

		// default: is the card image available within the framework?
		if ( is_readable( $this->get_plugin()->get_payment_gateway_framework_assets_path() . '/images/card-' . $image_type . $image_extension ) ) {
			return WC_HTTPS::force_https_url( $this->get_plugin()->get_payment_gateway_framework_assets_url() . '/images/card-' . $image_type . $image_extension );
		}

		return null;
	}


	/**
	 * Add payment and transaction information as class members of WC_Order
	 * instance.  The standard information that can be added includes:
	 *
	 * $order->payment_total           - the payment total
	 * $order->customer_id             - optional payment gateway customer id (useful for tokenized payments, etc)
	 * $order->payment->type           - one of 'credit_card' or 'check'
	 * $order->description             - an order description based on the order
	 * $order->unique_transaction_ref  - a combination of order number + retry count, should provide a unique value for each transaction attempt
	 *
	 * Note that not all gateways will necessarily pass or require all of the
	 * above.  These represent the most common attributes used among a variety
	 * of gateways, it's up to the specific gateway implementation to make use
	 * of, or ignore them, or add custom ones by overridding this method.
	 *
	 * The returned order is expected to be used in a transaction request.
	 *
	 * @since 1.0.0
	 * @param int|\WC_Order $order the order or order ID being processed
	 * @return \WC_Order object with payment and transaction information attached
	 */
	public function get_order( $order ) {

		if ( is_numeric( $order ) ) {
			$order = wc_get_order( $order );
		}

		// set payment total here so it can be modified for later by add-ons like subscriptions which may need to charge an amount different than the get_total()
		$order->payment_total = number_format( $order->get_total(), 2, '.', '' );

		$order->customer_id = '';

		// logged in customer?
		if ( 0 != $order->get_user_id() && false !== ( $customer_id = $this->get_customer_id( $order->get_user_id(), array( 'order' => $order ) ) ) ) {
			$order->customer_id = $customer_id;
		}

		// add payment info
		$order->payment = new stdClass();

		// payment type (credit_card/check/etc)
		$order->payment->type = str_replace( '-', '_', $this->get_payment_type() );

		/* translators: Placeholders: %1$s - site title, %2$s - order number */
		$order->description = sprintf( esc_html__( '%1$s - Order %2$s', 'woocommerce-plugin-framework' ), wp_specialchars_decode( SV_WC_Helper::get_site_name(), ENT_QUOTES ), $order->get_order_number() );

		$order = $this->get_order_with_unique_transaction_ref( $order );

		/**
		 * Filter the base order for a payment transaction
		 *
		 * Actors can use this filter to adjust or add additional information to
		 * the order object that gateways use for processing transactions.
		 *
		 * @since 4.0.0
		 * @param \WC_Order $order order object
		 * @param \SV_WC_Payment_Gateway $this payment gateway instance
		 */
		return apply_filters( 'wc_payment_gateway_' . $this->get_id() . '_get_order_base', $order, $this );
	}


	/** Capture feature *******************************************************/


	/**
	 * Perform a credit card capture for an order.
	 *
	 * @since 4.5.0
	 * @param \WC_Order $order the order object
	 * @return \SV_WC_Payment_Gateway_API_Response|null
	 */
	public function do_credit_card_capture( $order ) {

		$order = $this->get_order_for_capture( $order );

		try {

			$response = $this->get_api()->credit_card_capture( $order );

			if ( $response->transaction_approved() ) {

				$message = sprintf(
					/* translators: Placeholders: %1$s - payment gateway title (such as Authorize.net, Braintree, etc), %2$s - transaction amount. Definitions: Capture, as in capture funds from a credit card. */
					esc_html__( '%1$s Capture of %2$s Approved', 'woocommerce-plugin-framework' ),
					$this->get_method_title(),
					get_woocommerce_currency_symbol() . wc_format_decimal( $order->capture_total )
				);

				// adds the transaction id (if any) to the order note
				if ( $response->get_transaction_id() ) {
					$message .= ' ' . sprintf( esc_html__( '(Transaction ID %s)', 'woocommerce-plugin-framework' ), $response->get_transaction_id() );
				}

				$order->add_order_note( $message );

				// prevent stock from being reduced when payment is completed as this is done when the charge was authorized
				add_filter( 'woocommerce_payment_complete_reduce_order_stock', '__return_false', 100 );

				// complete the order
				$order->payment_complete();

				// add the standard capture data to the order
				$this->add_capture_data( $order, $response );

				// let payment gateway implementations add their own data
				$this->add_payment_gateway_capture_data( $order, $response );

			} else {

				$message = sprintf(
					/* translators: Placeholders: %1$s - payment gateway title (such as Authorize.net, Braintree, etc), %2$s - transaction amount, %3$s - transaction status message. Definitions: Capture, as in capture funds from a credit card. */
					esc_html__( '%1$s Capture Failed: %2$s - %3$s', 'woocommerce-plugin-framework' ),
					$this->get_method_title(),
					$response->get_status_code(),
					$response->get_status_message()
				);

				$order->add_order_note( $message );

			}

			return $response;

		} catch ( SV_WC_Plugin_Exception $e ) {

			$message = sprintf(
				/* translators: Placeholders: %1$s - payment gateway title (such as Authorize.net, Braintree, etc), %2$s - failure message. Definitions: "capture" as in capturing funds from a credit card. */
				esc_html__( '%1$s Capture Failed: %2$s', 'woocommerce-plugin-framework' ),
				$this->get_method_title(),
				$e->getMessage()
			);

			$order->add_order_note( $message );

			return null;
		}
	}


	/**
	 * Gets an order object with payment data added for use in credit card
	 * capture transactions. Standard information can include:
	 *
	 * $order->capture->amount      - amount to capture (partial captures are not supported by the framework yet)
	 * $order->capture->description - capture description
	 * $order->capture->trans_id    - transaction ID for the order being captured
	 *
	 * included for backwards compat (4.1 and earlier)
	 *
	 * $order->capture_total
	 * $order->description
	 *
	 * @since 4.5.0
	 * @param \WC_Order|int $order the order being processed
	 * @return \WC_Order
	 */
	protected function get_order_for_capture( $order ) {

		if ( is_numeric( $order ) ) {
			$order = wc_get_order( $order );
		}

		// add capture info
		$order->capture = new stdClass();
		$order->capture->amount = SV_WC_Helper::number_format( $order->get_total() );
		/* translators: Placeholders: %1$s - site title, %2$s - order number. Definitions: Capture as in capture funds from a credit card. */
		$order->capture->description = sprintf( esc_html__( '%1$s - Capture for Order %2$s', 'woocommerce-plugin-framework' ), wp_specialchars_decode( SV_WC_Helper::get_site_name() ), $order->get_order_number() );
		$order->capture->trans_id = $this->get_order_meta( SV_WC_Order_Compatibility::get_prop( $order, 'id' ), 'trans_id' );

		// backwards compat for 4.1 and earlier
		$order->capture_total = $order->capture->amount;
		$order->description   = $order->capture->description;

		/**
		 * Direct Gateway Capture Get Order Filter.
		 *
		 * Allow actors to modify the order object used for performing charge captures.
		 *
		 * @since 2.0.0
		 * @param \WC_Order $order order object
		 * @param \SV_WC_Payment_Gateway_Direct $this instance
		 */
		return apply_filters( 'wc_payment_gateway_' . $this->get_id() . '_get_order_for_capture', $order, $this );
	}


	/**
	 * Adds the standard capture data to an order.
	 *
	 * @since 4.5.0
	 * @param \WC_Order $order the order object
	 * @param \SV_WC_Payment_Gateway_API_Response $response the transaction response
	 */
	protected function add_capture_data( $order, $response ) {

		// mark the order as captured
		$this->update_order_meta( $order, 'charge_captured', 'yes' );

		// add capture transaction ID
		if ( $response && $response->get_transaction_id() ) {
			$this->update_order_meta( $order, 'capture_trans_id', $response->get_transaction_id() );
		}
	}


	/**
	 * Adds any gateway-specific data to the order after a capture is performed.
	 *
	 * @since 4.5.0
	 * @param \WC_Order $order the order object
	 * @param \SV_WC_Payment_Gateway_API_Response $response the transaction response
	 */
	protected function add_payment_gateway_capture_data( $order, $response ) { }


	/** Refund feature ********************************************************/


	/**
	 * Returns true if this is gateway that supports refunds
	 *
	 * @since 3.1.0
	 * @return boolean true if the gateway supports refunds
	 */
	public function supports_refunds() {

		return $this->supports( self::FEATURE_REFUNDS );
	}


	/**
	 * Process refund
	 *
	 * @since 3.1.0
	 * @param int $order_id order being refunded
	 * @param float $amount refund amount
	 * @param string $reason user-entered reason text for refund
	 * @return bool|WP_Error true on success, or a WP_Error object on failure/error
	 */
	public function process_refund( $order_id, $amount = null, $reason = '' ) {

		// add transaction-specific refund info (amount, reason, transaction IDs, etc)
		$order = $this->get_order_for_refund( $order_id, $amount, $reason );

		// let implementations/actors error out early (e.g. order is missing required data for refund, etc)
		if ( is_wp_error( $order ) ) {
			return $order;
		}

		// if captures are supported and the order has an authorized, but not captured charge, void it instead
		if ( $this->supports_voids() && $this->authorization_valid_for_capture( $order ) ) {
			return $this->process_void( $order );
		}

		try {

			$response = $response = $this->get_api()->refund( $order );

			// allow gateways to void an order in response to a refund attempt
			if ( $this->supports_voids() && $this->maybe_void_instead_of_refund( $order, $response ) ) {
				return $this->process_void( $order );
			}

			if ( $response->transaction_approved() ) {

				// add standard refund-specific transaction data
				$this->add_refund_data( $order, $response );

				// let payment gateway implementations add their own data
				$this->add_payment_gateway_refund_data( $order, $response );

				// add order note
				$this->add_refund_order_note( $order, $response );

				// when full amount is refunded, update status to refunded
				if ( $order->get_total() == $order->get_total_refunded() ) {

					$this->mark_order_as_refunded( $order );
				}

				return true;

			} else {

				$error = $this->get_refund_failed_wp_error( $response->get_status_code(), $response->get_status_message() );

				$order->add_order_note( $error->get_error_message() );

				return $error;
			}

		} catch ( SV_WC_Plugin_Exception $e ) {

			$error = $this->get_refund_failed_wp_error( $e->getCode(), $e->getMessage() );

			$order->add_order_note( $error->get_error_message() );

			return $error;
		}
	}


	/**
	 * Add refund information as class members of WC_Order
	 * instance for use in refund transactions.  Standard information includes:
	 *
	 * $order->refund->amount = refund amount
	 * $order->refund->reason = user-entered reason text for the refund
	 * $order->refund->trans_id = the ID of the original payment transaction for the order
	 *
	 * Payment gateway implementations can override this to add their own
	 * refund-specific data
	 *
	 * @since 3.1.0
	 * @param WC_Order|int $order order being processed
	 * @param float $amount refund amount
	 * @param string $reason optional refund reason text
	 * @return WC_Order object with refund information attached
	 */
	protected function get_order_for_refund( $order, $amount, $reason ) {

		if ( is_numeric( $order ) ) {
			$order = wc_get_order( $order );
		}

		// add refund info
		$order->refund = new stdClass();
		$order->refund->amount = number_format( $amount, 2, '.', '' );

		/* translators: Placeholders: %1$s - site title, %2$s - order number */
		$order->refund->reason = $reason ? $reason : sprintf( esc_html__( '%1$s - Refund for Order %2$s', 'woocommerce-plugin-framework' ), esc_html( SV_WC_Helper::get_site_name() ), $order->get_order_number() );

		// almost all gateways require the original transaction ID, so include it by default
		$order->refund->trans_id = $this->get_order_meta( SV_WC_Order_Compatibility::get_prop( $order, 'id' ), 'trans_id' );

		/**
		 * Payment Gateway Get Order For Refund Filter.
		 *
		 * Allow actors to modify the order object used for refund transactions.
		 *
		 * @since 3.1.0
		 * @param \WC_Order $order order object
		 * @param \SV_WC_Payment_Gateway $this instance
		 */
		return apply_filters( 'wc_payment_gateway_' . $this->get_id() . '_get_order_for_refund', $order, $this );
	}


	/**
	 * Adds the standard refund transaction data to the order
	 *
	 * Note that refunds can be performed multiple times for a single order so
	 * transaction IDs keys are not unique
	 *
	 * @since 3.1.0
	 * @param WC_Order $order the order object
	 * @param SV_WC_Payment_Gateway_API_Response $response transaction response
	 */
	protected function add_refund_data( WC_Order $order, $response ) {

		// indicate the order was refunded along with the refund amount
		$this->add_order_meta( $order, 'refund_amount', $order->refund->amount );

		// add refund transaction ID
		if ( $response && $response->get_transaction_id() ) {
			$this->add_order_meta( $order, 'refund_trans_id', $response->get_transaction_id() );
		}
	}


	/**
	 * Adds any gateway-specific data to the order after a refund is performed
	 *
	 * @since 3.1.0
	 * @param WC_Order $order the order object
	 * @param SV_WC_Payment_Gateway_API_Response $response the transaction response
	 */
	protected function add_payment_gateway_refund_data( WC_Order $order, $response ) {
		// Optional method
	}


	/**
	 * Adds an order note with the amount and (optional) refund transaction ID
	 *
	 * @since 3.1.0
	 * @param WC_Order $order order object
	 * @param SV_WC_Payment_Gateway_API_Response $response transaction response
	 */
	protected function add_refund_order_note( WC_Order $order, $response ) {

		$message = sprintf(
			/* translators: Placeholders: %1$s - payment gateway title (such as Authorize.net, Braintree, etc), %2$s - a monetary amount */
			esc_html__( '%1$s Refund in the amount of %2$s approved.', 'woocommerce-plugin-framework' ),
			$this->get_method_title(),
			wc_price( $order->refund->amount, array( 'currency' => SV_WC_Order_Compatibility::get_prop( $order, 'currency', 'view' ) ) )
		);

		// adds the transaction id (if any) to the order note
		if ( $response->get_transaction_id() ) {
			$message .= ' ' . sprintf( esc_html__( '(Transaction ID %s)', 'woocommerce-plugin-framework' ), $response->get_transaction_id() );
		}

		$order->add_order_note( $message );
	}


	/**
	 * Build the WP_Error object for a failed refund
	 *
	 * @since 3.1.0
	 * @param int|string $error_code error code
	 * @param string $error_message error message
	 * @return WP_Error suitable for returning from the process_refund() method
	 */
	protected function get_refund_failed_wp_error( $error_code, $error_message ) {

		if ( $error_code ) {
			$message = sprintf(
				/* translators: Placeholders: %1$s - payment gateway title (such as Authorize.net, Braintree, etc), %2$s - error code, %3$s - error message */
				esc_html__( '%1$s Refund Failed: %2$s - %3$s', 'woocommerce-plugin-framework' ),
				$this->get_method_title(),
				$error_code,
				$error_message
			);
		} else {
			$message = sprintf(
				/* translators: Placeholders: %1$s - payment gateway title (such as Authorize.net, Braintree, etc), %2$s - error message */
				esc_html__( '%1$s Refund Failed: %2$s', 'woocommerce-plugin-framework' ),
				$this->get_method_title(),
				$error_message
			);
		}

		return new WP_Error( 'wc_' . $this->get_id() . '_refund_failed', $message );
	}


	/**
	 * Mark an order as refunded. This should only be used when the full order
	 * amount has been refunded.
	 *
	 * @since 3.1.0
	 * @param WC_Order $order order object
	 */
	public function mark_order_as_refunded( $order ) {

		/* translators: Placeholders: %s - payment gateway title (such as Authorize.net, Braintree, etc) */
		$order_note = sprintf( esc_html__( '%s Order completely refunded.', 'woocommerce-plugin-framework' ), $this->get_method_title() );

		// Mark order as refunded if not already set
		if ( ! $order->has_status( 'refunded' ) ) {
			$order->update_status( 'refunded', $order_note );
		} else {
			$order->add_order_note( $order_note );
		}
	}


	/** Void feature ********************************************************/


	/**
	 * Returns true if this is gateway that supports voids
	 *
	 * @since 3.1.0
	 * @return boolean true if the gateway supports voids
	 */
	public function supports_voids() {

		return $this->supports( self::FEATURE_VOIDS ) && $this->supports_credit_card_capture();
	}


	/**
	 * Allow gateways to void an order that was attempted to be refunded. This is
	 * particularly useful for gateways that can void an authorized & captured
	 * charge that has not yet settled (e.g. Authorize.net AIM/CIM)
	 *
	 * @since 4.0.0
	 * @param \WC_Order $order order
	 * @param \SV_WC_Payment_Gateway_API_Response $response refund response
	 * @return boolean true if a void should be performed for the given order/response
	 */
	protected function maybe_void_instead_of_refund( $order, $response ) {

		return false;
	}


	/**
	 * Process a void
	 *
	 * @since 3.1.0
	 * @param WC_Order $order order object (with refund class member already added)
	 * @return bool|WP_Error true on success, or a WP_Error object on failure/error
	 */
	protected function process_void( WC_Order $order ) {

		// partial voids are not supported
		if ( $order->refund->amount != $order->get_total() ) {
			return new WP_Error( 'wc_' . $this->get_id() . '_void_error', esc_html__( 'Oops, you cannot partially void this order. Please use the full order amount.', 'woocommerce-plugin-framework' ), 500 );
		}

		try {

			$response = $this->get_api()->void( $order );

			if ( $response->transaction_approved() ) {

				// add standard void-specific transaction data
				$this->add_void_data( $order, $response );

				// let payment gateway implementations add their own data
				$this->add_payment_gateway_void_data( $order, $response );

				// update order status to "refunded" and add an order note
				$this->mark_order_as_voided( $order, $response );

				return true;

			} else {

				$error = $this->get_void_failed_wp_error( $response->get_status_code(), $response->get_status_message() );

				$order->add_order_note( $error->get_error_message() );

				return $error;
			}

		} catch ( SV_WC_Plugin_Exception $e ) {

			$error = $this->get_void_failed_wp_error( $e->getCode(), $e->getMessage() );

			$order->add_order_note( $error->get_error_message() );

			return $error;
		}
	}


	/**
	 * Adds the standard void transaction data to the order
	 *
	 * @since 3.1.0
	 * @param WC_Order $order the order object
	 * @param SV_WC_Payment_Gateway_API_Response $response transaction response
	 */
	protected function add_void_data( WC_Order $order, $response ) {

		// indicate the order was voided along with the amount
		$this->update_order_meta( $order, 'void_amount', $order->refund->amount );

		// add refund transaction ID
		if ( $response && $response->get_transaction_id() ) {
			$this->add_order_meta( $order, 'void_trans_id', $response->get_transaction_id() );
		}
	}


	/**
	 * Adds any gateway-specific data to the order after a void is performed
	 *
	 * @since 3.1.0
	 * @param WC_Order $order the order object
	 * @param SV_WC_Payment_Gateway_API_Response $response the transaction response
	 */
	protected function add_payment_gateway_void_data( WC_Order $order, $response ) {
		// Optional method
	}


	/**
	 * Build the WP_Error object for a failed void
	 *
	 * @since 3.1.0
	 * @param int|string $error_code error code
	 * @param string $error_message error message
	 * @return WP_Error suitable for returning from the process_refund() method
	 */
	protected function get_void_failed_wp_error( $error_code, $error_message ) {

		if ( $error_code ) {
			$message = sprintf(
				/* translators: Placeholders: %1$s - payment gateway title, %2$s - error code, %3$s - error message. Void as in to void an order. */
				esc_html__( '%1$s Void Failed: %2$s - %3$s', 'woocommerce-plugin-framework' ),
				$this->get_method_title(),
				$error_code,
				$error_message
			);
		} else {
			$message = sprintf(
				/* translators: Placeholders: %1$s - payment gateway title, %2$s - error message. Void as in to void an order. */
				esc_html__( '%1$s Void Failed: %2$s', 'woocommerce-plugin-framework' ),
				$this->get_method_title(),
				$error_message
			);
		}

		return new WP_Error( 'wc_' . $this->get_id() . '_void_failed', $message );
	}


	/**
	 * Mark an order as voided. Because WC has no status for "void", we use
	 * refunded.
	 *
	 * @since 3.1.0
	 * @param WC_Order $order order object
	 */
	public function mark_order_as_voided( $order, $response ) {

		$message = sprintf(
			/* translators: Placeholders: %1$s - payment gateway title, %2$s - a monetary amount. Void as in to void an order. */
			esc_html__( '%1$s Void in the amount of %2$s approved.', 'woocommerce-plugin-framework' ),
			$this->get_method_title(),
			wc_price( $order->refund->amount, array( 'currency' => SV_WC_Order_Compatibility::get_prop( $order, 'currency', 'view' ) ) )
		);

		// adds the transaction id (if any) to the order note
		if ( $response->get_transaction_id() ) {
			$message .= ' ' . sprintf( esc_html__( '(Transaction ID %s)', 'woocommerce-plugin-framework' ), $response->get_transaction_id() );
		}

		// mark order as cancelled, since no money was actually transferred
		if ( ! $order->has_status( 'cancelled' ) ) {

			$this->voided_order_message = $message;

			add_filter( 'woocommerce_order_fully_refunded_status', array( $this, 'maybe_cancel_voided_order' ), 10, 2 );

		} else {

			$order->add_order_note( $message );
		}
	}


	/**
	 * Maybe change the order status for a voided order to cancelled
	 *
	 * @hooked woocommerce_order_fully_refunded_status filter
	 *
	 * @see SV_WC_Payment_Gateway::mark_order_as_voided()
	 * @since 4.0.0
	 * @param string $order_status default order status for fully refunded orders
	 * @param int $order_id order ID
	 * @return string 'cancelled'
	 */
	public function maybe_cancel_voided_order( $order_status, $order_id ) {

		if ( empty( $this->voided_order_message ) ) {
			return $order_status;
		}

		$order = wc_get_order( $order_id );

		// no way to set the order note with the status change
		$order->add_order_note( $this->voided_order_message );

		return 'cancelled';
	}


	/**
	 * Returns the $order object with a unique transaction ref member added
	 *
	 * @since 2.2.0
	 * @param WC_Order $order the order object
	 * @return WC_Order order object with member named unique_transaction_ref
	 */
	protected function get_order_with_unique_transaction_ref( $order ) {

		$order_id = SV_WC_Order_Compatibility::get_prop( $order, 'id' );

		// generate a unique retry count
		if ( is_numeric( $this->get_order_meta( $order_id, 'retry_count' ) ) ) {
			$retry_count = $this->get_order_meta( $order_id, 'retry_count' );

			$retry_count++;
		} else {
			$retry_count = 0;
		}

		// keep track of the retry count
		$this->update_order_meta( $order, 'retry_count', $retry_count );

		// generate a unique transaction ref based on the order number and retry count, for gateways that require a unique identifier for every transaction request
		$order->unique_transaction_ref = ltrim( $order->get_order_number(),  esc_html_x( '#', 'hash before order number', 'woocommerce-plugin-framework' ) ) . ( $retry_count > 0 ? '-' . $retry_count : '' );

		return $order;
	}


	/**
	 * Called after an unsuccessful transaction attempt
	 *
	 * @since 1.0.0
	 * @param WC_Order $order the order
	 * @param SV_WC_Payment_Gateway_API_Response $response the transaction response
	 * @return boolean false
	 */
	protected function do_transaction_failed_result( WC_Order $order, SV_WC_Payment_Gateway_API_Response $response ) {

		$order_note = '';

		// build the order note with what data we have
		if ( $response->get_status_code() && $response->get_status_message() ) {
			/* translators: Placeholders: %1$s - status code, %2$s - status message */
			$order_note = sprintf( esc_html__( 'Status code %1$s: %2$s', 'woocommerce-plugin-framework' ), $response->get_status_code(), $response->get_status_message() );
		} elseif ( $response->get_status_code() ) {
			/* translators: Placeholders: %s - status code */
			$order_note = sprintf( esc_html__( 'Status code: %s', 'woocommerce-plugin-framework' ), $response->get_status_code() );
		} elseif ( $response->get_status_message() ) {
			/* translators: Placeholders; %s - status message */
			$order_note = sprintf( esc_html__( 'Status message: %s', 'woocommerce-plugin-framework' ), $response->get_status_message() );
		}

		// add transaction id if there is one
		if ( $response->get_transaction_id() ) {
			$order_note .= ' ' . sprintf( esc_html__( 'Transaction ID %s', 'woocommerce-plugin-framework' ), $response->get_transaction_id() );
		}

		$this->mark_order_as_failed( $order, $order_note, $response );

		return false;
	}


	/**
	 * Adds the standard transaction data to the order
	 *
	 * @since 1.0.0
	 * @param WC_Order $order the order object
	 * @param SV_WC_Payment_Gateway_API_Response|null $response optional transaction response
	 */
	public function add_transaction_data( $order, $response = null ) {

		// transaction id if available
		if ( $response && $response->get_transaction_id() ) {

			$this->update_order_meta( $order, 'trans_id', $response->get_transaction_id() );

			update_post_meta( SV_WC_Order_Compatibility::get_prop( $order, 'id' ), '_transaction_id', $response->get_transaction_id() );
		}

		// transaction date
		$this->update_order_meta( $order, 'trans_date', current_time( 'mysql' ) );

		// if there's more than one environment
		if ( count( $this->get_environments() ) > 1 ) {
			$this->update_order_meta( $order, 'environment', $this->get_environment() );
		}

		// customer data
		if ( $this->supports_customer_id() ) {
			$this->add_customer_data( $order, $response );
		}

		/**
		 * Payment Gateway Add Transaction Data Action.
		 *
		 * Fired after a transaction is processed and provides actors a way to add additional
		 * transactional data to an order given the transaction response object.
		 *
		 * @since 4.1.0
		 * @param \WC_Order $order order object
		 * @param \SV_WC_Payment_Gateway_API_Response|null $response transaction response
		 * @param \SV_WC_Payment_Gateway $this instance
		 */
		do_action( 'wc_payment_gateway_' . $this->get_id() . '_add_transaction_data', $order, $response, $this );
	}


	/**
	 * Adds any gateway-specific transaction data to the order
	 *
	 * @since 1.0.0
	 * @param WC_Order $order the order object
	 * @param \SV_WC_Payment_Gateway_API_Customer_Response $response the transaction response
	 */
	public function add_payment_gateway_transaction_data( $order, $response ) {
		// Optional method
	}


	/**
	 * Add customer data to an order/user if the gateway supports the customer ID
	 * response
	 *
	 * @since 4.0.0
	 * @param \WC_Order $order order
	 * @param \SV_WC_Payment_Gateway_API_Customer_Response $response
	 */
	protected function add_customer_data( $order, $response = null ) {

		$user_id = $order->get_user_id();

		if ( $response && method_exists( $response, 'get_customer_id' ) && $response->get_customer_id() ) {

			$order->customer_id = $customer_id = $response->get_customer_id();

		} else {

			// default to the customer ID set on the order
			$customer_id = $order->customer_id;
		}

		// update the order with the customer ID, note environment is not appended here because it's already available
		// on the `environment` order meta
		$this->update_order_meta( $order, 'customer_id', $customer_id );

		// update the user
		if ( 0 != $user_id ) {
			$this->update_customer_id( $user_id, $customer_id );
		}
	}


	/**
	 * Mark the given order as 'on-hold', set an order note and display a message
	 * to the customer
	 *
	 * @since 1.0.0
	 * @param WC_Order $order the order
	 * @param string $message a message to display within the order note
	 * @param SV_WC_Payment_Gateway_API_Response $response optional, the transaction response object
	 */
	public function mark_order_as_held( $order, $message, $response = null ) {

		/* translators: Placeholders: %1$s - payment gateway title, %2$s - message (probably reason for the transaction being held for review) */
		$order_note = sprintf( esc_html__( '%1$s Transaction Held for Review (%2$s)', 'woocommerce-plugin-framework' ), $this->get_method_title(), $message );

		/**
		 * Held Order Status Filter.
		 *
		 * Actors may use this to change the order status that is used when an order
		 * status should be marked as held. Held orders are usually a result of an
		 * authorize-only transaction.
		 *
		 * @since 4.0.1
		 * @param string $order_status 'on-hold' by default
		 * @param \WC_Order $order WC order
		 * @param \SV_WC_Payment_Gateway_API_Response $response instance
		 * @param \SV_WC_Payment_Gateway $this gateway instance
		 */
		$order_status = apply_filters( 'wc_payment_gateway_' . $this->get_id() . '_held_order_status', 'on-hold', $order, $response, $this );

		// mark order as held
		if ( ! $order->has_status( $order_status ) ) {
			$order->update_status( $order_status, $order_note );
		} else {
			$order->add_order_note( $order_note );
		}

		// user message
		$user_message = '';
		if ( $response && $this->is_detailed_customer_decline_messages_enabled() ) {
			$user_message = $response->get_user_message();
		}

		if ( ! $user_message || ( $this->supports_credit_card_authorization() && $this->perform_credit_card_authorization( $order ) ) ) {
			$user_message = esc_html__( 'Your order has been received and is being reviewed. Thank you for your business.', 'woocommerce-plugin-framework' );
		}

		if ( isset( WC()->session ) ) {
			WC()->session->held_order_received_text = $user_message;
		}
	}


	/**
	 * Maybe render custom order received text on the thank you page when
	 * an order is held
	 *
	 * If detailed customer decline messages are enabled, this message may
	 * additionally include more detailed information.
	 *
	 * @since 4.0.0
	 * @param string $text order received text
	 * @param WC_Order|null $order order object
	 * @return string
	 */
	public function maybe_render_held_order_received_text( $text, $order ) {

		if ( $order && $order->has_status( 'on-hold' ) && isset( WC()->session->held_order_received_text ) ) {

			$text = WC()->session->held_order_received_text;

			unset( WC()->session->held_order_received_text );
		}

		return $text;
	}


	/**
	 * Mark the given order as failed and set the order note
	 *
	 * @since 1.0.0
	 * @param WC_Order $order the order
	 * @param string $error_message a message to display inside the "Payment Failed" order note
	 * @param SV_WC_Payment_Gateway_API_Response optional $response the transaction response object
	 */
	public function mark_order_as_failed( $order, $error_message, $response = null ) {

		/* translators: Placeholders: %1$s - payment gateway title, %2$s - error message; e.g. Order Note: [Payment method] Payment failed [error] */
		$order_note = sprintf( esc_html__( '%1$s Payment Failed (%2$s)', 'woocommerce-plugin-framework' ), $this->get_method_title(), $error_message );

		// Mark order as failed if not already set, otherwise, make sure we add the order note so we can detect when someone fails to check out multiple times
		if ( ! $order->has_status( 'failed' ) ) {
			$order->update_status( 'failed', $order_note );
		} else {
			$order->add_order_note( $order_note );
		}

		$this->add_debug_message( $error_message, 'error' );

		// user message
		$user_message = '';
		if ( $response && $this->is_detailed_customer_decline_messages_enabled() ) {
			$user_message = $response->get_user_message();
		}
		if ( ! $user_message ) {
			$user_message = esc_html__( 'An error occurred, please try again or try an alternate form of payment.', 'woocommerce-plugin-framework' );
		}
		SV_WC_Helper::wc_add_notice( $user_message, 'error' );
	}


	/**
	 * Mark the given order as cancelled and set the order note
	 *
	 * @since 2.1.0
	 * @param WC_Order $order the order
	 * @param string $error_message a message to display inside the "Payment Cancelled" order note
	 * @param SV_WC_Payment_Gateway_API_Response optional $response the transaction response object
	 */
	public function mark_order_as_cancelled( $order, $message, $response = null ) {

		/* translators: Placeholders: %1$s - payment gateway title, %2$s - message/error */
		$order_note = sprintf( esc_html__( '%1$s Transaction Cancelled (%2$s)', 'woocommerce-plugin-framework' ), $this->get_method_title(), $message );

		// Mark order as cancelled if not already set
		if ( ! $order->has_status( 'cancelled' ) ) {
			$order->update_status( 'cancelled', $order_note );
		} else {
			$order->add_order_note( $order_note );
		}

		$this->add_debug_message( $message, 'error' );
	}


	/** Customer ID Feature  **************************************************/


	/**
	 * Returns true if this is gateway that supports gateway customer IDs
	 *
	 * @since 4.0.0
	 * @return boolean true if the gateway supports gateway customer IDs
	 */
	public function supports_customer_id() {

		return $this->supports( self::FEATURE_CUSTOMER_ID );
	}


	/**
	 * Gets/sets the payment gateway customer id, this defaults to wc-{user id}
	 * and retrieves/stores to the user meta named by get_customer_id_user_meta_name()
	 * This can be overridden for gateways that use some other value, or made to
	 * return false for gateways that don't support a customer id.
	 *
	 * @since 1.0.0
	 * @see SV_WC_Payment_Gateway::get_customer_id_user_meta_name()
	 * @param int $user_id wordpress user identifier
	 * @param array $args optional additional arguments which can include: environment_id, autocreate (true/false), and order
	 * @return string payment gateway customer id
	 */
	public function get_customer_id( $user_id, $args = array() ) {

		$defaults = array(
			'environment_id' => $this->get_environment(),
			'autocreate'     => true,
			'order'          => null,
		);

		$args = array_merge( $defaults, $args );

		// does an id already exist for this user?
		$customer_id = get_user_meta( $user_id, $this->get_customer_id_user_meta_name( $args['environment_id'] ), true );

		if ( ! $customer_id && $args['autocreate'] ) {

			$billing_email = ( $args['order'] ) ? SV_WC_Order_Compatibility::get_prop( $args['order'], 'billing_email' ) : '';

			// generate a new customer id.  We try to use 'wc-<hash of billing email>'
			//  if an order is available, on the theory that it will avoid clashing of
			//  accounts if a customer uses the same merchant account on multiple independent
			//  shops.  Otherwise, we use 'wc-<user_id>-<random>'
			if ( $billing_email ) {
				$customer_id = 'wc-' . md5( $billing_email );
			} else {
				$customer_id = uniqid( 'wc-' . $user_id . '-' );
			}

			$this->update_customer_id( $user_id, $customer_id, $args['environment_id'] );
		}

		return $customer_id;
	}


	/**
	 * Updates the payment gateway customer id for the given $environment, or
	 * for the plugin current environment
	 *
	 * @since 1.0.0
	 * @see SV_WC_Payment_Gateway::get_customer_id()
	 * @param int $user_id WP user ID
	 * @param string $customer_id payment gateway customer id
	 * @param string $environment_id optional environment id, defaults to current environment
	 * @return boolean|int false if no change was made (if the new value was the same as previous value) or if the update failed, meta id if the value was different and the update a success
	 */
	public function update_customer_id( $user_id, $customer_id, $environment_id = null ) {

		// default to current environment
		if ( is_null( $environment_id ) ) {
			$environment_id = $this->get_environment();
		}

		return update_user_meta( $user_id, $this->get_customer_id_user_meta_name( $environment_id ), $customer_id );
	}


	/**
	 * Removes the payment gateway customer id for the given $environment, or
	 * for the plugin current environment
	 *
	 * @since 4.0.0
	 * @param int $user_id WP user ID
	 * @param string $environment_id optional environment id, defaults to current environment
	 * @return boolean true on success, false on failure
	 */
	public function remove_customer_id( $user_id, $environment_id = null ){

		if ( is_null( $environment_id ) ) {
			$environment_id = $this->get_environment();
		}

		// remove the user meta entry so it can be re-created
		return delete_user_meta( $user_id, $this->get_customer_id_user_meta_name( $environment_id ) );
	}


	/**
	 * Returns a payment gateway customer id for a guest customer.  This
	 * defaults to wc-guest-{order id} but can be overridden for gateways that
	 * use some other value, or made to return false for gateways that don't
	 * support a customer id
	 *
	 * @since 1.0.0
	 * @param WC_Order $order order object
	 * @return string payment gateway guest customer id
	 */
	public function get_guest_customer_id( WC_Order $order ) {

		// is there a customer id already tied to this order?
		$customer_id = $this->get_order_meta( SV_WC_Order_Compatibility::get_prop( $order, 'id' ), 'customer_id' );

		if ( $customer_id ) {
			return $customer_id;
		}

		// default
		return 'wc-guest-' . SV_WC_Order_Compatibility::get_prop( $order, 'id' );
	}


	/**
	 * Returns the payment gateway customer id user meta name for persisting the
	 * gateway customer id.  Defaults to wc_{plugin id}_customer_id for the
	 * production environment and wc_{plugin id}_customer_id_{environment}
	 * for other environments.  A particular environment can be passed,
	 * otherwise this will default to the plugin current environment.
	 *
	 * This can be overridden and made to return false for gateways that don't
	 * support a customer id.
	 *
	 * NOTE: the plugin id, rather than gateway id, is used by default to create
	 * the meta key for this setting, because it's assumed that in the case of a
	 * plugin having multiple gateways (ie credit card and eCheck) the customer
	 * id will be the same between them.
	 *
	 * @since 1.0.0
	 * @param string $environment_id optional environment id, defaults to plugin current environment
	 * @return string payment gateway customer id user meta name
	 */
	public function get_customer_id_user_meta_name( $environment_id = null ) {

		if ( is_null( $environment_id ) ) {
			$environment_id = $this->get_environment();
		}

		// no leading underscore since this is meant to be visible to the admin
		return 'wc_' . $this->get_plugin()->get_id() . '_customer_id' . ( ! $this->is_production_environment( $environment_id ) ? '_' . $environment_id : '' );
	}


	/** Authorization/Charge feature ******************************************/


	/**
	 * Returns true if this is a credit card gateway which supports
	 * authorization transactions
	 *
	 * @since 1.0.0
	 * @return boolean true if the gateway supports authorization
	 */
	public function supports_credit_card_authorization() {
		return $this->is_credit_card_gateway() && $this->supports( self::FEATURE_CREDIT_CARD_AUTHORIZATION );
	}


	/**
	 * Returns true if this is a credit card gateway which supports
	 * charge transactions
	 *
	 * @since 1.0.0
	 * @return boolean true if the gateway supports charges
	 */
	public function supports_credit_card_charge() {
		return $this->is_credit_card_gateway() && $this->supports( self::FEATURE_CREDIT_CARD_CHARGE );
	}


	/**
	 * Determines if this is a credit card gateway that supports charging virtual-only orders.
	 *
	 * @since 4.5.0
	 * @return bool
	 */
	public function supports_credit_card_charge_virtual() {
		return $this->is_credit_card_gateway() && $this->supports( self::FEATURE_CREDIT_CARD_CHARGE_VIRTUAL );
	}


	/**
	 * Returns true if the gateway supports capturing a charge
	 *
	 * @since 3.1.0
	 * @return boolean true if the gateway supports capturing a charge
	 */
	public function supports_credit_card_capture() {
		return $this->supports( self::FEATURE_CREDIT_CARD_CAPTURE );
	}


	/**
	 * Adds any credit card authorization/charge admin fields, allowing the
	 * administrator to choose between performing authorizations or charges
	 *
	 * @since 1.0.0
	 * @param array $form_fields gateway form fields
	 * @return array $form_fields gateway form fields
	 */
	protected function add_authorization_charge_form_fields( $form_fields ) {

		assert( $this->supports_credit_card_authorization() && $this->supports_credit_card_charge() );

		$form_fields['transaction_type'] = array(
			'title'    => esc_html__( 'Transaction Type', 'woocommerce-plugin-framework' ),
			'type'     => 'select',
			'desc_tip' => esc_html__( 'Select how transactions should be processed. Charge submits all transactions for settlement, Authorization simply authorizes the order total for capture later.', 'woocommerce-plugin-framework' ),
			'default'  => self::TRANSACTION_TYPE_CHARGE,
			'options'  => array(
				self::TRANSACTION_TYPE_CHARGE        => esc_html_x( 'Charge',  'noun, credit card transaction type', 'woocommerce-plugin-framework' ),
				self::TRANSACTION_TYPE_AUTHORIZATION => esc_html_x( 'Authorization', 'credit card transaction type', 'woocommerce-plugin-framework' ),
			),
		);

		if ( $this->supports_credit_card_charge_virtual() ) {

			$form_fields['charge_virtual_orders'] = array(
				'label'       => esc_html__( 'Charge Virtual-Only Orders', 'woocommerce-plugin-framework' ),
				'type'        => 'checkbox',
				'description' => esc_html__( 'If the order contains exclusively virtual items, enable this to immediately charge, rather than authorize, the transaction.', 'woocommerce-plugin-framework' ),
				'default'     => 'no',
			);
		}

		return $form_fields;
	}


	/**
	 * Returns true if the authorization for $order is still valid for capture
	 *
	 * @since 2.0.0
	 * @param WC_Order $order the order
	 * @return boolean true if the authorization is valid for capture, false otherwise
	 */
	public function authorization_valid_for_capture( $order ) {

		$order_id = SV_WC_Order_Compatibility::get_prop( $order, 'id' );

		// check whether the charge has already been captured by this gateway
		$charge_captured = $this->get_order_meta( $order_id, 'charge_captured' );

		if ( 'yes' == $charge_captured ) {
			return false;
		}

		// if for any reason the authorization can not be captured
		$auth_can_be_captured = $this->get_order_meta( $order_id, 'auth_can_be_captured' );

		if ( 'no' == $auth_can_be_captured ) {
			return false;
		}

		// authorization hasn't already been captured, but has it expired?
		return ! $this->has_authorization_expired( $order );
	}


	/**
	 * Returns true if the authorization for $order has expired
	 *
	 * @since 2.0.0
	 * @param WC_Order $order the order
	 * @return boolean true if the authorization has expired, false otherwise
	 */
	public function has_authorization_expired( $order ) {

		$transaction_time = strtotime( $this->get_order_meta( SV_WC_Order_Compatibility::get_prop( $order, 'id' ), 'trans_date' ) );

		return floor( ( time() - $transaction_time ) / 3600 ) > $this->get_authorization_time_window();
	}


	/**
	 * Return the authorization time window in hours. An authorization is considered
	 * expired if it is older than this.
	 *
	 * 30 days (720 hours) is the standard authorization window. Individual gateways
	 * can override this as necessary.
	 *
	 * @since 2.2.0
	 * @return int hours
	 */
	protected function get_authorization_time_window() {

		return 720;
	}


	/**
	 * Determines if a credit card transaction should result in a charge.
	 *
	 * @since 1.0.0
	 * @param \WC_Order $order Optional. The order being charged
	 * @throws Exception
	 * @return bool
	 */
	public function perform_credit_card_charge( WC_Order $order = null ) {

		assert( $this->supports_credit_card_charge() );

		$perform = self::TRANSACTION_TYPE_CHARGE === $this->transaction_type;

		if ( ! $perform && $order && $this->supports_credit_card_charge_virtual() && 'yes' === $this->charge_virtual_orders ) {
			$perform = SV_WC_Helper::is_order_virtual( $order );
		}

		/**
		 * Filters whether a credit card transaction should result in a charge.
		 *
		 * @since 4.5.0
		 * @param bool $perform whether the transaction should result in a charge
		 * @param \WC_Order|null $order the order being charged
		 * @param \SV_WC_Payment_Gateway $gateway the gateway object
		 */
		return apply_filters( 'wc_' . $this->get_id() . '_perform_credit_card_charge', $perform, $order, $this );
	}


	/**
	 * Determines if a credit card transaction should result in an authorization.
	 *
	 * @since 1.0.0
	 * @param \WC_Order $order Optional. The order being authorized
	 * @throws Exception
	 * @return bool
	 */
	public function perform_credit_card_authorization( WC_Order $order = null ) {

		assert( $this->supports_credit_card_authorization() );

		$perform = self::TRANSACTION_TYPE_AUTHORIZATION === $this->transaction_type && ! $this->perform_credit_card_charge( $order );

		/**
		 * Filters whether a credit card transaction should result in an authorization.
		 *
		 * @since 4.5.0
		 * @param bool $perform whether the transaction should result in an authorization
		 * @param \WC_Order|null $order the order being authorized
		 * @param \SV_WC_Payment_Gateway $gateway the gateway object
		 */
		return apply_filters( 'wc_' . $this->get_id() . '_perform_credit_card_authorization', $perform, $order, $this );
	}


	/** Card Types feature ******************************************************/


	/**
	 * Returns true if the gateway supports card_types: allows the admin to
	 * configure card type icons to display at checkout
	 *
	 * @since 1.0.0
	 * @return boolean true if the gateway supports card_types
	 */
	public function supports_card_types() {
		return $this->is_credit_card_gateway() && $this->supports( self::FEATURE_CARD_TYPES );
	}


	/**
	 * Returns the array of accepted card types if this is a credit card gateway
	 * that supports card types.  Return format is 'VISA', 'MC', 'AMEX', etc
	 *
	 * @since 1.0.0
	 * @see get_available_card_types()
	 * @return array of accepted card types, ie 'VISA', 'MC', 'AMEX', etc
	 */
	public function get_card_types() {

		assert( $this->supports_card_types() );

		return $this->card_types;
	}


	/**
	 * Adds any card types form fields, allowing the admin to configure the card
	 * types icons displayed during checkout
	 *
	 * @since 1.0.0
	 * @param array $form_fields gateway form fields
	 * @return array $form_fields gateway form fields
	 */
	protected function add_card_types_form_fields( $form_fields ) {

		assert( $this->supports_card_types() );

		$form_fields['card_types'] = array(
			'title'    => esc_html__( 'Accepted Card Types', 'woocommerce-plugin-framework' ),
			'type'     => 'multiselect',
			'desc_tip' => esc_html__( 'Select which card types you accept.', 'woocommerce-plugin-framework' ),
			'default'  => array_keys( $this->get_available_card_types() ),
			'class'    => 'wc-enhanced-select',
			'css'      => 'width: 350px;',
			'options'  => $this->get_available_card_types(),
		);

		return $form_fields;
	}


	/**
	 * Returns available card types, ie 'VISA' => 'Visa', 'MC' => 'MasterCard', etc
	 *
	 * @since 1.0.0
	 * @return array associative array of card type to display name
	 */
	public function get_available_card_types() {

		assert( $this->supports_card_types() );

		// default available card types
		if ( ! isset( $this->available_card_types ) ) {

			$this->available_card_types = array(
				'VISA'   => esc_html_x( 'Visa', 'credit card type', 'woocommerce-plugin-framework' ),
				'MC'     => esc_html_x( 'MasterCard', 'credit card type', 'woocommerce-plugin-framework' ),
				'AMEX'   => esc_html_x( 'American Express', 'credit card type', 'woocommerce-plugin-framework' ),
				'DISC'   => esc_html_x( 'Discover', 'credit card type', 'woocommerce-plugin-framework' ),
				'DINERS' => esc_html_x( 'Diners', 'credit card type', 'woocommerce-plugin-framework' ),
				'JCB'    => esc_html_x( 'JCB', 'credit card type', 'woocommerce-plugin-framework' ),
			);

		}

		/**
		 * Payment Gateway Available Card Types Filter.
		 *
		 * Allow actors to modify the available card types.
		 *
		 * @since 1.0.0
		 * @param array $available_card_types
		 */
		return apply_filters( 'wc_' . $this->get_id() . '_available_card_types', $this->available_card_types );
	}


	/** Tokenization feature **************************************************/


	/**
	 * Returns true if the gateway supports tokenization
	 *
	 * @since 1.0.0
	 * @return boolean true if the gateway supports tokenization
	 */
	public function supports_tokenization() {
		return $this->supports( self::FEATURE_TOKENIZATION );
	}


	/**
	 * Returns true if tokenization is enabled
	 *
	 * @since 1.0.0
	 * @return boolean true if tokenization is enabled
	 */
	public function tokenization_enabled() {

		assert( $this->supports_tokenization() );

		return 'yes' == $this->tokenization;
	}


	/**
	 * Adds any tokenization form fields for the settings page
	 *
	 * @since 1.0.0
	 * @param array $form_fields gateway form fields
	 * @return array $form_fields gateway form fields
	 */
	protected function add_tokenization_form_fields( $form_fields ) {

		assert( $this->supports_tokenization() );

		$form_fields['tokenization'] = array(
			/* translators: http://www.cybersource.com/products/payment_security/payment_tokenization/ and https://en.wikipedia.org/wiki/Tokenization_(data_security) */
			'title'   => esc_html__( 'Tokenization', 'woocommerce-plugin-framework' ),
			'label'   => esc_html__( 'Allow customers to securely save their payment details for future checkout.', 'woocommerce-plugin-framework' ),
			'type'    => 'checkbox',
			'default' => 'no',
		);

		return $form_fields;
	}


	/** Helper methods ******************************************************/


	/**
	 * Safely get and trim data from $_POST
	 *
	 * @deprecated use SV_WC_Helper::get_post()
	 * @since 1.0.0
	 * @param string $key array key to get from $_POST array
	 * @return string value from $_POST or blank string if $_POST[ $key ] is not set
	 */
	protected function get_post( $key ) {

		if ( isset( $_POST[ $key ] ) ) {
			return trim( $_POST[ $key ] );
		}

		return '';
	}


	/**
	 * Safely get and trim data from $_REQUEST
	 *
	 * @since 1.0.0
	 * @param string $key array key to get from $_REQUEST array
	 * @return string value from $_REQUEST or blank string if $_REQUEST[ $key ] is not set
	 */
	protected function get_request( $key ) {

		if ( isset( $_REQUEST[ $key ] ) ) {
			return trim( $_REQUEST[ $key ] );
		}

		return '';
	}


	/**
	 * Add API request logging for the gateway. The main plugin class typically handles this, but the payment
	 * gateway plugin class no-ops the method so each gateway's requests can be logged individually (e.g. credit card &
	 * eCheck) and make use of the payment gateway-specific add_debug_message() method
	 *
	 * @since 2.2.0
	 * @see SV_WC_Plugin::add_api_request_logging()
	 */
	public function add_api_request_logging() {

		if ( ! has_action( 'wc_' . $this->get_id() . '_api_request_performed' ) ) {
			add_action( 'wc_' . $this->get_id() . '_api_request_performed', array( $this, 'log_api_request' ), 10, 2 );
		}
	}


	/**
	 * Log gateway API requests/responses
	 *
	 * @since 2.2.0
	 * @param array $request request data, see SV_WC_API_Base::broadcast_request() for format
	 * @param array $response response data
	 */
	public function log_api_request( $request, $response ) {

		// request
		$this->add_debug_message( $this->get_plugin()->get_api_log_message( $request ), 'message' );

		// response
		if ( ! empty( $response ) ) {
			$this->add_debug_message( $this->get_plugin()->get_api_log_message( $response ), 'message' );
		}
	}


	/**
	 * Adds debug messages to the page as a WC message/error, and/or to the WC Error log
	 *
	 * @since 1.0.0
	 * @param string $message message to add
	 * @param string $type how to add the message, options are:
	 *     'message' (styled as WC message), 'error' (styled as WC Error)
	 */
	public function add_debug_message( $message, $type = 'message' ) {

		// do nothing when debug mode is off or no message
		if ( 'off' == $this->debug_off() || ! $message ) {
			return;
		}

		// add log message to WC logger if log/both is enabled
		if ( $this->debug_log() ) {
			$this->get_plugin()->log( $message, $this->get_id() );
		}

		// avoid adding notices when performing refunds, these occur in the admin as an Ajax call, so checking the current filter
		// is the only reliably way to do so
		if ( in_array( 'wp_ajax_woocommerce_refund_line_items', $GLOBALS['wp_current_filter'] ) ) {
			return;
		}

		// add debug message to woocommerce->errors/messages if checkout or both is enabled, the admin/Ajax check ensures capture charge transactions aren't logged as notices to the front end
		if ( ( $this->debug_checkout() || ( 'error' === $type && $this->is_test_environment() ) ) && ( ! is_admin() || is_ajax() ) ) {

			if ( 'message' === $type ) {

				SV_WC_Helper::wc_add_notice( str_replace( "\n", "<br/>", htmlspecialchars( $message ) ), 'notice' );

			} else {

				// defaults to error message
				SV_WC_Helper::wc_add_notice( str_replace( "\n", "<br/>", htmlspecialchars( $message ) ), 'error' );
			}
		}
	}


	/**
	 * Get payment currency, either from current order or WC settings
	 *
	 * @since 4.1.0
	 * @return string three-letter currency code
	 */
	protected function get_payment_currency() {

		$currency = get_woocommerce_currency();
		$order_id = $this->get_checkout_pay_page_order_id();

		// Gets currency for the current order, that is about to be paid for
		if ( $order_id ) {

			$order    = wc_get_order( $order_id );
			$currency = SV_WC_Order_Compatibility::get_prop( $order, 'currency', 'view' );
		}

		return $currency;
	}


	/**
	 * Returns true if $currency is accepted by this gateway
	 *
	 * @since 2.1.0
	 * @param string $currency optional three-letter currency code, defaults to
	 *        order currency (if available) or currently configured WooCommerce
	 *        currency
	 * @return boolean true if $currency is accepted, false otherwise
	 */
	public function currency_is_accepted( $currency = null ) {

		// accept all currencies
		if ( ! $this->currencies ) {
			return true;
		}

		// default to order/WC currency
		if ( is_null( $currency ) ) {
			$currency = $this->get_payment_currency();
		}

		return in_array( $currency, $this->currencies );
	}


	/**
	 * Returns true if the given order needs shipping, false otherwise.  This
	 * is based on the WooCommerce core Cart::needs_shipping()
	 *
	 * @since 2.2.0
	 * @param \WC_Order $order
	 * @return boolean true if $order needs shipping, false otherwise
	 */
	protected function order_needs_shipping( $order ) {

		if ( get_option( 'woocommerce_calc_shipping' ) == 'no' ) {
			return false;
		}

		foreach ( $order->get_items() as $item ) {

			if ( SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ) {
				$product = $item->get_product();
			} else {
				$product = $order->get_product_from_item( $item );
			}

			if ( $product && $product->needs_shipping() ) {
				return true;
			}
		}

		// no shipping required
		return false;
	}


	/** Order Meta helper methods *********************************************/


	/**
	 * Adds order meta data.
	 *
	 * @since 2.2.0
	 * @param \WC_Order|int the order to add meta to
	 * @param string $key meta key (already prefixed with gateway ID)
	 * @param mixed $value meta value
	 * @param bool $unique whether the meta value should be unique
	 * @return bool|int
	 */
	public function add_order_meta( $order, $key, $value, $unique = false ) {

		if ( is_numeric( $order ) ) {
			$order = wc_get_order( $order );
		}

		if ( ! $order instanceof WC_Order ) {
			return false;
		}

		return SV_WC_Order_Compatibility::add_meta_data( $order, $this->get_order_meta_prefix() . $key, $value, $unique );
	}


	/**
	 * Gets order meta data.
	 *
	 * Note this is hardcoded to return a single value for the get_post_meta() call.
	 *
	 * @since 2.2.0
	 * @param \WC_Order|int the order to get meta for
	 * @param string $key meta key
	 * @return mixed
	 */
	public function get_order_meta( $order, $key ) {

		if ( is_numeric( $order ) ) {
			$order = wc_get_order( $order );
		}

		if ( ! $order instanceof WC_Order ) {
			return false;
		}

		return SV_WC_Order_Compatibility::get_meta( $order, $this->get_order_meta_prefix() . $key, true );
	}


	/**
	 * Updates order meta data.
	 *
	 * @since 2.2.0
	 * @param \WC_Order|int the order to update meta for
	 * @param string $key meta key
	 * @param mixed $value meta value
	 * @return bool|int
	 */
	public function update_order_meta( $order, $key, $value ) {

		if ( is_numeric( $order ) ) {
			$order = wc_get_order( $order );
		}

		if ( ! $order instanceof WC_Order ) {
			return false;
		}

		return SV_WC_Order_Compatibility::update_meta_data( $order, $this->get_order_meta_prefix() . $key, $value );
	}


	/**
	 * Delete order meta data.
	 *
	 * @since 2.2.0
	 * @param \WC_Order|int the order to delete meta for
	 * @param string $key meta key
	 * @return bool
	 */
	public function delete_order_meta( $order, $key ) {

		if ( is_numeric( $order ) ) {
			$order = wc_get_order( $order );
		}

		if ( ! $order instanceof WC_Order ) {
			return false;
		}

		return SV_WC_Order_Compatibility::delete_meta_data( $order, $this->get_order_meta_prefix() . $key );
	}


	/**
	 * Gets the order meta prefixed used for the *_order_meta() methods
	 *
	 * Defaults to `_wc_{gateway_id}_`
	 *
	 * @since 2.2.0
	 * @return string
	 */
	public function get_order_meta_prefix() {
		return '_wc_' . $this->get_id() . '_';
	}


	/** Getters ******************************************************/


	/**
	 * Returns the payment gateway id
	 *
	 * @since 1.0.0
	 * @see WC_Payment_Gateway::$id
	 * @return string payment gateway id
	 */
	public function get_id() {
		return $this->id;
	}


	/**
	 * Returns the payment gateway id with dashes in place of underscores, and
	 * appropriate for use in frontend element names, classes and ids
	 *
	 * @since 1.0.0
	 * @return string payment gateway id with dashes in place of underscores
	 */
	public function get_id_dasherized() {
		return str_replace( '_', '-', $this->get_id() );
	}


	/**
	 * Returns the parent plugin object
	 *
	 * @since 1.0.0
	 * @return \SV_WC_Payment_Gateway_Plugin the parent plugin object
	 */
	public function get_plugin() {
		return $this->plugin;
	}


	/**
	 * Returns the admin method title.  This should be the gateway name, ie
	 * 'Intuit QBMS'
	 *
	 * @since 1.0.0
	 * @see WC_Settings_API::$method_title
	 * @return string method title
	 */
	public function get_method_title() {
		return $this->method_title;
	}


	/**
	 * Determines if the Card Security Code (CVV) field should be used at checkout.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function csc_enabled() {
		return 'yes' === $this->enable_csc;
	}


	/**
	 * Determines if the Card Security Code (CVV) field should be required at checkout.
	 *
	 * @since 4.5.0
	 * @return bool
	 */
	public function csc_required() {
		return $this->csc_enabled();
	}


	/**
	 * Determines if the gateway supports sharing settings with sibling gateways.
	 *
	 * @since 4.5.0
	 * @return bool
	 */
	public function share_settings() {
		return true;
	}


	/**
	 * Determines if settings should be inherited for this gateway.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function inherit_settings() {
		return 'yes' === $this->inherit_settings;
	}


	/**
	 * Returns an array of two-letter country codes this gateway is allowed for, defaults to all
	 *
	 * @since 2.2.0
	 * @see WC_Payment_Gateway::$countries
	 * @return array of two-letter country codes this gateway is allowed for, defaults to all
	 */
	public function get_available_countries() {
		return $this->countries;
	}


	/**
	 * Add support for the named feature or features
	 *
	 * @since 1.0.0
	 * @param string|array $feature the feature name or names supported by this gateway
	 */
	public function add_support( $feature ) {

		if ( ! is_array( $feature ) ) {
			$feature = array( $feature );
		}

		foreach ( $feature as $name ) {

			// add support for feature if it's not already declared
			if ( ! in_array( $name, $this->supports ) ) {

				$this->supports[] = $name;

				/**
				 * Payment Gateway Add Support Action.
				 *
				 * Fired when declaring support for a specific gateway feature. Allows other actors
				 * (including ourselves) to take action when support is declared.
				 *
				 * @since 1.0.0
				 * @param \SV_WC_Payment_Gateway $this instance
				 * @param string $name of supported feature being added
				 */
				do_action( 'wc_payment_gateway_' . $this->get_id() . '_supports_' . str_replace( '-', '_', $name ), $this, $name );
			}

		}
	}


	/**
	 * Remove support for the named feature or features
	 *
	 * @since 4.1.0
	 * @param string|array $feature feature name or names not supported by this gateway
	 */
	public function remove_support( $feature ) {

		if ( ! is_array( $feature ) ) {
			$feature = array( $feature );
		}

		foreach ( $feature as $name ) {

			unset( $this->supports[ array_search( $name, $this->supports ) ] );

			/**
			 * Payment Gateway Remove Support Action.
			 *
			 * Fired when removing support for a specific gateway feature. Allows other actors
			 * (including ourselves) to take action when support is removed.
			 *
			 * @since 4.1.0
			 * @param \SV_WC_Payment_Gateway $this instance
			 * @param string $name of supported feature being removed
			 */
			do_action( 'wc_payment_gateway_' . $this->get_id() . '_removed_support_' . str_replace( '-', '_', $name ), $this, $name );
		}
	}


	/**
	 * Set all features supported
	 *
	 * @since 1.0.0
	 * @param array $features array of supported feature names
	 */
	public function set_supports( $features ) {
		$this->supports = $features;
	}


	/**
	 * Returns true if this echeck gateway supports
	 *
	 * @since 1.0.0
	 * @param string $field_name check gateway field name, includes 'check_number', 'account_type'
	 * @return boolean true if this check gateway supports the named field
	 */
	public function supports_check_field( $field_name ) {

		assert( $this->is_echeck_gateway() );

		return is_array( $this->supported_check_fields ) && in_array( $field_name, $this->supported_check_fields );

	}


	/**
	 * Gets the set of environments supported by this gateway.  All gateways
	 * support at least the production environment
	 *
	 * @since 1.0.0
	 * @return array associative array of environment id to name supported by this gateway
	 */
	public function get_environments() {

		// default set of environments consists of 'production'
		if ( ! isset( $this->environments ) ) {
			/* translators: https://www.skyverge.com/for-translators-environments/  */
			$this->environments = array( self::ENVIRONMENT_PRODUCTION => esc_html_x( 'Production', 'software environment', 'woocommerce-plugin-framework' ) );
		}

		return $this->environments;
	}


	/**
	 * Returns the environment setting, one of the $environments keys, ie
	 * 'production'
	 *
	 * @since 1.0.0
	 * @return string the configured environment id
	 */
	public function get_environment() {
		return $this->environment;
	}


	/**
	 * Get the configured environment's display name.
	 *
	 * @since 4.3.0
	 * @return string The configured environment name
	 */
	public function get_environment_name() {

		$environments = $this->get_environments();

		$environment_id   = $this->get_environment();
		$environment_name = ( isset( $environments[ $environment_id ] ) ) ? $environments[ $environment_id ] : $environment_id;

		return $environment_name;
	}


	/**
	 * Returns true if the current environment is $environment_id
	 */
	public function is_environment( $environment_id ) {
		return $environment_id == $this->get_environment();
	}


	/**
	 * Returns true if the current gateway environment is configured to
	 * 'production'.  All gateways have at least the production environment
	 *
	 * @since 1.0.0
	 * @param string $environment_id optional environment id to check, otherwise defaults to the gateway current environment
	 * @return boolean true if $environment_id (if non-null) or otherwise the current environment is production
	 */
	public function is_production_environment( $environment_id = null ) {

		// if an environment was passed in, see whether it's the production environment
		if ( ! is_null( $environment_id ) ) {
			return self::ENVIRONMENT_PRODUCTION == $environment_id;
		}

		// default: check the current environment
		return $this->is_environment( self::ENVIRONMENT_PRODUCTION );
	}


	/**
	 * Returns true if the current gateway environment is configured to 'test'
	 *
	 * @since 2.1.0
	 * @param string $environment_id optional environment id to check, otherwise defaults to the gateway current environment
	 * @return boolean true if $environment_id (if non-null) or otherwise the current environment is test
	 */
	public function is_test_environment( $environment_id = null ) {

		// if an environment was passed in, see whether it's the production environment
		if ( ! is_null( $environment_id ) ) {
			return self::ENVIRONMENT_TEST == $environment_id;
		}

		// default: check the current environment
		return $this->is_environment( self::ENVIRONMENT_TEST );
	}


	/**
	 * Returns true if the gateway is enabled.  This has nothing to do with
	 * whether the gateway is properly configured or functional.
	 *
	 * @since 2.1.0
	 * @see WC_Payment_Gateway::$enabled
	 * @return boolean true if the gateway is enabled
	 */
	public function is_enabled() {
		return 'yes' == $this->enabled;
	}


	/**
	 * Returns true if detailed decline messages should be displayed to
	 * customers on checkout when available, rather than a single generic
	 * decline message
	 *
	 * @since 2.2.0
	 * @see SV_WC_Payment_Gateway_API_Response_Message_Helper
	 * @see SV_WC_Payment_Gateway_API_Response::get_user_message()
	 * @return boolean true if detailed decline messages should be displayed
	 *         on checkout
	 */
	public function is_detailed_customer_decline_messages_enabled() {
		return 'yes' == $this->enable_customer_decline_messages;
	}


	/**
	 * Returns the set of accepted currencies, or empty array if all currencies
	 * are accepted by this gateway
	 *
	 * @since 2.1.0
	 * @return array of currencies accepted by this gateway
	 */
	public function get_accepted_currencies() {
		return $this->currencies;
	}


	/**
	 * Returns true if all debugging is disabled
	 *
	 * @since 1.0.0
	 * @return boolean if all debuging is disabled
	 */
	public function debug_off() {
		return self::DEBUG_MODE_OFF === $this->debug_mode;
	}


	/**
	 * Returns true if debug logging is enabled
	 *
	 * @since 1.0.0
	 * @return boolean if debug logging is enabled
	 */
	public function debug_log() {
		return self::DEBUG_MODE_LOG === $this->debug_mode || self::DEBUG_MODE_BOTH === $this->debug_mode;
	}


	/**
	 * Returns true if checkout debugging is enabled.  This will cause debugging
	 * statements to be displayed on the checkout/pay pages
	 *
	 * @since 1.0.0
	 * @return boolean if checkout debugging is enabled
	 */
	public function debug_checkout() {
		return self::DEBUG_MODE_CHECKOUT === $this->debug_mode || self::DEBUG_MODE_BOTH === $this->debug_mode;
	}


	/**
	 * Returns true if this is a direct type gateway
	 *
	 * @since 1.0.0
	 * @return boolean if this is a direct payment gateway
	 */
	public function is_direct_gateway() {
		return false;
	}


	/**
	 * Returns true if this is a hosted type gateway
	 *
	 * @since 1.0.0
	 * @return boolean if this is a hosted IPN payment gateway
	 */
	public function is_hosted_gateway() {
		return false;
	}


	/**
	 * Returns the payment type for this gateway
	 *
	 * @since 2.1.0
	 * @return string the payment type, ie 'credit-card', 'echeck', etc
	 */
	public function get_payment_type() {
		return $this->payment_type;
	}


	/**
	 * Returns true if this is a credit card gateway
	 *
	 * @since 1.0.0
	 * @return boolean true if this is a credit card gateway
	 */
	public function is_credit_card_gateway() {
		return self::PAYMENT_TYPE_CREDIT_CARD == $this->get_payment_type();
	}


	/**
	 * Returns true if this is an echeck gateway
	 *
	 * @since 1.0.0
	 * @return boolean true if this is an echeck gateway
	 */
	public function is_echeck_gateway() {
		return self::PAYMENT_TYPE_ECHECK == $this->get_payment_type();
	}


	/**
	 * Returns the API instance for this gateway if it uses direct communication
	 *
	 * This is a stub method which must be overridden if this gateway performs
	 * direct communication
	 *
	 * @since 1.0.0
	 * @return SV_WC_Payment_Gateway_API the payment gateway API instance
	 */
	public function get_api() {

		// concrete stub method
		assert( false );
	}


	/**
	 * Returns the order_id if on the checkout pay page
	 *
	 * @since 3.0.0
	 * @return int order identifier
	 */
	public function get_checkout_pay_page_order_id() {
		global $wp;

		return isset( $wp->query_vars['order-pay'] ) ? absint( $wp->query_vars['order-pay'] ) : 0;
	}


	/**
	 * Returns the order_id if on the checkout order received page
	 *
	 * Note this must be used in the `wp` or later action, as earlier
	 * actions do not yet have access to the query vars
	 *
	 * @since 3.0.0
	 * @return int order identifier
	 */
	public function get_checkout_order_received_order_id() {
		global $wp;

		return isset( $wp->query_vars['order-received'] ) ? absint( $wp->query_vars['order-received'] ) : 0;
	}


}

endif;  // class exists check
