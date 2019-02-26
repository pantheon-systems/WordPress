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

if ( ! class_exists( 'SV_WC_Payment_Gateway_Direct' ) ) :

/**
 * # WooCommerce Payment Gateway Framework Direct Gateway
 *
 * @since 1.0.0
 */
abstract class SV_WC_Payment_Gateway_Direct extends SV_WC_Payment_Gateway {


	/** Add new payment method feature */
	const FEATURE_ADD_PAYMENT_METHOD = 'add_payment_method';

	/** Admin token editor feature */
	const FEATURE_TOKEN_EDITOR = 'token_editor';

	/** Subscriptions integration ID */
	const INTEGRATION_SUBSCRIPTIONS = 'subscriptions';

	/** Pre-orders integration ID */
	const INTEGRATION_PRE_ORDERS = 'pre_orders';

	/** @var \SV_WC_Payment_Gateway_Payment_Tokens_Handler payment tokens handler instance */
	protected $payment_tokens_handler;

	/** @var array of SV_WC_Payment_Gateway_Integration objects for Subscriptions, Pre-Orders, etc. */
	protected $integrations;


	/**
	 * Initialize the gateway
	 *
	 * See parent constructor for full method documentation
	 *
	 * @since 1.0.0
	 * @see SV_WC_Payment_Gateway::__construct()
	 * @param string $id the gateway id
	 * @param SV_WC_Payment_Gateway_Plugin $plugin the parent plugin class
	 * @param array $args gateway arguments
	 */
	public function __construct( $id, $plugin, $args ) {

		// parent constructor
		parent::__construct( $id, $plugin, $args );

		$this->init_payment_tokens_handler();

		$this->init_integrations();
	}


	/**
	 * Validate the payment fields when processing the checkout
	 *
	 * NOTE: if we want to bring billing field validation (ie length) into the
	 * fold, see the Elavon VM Payment Gateway for a sample implementation
	 *
	 * @since 1.0.0
	 * @see WC_Payment_Gateway::validate_fields()
	 * @return bool true if fields are valid, false otherwise
	 */
	public function validate_fields() {

		$is_valid = parent::validate_fields();

		if ( $this->supports_tokenization() ) {

			// tokenized transaction?
			if ( SV_WC_Helper::get_post( 'wc-' . $this->get_id_dasherized() . '-payment-token' ) ) {

				// unknown token?
				if ( ! $this->get_payment_tokens_handler()->user_has_token( get_current_user_id(), SV_WC_Helper::get_post( 'wc-' . $this->get_id_dasherized() . '-payment-token' ) ) ) {
					SV_WC_Helper::wc_add_notice( esc_html__( 'Payment error, please try another payment method or contact us to complete your transaction.', 'woocommerce-plugin-framework' ), 'error' );
					$is_valid = false;
				}

				// Check the CSC if enabled
				if ( $this->is_credit_card_gateway() && $this->csc_enabled() ) {
					$is_valid = $this->validate_csc( SV_WC_Helper::get_post( 'wc-' . $this->get_id_dasherized() . '-csc' ) ) && $is_valid;
				}

				// no more validation to perform
				return $is_valid;
			}
		}

		// validate remaining payment fields
		if ( $this->is_credit_card_gateway() ) {
			return $this->validate_credit_card_fields( $is_valid );
		} elseif ( $this->is_echeck_gateway() ) {
			return $this->validate_check_fields( $is_valid );
		} else {
			$method_name = 'validate_' . str_replace( '-', '_', strtolower( $this->get_payment_type() ) ) . '_fields';
			if ( is_callable( array( $this, $method_name ) ) ) {
				return $this->$method_name( $is_valid );
			}
		}

		// no more validation to perform. Return the parent method's outcome.
		return $is_valid;
	}


	/**
	 * Returns true if the posted credit card fields are valid, false otherwise
	 *
	 * @since 1.0.0
	 * @param boolean $is_valid true if the fields are valid, false otherwise
	 * @return boolean true if the fields are valid, false otherwise
	 */
	protected function validate_credit_card_fields( $is_valid ) {

		$account_number   = SV_WC_Helper::get_post( 'wc-' . $this->get_id_dasherized() . '-account-number' );
		$expiration_month = SV_WC_Helper::get_post( 'wc-' . $this->get_id_dasherized() . '-exp-month' );
		$expiration_year  = SV_WC_Helper::get_post( 'wc-' . $this->get_id_dasherized() . '-exp-year' );
		$expiry           = SV_WC_Helper::get_post( 'wc-' . $this->get_id_dasherized() . '-expiry' );
		$csc              = SV_WC_Helper::get_post( 'wc-' . $this->get_id_dasherized() . '-csc' );

		// handle single expiry field formatted like "MM / YY" or "MM / YYYY"
		if ( ! $expiration_month & ! $expiration_year && $expiry ) {
			list( $expiration_month, $expiration_year ) = array_map( 'trim', explode( '/', $expiry ) );
		}

		$is_valid = $this->validate_credit_card_account_number( $account_number ) && $is_valid;

		$is_valid = $this->validate_credit_card_expiration_date( $expiration_month, $expiration_year ) && $is_valid;

		// validate card security code
		if ( $this->csc_enabled() ) {
			$is_valid = $this->validate_csc( $csc ) && $is_valid;
		}

		/**
		 * Direct Payment Gateway Validate Credit Card Fields Filter.
		 *
		 * Allow actors to filter the credit card field validation.
		 *
		 * @since 4.3.0
		 * @param bool $is_valid true for validation to pass
		 * @param \SV_WC_Payment_Gateway_Direct $this direct gateway class instance
		 */
		return apply_filters( 'wc_payment_gateway_' . $this->get_id() . '_validate_credit_card_fields', $is_valid, $this );
	}


	/**
	 * Validates the provided credit card expiration date
	 *
	 * @since 2.1.0
	 * @param string $expiration_month the credit card expiration month
	 * @param string $expiration_year the credit card expiration month
	 * @return boolean true if the card expiration date is valid, false otherwise
	 */
	protected function validate_credit_card_expiration_date( $expiration_month, $expiration_year ) {

		$is_valid = true;

		if ( 2 === strlen( $expiration_year ) ) {
			$expiration_year = '20' . $expiration_year;
		}

		// validate expiration data
		$current_year  = date( 'Y' );
		$current_month = date( 'n' );

		if ( ! ctype_digit( $expiration_month ) || ! ctype_digit( $expiration_year ) ||
			$expiration_month > 12 ||
			$expiration_month < 1 ||
			$expiration_year < $current_year ||
			( $expiration_year == $current_year && $expiration_month < $current_month ) ||
			$expiration_year > $current_year + 20
		) {
			SV_WC_Helper::wc_add_notice( esc_html__( 'Card expiration date is invalid', 'woocommerce-plugin-framework' ), 'error' );
			$is_valid = false;
		}

		return $is_valid;
	}


