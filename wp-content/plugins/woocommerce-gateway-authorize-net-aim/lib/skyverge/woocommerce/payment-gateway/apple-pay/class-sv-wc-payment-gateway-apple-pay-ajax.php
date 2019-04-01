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

if ( ! class_exists( 'SV_WC_Payment_Gateway_Apple_Pay_AJAX' ) ) :

/**
 * The Apple Pay AJAX handler.
 *
 * @since 4.7.0
 */
class SV_WC_Payment_Gateway_Apple_Pay_AJAX {


	/** @var \SV_WC_Payment_Gateway_Apple_Pay $handler the Apple Pay handler instance */
	protected $handler;


	/**
	 * Constructs the class.
	 *
	 * @since 4.7.0
	 *
	 * @param \SV_WC_Payment_Gateway_Apple_Pay $handler the Apple Pay handler instance
	 */
	public function __construct( SV_WC_Payment_Gateway_Apple_Pay $handler ) {

		$this->handler = $handler;

		if ( $this->get_handler()->is_available() ) {

			add_action( 'wp_ajax_sv_wc_apple_pay_get_payment_request',        array( $this, 'get_payment_request' ) );
			add_action( 'wp_ajax_nopriv_sv_wc_apple_pay_get_payment_request', array( $this, 'get_payment_request' ) );

			// validate the merchant
			add_action( 'wp_ajax_sv_wc_apple_pay_validate_merchant',        array( $this, 'validate_merchant' ) );
			add_action( 'wp_ajax_nopriv_sv_wc_apple_pay_validate_merchant', array( $this, 'validate_merchant' ) );

			// recalculate the payment request totals
			add_action( 'wp_ajax_sv_wc_apple_pay_recalculate_totals',        array( $this, 'recalculate_totals' ) );
			add_action( 'wp_ajax_nopriv_sv_wc_apple_pay_recalculate_totals', array( $this, 'recalculate_totals' ) );

			// process the payment
			add_action( 'wp_ajax_sv_wc_apple_pay_process_payment',        array( $this, 'process_payment' ) );
			add_action( 'wp_ajax_nopriv_sv_wc_apple_pay_process_payment', array( $this, 'process_payment' ) );
		}
	}


	/**
	 * Gets a payment request for the specified type.
	 *
	 * @internal
	 *
	 * @since 4.7.0
	 */
	public function get_payment_request() {

		$this->get_handler()->log( 'Getting payment request' );

		try {

			$request = $this->get_handler()->get_cart_payment_request( WC()->cart );

			$this->get_handler()->log( "Payment Request:\n" . print_r( $request, true ) );

			wp_send_json_success( json_encode( $request ) );

		} catch ( SV_WC_Payment_Gateway_Exception $e ) {

			$this->get_handler()->log( 'Could not build payment request. ' . $e->getMessage() );

			wp_send_json_error( array(
				'message' => $e->getMessage(),
				'code'    => $e->getCode(),
			) );
		}
	}


	/**
	 * Validates the merchant.
	 *
	 * @internal
	 *
	 * @since 4.7.0
	 */
	public function validate_merchant() {

		$this->get_handler()->log( 'Validating merchant' );

		check_ajax_referer( 'sv_wc_apple_pay_validate_merchant', 'nonce' );

		$merchant_id = SV_WC_Helper::get_post( 'merchant_id' );
		$url         = SV_WC_Helper::get_post( 'url' );

		try {

			$response = $this->get_handler()->get_api()->validate_merchant( $url, $merchant_id, home_url(), get_bloginfo( 'name' ) );

			wp_send_json_success( $response->get_merchant_session() );

		} catch ( SV_WC_API_Exception $e ) {

			$this->get_handler()->log( 'Could not validate merchant. ' . $e->getMessage() );

			wp_send_json_error( array(
				'message' => $e->getMessage(),
				'code'    => $e->getCode(),
			) );
		}
	}


	/**
	 * Recalculates the totals for the current payment request.
	 *
	 * @internal
	 *
	 * @since 4.7.0
	 */
	public function recalculate_totals() {

		$this->get_handler()->log( 'Recalculating totals' );

		check_ajax_referer( 'sv_wc_apple_pay_recalculate_totals', 'nonce' );

		try {

			// if a contact is passed, set the customer address data
			if ( isset( $_REQUEST['contact'] ) && is_array( $_REQUEST['contact'] ) ) {

				$contact = wp_parse_args( $_REQUEST['contact'], array(
					'administrativeArea' => null,
					'countryCode'        => null,
					'locality'           => null,
					'postalCode'         => null,
				) );

				$state    = $contact['administrativeArea'];
				$country  = strtoupper( $contact['countryCode'] );
				$city     = $contact['locality'];
				$postcode = $contact['postalCode'];

				WC()->customer->set_shipping_city( $city );
				WC()->customer->set_shipping_state( $state );
				WC()->customer->set_shipping_country( $country );
				WC()->customer->set_shipping_postcode( $postcode );

				if ( $country ) {

					if ( SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ) {
						WC()->customer->set_calculated_shipping( true );
					} else {
						WC()->customer->calculated_shipping( true );
					}
				}
			}

			$chosen_shipping_methods = ( $method = SV_WC_Helper::get_request( 'method' ) ) ? array( wc_clean( $method ) ) : array();

			WC()->session->set( 'chosen_shipping_methods', $chosen_shipping_methods );

			$payment_request = $this->get_handler()->recalculate_totals();

			$data = array(
				'shipping_methods' => $payment_request['shippingMethods'],
				'line_items'       => array_values( $payment_request['lineItems'] ),
				'total'            => $payment_request['total'],
			);

			$this->get_handler()->log( "New totals:\n" . print_r( $data, true ) );

			wp_send_json_success( $data );

		} catch ( SV_WC_Payment_Gateway_Exception $e ) {

			$this->get_handler()->log( $e->getMessage() );

			wp_send_json_error( array(
				'message' => $e->getMessage(),
				'code'    => $e->getCode(),
			) );
		}
	}


	/**
	 * Processes the payment after the Apple Pay authorization.
	 *
	 * @internal
	 *
	 * @since 4.7.0
	 */
	public function process_payment() {

		$this->get_handler()->log( 'Processing payment' );

		$type     = SV_WC_Helper::get_post( 'type' );
		$response = stripslashes( SV_WC_Helper::get_post( 'payment' ) );

		$this->get_handler()->store_payment_response( $response );

		try {

			$result = $this->get_handler()->process_payment( $type, $response );

			wp_send_json_success( $result );

		} catch ( SV_WC_Payment_Gateway_Exception $e ) {

			$this->get_handler()->log( 'Payment failed. ' . $e->getMessage() );

			wp_send_json_error( array(
				'message' => $e->getMessage(),
				'code'    => $e->getCode(),
			) );
		}
	}


	/**
	 * Gets the Apple Pay handler instance.
	 *
	 * @since 4.7.0
	 *
	 * @return \SV_WC_Payment_Gateway_Apple_Pay
	 */
	protected function get_handler() {

		return $this->handler;
	}


}

endif;
