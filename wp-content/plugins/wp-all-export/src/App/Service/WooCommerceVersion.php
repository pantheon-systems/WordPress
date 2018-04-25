<?php

namespace Wpae\App\Service;


class WooCommerceVersion
{
    public static function isWooCommerceNewerThan( $version = '3.0' ) {
        if ( class_exists( 'WooCommerce' ) ) {
            global $woocommerce;
            if ( version_compare( $woocommerce->version, $version, ">=" ) ) {
                return true;
            }
        }
        return false;
    }
}