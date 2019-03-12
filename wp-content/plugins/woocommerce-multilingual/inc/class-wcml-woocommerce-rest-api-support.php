<?php
// Should only be used for WooCommerce versions prior 2.6
class WCML_WooCommerce_Rest_API_Support{

    private $woocommerce_wpml;
    /**
     * @var SitePress
     */
    private $sitepress;
    private $sitepress_settings;

    function __construct( &$woocommerce_wpml, &$sitepress ){

        $this->woocommerce_wpml     =& $woocommerce_wpml;
        $this->sitepress            =& $sitepress;
        $this->sitepress_settings   =  $this->sitepress->get_settings();

        add_action( 'parse_request', array( $this, 'use_canonical_home_url' ), -10 );
        add_action( 'init', array( $this, 'init' ) );

        add_filter( 'woocommerce_api_query_args', array($this, 'add_lang_parameter'), 10, 2 );
        add_filter( 'woocommerce_api_dispatch_args', array($this, 'dispatch_args_filter'), 10, 2 );

        add_filter( 'woocommerce_api_order_response' , array( $this, 'filter_order_items_by_language' ), 10, 4 );
        add_action( 'woocommerce_api_create_order' , array( $this, 'set_order_language' ), 10, 2 );


        add_action( 'woocommerce_api_create_product', array( $this, 'set_product_language' ), 10 , 2 );
        add_action( 'woocommerce_api_create_product', array( $this, 'set_product_custom_prices' ), 10 , 2 );
        add_action( 'woocommerce_api_product_response', array( $this, 'append_product_language_and_translations' ) );
        add_action( 'woocommerce_api_product_response', array( $this, 'append_product_secondary_prices' ) );

        add_action( 'woocommerce_api_edit_product', array( $this, 'sync_product_with_translations' ), 10, 2 );
    }

    public function init(){

        //remove rewrite rules filtering for PayPal IPN url
        if( strstr($_SERVER['REQUEST_URI'],'WC_Gateway_Paypal') && $this->sitepress_settings[ 'urls' ][ 'directory_for_default_language' ] ) {
            remove_filter('option_rewrite_rules', array($this->sitepress, 'rewrite_rules_filter'));
        }

    }

    // Use url without the language parameter. Needed for the signature match.
    public function use_canonical_home_url(){
        global $wp;

        if(!empty($wp->query_vars['wc-api-version'])) {
            global $wpml_url_filters;
	        $wpml_url_filters->remove_global_hooks();
            remove_filter('home_url', array($wpml_url_filters, 'home_url_filter'), -10, 4);

        }

    }

    public function add_lang_parameter( $args, $request_args ){

        if( isset( $request_args['lang'] ) ) {
            $args['lang'] = $request_args['lang'];
        }

        return $args;
    }

    public function dispatch_args_filter( $args, $callback ){
        global $wp;

        $route = $wp->query_vars['wc-api-route'];


        if( isset( $args['filter']['lang'] ) ){

            $lang = $args['filter']['lang'];

            $active_languages = $this->sitepress->get_active_languages();

            if ( !isset($active_languages[$lang]) && $lang != 'all' ) {
                throw new WC_API_Exception( '404', sprintf( __( 'Invalid language parameter: %s' ), $lang ), '404' );
            }

            if ( $lang != $this->sitepress->get_default_language() ) {
                if ( $lang != 'all' ) {

                    $this->sitepress->switch_lang( $lang  );

                }else{

                    switch($route){
                        case '/products':
                            // Remove filters for the post query
                            remove_action( 'query_vars', array( $this->sitepress, 'query_vars' ) );
                            global $wpml_query_filter;
                            remove_filter( 'posts_join', array( $wpml_query_filter, 'posts_join_filter' ), 10 );
                            remove_filter( 'posts_where', array( $wpml_query_filter, 'posts_where_filter' ), 10 );
                            break;

                        case '/products/categories':
                            // Remove WPML language filters for the terms query
                            remove_filter('terms_clauses', array($this->sitepress,'terms_clauses'));
                            remove_filter( 'get_term', array( $this->sitepress, 'get_term_adjust_id' ), 1 );
                            break;

                    }

                }
            }

            if( $route == '/orders'){
                add_filter( 'woocommerce_order_get_items', array( $this, 'get_order_items_in_the_current_language' ) );
            }


        }


        return $args;

    }