	/**
	 * Validates the provided credit card account number
	 *
	 * @since 2.1.0
	 * @param string $account_number the credit card account number
	 * @return boolean true if the card account number is valid, false otherwise
	 */
	protected function validate_credit_card_account_number( $account_number ) {

		$is_valid = true;

		// validate card number
		$account_number = str_replace( array( ' ', '-' ), '', $account_number );

		if ( empty( $account_number ) ) {

			SV_WC_Helper::wc_add_notice( esc_html__( 'Card number is missing', 'woocommerce-plugin-framework' ), 'error' );
			$is_valid = false;

		} else {

			if ( strlen( $account_number ) < 12 || strlen( $account_number ) > 19 ) {
				SV_WC_Helper::wc_add_notice( esc_html__( 'Card number is invalid (wrong length)', 'woocommerce-plugin-framework' ), 'error' );
				$is_valid = false;
			}

			if ( ! ctype_digit( $account_number ) ) {
				SV_WC_Helper::wc_add_notice( esc_html__( 'Card number is invalid (only digits allowed)', 'woocommerce-plugin-framework' ), 'error' );
				$is_valid = false;
			}

			if ( ! SV_WC_Payment_Gateway_Helper::luhn_check( $account_number ) ) {
				SV_WC_Helper::wc_add_notice( esc_html__( 'Card number is invalid', 'woocommerce-plugin-framework' ), 'error' );
				$is_valid = false;
			}

		}

		return $is_valid;
	}


	/**
	 * Validates the provided Card Security Code, adding user error messages as
	 * needed
	 *
	 * @since 1.0.0
	 * @param string $csc the customer-provided card security code
	 * @return boolean true if the card security code is valid, false otherwise
	 */
	protected function validate_csc( $csc ) {

		$is_valid = true;

		// validate security code
		if ( ! empty( $csc ) ) {

			// digit validation
			if ( ! ctype_digit( $csc ) ) {
				SV_WC_Helper::wc_add_notice( esc_html__( 'Card security code is invalid (only digits are allowed)', 'woocommerce-plugin-framework' ), 'error' );
				$is_valid = false;
			}

			// length validation
			if ( strlen( $csc ) < 3 || strlen( $csc ) > 4 ) {
				SV_WC_Helper::wc_add_notice( esc_html__( 'Card security code is invalid (must be 3 or 4 digits)', 'woocommerce-plugin-framework' ), 'error' );
				$is_valid = false;
			}

		} elseif ( $this->csc_required() ) {

			SV_WC_Helper::wc_add_notice( esc_html__( 'Card security code is missing', 'woocommerce-plugin-framework' ), 'error' );
			$is_valid = false;
		}

		return $is_valid;
	}


	/**
	 * Returns true if the posted echeck fields are valid, false otherwise
	 *
	 * @since 1.0.0
	 * @param bool $is_valid true if the fields are valid, false otherwise
	 * @return bool
	 */
	protected function validate_check_fields( $is_valid ) {

		$account_number         = SV_WC_Helper::get_post( 'wc-' . $this->get_id_dasherized() . '-account-number' );
		$routing_number         = SV_WC_Helper::get_post( 'wc-' . $this->get_id_dasherized() . '-routing-number' );

		// optional fields (excluding account type for now)
		$drivers_license_number = SV_WC_Helper::get_post( 'wc-' . $this->get_id_dasherized() . '-drivers-license-number' );
		$check_number           = SV_WC_Helper::get_post( 'wc-' . $this->get_id_dasherized() . '-check-number' );

		// routing number exists?
		if ( empty( $routing_number ) ) {

			SV_WC_Helper::wc_add_notice( esc_html__( 'Routing Number is missing', 'woocommerce-plugin-framework' ), 'error' );
			$is_valid = false;

		} else {

			// routing number digit validation
			if ( ! ctype_digit( $routing_number ) ) {
				SV_WC_Helper::wc_add_notice( esc_html__( 'Routing Number is invalid (only digits are allowed)', 'woocommerce-plugin-framework' ), 'error' );
				$is_valid = false;
			}

			// routing number length validation
			if ( 9 != strlen( $routing_number ) ) {
				SV_WC_Helper::wc_add_notice( esc_html__( 'Routing number is invalid (must be 9 digits)', 'woocommerce-plugin-framework' ), 'error' );
				$is_valid = false;
			}

		}

		// account number exists?
		if ( empty( $account_number ) ) {

			SV_WC_Helper::wc_add_notice( esc_html__( 'Account Number is missing', 'woocommerce-plugin-framework' ), 'error' );
			$is_valid = false;

		} else {

			// account number digit validation
			if ( ! ctype_digit( $account_number ) ) {
				SV_WC_Helper::wc_add_notice( esc_html__( 'Account Number is invalid (only digits are allowed)', 'woocommerce-plugin-framework' ), 'error' );
				$is_valid = false;
			}

			// account number length validation
			if ( strlen( $account_number ) < 5 || strlen( $account_number ) > 17 ) {
				SV_WC_Helper::wc_add_notice( esc_html__( 'Account number is invalid (must be between 5 and 17 digits)', 'woocommerce-plugin-framework' ), 'error' );
				$is_valid = false;
			}
		}

		// optional drivers license number validation
		if ( ! empty( $drivers_license_number ) &&  preg_match( '/^[a-zA-Z0-9 -]+$/', $drivers_license_number ) ) {
			SV_WC_Helper::wc_add_notice( esc_html__( 'Drivers license number is invalid', 'woocommerce-plugin-framework' ), 'error' );
			$is_valid = false;
		}

		// optional check number validation
		if ( ! empty( $check_number ) && ! ctype_digit( $check_number ) ) {
			SV_WC_Helper::wc_add_notice( esc_html__( 'Check Number is invalid (only digits are allowed)', 'woocommerce-plugin-framework' ), 'error' );
			$is_valid = false;
		}

		/**
		 * Direct Payment Gateway Validate eCheck Fields Filter.
		 *
		 * Allow actors to filter the eCheck field validation.
		 *
		 * @since 4.3.0
		 * @param bool $is_valid true for validation to pass
		 * @param \SV_WC_Payment_Gateway_Direct $this direct gateway class instance
		 */
		return apply_filters( 'wc_payment_gateway_' . $this->get_id() . '_validate_echeck_fields', $is_valid, $this );
	}


