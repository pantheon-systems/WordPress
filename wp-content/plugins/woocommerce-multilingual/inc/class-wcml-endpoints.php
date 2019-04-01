<?php

class WCML_Endpoints {

	/** @var woocommerce_wpml */
	private $woocommerce_wpml;
	/**
	 * @var SitePress
	 */
	private $sitepress;
	/**
	 * @var wpdb
	 */
	private $wpdb;

	var $endpoints_strings = array();

	function __construct( woocommerce_wpml $woocommerce_wpml, SitePress $sitepress, wpdb $wpdb ) {
		$this->woocommerce_wpml = $woocommerce_wpml;
		$this->sitepress        = $sitepress;
		$this->wpdb             = $wpdb;
	}

	public function add_hooks() {

		if ( class_exists( 'WPML_Endpoints_Support_Factory' ) ) {
			add_action( 'init', array( $this, 'migrate_ones_string_translations' ), 9 );
			add_action( 'wpml_after_add_endpoints_translations', array( $this, 'add_wc_endpoints_translations' ) );

			add_filter( 'wpml_endpoint_permalink_filter', array( $this, 'endpoint_permalink_filter' ), 10, 2 );
			add_filter( 'wpml_endpoint_url_value', array( $this, 'filter_endpoint_url_value' ), 10, 2 );
			add_filter( 'wpml_current_ls_language_url_endpoint', array( $this, 'add_endpoint_to_current_ls_language_url' ), 10, 4 );
		}else{
			$legacy_endpoints = new WCML_Endpoints_Legacy( $this->woocommerce_wpml );
			$legacy_endpoints->add_hooks();
		}

		add_filter( 'wpml_sl_blacklist_requests', array( $this, 'reserved_requests' ) );
		add_filter( 'woocommerce_get_endpoint_url', array( $this, 'filter_get_endpoint_url' ), 10, 4 );
		if ( ! is_admin() ) {
			add_filter( 'pre_get_posts', array( $this, 'check_if_endpoint_exists' ) );
		}
	}

	public function migrate_ones_string_translations() {

		if ( ! get_option( 'wcml_endpoints_context_updated' ) ) {

			$endpoint_keys = array(
				'order-pay',
				'order-received',
				'view-order',
				'edit-account',
				'edit-address',
				'lost-password',
				'customer-logout',
				'add-payment-method',
				'set-default-payment-method',
				'delete-payment-method',
				'payment-methods',
				'downloads',
				'orders'
			);

			foreach ( $endpoint_keys as $endpoint_key ) {

				$existing_string_id = $this->wpdb->get_var(
					$this->wpdb->prepare( "SELECT id FROM {$this->wpdb->prefix}icl_strings 
											WHERE context = %s AND name = %s",
						WPML_Endpoints_Support::STRING_CONTEXT, $endpoint_key )
				);

				if( $existing_string_id ){

					$existing_wcml_string_id = $this->wpdb->get_var(
						$this->wpdb->prepare( "SELECT id FROM {$this->wpdb->prefix}icl_strings 
											WHERE context = %s AND name = %s",
							'WooCommerce Endpoints', $endpoint_key )
					);

					if( $existing_wcml_string_id ){
						$wcml_string_translations = icl_get_string_translations_by_id( $existing_wcml_string_id );

						foreach( $wcml_string_translations as $language_code => $translation_data ){
							icl_add_string_translation( $existing_string_id, $language_code, $translation_data['value'], ICL_STRING_TRANSLATION_COMPLETE );
						}

						wpml_unregister_string_multi( $existing_wcml_string_id );
					}
				}else{

					$this->wpdb->query(
						$this->wpdb->prepare( "UPDATE {$this->wpdb->prefix}icl_strings
                                  SET context = %s
                                  WHERE context = 'WooCommerce Endpoints' AND name = %s",
							WPML_Endpoints_Support::STRING_CONTEXT, $endpoint_key )
					);

					// update domain_name_context_md5 value
					$string_id = $this->wpdb->get_var( $this->wpdb->prepare( "SELECT id FROM {$this->wpdb->prefix}icl_strings WHERE context = %s AND name = %s", WPML_Endpoints_Support::STRING_CONTEXT, $endpoint_key ) );

					if ( $string_id ) {
						$this->wpdb->query(
							$this->wpdb->prepare( "UPDATE {$this->wpdb->prefix}icl_strings
                              SET domain_name_context_md5 = %s
                              WHERE id = %d",
								md5( $endpoint_key, WPML_Endpoints_Support::STRING_CONTEXT ), $string_id )
						);
					}
				}
			}
			update_option( 'wcml_endpoints_context_updated', true );
		}
	}