    /**
     * Filter orders content in the current language
     */
    public function get_order_items_in_the_current_language( $items ){

        $lang = get_query_var('lang');
        $wc_taxonomies = wc_get_attribute_taxonomies();
        $attributes = array();
        foreach( $wc_taxonomies as $taxonomy ){
            $attributes[] = 'pa_' . $taxonomy->attribute_name;
        }

        foreach( $items as $key => $item ){

            if( isset( $item['product_id'] ) ) {
                $translated_product_id = apply_filters( 'translate_object_id', $item['product_id'], 'product', true, $lang );
                $items[$key]['product_id'] = $translated_product_id;
                $items[$key]['item_meta']['_product_id'] = $translated_product_id;
                $items[$key]['name'] = get_post_field( 'post_title', $translated_product_id );
                foreach ( $item['item_meta_array'] as $k => $m ) {
                    if ( $m->key == '_product_id' ) {
                        $items[$key]['item_meta_array'][$k]->value = $translated_product_id;
                    }
                }
            }

            // Variations included
            if( !empty( $item['variation_id'] ) ){
                $translated_variation_id = apply_filters('translate_object_id', $item['variation_id'], 'product_variation', true, $lang);
                $items[$key]['variation_id'] = $translated_variation_id;
                $items[$key]['item_meta']['_variation_id'] = $translated_variation_id;
                foreach( $attributes as $attribute_name ){
                    if( isset( $item['item_meta'][$attribute_name] ) ){

                        foreach( $item['item_meta'][$attribute_name] as $idx => $attr ){
                            $term = get_term_by('slug',  $attr, $attribute_name);
                            $translated_term_id = apply_filters('translate_object_id', $term->term_id, $attribute_name, true, $lang);
                            $translated_term = get_term_by('id',  $translated_term_id, $attribute_name);
                            $items[$key]['item_meta'][$attribute_name][$idx] = $translated_term->slug;
                        }

                    }

                    if( isset( $item[$attribute_name] ) ){
                        $term = get_term_by('slug',  $item[$attribute_name], $attribute_name);
                        $translated_term_id = apply_filters('translate_object_id', $term->term_id, $attribute_name, true, $lang);
                        $translated_term = get_term_by('id',  $translated_term_id, $attribute_name);
                        $items[$key][$attribute_name] = $translated_term->slug;
                    }
                }

                foreach( $item['item_meta_array'] as $k => $m){
                    if($m->key == '_variation_id'){

                        $items[$key]['item_meta_array'][$k]->value = $translated_variation_id;

                    } elseif( in_array( $m->key, $attributes ) ){

                        $term = get_term_by('slug',  $m->value, $m->key);
                        $translated_term_id = apply_filters('translate_object_id', $term->term_id, $m->key, true, $lang);
                        $translated_term = get_term_by('id',  $translated_term_id, $m->key);
                        $items[$key]['item_meta_array'][$k]->value = $translated_term->slug;

                    }
                }


            }

        }

        return $items;

    }

    /**
     * Filters the items of an order according to a given languages
     *
     * @param $order_data
     * @param $order
     * @param $fields
     * @param $server
     * @return mixed
     */
    public function filter_order_items_by_language( $order_data, $order, $fields, $server ){

        $lang = get_query_var('lang');

        $order_lang = get_post_meta($order->ID, 'wpml_language');

        if( $order_lang != $lang ){

            foreach( $order_data['line_items'] as $k => $item ){

                if( isset( $item['product_id'] ) ){

                    $translated_product_id = apply_filters( 'translate_object_id', $item['product_id'], 'product', true, $lang );
                    if( $translated_product_id ){
                        $translated_product = get_post( $translated_product_id );
                        $order_data['line_items'][$k]['product_id'] = $translated_product_id;
                        if( $translated_product->post_type == 'product_variation' ){
                            $post_parent = get_post( $translated_product->post_parent );
                            $post_name = $post_parent->post_title;
                        } else {
                            $post_name = $translated_product->post_title;
                        }
                        $order_data['line_items'][$k]['name'] = $post_name;
                    }

                }

            }

        }

        return $order_data;
    }


    /**
     * Sets the language for a new order
     *
     * @param $order_id
     * @param $data
     *
     * @throws WC_API_Exception
     */
    public function set_order_language( $order_id, $data ){

        if( isset( $data['lang'] ) ){

            $active_languages = $this->sitepress->get_active_languages();
            if( !isset( $active_languages[$data['lang']] ) ){
                throw new WC_API_Exception( '404', sprintf( __( 'Invalid language parameter: %s' ), $data['lang'] ), '404' );
            }

            update_post_meta( $order_id, 'wpml_language', $data['lang']);

        }

    }

