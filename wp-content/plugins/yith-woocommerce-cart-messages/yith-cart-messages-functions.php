<?php

if ( !function_exists( 'ywcm_get_shop_categories' ) ) {
    function ywcm_get_shop_categories( $show_all = true ) {
        global $wpdb;

        $terms = $wpdb->get_results( 'SELECT name, slug FROM ' . $wpdb->prefix . 'terms, ' . $wpdb->prefix . 'term_taxonomy WHERE ' . $wpdb->prefix . 'terms.term_id = ' . $wpdb->prefix . 'term_taxonomy.term_id AND taxonomy = "product_cat" ORDER BY name ASC;' );

        $categories = array();
        if ( $show_all ) {
            $categories['0'] = __( 'All categories', 'yith-woocommerce-cart-messages' );
        }
        if ( $terms ) {
            foreach ( $terms as $cat ) {
                $categories[$cat->slug] = ( $cat->name ) ? $cat->name : 'ID: ' . $cat->slug;
            }
        }
        return $categories;
    }
}