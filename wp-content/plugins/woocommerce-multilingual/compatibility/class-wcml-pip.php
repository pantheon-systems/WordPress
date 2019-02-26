<?php

class WCML_Pip{

    function __construct(){

        add_filter( 'wcml_send_email_order_id', array( $this, 'wcml_send_email_order_id') );
        add_filter( 'woocommerce_currency_symbol', array( $this, 'filter_pip_currency_symbol' ) );
        add_filter( 'wcml_filter_currency_position', array( $this, 'filter_pip_currency_position' ) );

    }

    public function wcml_send_email_order_id( $order_id ){

        $pip_order_id = $this->get_pip_order_id();

        if( $pip_order_id ){
            $order_id = $pip_order_id;
        }

        return $order_id;

    }

    public function filter_pip_currency_symbol( $currency_symbol ) {

        remove_filter( 'woocommerce_currency_symbol', array( $this, 'filter_pip_currency_symbol' ) );

        $currency = $this->get_pip_order_currency( );

        if( $currency ){
            $currency_symbol = get_woocommerce_currency_symbol( $currency );
        }

        add_filter( 'woocommerce_currency_symbol', array( $this, 'filter_pip_currency_symbol' ) );

        return $currency_symbol;
    }

    public function filter_pip_currency_position( $currency ){

        remove_filter( 'wcml_filter_currency_position', array( $this, 'filter_pip_currency_position' ) );

        $currency = $this->get_pip_order_currency( $currency );

        add_filter( 'wcml_filter_currency_position', array( $this, 'filter_pip_currency_position' ) );

        return $currency;

    }

    public function get_pip_order_id(){

        $order_id = false;

        if( isset( $_GET[ 'wc_pip_action' ] ) && isset( $_GET[ 'order_id' ] ) ){
            $order_id = $_GET[ 'order_id' ];
        }elseif(
            isset( $_POST[ 'action' ] ) &&
            (
                $_POST[ 'action' ] == 'wc_pip_order_send_email' ||
                $_POST[ 'action' ] == 'wc_pip_send_email_packing_list'
            ) &&
            isset( $_POST[ 'order_id' ] )
        ){
            $order_id = $_POST[ 'order_id' ];
        }

        return $order_id;
    }

    public function get_pip_order_currency( $currency = false ){

        $pip_order_id = $this->get_pip_order_id();

        if( $pip_order_id && isset( WC()->order_factory ) ){

            $the_order = WC()->order_factory->get_order( $pip_order_id );

            if( $the_order ){
                $currency = WooCommerce_Functions_Wrapper::get_order_currency( $the_order );

                if( !$currency && isset( $_COOKIE[ '_wcml_order_currency' ] ) ){
                    $currency =  $_COOKIE[ '_wcml_order_currency' ];
                }

            }
        }

        return $currency;

    }

}
