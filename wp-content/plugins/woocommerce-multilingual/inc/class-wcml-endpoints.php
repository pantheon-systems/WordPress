<?php

class WCML_Endpoints{

	/** @var woocommerce_wpml */
	private $woocommerce_wpml;
    var $endpoints_strings = array();

    function __construct( $woocommerce_wpml ){

    	$this->woocommerce_wpml =& $woocommerce_wpml;
        add_action( 'icl_ajx_custom_call', array( $this, 'rewrite_rule_endpoints' ), 11, 2 );
        add_action( 'woocommerce_update_options', array( $this, 'add_endpoints' ) );
        add_filter( 'pre_update_option_rewrite_rules', array( $this, 'update_rewrite_rules' ), 100, 2 );
	    add_filter( 'wpml_sl_blacklist_requests', array( $this, 'reserved_requests' ), 10, 2 );

        add_filter( 'page_link', array( $this, 'endpoint_permalink_filter' ), 10, 2 ); //after WPML

        if( !is_admin() ){
            //endpoints hooks
            $this->maybe_flush_rules();
            $this->register_endpoints_translations();
            add_filter('pre_get_posts', array($this, 'check_if_endpoint_exists'));
        }

        add_filter( 'woocommerce_get_endpoint_url', array( $this, 'filter_get_endpoint_url' ), 10, 4 );

        add_filter( 'woocommerce_settings_saved', array( $this, 'update_original_endpoints_strings') );

	    add_filter( 'option_rewrite_rules', array( $this, 'translate_endpoints_in_rewrite_rules' ), 0, 1 ); // high priority
    }

	public function reserved_requests( $requests, SitePress $sitepress ) {
		$cache_key   = 'reserved_requests';
		$cache_group = 'wpml-endpoints';

		$found        = null;
		$reserved_requests = wp_cache_get( $cache_key, $cache_group, false, $found );

		if ( ! $found || ! $reserved_requests ) {
			$reserved_requests = array();

			$current_language = $sitepress->get_current_language();
			$languages        = $sitepress->get_active_languages();
			$languages_codes  = array_keys( $languages );
			foreach ( $languages_codes as $language_code ) {
				$sitepress->switch_lang( $language_code );

				$my_account_page_id = get_option( 'woocommerce_myaccount_page_id' );
				if ( $my_account_page_id ) {
					$my_account_page = get_post( $my_account_page_id );
					if ( $my_account_page ) {
						$account_base = $my_account_page->post_name;

						$reserved_requests[] = $account_base;
						$reserved_requests[] = '/^' . $account_base . '/'; // regex version

						foreach ( $this->woocommerce_wpml->get_wc_query_vars() as $key => $endpoint ) {
							$translated_endpoint = $this->get_endpoint_translation( $key, $endpoint, $language_code );

							$reserved_requests[] = $account_base . '/' . $translated_endpoint;
						}
					}
				}
			}
			$sitepress->switch_lang( $current_language );

			if ( $reserved_requests ) {
				wp_cache_set( $cache_key, $reserved_requests, $cache_group );
			}
		}

		if ( $reserved_requests ) {
			$requests = array_unique( array_merge( $requests, $reserved_requests ) );
		}

		return $requests;
	}

