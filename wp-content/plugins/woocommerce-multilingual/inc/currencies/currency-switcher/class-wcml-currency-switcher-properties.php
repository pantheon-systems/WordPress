<?php

/**
 * Class WCML_Currency_Switcher_Properties
 *
 * Main class
 */
class WCML_Currency_Switcher_Properties{

	public function is_currency_switcher_active( $switcher_id, $wcml_settings ){

		$product_page_switcher_is_active = 'product' === $switcher_id && 1 === $wcml_settings[ 'currency_switcher_product_visibility' ];
		$sidebar_switcher_is_active = 'product' !== $switcher_id && is_active_sidebar( $switcher_id );

		if( $product_page_switcher_is_active || $sidebar_switcher_is_active ){
			return true;
		}

		return false;
	}

}