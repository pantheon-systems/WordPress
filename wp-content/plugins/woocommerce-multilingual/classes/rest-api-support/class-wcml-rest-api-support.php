<?php

class WCML_REST_API_Support{

	/** @var woocommerce_wpml */
	private $woocommerce_wpml;
	/** @var Sitepress */
	private $sitepress;
	/** @var wpdb */
	private $wpdb;
	/** @var  WCML_REST_API_Query_Filters_Products */
	private $query_filters_products;
	/** @var  WCML_REST_API_Query_Filters_Orders */
	private $query_filters_orders;
	/** @var  WCML_REST_API_Query_Filters_Terms */
	private $query_filters_terms;
	/** @var WPML_Admin_Post_Actions */
	private $wpml_post_translations;


	/**
	 * WCML_REST_API_Support constructor.
	 *
	 * @param woocommerce_wpml $woocommerce_wpml
	 * @param SitePress $sitepress
	 * @param wpdb $wpdb
	 * @param WCML_REST_API_Query_Filters_Products $query_filters_products
	 * @param WCML_REST_API_Query_Filters_Orders $query_filters_orders
	 * @param WCML_REST_API_Query_Filters_Terms $query_filters_terms
	 * @param WPML_Post_Translation $wpml_post_translations
	 */
	public function __construct(
		woocommerce_wpml $woocommerce_wpml,
		SitePress $sitepress,
		wpdb $wpdb,
		WCML_REST_API_Query_Filters_Products $query_filters_products,
		WCML_REST_API_Query_Filters_Orders $query_filters_orders,
		WCML_REST_API_Query_Filters_Terms $query_filters_terms,
		WPML_Post_Translation $wpml_post_translations
	) {
		$this->woocommerce_wpml       = $woocommerce_wpml;
		$this->sitepress              = $sitepress;
		$this->wpdb                   = $wpdb;
		$this->query_filters_products = $query_filters_products;
		$this->query_filters_orders   = $query_filters_orders;
		$this->query_filters_terms    = $query_filters_terms;
		$this->wpml_post_translations = $wpml_post_translations;

		$this->prevent_default_lang_url_redirect();
	}

	public function add_hooks(){
		add_action( 'rest_api_init', array( $this, 'set_language_for_request' ) );

		add_action( 'parse_query', array($this, 'auto_adjust_included_ids') );

		// Products
		add_action( 'woocommerce_rest_prepare_product_object', array( $this, 'append_product_language_and_translations' ) );
		add_action( 'woocommerce_rest_prepare_product_object', array( $this, 'append_product_secondary_prices' ) );

		add_action( 'woocommerce_rest_insert_product_object', array( $this, 'set_product_language' ), 10, 2 );
		add_action( 'woocommerce_rest_insert_product_object', array( $this, 'set_product_custom_prices' ), 10, 2 );
		add_action( 'woocommerce_rest_insert_product_object', array( $this, 'copy_custom_fields_from_original' ), 10, 1 );

		add_action( 'woocommerce_rest_prepare_product_object', array( $this, 'copy_product_custom_fields' ), 10 , 3 );

		// Orders
		add_action( 'woocommerce_rest_insert_shop_order_object' , array( $this, 'set_order_language' ), 10, 2 );

		$this->query_filters_products->add_hooks();
		$this->query_filters_orders->add_hooks();
		$this->query_filters_terms->add_hooks();

	}

	/**
	 * Enforces the language of request as the current language to be able to filter items by language
	 * @param WP_REST_Server $wp_rest_server
	 */
	public function set_language_for_request( $wp_rest_server ){
		if( isset( $_GET['lang'] )  ){
			$request_language = $_GET['lang'];
			$active_languages = $this->sitepress->get_active_languages();
			if( isset( $active_languages[ $request_language ] ) ){
				$this->sitepress->switch_lang( $request_language );
			}
		}
	}

