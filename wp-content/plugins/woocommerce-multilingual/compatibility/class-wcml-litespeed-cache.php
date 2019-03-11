<?php

class WCML_LiteSpeed_Cache {

	function add_hooks() {
		// LiteSpeed_Cache_API::vary is available since 2.6.
		if ( method_exists( 'LiteSpeed_Cache_API', 'v' ) && LiteSpeed_Cache_API::v( '2.6' ) ) {
			add_filter( 'wcml_client_currency', array( $this, 'apply_client_currency' ) );
			add_action( 'wcml_set_client_currency', array( $this, 'set_client_currency' ) );
		}
	}

	function set_client_currency( $currency ) {
		$this->apply_client_currency( $currency );

		LiteSpeed_Cache_API::force_vary();
	}

	function apply_client_currency( $currency ) {
		LiteSpeed_Cache_API::vary( 'wcml_currency', $currency, get_option( 'woocommerce_currency' ) );

		return $currency;
	}

}