	public function reserved_requests( $requests ) {
		$cache_key   = 'reserved_requests';
		$cache_group = 'wpml-endpoints';

		$found             = null;
		$reserved_requests = wp_cache_get( $cache_key, $cache_group, false, $found );
		$is_page_display_as_translated = $this->sitepress->is_display_as_translated_post_type( 'page' );

		if (
			! $is_page_display_as_translated &&
			(
				! $found ||
				! $reserved_requests
			)
		) {
			$reserved_requests = array();

			$current_language = $this->sitepress->get_current_language();
			$languages        = $this->sitepress->get_active_languages();
			$languages_codes  = array_keys( $languages );
			foreach ( $languages_codes as $language_code ) {
				$this->sitepress->switch_lang( $language_code );

				$my_account_page_id = wc_get_page_id( 'myaccount' );

				if ( $my_account_page_id ) {
					$my_account_page = get_post( $my_account_page_id );
					if ( $my_account_page ) {
						$account_base = $my_account_page->post_name;

						$reserved_requests[] = $account_base;
						$reserved_requests[] = '/^' . $account_base . '/'; // regex version

						foreach ( $this->woocommerce_wpml->get_wc_query_vars() as $key => $endpoint ) {

							$translated_endpoint = $this->get_endpoint_translation( $endpoint, $language_code );

							$reserved_requests[] = $account_base . '/' . $translated_endpoint;
						}
					}
				}
			}
			$this->sitepress->switch_lang( $current_language );

			if ( $reserved_requests ) {
				wp_cache_set( $cache_key, $reserved_requests, $cache_group );
			}
		}

		if ( $reserved_requests ) {
			$requests = array_unique( array_merge( $requests, $reserved_requests ) );
		}

		return $requests;
	}

	public function add_wc_endpoints_translations( $language ) {

		if ( ! class_exists( 'WooCommerce' ) || ! defined( 'ICL_SITEPRESS_VERSION' ) || ICL_PLUGIN_INACTIVE || version_compare( WOOCOMMERCE_VERSION, '2.2', '<' ) ) {
			return false;
		}

		$wc_vars = WC()->query->query_vars;

		if ( ! empty( $wc_vars ) ) {

			foreach ( $wc_vars as $key => $endpoint ){
				$endpoint_translation = $this->get_endpoint_translation( $endpoint, $language );
				$query_vars[ $endpoint_translation ] = $endpoint_translation;
			}

			$query_vars = apply_filters( 'wcml_register_endpoints_query_vars', $query_vars, $wc_vars, $this );
			WC()->query->query_vars = array_merge( $wc_vars, $query_vars );
		}

	}

	public function get_endpoint_translation( $endpoint, $language = null ) {
		return apply_filters( 'wpml_get_endpoint_translation', $endpoint, $endpoint, $language );
	}

	public function endpoint_permalink_filter( $data, $endpoint_key ) {

		$link     = $data[0];
		$endpoint = $data[1];

		$endpoint = apply_filters( 'wcml_endpoint_permalink_filter', $endpoint, $endpoint_key );

		return array( $link, $endpoint );
	}

