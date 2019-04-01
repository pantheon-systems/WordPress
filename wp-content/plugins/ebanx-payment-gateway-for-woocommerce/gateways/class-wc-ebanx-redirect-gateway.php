<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_EBANX_Redirect_Gateway
 */
abstract class WC_EBANX_Redirect_Gateway extends WC_EBANX_New_Gateway {

	/**
	 *
	 * @var string
	 */
	protected $redirect_url;

	/**
	 *
	 * @param array    $response
	 * @param WC_Order $order
	 *
	 * @throws Exception Throw parameter missing exception.
	 * @throws WC_EBANX_Payment_Exception Throws error message.
	 */
	protected function process_response( $response, $order ) {
		if ( 'ERROR' === $response['status'] ) {
			$this->process_response_error( $response, $order );
		}
		$redirect = $response['redirect_url'];
		if ( ! $redirect && ! isset( $response['payment']['redirect_url'] ) ) {
			$this->process_response_error( $response, $order );
		}
		$redirect = $response['payment']['redirect_url'];

		parent::process_response( $response, $order );

		update_post_meta( $order->id, '_ebanx_payment_hash', $response['payment']['hash'] );

		$this->redirect_url = $redirect;
	}

	/**
	 * Dispatch an array to request, always dispatch success
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	final protected function dispatch( $data ) {
		return parent::dispatch(
			array(
				'result'   => 'success',
				'redirect' => $this->redirect_url,
			)
		);
	}
}