	/**
	 * Handles payment processing
	 *
	 * @since 1.0.0
	 * @see WC_Payment_Gateway::process_payment()
	 * @param int|string $order_id
	 * @return array associative array with members 'result' and 'redirect'
	 */
	public function process_payment( $order_id ) {

		$default = parent::process_payment( $order_id );

		/**
		 * Direct Gateway Process Payment Filter.
		 *
		 * Allow actors to intercept and implement the process_payment() call for
		 * this transaction. Return an array value from this filter will return it
		 * directly to the checkout processing code and skip this method entirely.
		 *
		 * @since 1.0.0
		 * @param bool $result default true
		 * @param int|string $order_id order ID for the payment
		 * @param \SV_WC_Payment_Gateway_Direct $this instance
		 */
		if ( is_array( $result = apply_filters( 'wc_payment_gateway_' . $this->get_id() . '_process_payment', true, $order_id, $this ) ) ) {
			return $result;
		}

		// add payment information to order
		$order = $this->get_order( $order_id );

		try {

			// registered customer checkout (already logged in or creating account at checkout)
			if ( $this->supports_tokenization() && 0 != $order->get_user_id() && $this->get_payment_tokens_handler()->should_tokenize() &&
				( '0.00' === $order->payment_total || $this->tokenize_before_sale() ) ) {
				$order = $this->get_payment_tokens_handler()->create_token( $order );
			}

			// payment failures are handled internally by do_transaction()
			// the order amount will be $0 if a WooCommerce Subscriptions free trial product is being processed
			// note that customer id & payment token are saved to order when create_token() is called
			if ( ( '0.00' === $order->payment_total && ! $this->transaction_forced() ) || $this->do_transaction( $order ) ) {

				// add transaction data for zero-dollar "orders"
				if ( '0.00' === $order->payment_total ) {
					$this->add_transaction_data( $order );
				}

				if ( $order->has_status( 'on-hold' ) ) {
					SV_WC_Order_Compatibility::reduce_stock_levels( $order ); // reduce stock for held orders, but don't complete payment
				} else {
					$order->payment_complete(); // mark order as having received payment
				}

				// process_payment() can sometimes be called in an admin-context
				if ( isset( WC()->cart ) ) {
					WC()->cart->empty_cart();
				}

				/**
				 * Payment Gateway Payment Processed Action.
				 *
				 * Fired when a payment is processed for an order.
				 *
				 * @since 4.1.0
				 * @param \WC_Order $order order object
				 * @param \SV_WC_Payment_Gateway_Direct $this instance
				 */
				do_action( 'wc_payment_gateway_' . $this->get_id() . '_payment_processed', $order, $this );

				return array(
					'result'   => 'success',
					'redirect' => $this->get_return_url( $order ),
				);
			}

		} catch ( SV_WC_Plugin_Exception $e ) {

			$this->mark_order_as_failed( $order, $e->getMessage() );

			return array(
				'result'  => 'failure',
				'message' => $e->getMessage(),
			);
		}

		return $default;
	}