    /**
     * Sets the product information according to the provided language
     *
     * @param $id
     * @param $data
     *
     * @throws WC_API_Exception
     *
     */
    public function set_product_language( $id, $data ){

        if( isset( $data['lang'] )){
            $active_languages = $this->sitepress->get_active_languages();
            if( !isset( $active_languages[$data['lang']] ) ){
                throw new WC_API_Exception( '404', sprintf( __( 'Invalid language parameter: %s' ), $data['lang'] ), '404' );
            }
            if( isset( $data['translation_of'] ) ){
                $trid = $this->sitepress->get_element_trid( $data['translation_of'], 'post_product' );
                if( empty($trid) ){
                    throw new WC_API_Exception( '404', sprintf( __( 'Source product id not found: %s' ), $data['translation_of'] ), '404' );
                }
            }else{
                $trid = null;
            }
            $this->sitepress->set_element_language_details( $id, 'post_product', $trid, $data['lang'] );
        }

    }

    /**
     * Sets custom prices in secondary currencies for products
     *
     * @param $id
     * @param @data
     *
     */
    public function set_product_custom_prices( $id, $data ){

        if( !empty($this->woocommerce_wpml->multi_currency)  ){

            if( (!empty($data['custom_prices'])) && (empty($data['translation_of']))){
                update_post_meta( $id, '_wcml_custom_prices_status', 1);

                foreach( $data['custom_prices'] as $currency => $prices ){

                    $prices_uscore = array();
                    foreach( $prices as $k => $p){
                        $prices_uscore['_' . $k] = $p;
                    }

                    $this->woocommerce_wpml->multi_currency->custom_prices->update_custom_prices( $id, $prices_uscore, $currency );

                }

            }
        }

    }

    /**
     * Synchronizes product fields with its translations
     *
     * @param $id
     * @param $data
     */
    public function sync_product_with_translations( $id, $data ){

        // Force the WPML sync on post update for the changes
        // that don't trigger wp_save_post. e.g. stock
        if(
            !isset( $data['title']) ||
            !isset( $data['name']) ||
            !isset( $data['status']) ||
            !isset( $data['short_description']) ||
            !isset( $data['description'])
        ){
            $post = get_post( $id );
            wp_update_post( array( 'ID' => $id, 'post_title' => wc_clean( $post->post_title ) ) ); // triggers WPML sync
        }
    }

    /**
     * Appends the language and translation information to the get_product response
     *
     * @param $product_data
     */
    public function append_product_language_and_translations( $product_data ){

        $product_data['translations'] = array();

        $trid = $this->sitepress->get_element_trid( $product_data['id'], 'post_product' );
        $translations = $this->sitepress->get_element_translations( $trid, 'post_product');
        foreach( $translations as $translation ){
            if( $translation->element_id == $product_data['id'] ){
                $product_language = $translation->language_code;
            }else{
                $product_data['translations'][$translation->language_code] = $translation->element_id;
            }
        }

        $product_data['lang'] = $product_language;

        return $product_data;
    }

    /**
     * Appends the secondary prices information to the get_product response
     *
     * @param $product_data
     */
    public function append_product_secondary_prices( $product_data ){

        if( !empty($this->woocommerce_wpml->multi_currency) && !empty($this->woocommerce_wpml->settings['currencies_order']) ){

            $product_data['multi-currency-prices'] = array();

            $custom_prices_on = get_post_meta( $product_data['id'], '_wcml_custom_prices_status', true);

            foreach( $this->woocommerce_wpml->settings['currencies_order'] as $currency ){

                if( $currency != get_option('woocommerce_currency') ){

                    if( $custom_prices_on ){

                        $custom_prices = (array) $this->woocommerce_wpml->multi_currency->custom_prices->get_product_custom_prices( $product_data['id'], $currency );
                        foreach( $custom_prices as $key => $price){
                            $product_data['multi-currency-prices'][$currency][ preg_replace('#^_#', '', $key) ] = $price;

                        }

                    } else {
                        $product_data['multi-currency-prices'][$currency]['regular_price'] =
                            $this->woocommerce_wpml->multi_currency->prices->raw_price_filter( $product_data['regular_price'], $currency );
                        if( !empty($product_data['sale_price']) ){
                            $product_data['multi-currency-prices'][$currency]['sale_price'] =
                                $this->woocommerce_wpml->multi_currency->prices->raw_price_filter( $product_data['sale_price'], $currency );
                        }
                    }

                }

            }

        }

        return $product_data;
    }

}

?>