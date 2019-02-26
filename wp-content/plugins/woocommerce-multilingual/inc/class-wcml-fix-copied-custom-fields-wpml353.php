<?php

class WCML_Fix_Copied_Custom_Fields_WPML353{

    public function __construct(){
        //@TODO review after WPML 3.6
        if ( is_admin() && version_compare( ICL_SITEPRESS_VERSION, '3.5.3', '>=' ) && version_compare( ICL_SITEPRESS_VERSION, '3.6', '<' ) ) {
            add_action( 'added_post_meta', array(
                $this,
                'fix_copied_custom_fields'
            ), 10, 4 );
        }

    }

    public function fix_copied_custom_fields( $mid, $object_id, $meta_key, $_meta_value ) {
        global $wpdb;

        if ( in_array( get_post_type( $object_id ), array( 'product', 'product_variation' ) ) ) {

            $meta_keys_to_fix = array(
                '_price',
                '_regular_price',
                '_sale_price',
                '_sku'
            );

            if ( in_array( $meta_key, $meta_keys_to_fix ) ) {

                if ( is_null( $_meta_value ) ) {
                    $wpdb->update( $wpdb->postmeta, array( 'meta_value' => '' ), array( 'meta_id' => $mid ) );
                }

            }

        }

    }

}