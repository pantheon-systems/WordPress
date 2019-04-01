<?php

class WCML_Extra_Product_Options{

    function __construct(){

        // commented out because of wcml-1218
        // add_filter( 'get_post_metadata', array( $this, 'product_options_filter'), 100, 4 );
        // add_action( 'updated_post_meta', array( $this, 'register_options_strings' ), 10, 4 );

        add_action( 'tm_before_extra_product_options', array( $this, 'inf_translate_product_page_strings' ) );
        add_action( 'tm_before_price_rules', array( $this, 'inf_translate_strings' ) );
    }
    
    // commented out because of wcml-1218
    /*

    function register_options_strings( $meta_id, $id, $meta_key, $options ){
        if( $meta_key != 'tm_meta' )
            return false;

        $this->filter_options( $options, $id, 'register' );

    }

    function product_options_filter( $null, $object_id, $meta_key, $single ){
        static $no_filter = false;

        if( empty($no_filter) && $meta_key == 'tm_meta' && !is_admin() ){
            $no_filter = true;

            $options = maybe_unserialize( get_post_meta( $object_id, $meta_key, $single ) );

            $options = $this->filter_options( $options, $object_id, 'translate' );

            $no_filter = false;
        }

        return isset( $options ) ?  array( $options ) : $null;
    }

    function filter_options( $options, $id, $action ){

        if( !isset( $options[ 'tmfbuilder' ] ) ){
            return $options;
        }

        global $sitepress,$woocommerce_wpml;
        $keys_to_translate = array( 'header_title', 'header_subtitle', 'text_after_price', 'placeholder' );

        $id = apply_filters( 'translate_object_id', $id, get_post_type( $id ), true, $woocommerce_wpml->products->get_original_product_language( $id ) );

        foreach( $options[ 'tmfbuilder' ] as $key => $values ){
            foreach( $keys_to_translate as $key_text ){
                if ( preg_match('/.*'.$key_text.'$/', $key ) ) {
                    foreach( $values as $value_key => $value ){
                        if( $value ){
                            if( $action == 'register'){
                                do_action('wpml_register_single_string', 'wc_extra_product_options', $id.'_option_'.$value_key.'_'.$key, $value );
                            }else{
                                $options[ 'tmfbuilder' ][ $key ][ $value_key ] = apply_filters( 'wpml_translate_single_string', $value, 'wc_extra_product_options', $id.'_option_'.$value_key.'_'.$key);
                            }
                        }

                    }
                }
            }

            //convert prices
            if( $action == 'translate' && preg_match('/.*price$/', $key ) && !preg_match('/.*text_after_price/', $key )){
                foreach( $values as $value_key => $value ){
                    if( $value ){
                        if( is_array( $value ) ){
                            foreach( $value as $key_price => $price ){
                                $options[ 'tmfbuilder' ][ $key ][ $value_key ][ $key_price ] = apply_filters( 'wcml_raw_price_amount', $price );
                            }
                        }else{
                            $options[ 'tmfbuilder' ][ $key ][ $value_key ] = apply_filters( 'wcml_raw_price_amount', $value );
                        }
                    }
                }
            }

        }

        return $options;
    }

    */

    function inf_translate_strings(){
        if( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == 'tm-global-epo' )
            $this->inf_message( 'Options Form' );
    }

    function inf_translate_product_page_strings(){
        $this->inf_message( 'Product' );
    }

    function inf_message( $text ){
        $message = '<div><p class="icl_cyan_box">';
        $message .= sprintf(__('To translate Extra Options strings please save %s and go to the <b><a href="%s">String Translation interface</a></b>', 'woocommerce-multilingual'), $text, admin_url('admin.php?page='.WPML_ST_FOLDER.'/menu/string-translation.php&context=wc_extra_product_options'));
        $message .= '</p></div>';

        echo $message;
    }
}
