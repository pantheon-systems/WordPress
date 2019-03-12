<?php

class WCML_Status_Status_UI extends WPML_Templates_Factory {

    private $sitepress;

    function __construct( &$sitepress ){
        parent::__construct();

        $this->sitepress = $sitepress;
    }

    public function get_model() {

        $model = array(
            'icl_version' => defined( 'ICL_SITEPRESS_VERSION' ),
            'tm_version' => defined( 'WPML_TM_VERSION' ),
            'st_version' => defined( 'WPML_ST_VERSION' ),
            'wc' => class_exists( 'WooCommerce' ),
            'icl_setup' => $this->sitepress->setup(),
            'strings' => array(
                'status' => __( 'Plugins Status', 'woocommerce-multilingual' ),
                'inst_active' => __( '%s is installed and active.', 'woocommerce-multilingual' ),
                'is_setup' => __( '%s is set up.', 'woocommerce-multilingual' ),
                'not_setup' => __( '%s is not set up.', 'woocommerce-multilingual' ),
                'wpml' => '<strong>WPML</strong>',
                'tm' => '<strong>WPML Translation Management</strong>',
                'st' => '<strong>WPML String Translation</strong>',
                'wc' => '<strong>WooCommerce</strong>',
                'depends' => __( 'WooCommerce Multilingual depends on several plugins to work. If any required plugin is missing, you should install and activate it.', 'woocommerce-multilingual' )
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
        return 'plugins-status.twig';
    }

}