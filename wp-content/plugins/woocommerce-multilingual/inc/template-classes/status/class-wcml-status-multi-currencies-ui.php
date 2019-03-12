<?php

class WCML_Status_Multi_Currencies_UI extends WPML_Templates_Factory {

    private $woocommerce_wpml;

    function __construct( &$woocommerce_wpml ){
        parent::__construct();

        $this->woocommerce_wpml = $woocommerce_wpml;
    }

    public function get_model() {

        $sec_currencies = array();
        $sec_currencies_codes = array();

        if( $this->woocommerce_wpml->settings['enable_multi_currency'] == WCML_MULTI_CURRENCIES_INDEPENDENT ){
            $sec_currencies = $this->woocommerce_wpml->multi_currency->get_currencies();
            foreach( $sec_currencies as $code => $sec_currency ){
                $sec_currencies_codes[] = $code;
            }
        }

        $model = array(
            'mc_enabled' => $this->woocommerce_wpml->settings['enable_multi_currency'] == WCML_MULTI_CURRENCIES_INDEPENDENT,
            'sec_currencies' => join( ', ', $sec_currencies_codes ),
            'add_cur_link' => admin_url( 'admin.php?page=wpml-wcml&tab=multi-currency' ),
            'strings' => array(
                'mc_missing' => __( 'Multi-currency', 'woocommerce-multilingual' ),
                'no_secondary' => __( "You haven't added any secondary currencies.", 'woocommerce-multilingual' ),
                'sec_currencies' => __( 'Secondary currencies: %s', 'woocommerce-multilingual' ),
                'not_enabled' => __( 'Multi-currency is not enabled.', 'woocommerce-multilingual' ),
                'add_cur' => __( 'Add Currencies', 'woocommerce-multilingual' )
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
        return 'multi_currencies.twig';
    }

}