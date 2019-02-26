<?php

use Ebanx\Benjamin\Models\Address;
use Ebanx\Benjamin\Models\Country;
use Ebanx\Benjamin\Models\Person;
use Ebanx\Benjamin\Models\Request;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_EBANX_Payment_By_Link
 */
class WC_EBANX_Payment_By_Link {

	/**
	 *
	 * @var array
	 */
	private static $errors = array();

	/**
	 *
	 * @var int
	 */
	private static $post_id;

	/**
	 *
	 * @var boolean|WC_Order|WC_Refund
	 */
	private static $order;

	/**
	 *
	 * @var WC_EBANX_Global_Gateway
	 */
	private static $configs;

	/**
	 *
	 * @var WC_EBANX_Payment_Validator
	 */
	private static $validator;

	/**
	 * The core method. It uses the other methods to create a payment link
	 *
	 * @param  int $post_id The post id.
	 * @return void
	 */
	public static function create( $post_id ) {
		self::$post_id   = $post_id;
		self::$order     = wc_get_order( $post_id );
		self::$configs   = new WC_EBANX_Global_Gateway();
		self::$validator = new WC_EBANX_Payment_Validator( self::$order );

		if ( ! self::can_create_payment() ) {
			return;
		}

		if ( self::$validator->validate() ) {
			self::$errors = array_merge( self::$errors, self::$validator->get_errors() );
			self::send_errors();
			return;
		}

		$response = self::send_request();
		if ( $response && 'SUCCESS' !== $response['status'] ) {
			self::add_error( self::get_error_message( $response ) );
			self::send_errors();
			return;
		}

		self::post_request( $response['payment']['hash'], $response['redirect_url'] );
	}

	/**
	 * Checks if user has permissions to save data.
	 * AND Checks if not an autosave.
	 * AND Checks if not a revision.
	 *
	 * @return bool Can we create a payment by link now?
	 */
	private static function can_create_payment() {
		return current_user_can( 'edit_post', self::$post_id )
				&& ! wp_is_post_autosave( self::$post_id )
				&& ! wp_is_post_revision( self::$post_id );
	}

	/**
	 * Flashes every error from self::$errors to WC_EBANX_Flash
	 *
	 * @return void
	 */
	private static function send_errors() {
		WC_EBANX_Flash::clear_messages();
		foreach ( array_unique( self::$errors ) as $error ) {
			WC_EBANX_Flash::add_message( $error );
		}
	}


	/**
	 *
	 * @return object
	 */
	private static function send_request() {
		$person = new Person(
			[
				'name'  => self::$order->billing_first_name . ' ' . self::$order->billing_last_name,
				'email' => self::$order->billing_email,
			]
		);

		$address = new Address( [ 'country' => Country::fromIso( self::$order->billing_country ) ] );

		$data = new Request(
			[
				'person'              => $person,
				'address'             => $address,
				'orderNumber'         => self::$order->id,
				'type'                => empty( self::$order->payment_method ) ? '_all' : WC_EBANX_Constants::$gateway_to_payment_type_code[ self::$order->payment_method ],
				'merchantPaymentCode' => substr( self::$order->id . '_' . md5( time() ), 0, 40 ),
				'amount'              => self::$order->get_total(),
				'maxInstalments'      => get_post_meta( self::$order->id, '_ebanx_instalments', true ),
				'manualReview'        => 'yes' === self::$configs->settings['manual_review_enabled'],
				'userValues'          => [
					1 => 'from_woocommerce',
					3 => 'version=' . WC_EBANX::get_plugin_version(),
				],
			]
		);

		$response = false;
		try {
			$response = ( new WC_EBANX_Api( self::$configs, self::$order->get_order_currency() ) )->ebanx()->hosted()->create( $data );
		} catch ( Exception $e ) {
			self::add_error( $e->getMessage() );
			self::send_errors();
		} finally {
			WC_EBANX_Payment_By_Link_Logger::persist(
				[
					'request'  => $data,
					'response' => $response,
					'errors'   => self::$errors,
				]
			);
		}

		return $response;
	}

	/**
	 * If the request was successful, this method is called before ending the proccess
	 *
	 * @param  string $hash The payment hash.
	 * @param  string $url  The payment url.
	 * @return void
	 */
	private static function post_request( $hash, $url ) {
		self::$order->add_order_note( __( 'EBANX: Your order was created via EBANX.', 'woocommerce-gateway-ebanx' ) );
		update_post_meta( self::$post_id, '_ebanx_payment_hash', $hash );
		update_post_meta( self::$post_id, '_ebanx_checkout_url', $url );
	}

	/**
	 * Check if the error is not already in the array and add it.
	 * To make sure it will show no duplicates.
	 *
	 * @param string $error The error message.
	 */
	private static function add_error( $error ) {
		if ( ! in_array( $error, self::$errors ) ) {
			self::$errors[] = $error;
		}
	}

	/**
	 *
	 * @param object $request
	 *
	 * @return string
	 */
	private static function get_error_message( $request ) {
		if ( WP_DEBUG ) {
			return $request->status_code . ': ' . $request->status_message;
		}

		switch ( $request->status_code ) {
			case 'BP-R-32':
				// Amount must be less than {currency_code} {amount}.
				$value         = explode( ' ', substr( $request->status_message, 25 ) );
				$currency_code = $value[0];
				$amount        = number_format( $value[1], 0, wc_get_price_decimal_separator(), wc_get_price_thousand_separator() );

				return sprintf( __( "Your transaction's value must be lower than %1\$s %2\$s. Please, set a lower one.", 'woocommerce-gateway-ebanx' ), $currency_code, $amount );
			default:
				return __( 'We couldn\'t create your EBANX order. Could you review your fields and try again?', 'woocommerce-gateway-ebanx' );
		}
	}
}