    function register_endpoints_translations( $language = null ){

        if( !class_exists( 'WooCommerce' ) || !defined( 'ICL_SITEPRESS_VERSION' ) || ICL_PLUGIN_INACTIVE || version_compare( WOOCOMMERCE_VERSION, '2.2', '<' ) ) return false;

        $wc_vars = WC()->query->query_vars;

        if ( !empty( $wc_vars ) ){
            $query_vars = array(
                // Checkout actions
                'order-pay'          => $this->get_endpoint_translation( 'order-pay', $wc_vars['order-pay'], $language ),
                'order-received'     => $this->get_endpoint_translation( 'order-received', $wc_vars['order-received'], $language ),

                // My account actions
                'view-order'                 => $this->get_endpoint_translation( 'view-order', $wc_vars['view-order'], $language ),
                'edit-account'               => $this->get_endpoint_translation( 'edit-account', $wc_vars['edit-account'], $language ),
                'edit-address'               => $this->get_endpoint_translation( 'edit-address', $wc_vars['edit-address'], $language ),
                'lost-password'              => $this->get_endpoint_translation( 'lost-password', $wc_vars['lost-password'], $language ),
                'customer-logout'            => $this->get_endpoint_translation( 'customer-logout', $wc_vars['customer-logout'], $language ),
                'add-payment-method'         => $this->get_endpoint_translation( 'add-payment-method', $wc_vars['add-payment-method'], $language )
            );

            if( isset( $wc_vars['orders'] ) ) $query_vars[ 'orders' ] = $this->get_endpoint_translation( 'orders', $wc_vars['orders'], $language );
            if( isset( $wc_vars['downloads'] ) ) $query_vars[ 'downloads' ] = $this->get_endpoint_translation( 'downloads', $wc_vars['downloads'], $language );
            if( isset( $wc_vars['payment-methods'] ) ) $query_vars[ 'payment-methods' ] = $this->get_endpoint_translation( 'payment-methods', $wc_vars['payment-methods'], $language );
            if( isset( $wc_vars['delete-payment-method'] ) ) $query_vars[ 'delete-payment-method' ] = $this->get_endpoint_translation( 'delete-payment-method', $wc_vars['delete-payment-method'], $language );
            if( isset( $wc_vars['set-default-payment-method'] ) ) $query_vars[ 'set-default-payment-method' ] = $this->get_endpoint_translation( 'set-default-payment-method', $wc_vars['set-default-payment-method'], $language );

            $query_vars = apply_filters( 'wcml_register_endpoints_query_vars', $query_vars, $wc_vars, $this );

            $query_vars = array_merge( $wc_vars , $query_vars );
            WC()->query->query_vars = $query_vars;

        }

        return  WC()->query->query_vars;
    }

    function get_endpoint_translation( $key, $endpoint, $language = null ){

        $this->register_endpoint_string( $key, $endpoint );

        if( function_exists('icl_t') ){
            $trnsl = apply_filters( 'wpml_translate_single_string', $endpoint, 'WooCommerce Endpoints', $key, $language );

            if( !empty( $trnsl ) ){
                return $trnsl;
            }else{
                return $endpoint;
            }
        }else{
            return $endpoint;
        }
    }

    public function register_endpoint_string( $key, $endpoint ){

        $string = icl_get_string_id( $endpoint, 'WooCommerce Endpoints', $key );

        if( !$string && function_exists( 'icl_register_string' ) ){
            do_action( 'wpml_register_single_string', 'WooCommerce Endpoints', $key, $endpoint );
        }else{
            $this->endpoints_strings[] = $string;
        }

    }

    function rewrite_rule_endpoints( $call, $data ){

        if( $call == 'icl_st_save_translation' && in_array( $data['icl_st_string_id'], $this->endpoints_strings ) ){
            $this->add_endpoints();
            $this->flush_rules_for_endpoints_translations();
        }
    }

    function flush_rules_for_endpoints_translations( ){
        add_option( 'flush_rules_for_endpoints_translations', true );
    }

    function maybe_flush_rules(){
        if( get_option( 'flush_rules_for_endpoints_translations' ) ){
            delete_option( 'flush_rules_for_endpoints_translations' );
            WC()->query->init_query_vars();
            WC()->query->add_endpoints();
            WC()->query->query_vars = apply_filters( 'wcml_flush_rules_query_vars', WC()->query->query_vars, $this );

	        remove_filter( 'gettext_with_context', array( $this->woocommerce_wpml->strings, 'category_base_in_strings_language' ), 99, 3 );
	        if ( (int) get_option( 'page_on_front' ) !== wc_get_page_id( 'myaccount' ) ) {
		        flush_rewrite_rules( false );
	        }
	        add_filter( 'gettext_with_context', array( $this->woocommerce_wpml->strings, 'category_base_in_strings_language' ), 99, 3 );
            delete_option( 'flush_rules_for_endpoints_translations' );
        }
    }