	/**
	 * Add payment and transaction information as class members of WC_Order
	 * instance.  The standard information that can be added includes:
	 *
	 * $order->payment_total           - the payment total
	 * $order->customer_id             - optional payment gateway customer id (useful for tokenized payments for certain gateways, etc)
	 * $order->payment->account_number - the credit card or checking account number
	 * $order->payment->last_four      - the last four digits of the account number
	 * $order->payment->card_type      - the card type (e.g. visa) derived from the account number
	 * $order->payment->routing_number - account routing number (check transactions only)
	 * $order->payment->account_type   - optional type of account one of 'checking' or 'savings' if type is 'check'
	 * $order->payment->card_type      - optional card type, ie one of 'visa', etc
	 * $order->payment->exp_month      - the 2 digit credit card expiration month (for credit card gateways), e.g. 07
	 * $order->payment->exp_year       - the 2 digit credit card expiration year (for credit card gateways), e.g. 17
	 * $order->payment->csc            - the card security code (for credit card gateways)
	 * $order->payment->check_number   - optional check number (check transactions only)
	 * $order->payment->drivers_license_number - optional driver license number (check transactions only)
	 * $order->payment->drivers_license_state  - optional driver license state code (check transactions only)
	 * $order->payment->token          - payment token (for tokenized transactions)
	 *
	 * Note that not all gateways will necessarily pass or require all of the
	 * above.  These represent the most common attributes used among a variety
	 * of gateways, it's up to the specific gateway implementation to make use
	 * of, or ignore them, or add custom ones by overridding this method.
	 *
	 * @since 1.0.0
	 * @see SV_WC_Payment_Gateway::get_order()
	 * @param int|\WC_Order $order_id order ID being processed
	 * @return WC_Order object with payment and transaction information attached
	 */
	public function get_order( $order_id ) {

		$order = parent::get_order( $order_id );

		// payment info
		if ( SV_WC_Helper::get_post( 'wc-' . $this->get_id_dasherized() . '-account-number' ) && ! SV_WC_Helper::get_post( 'wc-' . $this->get_id_dasherized() . '-payment-token' ) ) {

			// common attributes
			$order->payment->account_number = str_replace( array( ' ', '-' ), '', SV_WC_Helper::get_post( 'wc-' . $this->get_id_dasherized() . '-account-number' ) );
			$order->payment->last_four = substr( $order->payment->account_number, -4 );

			if ( $this->is_credit_card_gateway() ) {

				// credit card specific attributes
				$order->payment->card_type      = SV_WC_Helper::get_post( 'wc-' . $this->get_id_dasherized() . '-card-type' );
				$order->payment->exp_month      = SV_WC_Helper::get_post( 'wc-' . $this->get_id_dasherized() . '-exp-month' );
				$order->payment->exp_year       = SV_WC_Helper::get_post( 'wc-' . $this->get_id_dasherized() . '-exp-year' );

				// add card type for gateways that don't require it displayed at checkout
				if ( empty( $order->payment->card_type ) ) {
					$order->payment->card_type = SV_WC_Payment_Gateway_Helper::card_type_from_account_number( $order->payment->account_number );
				}

				// handle single expiry field formatted like "MM / YY" or "MM / YYYY"
				if ( SV_WC_Helper::get_post( 'wc-' . $this->get_id_dasherized() . '-expiry' ) ) {
					list( $order->payment->exp_month, $order->payment->exp_year ) = array_map( 'trim', explode( '/', SV_WC_Helper::get_post( 'wc-' . $this->get_id_dasherized() . '-expiry' ) ) );
				}

				// add CSC if enabled
				if ( $this->csc_enabled() ) {
					$order->payment->csc = SV_WC_Helper::get_post( 'wc-' . $this->get_id_dasherized() . '-csc' );
				}

			} elseif ( $this->is_echeck_gateway() ) {

				// echeck specific attributes
				$order->payment->routing_number         = SV_WC_Helper::get_post( 'wc-' . $this->get_id_dasherized() . '-routing-number' );
				$order->payment->account_type           = SV_WC_Helper::get_post( 'wc-' . $this->get_id_dasherized() . '-account-type' );
				$order->payment->check_number           = SV_WC_Helper::get_post( 'wc-' . $this->get_id_dasherized() . '-check-number' );
				$order->payment->drivers_license_number = SV_WC_Helper::get_post( 'wc-' . $this->get_id_dasherized() . '-drivers-license-number' );
				$order->payment->drivers_license_state  = SV_WC_Helper::get_post( 'wc-' . $this->get_id_dasherized() . '-drivers-license-state' );

			}

		} elseif ( SV_WC_Helper::get_post( 'wc-' . $this->get_id_dasherized() . '-payment-token' ) ) {

			// paying with tokenized payment method (we've already verified that this token exists in the validate_fields method)
			$token = $this->get_payment_tokens_handler()->get_token( $order->get_user_id(), SV_WC_Helper::get_post( 'wc-' . $this->get_id_dasherized() . '-payment-token' ) );

			$order->payment->token          = $token->get_id();
			$order->payment->account_number = $token->get_last_four();
			$order->payment->last_four      = $token->get_last_four();

			if ( $this->is_credit_card_gateway() ) {

				// credit card specific attributes
				$order->payment->card_type = $token->get_card_type();
				$order->payment->exp_month = $token->get_exp_month();
				$order->payment->exp_year  = $token->get_exp_year();

				if ( $this->csc_enabled() ) {
					$order->payment->csc = SV_WC_Helper::get_post( 'wc-' . $this->get_id_dasherized() . '-csc' );
				}

			} elseif ( $this->is_echeck_gateway() ) {

				// echeck specific attributes
				$order->payment->account_type = $token->get_account_type();
			}

			// make this the new default payment token
			$this->get_payment_tokens_handler()->set_default_token( $order->get_user_id(), $token );
		}

		// standardize expiration date year to 2 digits
		if ( ! empty( $order->payment->exp_year ) && 4 === strlen( $order->payment->exp_year ) ) {
			$order->payment->exp_year = substr( $order->payment->exp_year, 2 );
		}

		/**
		 * Direct Gateway Get Order Filter.
		 *
		 * Allow actors to modify the order object.
		 *
		 * @since 1.0.0
		 * @param \WC_Order $order order object
		 * @param \SV_WC_Payment_Gateway_Direct $this instance
		 */
		return apply_filters( 'wc_payment_gateway_' . $this->get_id() . '_get_order', $order, $this );
	}


	/**
	 * Performs a check transaction for the given order and returns the
	 * result
	 *
	 * @since 1.0.0
	 * @param WC_Order $order the order object
	 * @return SV_WC_Payment_Gateway_API_Response the response
	 * @throws SV_WC_Payment_Gateway_Exception network timeouts, etc
	 */
	protected function do_check_transaction( $order ) {

		$response = $this->get_api()->check_debit( $order );

		// success! update order record
		if ( $response->transaction_approved() ) {

			$last_four = substr( $order->payment->account_number, -4 );

			// check order note. there may not be an account_type available, but that's fine
			/* translators: Placeholders: %1$s - payment method title, %2$s - payment account type (savings/checking) (may or may not be available), %3$s - last four digits of the account */
			$message = sprintf( esc_html__( '%1$s Check Transaction Approved: %2$s account ending in %3$s', 'woocommerce-plugin-framework' ), $this->get_method_title(), $order->payment->account_type, $last_four );

			// optional check number
			if ( ! empty( $order->payment->check_number ) ) {
				/* translators: Placeholders: %s - check number */
				$message .= '. ' . sprintf( esc_html__( 'Check number %s', 'woocommerce-plugin-framework' ), $order->payment->check_number );
			}

			// adds the transaction id (if any) to the order note
			if ( $response->get_transaction_id() ) {
				$message .= ' ' . sprintf( esc_html__( '(Transaction ID %s)', 'woocommerce-plugin-framework' ), $response->get_transaction_id() );
			}

			/**
			 * Direct Gateway eCheck Transaction Approved Order Note Filter.
			 *
			 * Allow actors to modify the order note added when an eCheck transaction
			 * is approved.
			 *
			 * @since 4.1.0
			 * @param string $message order note
			 * @param \WC_Order $order order object
			 * @param \SV_WC_Payment_Gateway_API_Response $response transaction response
			 * @param \SV_WC_Payment_Gateway_Direct $this instance
			 */
			$message = apply_filters( 'wc_payment_gateway_' . $this->get_id() . '_check_transaction_approved_order_note', $message, $order, $response, $this );

			$order->add_order_note( $message );

		}

		return $response;

	}


