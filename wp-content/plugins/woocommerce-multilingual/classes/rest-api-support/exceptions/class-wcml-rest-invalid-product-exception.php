<?php

class WCML_REST_Invalid_Product_Exception extends WC_REST_Exception {

	/**
	 * WCML_REST_Invalid_Product_Exception constructor.
	 *
	 * @param int $product_id
	 */
	public function __construct( $product_id ) {
		parent::__construct(
			422,
			sprintf( __( 'Product not found: %d', 'woocommerce-multilingual' ),
				$product_id ),
			422
		);
	}
}