    function update_rewrite_rules( $value, $old_value ){
        $this->add_endpoints();
        $this->flush_rules_for_endpoints_translations();

        return $value;
    }

    function add_endpoints(){
        if( !isset( $this->endpoints_strings ) )
            return;

        global $wpdb;
        //add endpoints and flush rules
        foreach( $this->endpoints_strings as $string_id ){

            $string_translations = icl_get_string_translations_by_id( $string_id );

            foreach( $string_translations as $string ){
                add_rewrite_endpoint( $string['value'], EP_ROOT | EP_PAGES );
            }
        }

    }

    function endpoint_permalink_filter( $p, $pid ){
        global $post;

        if( isset($post->ID) && !is_admin() && version_compare( WOOCOMMERCE_VERSION, '2.2', '>=' ) && defined( 'ICL_SITEPRESS_VERSION' ) && !ICL_PLUGIN_INACTIVE ){
            global $wp,$sitepress;

            $current_lang = $sitepress->get_current_language();
            $page_lang = $sitepress->get_language_for_element( $post->ID, 'post_page');

            if(
                (
                    $current_lang != $page_lang &&
                    apply_filters( 'translate_object_id', $pid, 'page', false, $page_lang ) == $post->ID
                ) ||
                apply_filters( 'wcml_endpoint_force_permalink_filter', false, $current_lang, $page_lang )
            ){

                $endpoints = WC()->query->get_query_vars();

                foreach( $endpoints as $key => $endpoint ){
                    if( isset($wp->query_vars[$key]) ){
                        if( 'order-pay' === $key ){
                            $endpoint = get_option( 'woocommerce_checkout_pay_endpoint' );
                            $p .= isset( $_SERVER[ 'QUERY_STRING' ] ) ? '?'.$_SERVER[ 'QUERY_STRING' ] : '';
                        }elseif( 'order-received' === $key ){
                            $endpoint = get_option( 'woocommerce_checkout_order_received_endpoint' );
                        }elseif( 'customer-logout' === $key ){
	                        $endpoint = get_option( 'woocommerce_logout_endpoint' );
                        }else{
                            $endpoint = get_option( 'woocommerce_myaccount_'.str_replace( '-','_',$key).'_endpoint', $endpoint );
                        }

                        $endpoint = apply_filters( 'wcml_endpoint_permalink_filter', $endpoint, $key );

                        $p = $this->get_endpoint_url( $this->get_endpoint_translation( $key, $endpoint, $current_lang ), $wp->query_vars[ $key ], $p, $page_lang );
                    }
                }
            }
        }

        return $p;
    }

    function get_endpoint_url($endpoint, $value = '', $permalink = '', $page_lang = false ){
        global $sitepress;

        if( $page_lang ){
            $edit_address_shipping = $this->get_translated_edit_address_slug( 'shipping', $page_lang );
            $edit_address_billing = $this->get_translated_edit_address_slug( 'billing', $page_lang );

            if( $edit_address_shipping == urldecode( $value ) ){
                $value = $this->get_translated_edit_address_slug( 'shipping', $sitepress->get_current_language() );
            }elseif( $edit_address_billing == urldecode( $value ) ){
                $value = $this->get_translated_edit_address_slug( 'billing', $sitepress->get_current_language() );
            }

        }


        if ( get_option( 'permalink_structure' ) ) {
            if ( strstr( $permalink, '?' ) ) {
                $query_string = '?' . parse_url( $permalink, PHP_URL_QUERY );
                $permalink    = current( explode( '?', $permalink ) );
            } else {
                $query_string = '';
            }
            $url = trailingslashit( $permalink ) . $endpoint . '/' . $value . $query_string;
        } else {
            $url = add_query_arg( $endpoint, $value, $permalink );
        }
        return $url;
    }

