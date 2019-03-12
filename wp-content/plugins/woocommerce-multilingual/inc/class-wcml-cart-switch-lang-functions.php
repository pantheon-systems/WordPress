<?php

class WCML_Cart_Switch_Lang_Functions{

    private $lang_from;
    private $lang_to;

    public function add_actions(){
	    add_action( 'wp_footer', array( $this, 'wcml_language_switch_dialog' ) );
	    add_action( 'wp_loaded', array( $this, 'wcml_language_force_switch' ) );
	    add_action( 'wcml_user_switch_language', array( $this, 'language_has_switched' ), 10 , 2 );

	    add_filter( 'woocommerce_product_add_to_cart_url', array( $this, 'remove_force_switch_from_add_to_cart_url' ) );
    }

    public function remove_force_switch_from_add_to_cart_url( $url ){
        $url = str_replace('force_switch=1&', '', $url );
        return $url;
    }

    public function language_has_switched( $lang_from, $lang_to ){

        $settings = get_option( '_wcml_settings' );

        if(
            !isset( $_GET[ 'force_switch' ] ) &&
            $lang_from != $lang_to &&
            !empty( $settings ) &&
            $settings[ 'cart_sync' ][ 'lang_switch' ] == WCML_CART_CLEAR
        ){
            $this->lang_from = $lang_from;
            $this->lang_to = $lang_to;
        }
    }

    public function wcml_language_force_switch(){
        global $woocommerce_wpml, $woocommerce;

        if( ! wpml_is_ajax() && isset( $_GET[ 'force_switch' ] ) && '1' === $_GET[ 'force_switch' ] ){
            $woocommerce_wpml->cart->empty_cart_if_needed( 'lang_switch' );
            $woocommerce->session->set( 'wcml_switched_type', 'lang_switch' );
        }
    }

    public function wcml_language_switch_dialog( ){
        global $woocommerce_wpml, $sitepress, $wp, $post;

        $dependencies = new WCML_Dependencies;

        if( $dependencies->check() ){

            $current_url = home_url( add_query_arg( array(), $wp->request ) );

            if( is_shop() ){
                $requested_page_id = apply_filters( 'translate_object_id', wc_get_page_id('shop'), 'post', true, $this->lang_from );
            }elseif( isset( $post->ID ) ){
                $requested_page_id = apply_filters( 'translate_object_id', $post->ID, get_post_type( $post->ID ), true, $this->lang_from );
            }
            
            if( isset( $requested_page_id ) ){
                $request_url = add_query_arg( 'force_switch', '0', $sitepress->convert_url( get_permalink( $requested_page_id ), $this->lang_from ) );
            }else{
                $request_url = $current_url;
            }

            $cart_for_session = false;
            if( isset( WC()->cart ) ){
                $cart_for_session = WC()->cart->get_cart_for_session();
            }

            if( $this->lang_from && $this->lang_to && $request_url && !empty( $cart_for_session ) ) {

	            $force_cart_url   = add_query_arg( 'force_switch', '1', $current_url );
	            $active_languages = apply_filters( 'wpml_active_languages', null, null );
	            $dialog_title     = __( 'Switching language?', 'woocommerce-multilingual' );

	            $confirmation_message = sprintf(
		            __( "You've switched the language and there are items in the cart. If you keep the %s language, the cart will be emptied and you will have to add the items again to the cart.",
			            'woocommerce-multilingual' ),
		            $active_languages[ $this->lang_to ]['translated_name']
	            );
	            $stay_in = sprintf( __( 'Keep %s', 'woocommerce-multilingual' ), $active_languages[ $this->lang_to ]['translated_name'] );
	            $switch_to = sprintf( __( 'Switch back to %s', 'woocommerce-multilingual' ), $active_languages[ $this->lang_from ]['translated_name'] );

                $woocommerce_wpml->cart->cart_alert( $dialog_title, $confirmation_message, $stay_in, $switch_to, $force_cart_url, $request_url, true );
            }

        }

    }

}