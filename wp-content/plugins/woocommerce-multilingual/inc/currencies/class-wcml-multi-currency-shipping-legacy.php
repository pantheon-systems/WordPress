<?php

/**
 * Class WCML_Multi_Currency_Shipping_Legacy
 *
 * This code is only required for versions of WooCommerce prior 2.6.0
 *
 */
class WCML_Multi_Currency_Shipping_Legacy{

    public function __construct() {

        add_filter( 'option_woocommerce_free_shipping_settings', array( $this, 'adjust_min_amount_required' ) );
        add_filter( 'woocommerce_package_rates', array($this, 'shipping_taxes_filter'));

    }

    public function shipping_taxes_filter($methods){

        global $woocommerce;
        $woocommerce->shipping->load_shipping_methods();
        $shipping_methods = $woocommerce->shipping->get_shipping_methods();

        foreach($methods as $k => $method){

            // exceptions
            if(
                isset($shipping_methods[$method->id]) &&
                isset($shipping_methods[$method->id]->settings['type']) &&
                $shipping_methods[$method->id]->settings['type'] == 'percent'
                    || preg_match('/^table_rate-[0-9]+ : [0-9]+$/', $k)
            ){
                continue;
            }


            foreach($method->taxes as $j => $tax){

                $methods[$k]->taxes[$j] = apply_filters('wcml_shipping_price_amount', $methods[$k]->taxes[$j]);

            }

        }

        return $methods;
    }

    // Before WooCommerce 2.6
    public function adjust_min_amount_required($options){

        if( !empty( $options['min_amount'] ) ){
            $options['min_amount'] = apply_filters( 'wcml_shipping_free_min_amount', $options['min_amount'] );
        }

        return $options;
    }

}