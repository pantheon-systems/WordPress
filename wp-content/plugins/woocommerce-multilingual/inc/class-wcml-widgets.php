<?php

class WCML_Widgets{

    private $woocommerce_wpml;

    public function __construct( &$woocommerce_wpml ) {
        $this->woocommerce_wpml =& $woocommerce_wpml;

        add_action( 'widgets_init', array($this, 'register_widgets' ) );

    }

    public function register_widgets(){

        if( $this->woocommerce_wpml->settings[ 'enable_multi_currency' ] == WCML_MULTI_CURRENCIES_INDEPENDENT ){
            register_widget( 'WCML_Currency_Switcher_Widget' );
        }

        if( $this->woocommerce_wpml->settings[ 'cart_sync' ][ 'currency_switch' ] == WCML_CART_CLEAR || $this->woocommerce_wpml->settings[ 'cart_sync' ][ 'lang_switch' ] == WCML_CART_CLEAR ){
            register_widget( 'WCML_Cart_Removed_Items_Widget' );
        }

    }

}