	/*
	 * We need check special case - when you manually put in URL default not translated endpoint it not generated 404 error
	 */
	public function check_if_endpoint_exists( $q ) {
		global $wp_query;

		$my_account_id = wc_get_page_id( 'myaccount' );

		$current_id = $q->query_vars['page_id'];
		if ( ! $current_id ) {
			$current_id = $q->queried_object_id;
		}

		if ( ! $q->is_404 && $current_id == $my_account_id && $q->is_page ) {

			$uri_vars        = array_filter( explode( '/', $_SERVER['REQUEST_URI'] ) );
			$endpoints       = WC()->query->get_query_vars();
			$endpoint_in_url = urldecode( end( $uri_vars ) );

			$endpoints['shipping'] = urldecode( $this->get_translated_edit_address_slug( 'shipping' ) );
			$endpoints['billing']  = urldecode( $this->get_translated_edit_address_slug( 'billing' ) );

			$endpoint_not_pagename         = isset( $q->query['pagename'] ) && urldecode( $q->query['pagename'] ) != $endpoint_in_url;
			$endpoint_url_not_in_endpoints = ! in_array( $endpoint_in_url, $endpoints );
			$uri_vars_not_in_query_vars    = ! in_array( urldecode( prev( $uri_vars ) ), $q->query_vars );

			if ( $endpoint_not_pagename && $endpoint_url_not_in_endpoints && is_numeric( $endpoint_in_url ) && $uri_vars_not_in_query_vars ) {
				$wp_query->set_404();
				status_header( 404 );
				include( get_query_template( '404' ) );
				die();
			}
		}
	}

	private function get_translated_edit_address_slug( $slug, $language = false ) {

		$strings_language = $this->woocommerce_wpml->strings->get_string_language( $slug, 'woocommerce', 'edit-address-slug: ' . $slug );
		if ( $strings_language == $language ) {
			return $slug;
		}

		$translated_slug = apply_filters( 'wpml_translate_single_string', $slug, 'woocommerce', 'edit-address-slug: ' . $slug, $language );
		if ( $translated_slug == $slug ) {
			if ( $language ) {
				$translated_slug = $this->woocommerce_wpml->strings->get_translation_from_woocommerce_mo_file( 'edit-address-slug' . chr( 4 ) . $slug, $language );
			} else {
				$translated_slug = _x( $slug, 'edit-address-slug', 'woocommerce' );
			}
		}

		return $translated_slug;
	}

	public function filter_get_endpoint_url( $url, $endpoint, $value, $permalink ) {

		remove_filter( 'woocommerce_get_endpoint_url', array( $this, 'filter_get_endpoint_url' ), 10, 4 );

		$translated_endpoint = $this->get_endpoint_translation( $endpoint );
		$url                 = wc_get_endpoint_url( $translated_endpoint, $value, $this->sitepress->convert_url( $permalink ) );

		add_filter( 'woocommerce_get_endpoint_url', array( $this, 'filter_get_endpoint_url' ), 10, 4 );

		return $url;
	}

	public function filter_endpoint_url_value( $value, $page_lang ) {

		if ( $page_lang ) {
			$edit_address_shipping = $this->get_translated_edit_address_slug( 'shipping', $page_lang );
			$edit_address_billing  = $this->get_translated_edit_address_slug( 'billing', $page_lang );

			if ( $edit_address_shipping == urldecode( $value ) ) {
				$value = $this->get_translated_edit_address_slug( 'shipping', $this->sitepress->get_current_language() );
			} elseif ( $edit_address_billing == urldecode( $value ) ) {
				$value = $this->get_translated_edit_address_slug( 'billing', $this->sitepress->get_current_language() );
			}
		}

		return $value;
	}

	public function add_endpoint_to_current_ls_language_url( $url, $post_lang, $data, $current_endpoint ){
		global $post;

		if (
			$current_endpoint &&
			$post &&
			$post_lang !== $data['code'] &&
			'page' == get_option( 'show_on_front' )
		) {

			$myaccount_page_id = wc_get_page_id( 'myaccount' );

			if (
				$myaccount_page_id === (int) get_option( 'page_on_front' ) &&
				$post->ID === $myaccount_page_id
			) {
				$url = apply_filters( 'wpml_get_endpoint_url', $current_endpoint['key'], $current_endpoint['value'], $url );
			}
		}

		return $url;
	}

}
