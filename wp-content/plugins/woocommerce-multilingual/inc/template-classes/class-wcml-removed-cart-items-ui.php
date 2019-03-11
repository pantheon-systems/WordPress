<?php

class WCML_Removed_Cart_Items_UI extends WPML_Templates_Factory {

    /**
     * @var woocommerce_wpml
     */
    private $woocommerce_wpml;
    /**
     * @var array
     */
    private $args;
    /**
     * @var sitepress
     */
    private $sitepress;
    /**
     * @var woocommerce
     */
    private $woocommerce;

    function __construct( &$args, &$woocommerce_wpml, &$sitepress, $woocommerce ){

        $this->woocommerce_wpml =& $woocommerce_wpml;
        $this->args             = $args;
        $this->sitepress        = $sitepress;
        $this->woocommerce      = $woocommerce;

        parent::__construct();
    }

    public function get_model(){

        $language_details = $this->sitepress->get_language_details( $this->sitepress->get_current_language() );
        $switched_to = $this->woocommerce->session->get( 'wcml_switched_type' ) == 'currency' ? $this->woocommerce_wpml->multi_currency->get_client_currency() : $language_details[ 'display_name' ];

        $model = array(
            'products' => $this->get_removed_products(),
            'title' => sprintf( __( 'Products removed after switching to %s:', 'woocommerce-multilingual'), $switched_to ),
            'clear' => __( 'Clear list', 'woocommerce-multilingual'),
            'nonce' => wp_create_nonce( 'wcml_clear_removed_items' ),
        );

        return $model;
    }

    public function get_removed_products(){

        $current_language = $this->sitepress->get_current_language();
        $removed_products_from_session = maybe_unserialize( $this->woocommerce->session->get( 'wcml_removed_items' ) );
        $removed_products = array();
        $removed_product_ids = array();

        if( is_array( $removed_products_from_session ) ){
            foreach( $removed_products_from_session as $key => $product_id ){
                $tr_product_id = apply_filters( 'translate_object_id', $product_id, 'product', false, $current_language );

                if( !is_null( $tr_product_id ) && !in_array( $tr_product_id, $removed_product_ids ) ){
                    $removed_products[ $key ][ 'id' ] = $removed_product_ids[] = $tr_product_id;
                    $removed_products[ $key ][ 'url' ] = get_post_permalink( $tr_product_id );
                    $removed_products[ $key ][ 'title' ] = get_post( $tr_product_id )->post_title;
                }
            }
        }

        return $removed_products;

    }

    public function render(){
        echo $this->get_view();
    }

    protected function init_template_base_dir() {
        $this->template_paths = array(
            WCML_PLUGIN_PATH . '/templates/',
        );
    }

    public function get_template() {
        return 'removed-cart-items.twig';
    }



}