	/**
	 * Performs a credit card transaction for the given order and returns the
	 * result
	 *
	 * @since 1.0.0
	 * @param WC_Order $order the order object
	 * @param SV_WC_Payment_Gateway_API_Response $response optional credit card transaction response
	 * @return SV_WC_Payment_Gateway_API_Response the response
	 * @throws SV_WC_Payment_Gateway_Exception network timeouts, etc
	 */
	protected function do_credit_card_transaction( $order, $response = null ) {

		if ( is_null( $response ) ) {
			if ( $this->perform_credit_card_charge( $order ) ) {
				$response = $this->get_api()->credit_card_charge( $order );
			} else {
				$response = $this->get_api()->credit_card_authorization( $order );
			}
		}

		// success! update order record
		if ( $response->transaction_approved() ) {

			$last_four = substr( $order->payment->account_number, -4 );

			// use direct card type if set, or try to guess it from card number
			if ( ! empty( $order->payment->card_type ) ) {
				$card_type = $order->payment->card_type;
			} elseif ( $first_four = substr( $order->payment->account_number, 0, 4 ) ) {
				$card_type = SV_WC_Payment_Gateway_Helper::card_type_from_account_number( $first_four );
			} else {
				$card_type = 'card';
			}

			// credit card order note
			$message = sprintf(
				/* translators: Placeholders: %1$s - payment method title, %2$s - environment ("Test"), %3$s - transaction type (authorization/charge), %4$s - card type (mastercard, visa, ...), %5$s - last four digits of the card */
				esc_html__( '%1$s %2$s %3$s Approved: %4$s ending in %5$s', 'woocommerce-plugin-framework' ),
				$this->get_method_title(),
				$this->is_test_environment() ? esc_html_x( 'Test', 'noun, software environment', 'woocommerce-plugin-framework' ) : '',
				$this->perform_credit_card_authorization( $order ) ? esc_html_x( 'Authorization', 'credit card transaction type', 'woocommerce-plugin-framework' ) : esc_html_x( 'Charge', 'noun, credit card transaction type', 'woocommerce-plugin-framework' ),
				SV_WC_Payment_Gateway_Helper::payment_type_to_name( $card_type ),
				$last_four
			);

			// add the expiry date if it is available
			if ( ! empty( $order->payment->exp_month ) && ! empty( $order->payment->exp_year ) ) {

				$message .= ' ' . sprintf(
					/** translators: Placeholders: %s - credit card expiry date */
					__( '(expires %s)', 'woocommerce-plugin-framework' ),
					$order->payment->exp_month . '/' . substr( $order->payment->exp_year, -2 )
				);
			}

			// adds the transaction id (if any) to the order note
			if ( $response->get_transaction_id() ) {
				/* translators: Placeholders: %s - transaction ID */
				$message .= ' ' . sprintf( esc_html__( '(Transaction ID %s)', 'woocommerce-plugin-framework' ), $response->get_transaction_id() );
			}

			/**
			 * Direct Gateway Credit Card Transaction Approved Order Note Filter.
			 *
			 * Allow actors to modify the order note added when a Credit Card transaction
			 * is approved.
			 *
			 * @since 4.1.0
			 * @param string $message order note
			 * @param \WC_Order $order order object
			 * @param \SV_WC_Payment_Gateway_API_Response $response transaction response
			 * @param \SV_WC_Payment_Gateway_Direct $this instance
			 */
			$message = apply_filters( 'wc_payment_gateway_' . $this->get_id() . '_credit_card_transaction_approved_order_note', $message, $order, $response, $this );

			$order->add_order_note( $message );

		}

		return $response;

	}


	/**
	 * Create a transaction
	 *
	 * @since 1.0.0
	 * @param WC_Order $order the order object
	 * @return bool true if transaction was successful, false otherwise
	 * @throws SV_WC_Payment_Gateway_Exception network timeouts, etc
	 */
	protected function do_transaction( $order ) {

		// perform the credit card or check transaction
		if ( $this->is_credit_card_gateway() ) {
			$response = $this->do_credit_card_transaction( $order );
		} elseif ( $this->is_echeck_gateway() ) {
			$response = $this->do_check_transaction( $order );
		} else {
			$do_payment_type_transaction = 'do_' . $this->get_payment_type() . '_transaction';
			$response = $this->$do_payment_type_transaction( $order );
		}

		// handle the response
		if ( $response->transaction_approved() || $response->transaction_held() ) {

			if ( $this->supports_tokenization() && 0 != $order->get_user_id() && $this->get_payment_tokens_handler()->should_tokenize() &&
				( $order->payment_total > 0 && ( $this->tokenize_with_sale() || $this->tokenize_after_sale() ) ) ) {

				try {
					$order = $this->get_payment_tokens_handler()->create_token( $order, $response );
				} catch ( SV_WC_Plugin_Exception $e ) {

					// handle the case of a "tokenize-after-sale" request failing by marking the order as on-hold with an explanatory note
					if ( ! $response->transaction_held() && ! ( $this->supports( self::FEATURE_CREDIT_CARD_AUTHORIZATION ) && $this->perform_credit_card_authorization( $order ) ) ) {

						// transaction has already been successful, but we've encountered an issue with the post-tokenization, add an order note to that effect and continue on
						$message = sprintf(
							/* translators: Placeholders: %s - failure message */
							esc_html__( 'Tokenization Request Failed: %s', 'woocommerce-plugin-framework' ),
							$e->getMessage()
						);

						$this->mark_order_as_held( $order, $message, $response );

					} else {

						// transaction has already been successful, but we've encountered an issue with the post-tokenization, add an order note to that effect and continue on
						$message = sprintf(
							/* translators: Placeholders: %1$s - payment method title, %2$s - failure message */
							esc_html__( '%1$s Tokenization Request Failed: %2$s', 'woocommerce-plugin-framework' ),
							$this->get_method_title(),
							$e->getMessage()
						);

						$order->add_order_note( $message );
					}
				}
			}

			// add the standard transaction data
			$this->add_transaction_data( $order, $response );

			// allow the concrete class to add any gateway-specific transaction data to the order
			$this->add_payment_gateway_transaction_data( $order, $response );

			// if the transaction was held (ie fraud validation failure) mark it as such
			// TODO: consider checking whether the response *was* an authorization, rather than blanket-assuming it was because of the settings.  There are times when an auth will be used rather than charge, ie when performing in-plugin AVS handling (moneris)
			if ( $response->transaction_held() || ( $this->supports( self::FEATURE_CREDIT_CARD_AUTHORIZATION ) && $this->perform_credit_card_authorization( $order ) ) ) {
				// TODO: need to make this more flexible, and not force the message to 'Authorization only transaction' for auth transactions (re moneris efraud handling)
				/* translators: This is a message describing that the transaction in question only performed a credit card authorization and did not capture any funds. */
				$this->mark_order_as_held( $order, $this->supports( self::FEATURE_CREDIT_CARD_AUTHORIZATION ) && $this->perform_credit_card_authorization( $order ) ? esc_html__( 'Authorization only transaction', 'woocommerce-plugin-framework' ) : $response->get_status_message(), $response );
			}

			return true;

		} else { // failure

			return $this->do_transaction_failed_result( $order, $response );

		}
	}


