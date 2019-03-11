<?php

class WCML_REST_Invalid_Currency_Exception extends WC_REST_Exception {

	/**
	 * WCML_REST_Invalid_Currency_Exception constructor.
	 *
	 * @param string $currency_code
	 */
	public function __construct( $currency_code ) {
		parent::__construct(
			422,
			sprintf( __( 'Invalid currency parameter: "%s"', 'woocommerce-multilingual' ),
				$currency_code ),
			422
		);
	}
}
