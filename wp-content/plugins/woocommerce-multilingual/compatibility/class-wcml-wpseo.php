<?php

class WCML_WPSEO{

    private $updated_post_id;

    function __construct(){

        add_filter( 'wcml_product_content_label', array( $this, 'wpseo_custom_field_label' ), 10, 2 );

        if( defined( 'WPSEO_VERSION') && defined( 'WPSEO_PATH' ) && isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == 'wpml-wcml' && isset( $_GET[ 'tab' ] ) && $_GET[ 'tab' ] == 'products' ){
            if( version_compare( WPSEO_VERSION, '3', '<' ) ) {
                require_once WPSEO_PATH . 'admin/class-metabox.php';
            } elseif( file_exists( WPSEO_PATH . 'admin/metabox/class-metabox.php' ) ) {
                require_once WPSEO_PATH . 'admin/metabox/class-metabox.php';
            }
        }

        add_action( 'post_updated', array( $this, 'set_updated_post_id' ) );
        add_action( 'wpseo_premium_post_redirect_slug_change', array( $this, 'wpseo_premium_post_redirect_slug_change' ) );
    }

    function wpseo_custom_field_label( $field, $product_id ){
        global $woocommerce_wpml, $wpseo_metabox;

        $yoast_seo_fields = array( '_yoast_wpseo_focuskw', '_yoast_wpseo_title', '_yoast_wpseo_metadesc' );

        if ( !is_array(  maybe_unserialize( get_post_meta( $product_id, $field, true ) ) ) ) {

            if ( !is_null( $wpseo_metabox ) && in_array( $field, $yoast_seo_fields ) ) {

                $wpseo_metabox_values = $wpseo_metabox->get_meta_boxes( 'product' );

                $label = $wpseo_metabox_values[ str_replace( '_yoast_wpseo_', '', $field ) ][ 'title' ];

                return $label;
            }
        }

        return $field;
    }

    function set_updated_post_id( $post_id ){
        $this->updated_post_id = $post_id;
    }

    function wpseo_premium_post_redirect_slug_change( $slug_changed_flag ){

        if( null !== $this->updated_post_id && get_post_type( $this->updated_post_id ) === 'product_variation' ){
            return true;
        }

        return $slug_changed_flag;
    }

}