	/**
	 * Adds the standard transaction data to the order
	 *
	 * @since 1.0.0
	 * @see SV_WC_Payment_Gateway::add_transaction_data()
	 * @param WC_Order $order the order object
	 * @param SV_WC_Payment_Gateway_API_Response|null $response optional transaction response
	 */
	public function add_transaction_data( $order, $response = null ) {

		// add parent transaction data
		parent::add_transaction_data( $order, $response );

		// payment info
		if ( isset( $order->payment->token ) && $order->payment->token ) {
			$this->update_order_meta( $order, 'payment_token', $order->payment->token );
		}

		// account number
		if ( isset( $order->payment->account_number ) && $order->payment->account_number ) {
			$this->update_order_meta( $order, 'account_four', substr( $order->payment->account_number, -4 ) );
		}

		if ( $this->is_credit_card_gateway() ) {

			// credit card gateway data
			if ( $response && $response instanceof SV_WC_Payment_Gateway_API_Authorization_Response ) {

				if ( $response->get_authorization_code() ) {
					$this->update_order_meta( $order, 'authorization_code', $response->get_authorization_code() );
				}

				if ( $order->payment_total > 0 ) {
					// mark as captured
					if ( $this->perform_credit_card_charge( $order ) ) {
						$captured = 'yes';
					} else {
						$captured = 'no';
					}
					$this->update_order_meta( $order, 'charge_captured', $captured );
				}

			}

			if ( isset( $order->payment->exp_year ) && $order->payment->exp_year && isset( $order->payment->exp_month ) && $order->payment->exp_month ) {
				$this->update_order_meta( $order, 'card_expiry_date', $order->payment->exp_year . '-' . $order->payment->exp_month );
			}

			if ( isset( $order->payment->card_type ) && $order->payment->card_type ) {
				$this->update_order_meta( $order, 'card_type', $order->payment->card_type );
			}

		} elseif ( $this->is_echeck_gateway() ) {

			// checking gateway data

			// optional account type (checking/savings)
			if ( isset( $order->payment->account_type ) && $order->payment->account_type ) {
				$this->update_order_meta( $order, 'account_type', $order->payment->account_type );
			}

			// optional check number
			if ( isset( $order->payment->check_number ) && $order->payment->check_number ) {
				$this->update_order_meta( $order, 'check_number', $order->payment->check_number );
			}
		}
	}


	/** Tokenization **************************************************/


	/**
	 * Initialize payment tokens handler.
	 *
	 * @since 4.3.0
	 */
	protected function init_payment_tokens_handler() {

		$this->payment_tokens_handler = $this->build_payment_tokens_handler();
	}


	/**
	 * Return the Payment Tokens Handler class instance. Concrete classes
	 * can override this method to return a custom implementation.
	 *
	 * @since 4.3.0
	 * @return \SV_WC_Payment_Gateway_Payment_Tokens_Handler
	 */
	protected function build_payment_tokens_handler() {

		return new SV_WC_Payment_Gateway_Payment_Tokens_Handler( $this );
	}


	/**
	 * Get the payment tokens handler instance.
	 *
	 * @since 4.3.0
	 * @return \SV_WC_Payment_Gateway_Payment_Tokens_Handler
	 */
	public function get_payment_tokens_handler() {

		return $this->payment_tokens_handler;
	}


	/**
	 * Returns true if tokenization takes place prior authorization/charge
	 * transaction.
	 *
	 * Defaults to false but can be overridden by child gateway class
	 *
	 * @since 2.1.0
	 * @return boolean true if there is a tokenization request that is issued
	 *         before a authorization/charge transaction
	 */
	public function tokenize_before_sale() {
		return false;
	}


	/**
	 * Returns true if authorization/charge requests also tokenize the payment
	 * method.  False if this gateway has a separate "tokenize" method which
	 * is always used.
	 *
	 * Defaults to false but can be overridden by child gateway class
	 *
	 * @since 2.0.0
	 * @return boolean true if tokenization is combined with sales, false if
	 *         there is a special request for tokenization
	 */
	public function tokenize_with_sale() {
		return false;
	}


	/**
	 * Returns true if tokenization takes place after an authorization/charge
	 * transaction.
	 *
	 * Defaults to false but can be overridden by child gateway class
	 *
	 * @since 2.1.0
	 * @return boolean true if there is a tokenization request that is issued
	 *         after an authorization/charge transaction
	 */
	public function tokenize_after_sale() {
		return false;
	}


	/**
	 * Determine if the gateway supports the admin token editor feature.
	 *
	 * @since 4.3.0
	 * @return boolean
	 */
	public function supports_token_editor() {
		return $this->supports( self::FEATURE_TOKEN_EDITOR );
	}


	/** Integrations Feature **************************************************/


	/**
	 * Initialize supported integrations
	 *
	 * @since 4.1.0
	 */
	public function init_integrations() {

		if ( $this->supports_subscriptions() ) {
			$this->integrations[ self::INTEGRATION_SUBSCRIPTIONS ] = $this->build_subscriptions_integration();
		}

		if ( $this->supports_pre_orders() ) {
			$this->integrations[ self::INTEGRATION_PRE_ORDERS ] = $this->build_pre_orders_integration();
		}

		/**
		 * Payment Gateway Integrations Initialized Action.
		 *
		 * Fired when integrations (Subscriptons/Pre-Orders) have been loaded and
		 * initialized.
		 *
		 * @since 4.1.0
		 * @param \SV_WC_Payment_Gateway_Direct $this instance
		 */
		do_action( 'wc_payment_gateway_' . $this->get_id() . '_init_integrations', $this );
	}


	/**
	 * Return an array of available integration objects
	 *
	 * @since 4.1.0
	 * @return array
	 */
	public function get_integrations() {

		return $this->integrations;
	}


	/**
	 * Get the integration object for the given ID
	 *
	 * @since 4.1.0
	 * @param string $id the integration ID, e.g. subscriptions
	 * @return \SV_WC_Payment_Gateway_Integration|null
	 */
	public function get_integration( $id ) {

		return isset( $this->integrations[ $id ] ) ? $this->integrations[ $id ] : null;
	}


	/**
	 * A factory method to build and return the Subscriptions class instance.
	 * Concrete classes can override this method to return a custom
	 * implementation.
	 *
	 * @since 4.1.0
	 * @return \SV_WC_Payment_Gateway_Integration_Subscriptions
	 */
	protected function build_subscriptions_integration() {

		return new SV_WC_Payment_Gateway_Integration_Subscriptions( $this );
	}


