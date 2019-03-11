<?php

/**
 * Class WCML_Multi_Currency_Table_Rate_Shipping
 *
 * This is only required for versions of WooCommerce Table Rating older than 3.0
 */

class WCML_Multi_Currency_Table_Rate_Shipping{

    public function add_hooks(){
        // table rate shipping support
        if( defined('TABLE_RATE_SHIPPING_VERSION' ) && version_compare( TABLE_RATE_SHIPPING_VERSION, '3.0', '<' ) ){
            add_filter('woocommerce_table_rate_query_rates', array($this, 'table_rate_shipping_rates'));
            add_filter('woocommerce_table_rate_instance_settings', array($this, 'table_rate_instance_settings'));
        }
    }

    public function table_rate_shipping_rates($rates){
        foreach($rates as $k => $rate){
            $rates[$k]->rate_cost                   = apply_filters('wcml_shipping_price_amount', $rates[$k]->rate_cost);
            $rates[$k]->rate_cost_per_item          = apply_filters('wcml_shipping_price_amount', $rates[$k]->rate_cost_per_item);
            $rates[$k]->rate_cost_per_weight_unit   = apply_filters('wcml_shipping_price_amount', $rates[$k]->rate_cost_per_weight_unit);
        }
        return $rates;
    }

    public function table_rate_instance_settings($settings){
        if(is_numeric($settings['handling_fee'])){
            $settings['handling_fee'] = apply_filters('wcml_shipping_price_amount', $settings['handling_fee']);
        }
        $settings['min_cost'] = apply_filters('wcml_shipping_price_amount', $settings['min_cost']);

        return $settings;
    }
}