<?php

class WCML_REST_Invalid_Language_Exception extends WC_REST_Exception {

	/**
	 * WCML_REST_Invalid_Language_Exception constructor.
	 *
	 * @param string $language_code
	 */
	public function __construct( $language_code ) {
		parent::__construct(
			422,
			sprintf( __( 'Invalid language parameter: "%s"', 'woocommerce-multilingual' ),
				$language_code ),
			422
		);
	}
}
