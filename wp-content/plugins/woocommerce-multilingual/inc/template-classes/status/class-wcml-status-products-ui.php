<?php

class WCML_Status_Products_UI extends WPML_Templates_Factory {

    private $woocommerce_wpml;
    private $sitepress;

    function __construct( &$woocommerce_wpml, &$sitepress ){
        parent::__construct();

        $this->woocommerce_wpml = $woocommerce_wpml;
        $this->sitepress = $sitepress;
    }

    public function get_model() {

        $products = array();

        foreach( $this->sitepress->get_active_languages() as $language ){

            $products_count = $this->woocommerce_wpml->products->get_untranslated_products_count( $language[ 'code' ] );
            if( $products_count ){
                $products[ $language[ 'code' ] ][ 'count' ] = $this->woocommerce_wpml->products->get_untranslated_products_count( $language[ 'code' ] );
                $products[ $language[ 'code' ] ][ 'flag' ] = $this->sitepress->get_flag_img( $language[ 'code' ] );
                $products[ $language[ 'code' ] ][ 'display_name' ] = $language[ 'display_name' ];
            }
        }

        $model = array(
            'products' => $products,
            'trnsl_link' => admin_url( 'admin.php?page=wpml-wcml' ),
            'strings' => array(
                'products_missing'  => __( 'Products Missing Translations', 'woocommerce-multilingual' ),
                'miss_trnsl_one'    => __( '%d %s translation missing.', 'woocommerce-multilingual' ),
                'miss_trnsl_more'   => __( '%d %s translations missing.', 'woocommerce-multilingual' ),
                'transl'            => __( 'Translate Products', 'woocommerce-multilingual' ),
                'not_to_trnsl'      => __( 'Right now, there are no products needing translation.', 'woocommerce-multilingual' )
            )
        );

        return $model;

    }

    public function init_template_base_dir() {
        $this->template_paths = array(
            WCML_PLUGIN_PATH . '/templates/status/',
        );
    }

    public function get_template() {
        return 'products.twig';
    }

}