	/**
	 * Get the Subscriptions integration class instance
	 *
	 * @since 4.1.0
	 * @return \SV_WC_Payment_Gateway_Integration_Subscriptions|null
	 */
	public function get_subscriptions_integration() {

		return isset( $this->integrations[ self::INTEGRATION_SUBSCRIPTIONS ] ) ? $this->integrations[ self::INTEGRATION_SUBSCRIPTIONS ] : null;
	}


	/**
	 * A factory method to build and return the Pre-Orders class instance.
	 * Concrete classes can override this method to return a custom
	 * implementation.
	 *
	 * @since 4.1.0
	 * @return \SV_WC_Payment_Gateway_Integration_Pre_Orders
	 */
	protected function build_pre_orders_integration() {

		return new SV_WC_Payment_Gateway_Integration_Pre_Orders( $this );
	}


	/**
	 * Get the Pre-Orders integration class instance
	 *
	 * @since 4.1.0
	 * @return \SV_WC_Payment_Gateway_Integration_Pre_Orders|null
	 */
	public function get_pre_orders_integration() {

		return isset( $this->integrations[ self::INTEGRATION_PRE_ORDERS ] ) ? $this->integrations[ self::INTEGRATION_PRE_ORDERS ] : null;
	}


	/**
	 * A gateway supports Subscriptions if all of the following are true:
	 *
	 * + Subscriptions is active
	 * + tokenization is supported
	 * + tokenization is enabled
	 *
	 * Concrete gateways can override this to conditionally support Subscriptions
	 * based on certain settings (e.g. only when CSC is not required, etc.)
	 *
	 * @since 1.0.0
	 * @return boolean true if the gateway supports subscriptions
	 */
	public function supports_subscriptions() {

		return $this->get_plugin()->is_subscriptions_active() && $this->supports_tokenization() && $this->tokenization_enabled();
	}


	/**
	 * A gateway supports Pre-Orders if all of the following are true:
	 *
	 * + Pre-Orders is active
	 * + tokenization is supported
	 * + tokenization is enabled
	 *
	 * Concrete gateways can override this to conditionally support Pre-Orders
	 * based on certain settings (e.g. only when CSC is not required, etc.)
	 *
	 * @since 1.0.0
	 * @return boolean true if the gateway supports pre-orders
	 */
	public function supports_pre_orders() {

		return $this->get_plugin()->is_pre_orders_active() && $this->supports_tokenization() && $this->tokenization_enabled();
	}


	/** Add Payment Method feature ********************************************/


	/**
	 * Returns true if the gateway supports the add payment method feature
	 *
	 * @since 4.0.0
	 * @return boolean true if the gateway supports add payment method feature
	 */
	public function supports_add_payment_method() {
		return $this->supports( self::FEATURE_ADD_PAYMENT_METHOD );
	}


	/**
	 * Entry method for the Add Payment Method feature flow. Note this is *not*
	 * stubbed in the WC_Payment_Gateway abstract class, but is called if the
	 * gateway declares support for it.
	 *
	 * @since 4.0.0
	 */
	public function add_payment_method() {

		assert( $this->supports_add_payment_method() );

		$order = $this->get_order_for_add_payment_method();

		try {

			$result = $this->do_add_payment_method_transaction( $order );

		} catch ( SV_WC_Plugin_Exception $e ) {

			$result = array(
				/* translators: Placeholders: %s - failure message. Payment method as in a specific credit card, e-check or bank account */
				'message' => sprintf( esc_html__( 'Oops, adding your new payment method failed: %s', 'woocommerce-plugin-framework' ), $e->getMessage() ),
				'success' => false,
			);
		}

		SV_WC_Helper::wc_add_notice( $result['message'], $result['success'] ? 'success' : 'error' );

		// if successful, redirect to the newly added method
		if ( $result['success'] ) {

			$redirect_url = wc_get_account_endpoint_url( 'payment-methods' );

		// otherwise, back to the Add Payment Method page
		} else {

			$redirect_url = wc_get_endpoint_url( 'add-payment-method' );
		}

		wp_safe_redirect( $redirect_url );
		exit();
	}


	/**
	 * Perform the transaction to add the customer's payment method to their
	 * account
	 *
	 * @since 4.0.0
	 * @return array result with success/error message and request status (success/failure)
	 */
	protected function do_add_payment_method_transaction( WC_Order $order ) {

		$response = $this->get_api()->tokenize_payment_method( $order );

		if ( $response->transaction_approved() ) {

			$token = $response->get_payment_token();

			// set the token to the user account
			$this->get_payment_tokens_handler()->add_token( $order->get_user_id(), $token );

			// order note based on gateway type
			if ( $this->is_credit_card_gateway() ) {

				/* translators: Payment method as in a specific credit card. Placeholders: %1$s - card type (visa, mastercard, ...), %2$s - last four digits of the card, %3$s - card expiry date */
				$message = sprintf( esc_html__( 'Nice! New payment method added: %1$s ending in %2$s (expires %3$s)', 'woocommerce-plugin-framework' ),
					$token->get_type_full(),
					$token->get_last_four(),
					$token->get_exp_date()
				);

			} elseif ( $this->is_echeck_gateway() ) {

				// account type (checking/savings) may or may not be available, which is fine
				/* translators: Payment method as in a specific e-check account. Placeholders: %1$s - account type (checking/savings), %2$s - last four digits of the account */
				$message = sprintf( esc_html__( 'Nice! New payment method added: %1$s account ending in %2$s', 'woocommerce-plugin-framework' ),
					$token->get_account_type(),
					$token->get_last_four()
				);

			} else {
				/* translators: Payment method as in a specific credit card, e-check or bank account */
				$message = esc_html__( 'Nice! New payment method added.', 'woocommerce-plugin-framework' );
			}

			// add transaction data to user meta
			$this->add_add_payment_method_transaction_data( $response );

			// add customer data, primarily customer ID to user meta
			$this->add_add_payment_method_customer_data( $order, $response );

			$result = array( 'message' => $message, 'success' => true );

		} else {

			if ( $response->get_status_code() && $response->get_status_message() ) {
				$message = sprintf( 'Status code %s: %s', $response->get_status_code(), $response->get_status_message() );
			} elseif ( $response->get_status_code() ) {
				$message = sprintf( 'Status code: %s', $response->get_status_code() );
			} elseif ( $response->get_status_message() ) {
				$message = sprintf( 'Status message: %s', $response->get_status_message() );
			} else {
				$message = 'Unknown Error';
			}

			$result = array( 'message' => $message, 'success' => false );
		}

		/**
		 * Add Payment Method Transaction Result Filter.
		 *
		 * Filter the result data from an add payment method transaction attempt -- this
		 * can be used to control the notice message displayed and whether the
		 * user is redirected back to the My Account page or remains on the add
		 * new payment method screen
		 *
		 * @since 4.0.0
		 * @param array $result {
		 *   @type string $message notice message to render
		 *   @type bool $success true to redirect to my account, false to stay on page
		 * }
		 * @param \SV_WC_Payment_Gateway_API_Create_Payment_Token_Response $response instance
		 * @param \WC_Order $order order instance
		 * @param \SV_WC_Payment_Gateway_Direct $this direct gateway instance
		 */
		return apply_filters( 'wc_payment_gateway_' . $this->get_id() . '_add_payment_method_transaction_result', $result, $response, $order, $this );
	}


