<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class AffiliateWP_PayPal_API {

	public $credentials;
	private $sandbox = '';

	/**
	 * Get thigns started
	 *
	 * @access public
	 * @since 1.1
	 * @return void
	 */
	public function __construct() {

		if( affiliate_wp_paypal()->is_test_mode() ) {
			$this->sandbox = 'sandbox.';
		}
	}

	/**
	 * Process a single referral payment
	 *
	 * @access public
	 * @since 1.1
	 * @return bool|WP_Error
	 */
	public function send_payment( $args = array() ) {

		$token = $this->get_token();

		if( is_wp_error( $token ) ) {
			return $token;
		}

		$request = wp_remote_post( 'https://api.' . $this->sandbox . 'paypal.com/v1/payments/payouts?sync_mode=false', array(
			'headers'    => array(
				'Content-Type'    => 'application/json',
				'Authorization'   => 'Bearer ' . $token->access_token,
				'PayPal-Partner-Attribution-Id' => 'EasyDigitalDownloads_SP',
			),
			'timeout'     => 45,
			'httpversion' => '1.1',
			'body'        => json_encode( array(
				'sender_batch_header' => array(
					'email_subject'   => __( 'Affiliate Earnings Payout', 'affwp-paypal-payouts' )
				),
				'items'   => array(
					array(
						'recipient_type' => 'EMAIL',
						'amount'         => array(
							'value'      => affwp_sanitize_amount( $args['amount'] ),
							'currency'   => affwp_get_currency()
						),
						'receiver'       => $args['email'],
						'note'           => $args['description'],
						'sender_item_id' => $args['referral_id']
					)
				)
			) )
		) );

		$body     = wp_remote_retrieve_body( $request );
		$code     = wp_remote_retrieve_response_code( $request );
		$message  = wp_remote_retrieve_response_message( $request );

		if ( is_wp_error( $request ) ) {

			affiliate_wp()->utils->log( 'send_payment() request failed with error code ' . $code  . ': ' . print_r( $body, true ) );

			return $request;

		} elseif ( 201 === $code && 'created' === strtolower( $message ) ) {

			if ( function_exists( 'affwp_add_payout' ) ) {
				if ( $referral = affwp_get_referral( $args['referral_id'] ) ) {
					affwp_add_payout( array(
						'affiliate_id'  => $referral->affiliate_id,
						'referrals'     => $referral->ID,
						'amount'        => $referral->amount,
						'payout_method' => 'PayPal',
					) );
				}
			} else {
				affwp_set_referral_status( $args['referral_id'], 'paid' );
			}

		} else {

			affiliate_wp()->utils->log( 'send_payment() request failed with error code ' . $code  . ': ' . $message );
			affiliate_wp()->utils->log( 'send_payment() request args: ' . print_r( $args, true ) );
			affiliate_wp()->utils->log( 'send_payment() request attempt: ' . print_r( $request, true ) );

			return new WP_Error( $code, $message );

		}

		return true;

	}

	/**
	 * Process a referral payment for a bulk payout
	 *
	 * @access public
	 * @since 1.1
	 * @return bool|WP_Error
	 */
	public function send_bulk_payment( $payouts = array() ) {

		$token = $this->get_token();

		if( is_wp_error( $token ) ) {
			return $token;
		}

		$items = array();
		foreach( $payouts as $affilate_id => $payout ) {

			$items[] = array(
				'recipient_type' => 'EMAIL',
				'amount'         => array(
					'value'      => affwp_sanitize_amount( $payout['amount'] ),
					'currency'   => affwp_get_currency()
				),
				'receiver'       => $payout['email'],
				'note'           => $payout['description'],
				'sender_item_id' => $affilate_id
			);

		}

		$request = wp_remote_post( 'https://api.' . $this->sandbox . 'paypal.com/v1/payments/payouts?sync_mode=false', array(
			'headers'     => array(
				'Content-Type'  => 'application/json',
				'Authorization' => 'Bearer ' . $token->access_token,
				'PayPal-Partner-Attribution-Id' => 'EasyDigitalDownloads_SP',
			),
			'timeout'     => 45,
			'httpversion' => '1.1',
			'body'        => json_encode( array(
				'sender_batch_header' => array(
					'email_subject'   => __( 'Affiliate Earnings Payout', 'affwp-paypal-payouts' )
				),
				'items'   => $items
			) )
		) );

		$body     = wp_remote_retrieve_body( $request );
		$code     = wp_remote_retrieve_response_code( $request );
		$message  = wp_remote_retrieve_response_message( $request );

		if( 201 === $code && 'created' === strtolower( $message ) ) {

			return true;

		} else {

			affiliate_wp()->utils->log( 'send_bulk_payment() request failed with error code ' . $code  . ': ' . $message );
			affiliate_wp()->utils->log( 'send_payment() request items: ' . print_r( $items, true ) );
			affiliate_wp()->utils->log( 'send_payment() request attempt: ' . print_r( $request, true ) );

			$body = json_decode( $body );

			if( ! empty( $body->name ) && 'VALIDATION_ERROR' === $body->name ) {

				$code    = $body->name;
				$message = $body->message . '. Details: ' . json_encode( $body->details ) . ' - ' . $body->information_link;

			}

			return new WP_Error( $code, $message );

		}

	}

	/**
	 * Retrieve an API access token
	 *
	 * @access private
	 * @since 1.0
	 * @return object|WP_Error
	 */
	private function get_token() {

		$request = wp_remote_post( 'https://api.' . $this->sandbox . 'paypal.com/v1/oauth2/token', array(
			'headers'     => array(
				'Accept'          => 'application/json',
				'Accept-Language' => 'en_US',
				'Authorization'   => 'Basic ' . base64_encode( $this->credentials['client_id'] . ':' . $this->credentials['secret'] )
			),
			'timeout'     => 45,
			'httpversion' => '1.1',
			'body'        => array(
				'grant_type'      => 'client_credentials'
			)
		) );

		$body     = wp_remote_retrieve_body( $request );
		$code     = wp_remote_retrieve_response_code( $request );
		$message  = wp_remote_retrieve_response_message( $request );

		if( is_wp_error( $request ) ) {

			affiliate_wp()->utils->log( 'get_token() request failed with error code ' . $code  . ': ' . print_r( $body, true ) );

			return $request;

		} else if( 200 === $code && 'ok' === strtolower( $message ) ) {

			affiliate_wp()->utils->log( 'get_token() request succeeded: ' . print_r( $body, true ) );

			return json_decode( $body );

		} else {

			$body = json_decode( $body );

			if( ! empty( $body->error ) ) {

				$code  = $body->error;
				$error = $body->error_description;

			} else {

				$code  = $code;
				$error = $message;

			}

			affiliate_wp()->utils->log( 'get_token() request failed with error code ' . $code  . ': ' . $error );

			return new WP_Error( $code, $error );

		}

	}

}