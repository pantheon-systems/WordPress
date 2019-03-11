<?php

class WCML_Status_Store_Pages_UI extends WPML_Templates_Factory {

    private $woocommerce_wpml;
    private $sitepress;

    function __construct( &$sitepress, &$woocommerce_wpml ){
        parent::__construct();

        $this->woocommerce_wpml = $woocommerce_wpml;
        $this->sitepress = $sitepress;

    }

    public function init_twig_functions() {
        $function = new Twig_SimpleFunction( 'get_flag_url', array( $this, 'get_flag_url' ) );
        $this->get_twig()->addFunction( $function );
    }

    public function get_model() {
        $this->init_twig_functions();

        $model = array(
            'miss_lang' => $this->woocommerce_wpml->store->get_missing_store_pages(),
            'install_link' => admin_url( 'admin.php?page=wc-status&tab=tools' ),
            'request_uri' => $_SERVER["REQUEST_URI"],
            'strings' => array(
                'store_pages' => __( 'WooCommerce Store Pages', 'woocommerce-multilingual' ),
                'pages_trnsl' => __( "To run a multilingual e-commerce site, you need to have the WooCommerce shop pages translated to all the site's languages. Once all the pages are installed you can add the translations for them from this menu.", 'woocommerce-multilingual' ),
                'store_pages' => __( 'WooCommerce Store Pages', 'woocommerce-multilingual' ),
                'not_created' => __( 'One or more WooCommerce pages have not been created.', 'woocommerce-multilingual' ),
                'install' => __( 'Install WooCommerce Pages', 'woocommerce-multilingual' ),
                'not_exist' => __( 'WooCommerce store pages do not exist for these languages:', 'woocommerce-multilingual' ),
                'create_transl' => __( 'Create missing translations', 'woocommerce-multilingual' ),
                'translated_wpml' => __( 'These pages are currently being translated by translators via WPML: ', 'woocommerce-multilingual' ),
                'translated' => __( "WooCommerce store pages are translated to all the site's languages.", 'woocommerce-multilingual' )
            ),
            'nonces' => array(
                'create_pages' => wp_nonce_field( 'create_pages', 'wcml_nonce' )
            )
        );

        return $model;

    }

    public function get_flag_url( $language ){
        return $this->sitepress->get_flag_url( $language );
    }

    public function init_template_base_dir() {
        $this->template_paths = array(
            WCML_PLUGIN_PATH . '/templates/status/',
        );
    }

    public function get_template() {
        return 'store-pages.twig';
    }

}