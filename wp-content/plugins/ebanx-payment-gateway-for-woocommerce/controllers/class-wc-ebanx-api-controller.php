<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_EBANX_Api_Controller
 */
class WC_EBANX_Api_Controller {

	/**
	 *
	 * @var WC_EBANX_Global_Gateway
	 */
	private $config;

	/**
	 * Construct the controller with our config object
	 *
	 * @param WC_EBANX_Global_Gateway $config
	 */
	public function __construct( WC_EBANX_Global_Gateway $config ) {
		$this->config = $config;
	}

	/**
	 * Responds that the plugin is installed
	 *
	 * @return void
	 */
	public function dashboard_check() {
		echo json_encode(
			array(
				'ebanx'   => true,
				'version' => WC_EBANX::get_plugin_version(),
			)
		);
	}

	/**
	 * Responds that the plugin logs
	 *
	 * @return void
	 * @throws Exception Throws missing param message.
	 */
	public function retrieve_logs() {
		header( 'Content-Type: application/json' );

		if ( empty( WC_EBANX_Request::has( 'integration_key' ) )
			|| ( WC_EBANX_Request::read( 'integration_key' ) !== $this->config->settings['live_private_key']
			&& WC_EBANX_Request::read( 'integration_key' ) !== $this->config->settings['sandbox_private_key'] ) ) {
			die( json_encode( [] ) );
		}

		$where = "integration_key = '" . WC_EBANX_Request::read( 'integration_key' ) . "'";
		$logs  = WC_EBANX_Database::select( 'logs', $where );

		WC_EBANX_Database::truncate( 'logs', $where );

		die( json_encode( $logs ) );
	}

	/**
	 * Captures a credit card payment made while auto capture was disabled
	 *
	 * @param int $order_id
	 *
	 * @return void
	 */
	public function capture_payment( $order_id ) {
		WC_EBANX_Capture_Payment::capture_payment( $order_id );

		wp_redirect( $this->get_admin_order_url( $order_id ) );
	}

	/**
	 *
	 * @param int $order_id
	 *
	 * @return String
	 */
	public function get_admin_order_url( $order_id ) {
		return admin_url() . 'post.php?post=' . $order_id . '&action=edit';
	}

	/**
	 * Cancels an open cash payment order with "On hold" status
	 *
	 * @param int    $order_id
	 * @param string $user_id
	 *
	 * @return void
	 */
	public function cancel_order( $order_id, $user_id ) {
		$order = new WC_Order( $order_id );
		if ( get_current_user_id() != $user_id
			|| $order->get_status() !== 'on-hold'
			|| ! in_array( $order->get_payment_method(), WC_EBANX_Constants::$cash_payment_gateways_code )
			) {
			wp_redirect( get_site_url() );
			return;
		}

		$hash = get_post_meta( $order_id, '_ebanx_payment_hash', true );

		$ebanx = ( new WC_EBANX_Api( $this->config ) )->ebanx();

		try {
			$response = $ebanx->cancelPayment()->request( $hash );

			WC_EBANX_Cancel_Logger::persist(
				[
					'paymentHash' => $hash,
					'$response'   => $response,
				]
			);

			if ( 'SUCCESS' === $response['status'] ) {
				$order->update_status( 'cancelled', __( 'EBANX: Cancelled by customer', 'woocommerce-gateway-ebanx' ) );
			}

			wp_redirect( $order->get_view_order_url() );

		} catch ( Exception $e ) {
			$message = $e->getMessage();
			WC_EBANX::log( "EBANX Error: $message" );

			wc_add_notice( $message, 'error' );
			wp_redirect( get_site_url() );
		}
	}

	/**
	 * Gets the banking ticket HTML by cUrl with url fopen fallback
	 *
	 * @param string $hash
	 *
	 * @return void
	 */
	public function order_received( $hash ) {
		$ebanx = ( new WC_EBANX_Api( $this->config ) )->ebanx();

		echo $ebanx->getTicketHtml( $hash ); // phpcs:ignore WordPress.XSS.EscapeOutput
	}

	/**
	 * Get list of plugin check
	 *
	 * @return void
	 */
	public function plugin_check() {
		$list = WC_EBANX_Helper::plugin_check( $this->config );

		echo json_encode(
			$list
		);
	}
}
