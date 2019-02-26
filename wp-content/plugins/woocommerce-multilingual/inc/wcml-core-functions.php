<?php

/**
 * @return bool
 *
 * @since 3.8.3
 */
function wcml_is_multi_currency_on(){
    global $woocommerce_wpml;

    if( is_null( $woocommerce_wpml ) ){
        return false;
    }

    return $woocommerce_wpml->settings['enable_multi_currency'] == WCML_MULTI_CURRENCIES_INDEPENDENT;
}