	/**
	 * Prevent WPML redirection when using the default language as a parameter in the url
	 */
	private function prevent_default_lang_url_redirect(){
		$exp = explode( '?', $_SERVER['REQUEST_URI'] );
		if ( ! empty( $exp[1] ) ) {
			parse_str( $exp[1], $vars );
			if ( isset($vars['lang']) && $vars['lang'] === $this->sitepress->get_default_language() ) {
				unset( $vars['lang'] );
				$_SERVER['REQUEST_URI'] = $exp[0] . '?' . http_build_query( $vars );
			}
		}
	}

	/**
	 * @param WP_Query $wp_query
	 */
	public function auto_adjust_included_ids( $wp_query ){
		$lang = $wp_query->get('lang');
		$include = $wp_query->get('post__in');
		if( empty( $lang ) && !empty( $include ) ){
			$filtered_include = array();
			foreach( $include as $id ){
				$filtered_include[] = apply_filters( 'translate_object_id', $id, get_post_type($id), true );
			}
			$wp_query->set( 'post__in' , $filtered_include );
		}
	}

	/**
	 * Appends the language and translation information to the get_product response
	 *
	 * @param $product_data
	 *
	 * @return WP_REST_Response
	 */
	public function append_product_language_and_translations( $product_data ){

		$product_data->data['translations'] = array();

		$trid = $this->sitepress->get_element_trid( $product_data->data['id'], 'post_product' );

		if( $trid ) {
			$translations = $this->sitepress->get_element_translations( $trid, 'post_product' );
			foreach ( $translations as $translation ) {
				if ( $translation->element_id == $product_data->data['id'] ) {
					$product_language = $translation->language_code;
				} else {
					$product_data->data['translations'][ $translation->language_code ] = $translation->element_id;
				}
			}

			$product_data->data['lang'] = $product_language;
		}

		return $product_data;
	}

	/**
	 * Appends the secondary prices information to the get_product response
	 *
	 * @param $product_data
	 *
	 * @return WP_REST_Response
	 */
	public function append_product_secondary_prices( $product_data ){

		if( !empty($this->woocommerce_wpml->multi_currency) && !empty($this->woocommerce_wpml->settings['currencies_order']) ){

			$product_data->data['multi-currency-prices'] = array();

			$custom_prices_on = get_post_meta( $product_data->data['id'], '_wcml_custom_prices_status', true);

			foreach( $this->woocommerce_wpml->settings['currencies_order'] as $currency ){

				if( $currency != get_option('woocommerce_currency') ){

					if( $custom_prices_on ){

						$custom_prices = (array) $this->woocommerce_wpml->multi_currency->custom_prices->get_product_custom_prices( $product_data->data['id'], $currency );
						foreach( $custom_prices as $key => $price){
							$product_data->data['multi-currency-prices'][$currency][ preg_replace('#^_#', '', $key) ] = $price;

						}

					} else {
						$product_data->data['multi-currency-prices'][$currency]['regular_price'] =
							$this->woocommerce_wpml->multi_currency->prices->raw_price_filter( $product_data->data['regular_price'], $currency );
						if( !empty($product_data->data['sale_price']) ){
							$product_data->data['multi-currency-prices'][$currency]['sale_price'] =
								$this->woocommerce_wpml->multi_currency->prices->raw_price_filter( $product_data->data['sale_price'], $currency );
						}
					}

				}

			}

		}

		return $product_data;
	}