    /*
     * We need check special case - when you manually put in URL default not translated endpoint it not generated 404 error
     */
    function check_if_endpoint_exists($q){
        global $wp_query;

        $my_account_id = wc_get_page_id('myaccount');

        $current_id = $q->query_vars['page_id'];
        if(!$current_id){
            $current_id = $q->queried_object_id;
        }

        if( !$q->is_404 && $current_id == $my_account_id && $q->is_page ){

            $uri_vars = array_filter( explode( '/', $_SERVER['REQUEST_URI']) );
            $endpoints =  WC()->query->get_query_vars();
            $endpoint_in_url = urldecode( end( $uri_vars ) );

            $endpoints['shipping'] = urldecode(  $this->get_translated_edit_address_slug( 'shipping' ) );
            $endpoints['billing'] = urldecode(  $this->get_translated_edit_address_slug( 'billing' )  );

            $endpoint_not_pagename = isset( $q->query['pagename'] ) && urldecode( $q->query['pagename'] ) != $endpoint_in_url;
            $endpoint_url_not_in_endpoints = !in_array( $endpoint_in_url,$endpoints );
            $uri_vars_not_in_query_vars = !in_array( urldecode( prev( $uri_vars ) ) ,$q->query_vars );

            if( $endpoint_not_pagename && $endpoint_url_not_in_endpoints && is_numeric( $endpoint_in_url ) && $uri_vars_not_in_query_vars ){
                $wp_query->set_404();
                status_header(404);
                include( get_query_template( '404' ) );
                die();
            }

        }

    }

    function get_translated_edit_address_slug( $slug, $language = false ){
        global $woocommerce_wpml;

        $strings_language = $woocommerce_wpml->strings->get_string_language( $slug, 'woocommerce', 'edit-address-slug: '.$slug );

        if( $strings_language == $language ){
            return $slug;
        }

        $translated_slug = apply_filters( 'wpml_translate_single_string', $slug, 'woocommerce', 'edit-address-slug: '.$slug, $language );

        if( $translated_slug == $slug ){

            if( $language ){
                $translated_slug = $woocommerce_wpml->strings->get_translation_from_woocommerce_mo_file( 'edit-address-slug'. chr(4) .$slug, $language );
            }else{
                $translated_slug = _x( $slug, 'edit-address-slug', 'woocommerce' );
            }

        }

        return $translated_slug;
    }

	function filter_get_endpoint_url( $url, $endpoint, $value, $permalink ) {

		// return translated edit account slugs
		remove_filter( 'woocommerce_get_endpoint_url', array( $this, 'filter_get_endpoint_url' ), 10, 4 );
		if ( isset( WC()->query->query_vars['edit-address'] ) && WC()->query->query_vars['edit-address'] == $endpoint && in_array( $value, array(
				'shipping',
				'billing'
			) )
		) {
			$url = wc_get_endpoint_url( 'edit-address', $this->get_translated_edit_address_slug( $value ) );
		} elseif ( $endpoint === get_option( 'woocommerce_myaccount_lost_password_endpoint' ) ) {
			$translated_lost_password_endpoint = apply_filters( 'wpml_translate_single_string', $endpoint, 'WooCommerce Endpoints', 'lost-password' );

			$wc_account_page_url = wc_get_page_permalink( 'myaccount' );
			$url                 = wc_get_endpoint_url( $translated_lost_password_endpoint, '', $wc_account_page_url );

		}
		add_filter( 'woocommerce_get_endpoint_url', array( $this, 'filter_get_endpoint_url' ), 10, 4 );

		return $url;
	}

    public function update_original_endpoints_strings(){

        foreach( WC()->query->query_vars as $endpoint_key => $endpoint ){

            $this->register_endpoint_string( $endpoint_key, $endpoint );

        }

    }

	public function translate_endpoints_in_rewrite_rules( $value ) {

		if ( ! empty( $value ) ) {

			foreach ( WC()->query->query_vars as $endpoint_key => $endpoint_translation ) {
				if ( $endpoint_key == $endpoint_translation ) {
					continue;
				}

				$buff_value = array();

				foreach ( $value as $k => $v ) {
					$k = preg_replace( '/(\/)?' . $endpoint_key . '(\/)?(\(\/\(\.\*\)\)\?\/\?\$)/', '$1' . $endpoint_translation . '$2$3', $k );
					$buff_value[ $k ] = $v;
				}
				$value = $buff_value;
			}
		}

		return $value;
	}


}
