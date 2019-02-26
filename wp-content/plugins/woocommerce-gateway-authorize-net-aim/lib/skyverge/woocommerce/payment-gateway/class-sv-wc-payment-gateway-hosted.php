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

if ( ! class_exists( 'SV_WC_Payment_Gateway_Hosted' ) ) :

/**
 * # WooCommerce Payment Gateway Framework Hosted Gateway
 *
 * Implement the following methods:
 *
 * + `get_hosted_pay_page_url()` - Return the hosted pay page url
 * + `get_hosted_pay_page_params()` - Return any hosted pay page parameters (optional)
 * + `get_transaction_response()` - Return the transaction response object on redirect-back/IPN
 *
 * @since 1.0.0
 */
abstract class SV_WC_Payment_Gateway_Hosted extends SV_WC_Payment_Gateway {


	/** @var string the WC API url, used for the IPN and/or redirect-back handler */
	protected $transaction_response_handler_url;


	/**
	 * Initialize the gateway
	 *
	 * See parent constructor for full method documentation
	 *
	 * @since 2.1.0
	 * @see SV_WC_Payment_Gateway::__construct()
	 * @param string $id the gateway id
	 * @param SV_WC_Payment_Gateway_Plugin $plugin the parent plugin class
	 * @param array $args gateway arguments
	 */
	public function __construct( $id, $plugin, $args ) {

		// parent constructor
		parent::__construct( $id, $plugin, $args );

		// payment notification listener hook
		if ( ! has_action( 'woocommerce_api_' . strtolower( get_class( $this ) ), array( $this, 'handle_transaction_response_request' ) ) ) {
			add_action( 'woocommerce_api_' . strtolower( get_class( $this ) ), array( $this, 'handle_transaction_response_request' ) );
		}
	}


	/**
	 * Display the payment fields on the checkout page
	 *
	 * @since 1.0.0
	 * @see WC_Payment_Gateway::payment_fields()
	 */
	public function payment_fields() {

		parent::payment_fields();
		?><style type="text/css">#payment ul.payment_methods li label[for='payment_method_<?php echo $this->get_id(); ?>'] img:nth-child(n+2) { margin-left:1px; }</style><?php
	}


	/**
	 * Process the payment by redirecting customer to the WooCommerce pay page
	 * or the gatway hosted pay page
	 *
	 * @since 1.0.0
	 * @see WC_Payment_Gateway::process_payment()
	 * @param int $order_id the order to process
	 * @return array with keys 'result' and 'redirect'
	 * @throws \SV_WC_Payment_Gateway_Exception if payment processing must be halted, and a message displayed to the customer
	 */
	public function process_payment( $order_id ) {

		$payment_url = $this->get_payment_url( $order_id );

		if ( ! $payment_url ) {
			// be sure to have either set a notice via `wc_add_notice` to be
			// displayed, or have thrown an exception with a message
			return array( 'result' => 'failure' );
		}

		WC()->cart->empty_cart();

		return array(
			'result'   => 'success',
			'redirect' => $payment_url,
		);
	}


	/**
	 * Gets the payment URL: the checkout pay page
	 *
	 * @since 2.1.0
	 * @param int $order_id the order id
	 * @return string the payment URL, or false if unavailable
	 */
	protected function get_payment_url( $order_id ) {

		if ( $this->use_form_post() ) {
			// the checkout pay page
			$order = wc_get_order( $order_id );
			return $order->get_checkout_payment_url( true );
		} else {

			// setup the order object
			$order = $this->get_order( $order_id );

			// direct-redirect, so append the hosted pay page params to the hosted pay page url
			$pay_page_url = $this->get_hosted_pay_page_url( $order );

			if ( $pay_page_url ) {
				return add_query_arg( $this->get_hosted_pay_page_params( $order ), $pay_page_url );
			}
		}

		return false;
	}


	/**
	 * Render the payment page for gateways that use a form post method
	 *
	 * @since 2.1.0
	 * @see SV_WC_Payment_Gateway::payment_page()
	 * @see SV_WC_Payment_Gateway_Hosted::use_form_post()
	 * @see SV_WC_Payment_Gateway_Hosted::add_pay_page_handler()
	 * @param int $order_id identifies the order
	 */
	public function payment_page( $order_id ) {

		if ( ! $this->use_form_post() ) {
			// default behavior: pay page is not used, direct-redirect from checkout
			parent::payment_page( $order_id );
		} else {
			$this->generate_pay_form( $order_id );
		}
	}


