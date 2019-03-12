<?php
/*
 * WPBakery Page Builder ( formerly Visual Composer ) Compatibility class
 */

class WCML_Wpb_Vc {

	function add_hooks() {

		add_filter( 'wcml_is_localize_woocommerce_on_ajax', array( $this, 'is_localize_woocommerce_on_ajax' ), 10, 2 );
	}

	function is_localize_woocommerce_on_ajax( $localize, $action ) {

		if ( 'vc_edit_form' === $action ) {
			$localize = false;
		}

		return $localize;
	}

}
