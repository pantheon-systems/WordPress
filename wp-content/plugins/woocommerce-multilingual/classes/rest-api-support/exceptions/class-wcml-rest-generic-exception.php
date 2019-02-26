<?php

class WCML_REST_Generic_Exception extends WC_REST_Exception {

	/**
	 * WCML_REST_Generic_Exception constructor.
	 *
	 * @param string $text
	 */
	public function __construct( $text ) {
		parent::__construct( 422, $text, 422 );
	}
}