	/**
	 * Generates the POST pay form.  Some inline javascript will attempt to
	 * auto-submit this pay form, so as to make the checkout process as
	 * seamless as possile
	 *
	 * @since 2.1.0
	 * @param int $order_id the order identifier
	 */
	public function generate_pay_form( $order_id ) {

		// setup the order object
		$order = $this->get_order( $order_id );

		$request_params = $this->get_hosted_pay_page_params( $order );

		// standardized request data, for logging purposes
		$request = array(
			'method' => 'POST',
			'uri'    => $this->get_hosted_pay_page_url( $order ),
			'body'   => print_r( $request_params, true ),
		);

		// log the request
		$this->log_hosted_pay_page_request( $request );

		// render the appropriate content
		if ( $this->use_auto_form_post() ) {
			$this->render_auto_post_form( $order, $request_params );
		} else {
			$this->render_pay_page_form( $order, $request_params );
		}
	}


	/**
	 * Renders the gateway pay page direct post form.  This is used by gateways
	 * that collect some or all payment information on-site, and POST the
	 * entered information to a remote server for processing
	 *
	 * @since 2.2.0
	 * @see SV_WC_Payment_Gateway_Hosted::use_auto_form_post()
	 * @param WC_Order $order the order object
	 * @param array $request_params associative array of request parameters
	 */
	public function render_pay_page_form( $order, $request_params ) {
		// implemented by concrete class
	}


