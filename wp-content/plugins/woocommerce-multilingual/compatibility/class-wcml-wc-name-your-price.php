<?php

class WCML_WC_Name_Your_Price{

    function __construct(){

        add_action('init', array($this, 'init'),9);

    }

    function init(){
        if( !is_admin() ){
            add_filter('woocommerce_raw_suggested_price', array($this, 'product_price_filter'), 10, 2);  
            add_filter('woocommerce_raw_minimum_price', array($this, 'product_price_filter'), 10, 2);                
        }
    }
    
    function product_price_filter($price, $product){
        
        return apply_filters('wcml_raw_price_amount', $price );

    }

}
