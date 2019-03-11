<?php

class WCML_Vpc {

	function __construct() {
		add_filter( 'wcml_calculate_totals_exception', array( $this, 'wcml_vpc_cart_exc' ), 10, 2 );
	}

	function wcml_vpc_cart_exc( $exc, $cart ) {

		foreach( $cart->cart_contents as $cart_item ){
			if ( array_key_exists( 'visual-product-configuration', $cart_item ) ) {
				return false;
			}
		}

		return $exc;
	}

}