	/**
	 * Sets the product information according to the provided language
	 *
	 * @param object $product
	 * @param WP_REST_Request $request
	 *
	 * @throws WCML_REST_Invalid_Language_Exception
	 * @throws WCML_REST_Invalid_Product_Exception
	 * @throws WCML_REST_Generic_Exception
	 *
	 */
	public function set_product_language( $product, $request ){

		$data = $request->get_params();

		if( isset( $data['lang'] ) && 'POST' === $request->get_method() ){
			$active_languages = $this->sitepress->get_active_languages();
			if( !isset( $active_languages[$data['lang']] ) ){
				throw new WCML_REST_Invalid_Language_Exception( $data['lang'] );
			}
			if( isset( $data['translation_of'] ) ){
				$trid = $this->sitepress->get_element_trid( $data['translation_of'], 'post_product' );
				if( empty($trid) ){
					throw new WCML_REST_Invalid_Product_Exception( $data['translation_of'] );
				}
			}else{
				$trid = null;
			}

			$this->sitepress->set_element_language_details( $product->get_id(), 'post_product', $trid, $data['lang'] );
			wpml_tm_save_post( $product->get_id(), get_post( $product->get_id() ), ICL_TM_COMPLETE );
		}else{
			if( isset( $data['translation_of'] ) ){
				throw new WCML_REST_Generic_Exception( __( 'Using "translation_of" requires providing a "lang" parameter too', 'woocommerce-multilingual' ) );
			}
		}

	}

	/**
	 * Sets custom prices in secondary currencies for products
	 *
	 * @param object $product
	 * @param WP_REST_Request $request
	 *
	 * @throws WC_API_Exception
	 *
	 */
	public function set_product_custom_prices( $product, $request ){

		$data = $request->get_params();

		if( !empty( $this->woocommerce_wpml->multi_currency )  ){

			if( !empty( $data['custom_prices'] ) ){

				$original_post_id = $this->sitepress->get_original_element_id_filter('', $product->get_id(), 'post_product' );

				update_post_meta( $original_post_id, '_wcml_custom_prices_status', 1);

				foreach( $data['custom_prices'] as $currency => $prices ){

					$prices_uscore = array();
					foreach( $prices as $k => $p){
						$prices_uscore['_' . $k] = $p;
					}
					$this->woocommerce_wpml->multi_currency->custom_prices->update_custom_prices( $original_post_id, $prices_uscore, $currency );

				}

			}
		}

	}

	/**
	 * @param WC_Product $product
	 */
	public function copy_custom_fields_from_original( WC_Product $product ){
		$original_post_id = $this->sitepress->get_original_element_id_filter('', $product->get_id(), 'post_product' );

		if( $original_post_id !== $product->get_id() ){
			$this->sitepress->copy_custom_fields( $original_post_id, $product->get_id() );
		}
	}

	/**
	 * @param WP_REST_Response $response
	 * @param mixed $object
	 * @param WP_REST_Request $request
	 *
	 * Copy custom fields explicitly
	 *
	 * @return WP_REST_Response
	 */
	public function copy_product_custom_fields($response, $object, $request){
		$data = $request->get_params();

		if( isset( $data['id'] ) ) {
			$translations = $this->wpml_post_translations->get_element_translations( $data['id'], false, true );
			foreach ( $translations as $translation_id ) {
				$this->sitepress->copy_custom_fields( $data['id'], $translation_id );
			}
		}

		return $response;
	}

	/**
	 * Sets the language for a new order
	 *
	 * @param WC_Order $order
	 * @param WP_REST_Request $request
	 *
	 * @throws WCML_REST_Invalid_Language_Exception
	 */
	public function set_order_language( $order, $request ){

		$data = $request->get_params();
		if( isset( $data['lang'] ) ){
			$order_id = $order->get_id();
			$active_languages = $this->sitepress->get_active_languages();
			if( !isset( $active_languages[$data['lang']] ) ){
				throw new WCML_REST_Invalid_Language_Exception( $data['lang'] );
			}

			update_post_meta( $order_id, 'wpml_language', $data['lang'] );

		}

	}

	/**
	 * @param WC_Order $order
	 * @param WP_REST_Request $request
	 *
	 * @throws WCML_REST_Invalid_Currency_Exception
	 */
	public function set_order_currency( $order, $request ) {
		$data = $request->get_params();
		if ( isset( $data['currency'] ) ) {
			$order_id   = $order->get_id();
			$currencies = get_woocommerce_currencies();
			if ( ! isset( $currencies[ $data['currency'] ] ) ) {
				throw new WCML_REST_Invalid_Currency_Exception( $data['currency'] );
			}
			update_post_meta( $order_id, '_order_currency', $data['currency'] );
		}
	}

}