	/**
	 * Creates the order required for adding a new payment method. Note that
	 * a mock order is generated as there is no actual order associated with the
	 * request.
	 *
	 * @since 4.0.0
	 * @return WC_Order generated order object
	 */
	protected function get_order_for_add_payment_method() {

		// mock order, as all gateway API implementations require an order object for tokenization
		$order = new WC_Order( 0 );
		$order = $this->get_order( $order );

		$user = get_userdata( get_current_user_id() );

		$properties = array(
			'currency'    => get_woocommerce_currency(), // default to base store currency
			'customer_id' => $user->ID,
		);

		$defaults = array(
			// billing
			'billing_first_name' => '',
			'billing_last_name'  => '',
			'billing_company'    => '',
			'billing_address_1'  => '',
			'billing_address_2'  => '',
			'billing_city'       => '',
			'billing_postcode'   => '',
			'billing_state'      => '',
			'billing_country'    => '',
			'billing_phone'      => '',
			'billing_email'      => $user->user_email,

			// shipping
			'shipping_first_name' => '',
			'shipping_last_name'  => '',
			'shipping_company'    => '',
			'shipping_address_1'  => '',
			'shipping_address_2'  => '',
			'shipping_city'       => '',
			'shipping_postcode'   => '',
			'shipping_state'      => '',
			'shipping_country'    => '',
		);

		foreach ( $defaults as $prop => $value ) {

			if ( ! empty( $user->$prop ) ) {
				$properties[ $prop ] = $user->$prop;
			}
		}

		$order = SV_WC_Order_Compatibility::set_props( $order, $properties );

		// other default info
		$order->customer_id = $this->get_customer_id( $order->get_user_id() );

		/* translators: Placeholders: %1$s - site title, %2$s - customer email. Payment method as in a specific credit card, e-check or bank account */
		$order->description = sprintf( esc_html__( '%1$s - Add Payment Method for %2$s', 'woocommerce-plugin-framework' ), sanitize_text_field( SV_WC_Helper::get_site_name() ), $properties['billing_email'] );

		// force zero amount
		$order->payment_total = '0.00';

		/**
		 * Direct Gateway Get Order for Add Payment Method Filter.
		 *
		 * Allow actors to modify the order object used for an add payment method
		 * transaction.
		 *
		 * @since 4.0.0
		 * @param \WC_Order $order order object
		 * @param \SV_WC_Payment_Gateway_Direct $this instance
		 */
		return apply_filters( 'wc_payment_gateway_' . $this->get_id() . '_get_order_for_add_payment_method', $order, $this );
	}


	/**
	 * Add customer data as part of the add payment method transaction, primarily
	 * customer ID
	 *
	 * @since 4.0.0
	 * @param WC_Order $order mock order
	 * @param SV_WC_Payment_Gateway_API_Create_Payment_Token_Response $response
	 */
	protected function add_add_payment_method_customer_data( $order, $response ) {

		$user_id = $order->get_user_id();

		// set customer ID from response if available
		if ( $this->supports_customer_id() && method_exists( $response, 'get_customer_id' ) && $response->get_customer_id() ) {

			$order->customer_id = $customer_id = $response->get_customer_id();

		} else {

			// default to the customer ID on "order"
			$customer_id = $order->customer_id;
		}

		// update the user
		if ( 0 != $user_id ) {
			$this->update_customer_id( $user_id, $customer_id );
		}
	}


	/**
	 * Adds data from the add payment method transaction, primarily:
	 *
	 * + transaction ID
	 * + transaction date
	 * + transaction environment
	 *
	 * @since 4.0.0
	 * @param \SV_WC_Payment_Gateway_API_Create_Payment_Token_Response $response
	 */
	protected function add_add_payment_method_transaction_data( $response ) {

		$user_meta_key = '_wc_' . $this->get_id() . '_add_payment_method_transaction_data';

		$data = (array) get_user_meta( get_current_user_id(), $user_meta_key, true );

		$new_data = array(
			'trans_id'    => $response->get_transaction_id() ? $response->get_transaction_id() : null,
			'trans_date'  => current_time( 'mysql' ),
			'environment' => $this->get_environment(),
		);

		$data[] = array_merge( $new_data, $this->get_add_payment_method_payment_gateway_transaction_data( $response ) );

		// only keep the 5 most recent transactions
		if ( count( $data ) > 5 ) {
			array_shift( $data );
		}

		update_user_meta( get_current_user_id(), $user_meta_key, array_filter( $data ) );
	}


	/**
	 * Allow gateway implementations to add additional data to the data saved
	 * during the add payment method transaction
	 *
	 * @since 4.0.0
	 * @param SV_WC_Payment_Gateway_API_Create_Payment_Token_Response $response create payment token response
	 * @return array
	 */
	protected function get_add_payment_method_payment_gateway_transaction_data( $response ) {

		// stub method
		return array();
	}


	/** Getters ******************************************************/


	/**
	 * Returns true if this is a direct type gateway
	 *
	 * @since 1.0.0
	 * @return boolean if this is a direct payment gateway
	 */
	public function is_direct_gateway() {
		return true;
	}


	/**
	 * Returns true if a transaction should be forced (meaning payment
	 * processed even if the order amount is 0).  This is useful mostly for
	 * testing situations
	 *
	 * @since 2.2.0
	 * @return boolean true if the transaction request should be forced
	 */
	public function transaction_forced() {
		return false;
	}

}

endif;  // class exists check