	/**
	 * Renders the gateway auto post form.  This is used for gateways that
	 * collect no payment information on-site, but must POST parameters to a
	 * hosted payment page where payment information is entered.
	 *
	 * @since 2.2.0
	 * @see SV_WC_Payment_Gateway_Hosted::use_auto_form_post()
	 * @param WC_Order $order the order object
	 * @param array $request_params associative array of request parameters
	 */
	public function render_auto_post_form( WC_Order $order, $request_params ) {

		$args = $this->get_auto_post_form_args( $order );

		// attempt to automatically submit the form and redirect
		wc_enqueue_js('
			$( "body" ).block( {
					message: "<img src=\"' . esc_url( $this->get_plugin()->get_framework_assets_url() . '/images/ajax-loader.gif' ) . '\" alt=\"Redirecting&hellip;\" style=\"float:left; margin-right: 10px;\" />' . esc_html( $args['thanks_message'] ) . '",
					overlayCSS: {
						background: "#fff",
						opacity: 0.6
					},
					css: {
						padding:         20,
						textAlign:       "center",
						color:           "#555",
						border:          "3px solid #aaa",
						backgroundColor: "#fff",
						cursor:          "wait",
						lineHeight:      "32px"
					}
				} );

			$( "#submit_' . $this->get_id() . '_payment_form" ).click();
		');

		echo '<p>' . esc_html( $args['message'] ) . '</p>';
		echo '<form action="' . esc_url( $args['submit_url'] ) . '" method="post">';

			// Output the param inputs
			echo $this->get_auto_post_form_params_html( $request_params );

			echo '<input type="submit" class="button alt button-alt" id="submit_' . $this->get_id() . '_payment_form" value="' . esc_attr( $args['button_text'] ) . '" />';
			echo '<a class="button cancel" href="' . esc_url( $args['cancel_url'] ) . '">' . esc_html( $args['cancel_text'] ) . '</a>';

		echo '</form>';
	}


	/**
	 * Get the auto post form display arguments.
	 *
	 * @since 4.3.0
	 * @see SV_WC_Payment_Gateway_Hosted::render_auto_post_form() for args
	 * @param \WC_Order $order the order object
	 * @return array
	 */
	protected function get_auto_post_form_args( WC_Order $order ) {

		$args = array(
			'submit_url'     => $this->get_hosted_pay_page_url( $order ),
			'cancel_url'     => $order->get_cancel_order_url(),
			'message'        => __( 'Thank you for your order, please click the button below to pay.', 'woocommerce-plugin-framework' ),
			'thanks_message' => __( 'Thank you for your order. We are now redirecting you to complete payment.', 'woocommerce-plugin-framework' ),
			'button_text'    => __( 'Pay Now', 'woocommerce-plugin-framework' ),
			'cancel_text'    => __( 'Cancel Order', 'woocommerce-plugin-framework' ),
		);

		/**
		 * Filter the auto post form display arguments.
		 *
		 * @since 4.3.0
		 * @param array $args {
		 *     The form display arguments.
		 *
		 *     @type string $submit_url     Form submit URL
		 *     @type string $cancel_url     Cancel payment URL
		 *     @type string $message        The message before the form
		 *     @type string $thanks_message The message displayed when the form is submitted
		 *     @type string $button_text    Submit button text
		 *     @type string $cancel_text    Cancel link text
		 * }
		 * @param \WC_Order $order the order object
		 */
		return (array) apply_filters( 'wc_payment_gateway_' . $this->get_id() . '_auto_post_form_args', $args, $order );
	}


	/**
	 * Get the auto post form params HTML.
	 *
	 * This can be overridden by concrete gateways to support more complex param arrays.
	 *
	 * @since 4.3.0
	 * @param array $request_params The request params
	 * @return string
	 */
	protected function get_auto_post_form_params_html( $request_params = array() ) {

		$html = '';

		foreach ( $request_params as $key => $value ) {

			foreach ( (array) $value as $field_value ) {
				$html .= '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $field_value ) . '" />';
			}
		}

		return $html;
	}


	/**
	 * Returns the gateway hosted pay page parameters, if any
	 *
	 * @since 2.1.0
	 * @param WC_Order $order the order object
	 * @return array associative array of name-value parameters
	 */
	protected function get_hosted_pay_page_params( $order ) {
		// stub method
		return array();
	}


	/**
	 * Gets the hosted pay page url to redirect to, to allow the customer to
	 * remit payment.  This is generally the bare URL, without any query params.
	 *
	 * This method may be called more than once during a single request.
	 *
	 * @since 2.1.0
	 * @see SV_WC_Payment_Gateway_Hosted::get_hosted_pay_page_params()
	 * @param WC_Order $order optional order object, defaults to null
	 * @return string hosted pay page url, or false if it could not be determined
	 */
	abstract public function get_hosted_pay_page_url( $order = null );


	/**
	 * Handle a payment notification request.
	 *
	 * @since 4.3.0
	 */
	public function handle_transaction_response_request() {

		// log the request
		$this->log_transaction_response_request( $_REQUEST );

		$order = null;

		try {

			// get the transaction response object for the current request
			$response = $this->get_transaction_response( $_REQUEST );

			// get the associated order, or die trying
			$order = $response->get_order();

			// Validate the response data such as order ID and payment status
			$this->validate_transaction_response( $order, $response );

			// Handle the order based on the response
			$this->process_transaction_response( $order, $response );

		} catch ( SV_WC_Payment_Gateway_Exception $e ) {

			if ( $order && $order->needs_payment() ) {
				$this->mark_order_as_failed( $order, $e->getMessage(), $response );
			}

			if ( $this->debug_log() ) {

				$this->get_plugin()->log(
					/* translators: Placeholders: %1$s - transaction request type such as IPN or Redirect-back, %2$s - the error message */
					sprintf( '%1$s processing error: %2$s',
					( $response->is_ipn() ) ? 'IPN' : 'Redirect-back',
					$e->getMessage()
				), $this->get_id() );
			}

			$this->do_invalid_transaction_response( $order, $response );
		}
	}


	/**
	 * Validate a transaction response.
	 *
	 * @since 4.3.0
	 * @param \WC_Order $order the order object
	 * @param \SV_WC_Payment_Gateway_API_Payment_Notification_Response the response object
	 * @throws \SV_WC_Payment_Gateway_Exception
	 */
	protected function validate_transaction_response( $order, $response ) {

		// If the order is invalid, bail
		if ( ! $order || ! SV_WC_Order_Compatibility::get_prop( $order, 'id' ) ) {

			throw new SV_WC_Payment_Gateway_Exception( sprintf(
				__( 'Could not find order %s', 'woocommerce-plugin-framework' ),
				$response->get_order_id()
			) );
		}

		// If the order has already been completed, bail
		if ( ! $order->needs_payment() ) {

			/* translators: Placeholders: %s - payment gateway title (such as Authorize.net, Braintree, etc) */
			$order->add_order_note( sprintf( esc_html__( '%s duplicate transaction received', 'woocommerce-plugin-framework' ), $this->get_method_title() ) );

			throw new SV_WC_Payment_Gateway_Exception( sprintf(
				__( 'Order %s is already paid for.', 'woocommerce-plugin-framework' ),
				$order->get_order_number()
			) );
		}
	}


	/**
	 * Process the transaction response for the given order
	 *
	 * @since 2.1.0
	 * @param WC_Order $order the order
	 * @param SV_WC_Payment_Gateway_API_Payment_Notification_Response transaction response
	 * @return boolean true if transaction did not fail, false otherwise
	 */
	protected function process_transaction_response( $order, $response ) {

		if ( $response->transaction_approved() || $response->transaction_held() ) {

			// Always add transasaction data to the order for approved and held transactions
			$this->add_transaction_data( $order, $response );
			$this->add_payment_gateway_transaction_data( $order, $response );

			// If approved, payment is complete
			if ( $response->transaction_approved() ) {

				// determine whether we should complete payment or set to on-hold for later capture
				if ( $this->supports( self::FEATURE_CREDIT_CARD_AUTHORIZATION ) && $this->perform_credit_card_authorization( $order ) ) {

					$this->mark_order_as_held( $order, __( 'Authorization only transaction', 'woocommerce-plugin-framework' ), $response );
					SV_WC_Order_Compatibility::reduce_stock_levels( $order ); // reduce stock for held orders, but don't complete payment

				} else {

					$order->payment_complete(); // mark order as having received payment
				}

				if ( self::PAYMENT_TYPE_CREDIT_CARD == $response->get_payment_type() ) {
					$this->do_credit_card_transaction_approved( $order, $response );
				} elseif ( self::PAYMENT_TYPE_ECHECK == $response->get_payment_type() ) {
					$this->do_check_transaction_approved( $order, $response );
				} else {
					$this->do_transaction_approved( $order, $response );
				}

			// Otherwise, if the transaction was held (ie fraud validation failure) mark it as such and reduce stock
			} elseif ( $response->transaction_held() ) {

				$this->mark_order_as_held( $order, $response->get_status_message(), $response );

				SV_WC_Order_Compatibility::reduce_stock_levels( $order );

				$this->do_transaction_held( $order, $response );
			}

		} elseif ( $response->transaction_cancelled() ) {

			$this->mark_order_as_cancelled( $order, $response->get_status_message(), $response );

			$this->do_transaction_cancelled( $order, $response );

		} else { // failure

			// Add the order note and debug info
			$this->do_transaction_failed_result( $order, $response );

			$this->do_transaction_failed( $order, $response );
		}
	}


	/**
	 * Adds the standard transaction data to the order
	 *
	 * @since 2.2.0
	 * @see SV_WC_Payment_Gateway::add_transaction_data()
	 * @param WC_Order $order the order object
	 * @param SV_WC_Payment_Gateway_API_Response|null $response optional transaction response
	 */
	public function add_transaction_data( $order, $response = null ) {

		// add parent transaction data
		parent::add_transaction_data( $order, $response );

		// account number
		if ( $response->get_account_number() ) {
			$this->update_order_meta( $order, 'account_four', substr( $response->get_account_number(), -4 ) );
		}

		if ( self::PAYMENT_TYPE_CREDIT_CARD == $response->get_payment_type() ) {

			if ( $response->get_authorization_code() ) {
				$this->update_order_meta( $order, 'authorization_code', $response->get_authorization_code() );
			}

			if ( $order->get_total() > 0 ) {
				// mark as captured
				if ( $response->is_charge() ) {
					$captured = 'yes';
				} else {
					$captured = 'no';
				}
				$this->update_order_meta( $order, 'charge_captured', $captured );
			}

			if ( $response->get_exp_month() && $response->get_exp_year() ) {
				$this->update_order_meta( $order, 'card_expiry_date', $response->get_exp_year() . '-' . $response->get_exp_month() );
			}

			if ( $response->get_card_type() ) {
				$this->update_order_meta( $order, 'card_type', $response->get_card_type() );
			}

		} elseif ( self::PAYMENT_TYPE_ECHECK == $response->get_payment_type() ) {

			// optional account type (checking/savings)
			if ( $response->get_account_type() ) {
				$this->update_order_meta( $order, 'account_type', $response->get_account_type() );
			}

			// optional check number
			if ( $response->get_check_number() ) {
				$this->update_order_meta( $order, 'check_number', $response->get_check_number() );
			}
		}
	}


	/**
	 * Adds an order note, along with anything else required after an approved
	 * credit card transaction
	 *
	 * @since 2.2.0
	 * @param WC_Order $order the order
	 * @param SV_WC_Payment_Gateway_API_Payment_Notification_Credit_Card_Response transaction response
	 */
	protected function do_credit_card_transaction_approved( $order, $response ) {

		$note = '';

		// Add the card type and last four digits, if available
		if ( $response->get_account_number() ) {

			$note .= ': ' . sprintf(
				__( '%1$s ending in %2$s', 'woocommerce-plugin-framework' ),
				SV_WC_Payment_Gateway_Helper::payment_type_to_name( ( $response->get_card_type() ? $response->get_card_type() : 'card' ) ),
				substr( $response->get_account_number(), -4 )
			);

			// Add the expiration date, if available
			if ( $response->get_exp_month() && $response->get_exp_year() ) {
				$note .= ' ' . sprintf( __( '(expires %s)', 'woocommerce-plugin-framework' ), $response->get_exp_month() . '/' . substr( $response->get_exp_year(), -2 ) );
			}
		}

		// Set the specific credit card args
		$note_args = array(
			'method_type'     => self::PAYMENT_TYPE_CREDIT_CARD,
			'additional_note' => $note,
			'transaction_id'  => $response->get_transaction_id(),
		);

		// Set the transaction type
		if ( $response->is_authorization() ) {
			$note_args['transaction_type'] = _x( 'Authorization', 'credit card transaction type', 'woocommerce-plugin-framework' );
		} elseif ( $response->is_charge() ) {
			$note_args['transaction_type'] = _x( 'Charge', 'noun, credit card transaction type', 'woocommerce-plugin-framework' );
		}

		$this->do_transaction_approved( $order, $response, $note_args );
	}


	/**
	 * Adds an order note, along with anything else required after an approved
	 * echeck transaction
	 *
	 * @since 2.2.0
	 * @param WC_Order $order the order
	 * @param SV_WC_Payment_Gateway_API_Payment_Notification_Response transaction response
	 */
	protected function do_check_transaction_approved( $order, $response ) {

		$note = '';

		// Add the check type and last four digits, if available
		if ( $response->get_account_number() ) {

			$note .= ': ' . sprintf(
				__( '%1$s ending in %2$s', 'woocommerce-plugin-framework' ),
				SV_WC_Payment_Gateway_Helper::payment_type_to_name( ( $response->get_account_type() ? $response->get_account_type() : 'bank' ) ),
				substr( $response->get_account_number(), -4 )
			);
		}

		// Add the check number, if available
		if ( $response->get_check_number() ) {
			$note .= ' ' . sprintf( __( '(check number %s)', 'woocommerce-plugin-framework' ), $response->get_check_number() );
		}

		$this->do_transaction_approved( $order, $response, array(
			'method_type'     => self::PAYMENT_TYPE_ECHECK,
			'additional_note' => $note,
			'transaction_id'  => $response->get_transaction_id(),
		) );
	}


	/**
	 * Adds an order note, along with anything else required after an approved
	 * transaction.  This is a generic, default approved handler.
	 *
	 * @since 2.1.0
	 * @param \WC_Order $order the order object
	 * @param \WC_Paytrail_API_Payment_Response $response the response object
	 * @param array $note_args Optional. The order note arguments. @see `SV_WC_Payment_Gateway_Hosted::add_transaction_approved_order_note()`
	 */
	protected function do_transaction_approved( WC_Order $order, $response, $note_args = array() ) {

		// Add the order note
		$this->add_transaction_approved_order_note( $order, $note_args );

		// Die or redirect
		if ( $response->is_ipn() ) {

			status_header( 200 );
			die;

		} else {

			wp_redirect( $this->get_return_url( $order ) );
			exit;
		}
	}


	/**
	 * Add an order note with the approved transaction information.
	 *
	 * @since 4.3.0
	 * @param \WC_Order $order The order object
	 * @param array $args {
	 *     Optional. The order note options.
	 *
	 *     @type string $method_title       Payment method title
	 *     @type string $method_type        Payment method type, like credit-card or check
	 *     @type string $transaction_id     The transaction ID
	 *     @type string $transaction_type   Transaction type name for display
	 *     @type string $transaction_result Transaction result for display, like "Approved" or "Completed"
	 *     @type string $environment_name   The environment name, like Test
	 *     @type string $additional_note    Additional text to append to the transaction note
	 * }
	 */
	protected function add_transaction_approved_order_note( $order, $args = array() ) {

		$args = wp_parse_args( $args, array(
			'method_title'       => $this->get_method_title(),
			'method_type'        => '',
			'transaction_id'     => '',
			'transaction_type'   => __( 'Transaction', 'woocommerce-plugin-framework' ),
			'transaction_result' => __( 'Approved', 'woocommerce-plugin-framework' ),
			'environment_name'   => ( $this->is_test_environment() ) ? _x( 'Test', 'noun, software environment', 'woocommerce-plugin-framework' ) : '',
			'additional_note'    => '',
		) );

		// Build the order note
		$note = implode( ' ', array(
			$args['method_title'],
			$args['environment_name'],
			$args['transaction_type'],
			$args['transaction_result']
		) );

		// Add the additional information, if available
		if ( $args['additional_note'] ) {
			$note .= $args['additional_note'];
		}

		// Add the transaction ID, if available
		if ( $args['transaction_id'] ) {
			$note .= ' ' . sprintf( __( '(Transaction ID %s)', 'woocommerce-plugin-framework' ), $args['transaction_id'] );
		}

		if ( $args['method_type'] ) {

			/**
			 * Filter the note added to an order when a transaction is approved for a specific payment type.
			 *
			 * @since 4.3.0
			 * @param string $note The note text
			 * @param \WC_Order $order The order object
			 */
			$note = apply_filters( 'wc_payment_gateway_' . $this->get_id() . '_' . $args['method_type'] . '_transaction_approved_order_note', $note, $order );
		}

		/**
		 * Filter the note added to an order when a transaction is approved.
		 *
		 * @since 4.3.0
		 * @param string $note The note text
		 * @param \WC_Order $order The order object
		 */
		$note = apply_filters( 'wc_payment_gateway_' . $this->get_id() . '_transaction_approved_order_note', $note, $order );

		$order->add_order_note( $note );
	}


	/**
	 * Handle a held transaction response.
	 *
	 * @since 4.3.0
	 * @param \WC_Order $order the order object
	 * @param \SV_WC_Payment_Gateway_API_Payment_Notification_Response $response the response object
	 */
	protected function do_transaction_held( WC_Order $order, $response ) {

		if ( $response->is_ipn() ) {

			status_header( 200 );
			die;

		} else {

			wp_redirect( $order->get_return_url() );
			exit;
		}
	}


	/**
	 * Handle a cancelled transaction response.
	 *
	 * @since 4.3.0
	 * @param \WC_Order $order the order object
	 * @param \SV_WC_Payment_Gateway_API_Payment_Notification_Response $response the response object
	 */
	protected function do_transaction_cancelled( WC_Order $order, $response ) {

		if ( $response->is_ipn() ) {

			status_header( 200 );
			die;

		} else {

			wp_redirect( $order->get_cancel_order_url() );
			exit;
		}
	}


	/**
	 * Handle a failed transaction response.
	 *
	 * @since 4.3.0
	 * @param \WC_Order $order the order object
	 * @param \SV_WC_Payment_Gateway_API_Payment_Notification_Response $response the response object
	 */
	protected function do_transaction_failed( WC_Order $order, $response ) {

		if ( $response->is_ipn() ) {

			status_header( 200 );
			die;

		} else {

			wp_redirect( $order->get_checkout_payment_url( $this->use_form_post() && ! $this->use_auto_form_post() ) );
			exit;
		}
	}


	/**
	 * Handle an invalid transaction response.
	 *
	 * i.e. the order has already been paid or was not found
	 *
	 * @since 4.3.0
	 * @param \WC_Order $order Optional. The order object
	 * @param \SV_WC_Payment_Gateway_API_Payment_Notification_Response $response the response object
	 */
	protected function do_invalid_transaction_response( $order = null, $response ) {

		if ( $response->is_ipn() ) {

			status_header( 200 );
			die();

		} else {

			if ( $order ) {
				wp_redirect( $this->get_return_url( $order ) );
				exit;
			} else {
				wp_redirect( get_home_url( null, '' ) );
				exit;
			}
		}
	}


	/**
	 * Returns an API response object for the current response request
	 *
	 * @since 2.1.0
	 * @param array $request_response_data the current request response data
	 * @return SV_WC_Payment_Gateway_API_Payment_Notification_Response the response object
	 */
	abstract protected function get_transaction_response( $request_response_data );


	/** Helper methods ******************************************************/


	/**
	 * Returns the WC API URL for this gateway, based on the current protocol
	 *
	 * @since 2.1.0
	 * @return string the WC API URL for this server
	 */
	public function get_transaction_response_handler_url() {

		if ( $this->transaction_response_handler_url ) {
			return $this->transaction_response_handler_url;
		}

		$this->transaction_response_handler_url = add_query_arg( 'wc-api', get_class( $this ), home_url( '/' ) );

		// make ssl if needed
		if ( wc_checkout_is_https() ) {
			$this->transaction_response_handler_url = str_replace( 'http:', 'https:', $this->transaction_response_handler_url );
		}

		return $this->transaction_response_handler_url;
	}


	/**
	 * Returns true if currently doing a transaction response request
	 *
	 * @since 2.1.0
	 * @return boolean true if currently doing a transaction response request
	 */
	public function doing_transaction_response_handler() {
		return isset( $_REQUEST['wc-api'] ) && get_class( $this ) == $_REQUEST['wc-api'];
	}


	/**
	 * Log pay page form submission request
	 *
	 * @since 2.1.0
	 * @param array $request the request data associative array, which should
	 *        include members 'method', 'uri', 'body'
	 * @param object $response optional response object
	 */
	public function log_hosted_pay_page_request( $request ) {

		$this->add_debug_message(
			sprintf( "Request Method: %s\nRequest URI: %s\nRequest Body: %s",
				$request['method'],
				$request['uri'],
				$request['body']
			),
			'message'
		);
	}


	/**
	 * Log IPN/redirect-back transaction response request to the log file
	 *
	 * @since 2.1.0
	 * @param array $response the request data
	 * @param string $message optional message string with a %s to hold the
	 *        response data.  Defaults to 'Request %s'
	 * $response
	 */
	public function log_transaction_response_request( $response, $message = null ) {

		// add log message to WC logger if log/both is enabled
		if ( $this->debug_log() ) {

			// if a message wasn't provided, make our best effort
			if ( is_null( $message ) ) {
				$message = 'Request: %s';
			}

			$this->get_plugin()->log( sprintf( $message, print_r( $response, true ) ), $this->get_id() );
		}
	}


	/** Getters ******************************************************/


	/**
	 * Returns true if this is a hosted type gateway
	 *
	 * @since 1.0.0
	 * @return boolean true if this is a hosted payment gateway
	 */
	public function is_hosted_gateway() {
		return true;
	}


	/**
	 * Returns true if this gateway uses a form-post from the pay
	 * page to "redirect" to a hosted payment page
	 *
	 * @since 2.1.0
	 * @return boolean true if this gateway uses a form post, false if it
	 *         redirects directly to the hosted pay page from checkout
	 */
	public function use_form_post() {
		return false;
	}


	/**
	 * Returns true if this gateway uses an automatic form-post from the pay
	 * page to "redirect" to the hosted payment page where payment information
	 * is securely entered.  Return false if payment information is collected
	 * on the pay page and then posted to a remote server.
	 *
	 * This method has no effect if use_form_post() returns false
	 *
	 * @since 2.2.0
	 * @see SV_WC_Payment_Gateway_Hosted::use_form_post()
	 * @return boolean true if this gateway automatically posts to the remote
	 *         processor server from the pay page
	 */
	public function use_auto_form_post() {
		return $this->use_form_post() && true;
	}

}

endif;  // class exists check
