<?php

class WCML_Exchange_Rates_UI extends WPML_Templates_Factory {

    /**
     * @var woocommerce_wpml
     */
    private $woocommerce_wpml;
    /**
     * @var array
     */
    private $services;
    /**
     * @var array
     */
    private $settings;

    function __construct( $woocommerce_wpml ){
        parent::__construct();

        $this->woocommerce_wpml =& $woocommerce_wpml;
        $services = $this->woocommerce_wpml->multi_currency->exchange_rate_services->get_services();
        $this->settings = $this->woocommerce_wpml->multi_currency->exchange_rate_services->get_settings();

        foreach( $services as $id => $service ){
            $this->services[ $id ] = array(
                'name'          => $service->get_name(),
                'url'           => $service->get_url(),
                'requires_key'  => $service->is_key_required(),
                'api_key'       => $service->get_setting( 'api-key' ),
                'last_error'    => $service->get_last_error()
            );
        }
    }

    public function get_model(){

        $last_updated  = empty( $this->settings['last_updated'] ) ?
                            '<i>' . __( 'never', 'woocommerce-multilingual' ) . '</i>' :
                            date_i18n( 'F j, Y g:i a', $this->settings['last_updated'] );

        $model = array(
            'strings' => array(

                'header'            => __( 'Automatic Exchange Rates', 'woocommerce-multilingual' ),
                'no_currencies'     => __( "You haven't added any secondary currencies.", 'woocommerce-multilingual' ),
	            'enable_automatic'  => __( 'Enable automatic exchange rates', 'woocommerce-multilingual' ),
                'services_label'    => __( 'Exchange rates source', 'woocommerce-multilingual' ),
                'lifting_label'     => __( 'Lifting charge', 'woocommerce-multilingual' ),
                'lifting_details1'   => __( 'The lifting charge adjusts the exchange rate provided by the selected service before it is saved. The exchange rates displayed in the table above include the lifting charge.', 'woocommerce-multilingual' ),
                'lifting_details2'   => __( 'Exchange rate = %s exchange rate x (1 + lifting charge / 100)', 'woocommerce-multilingual' ),
                'services_api'      => __( 'API key (required)', 'woocommerce-multilingual' ),
                'frequency'         => __( 'Update frequency', 'woocommerce-multilingual' ),
                'update'            => __( 'Update manually now', 'woocommerce-multilingual' ),
	            'update_tip'        => __( 'You have to save all settings before updating exchange rates', 'woocommerce-multilingual' ),
                'manually'          => __( 'Manually', 'woocommerce-multilingual'),
                'daily'             => __( 'Daily', 'woocommerce-multilingual' ),
                'weekly'            => __( 'Weekly on', 'woocommerce-multilingual' ),
                'monthly'           => __( 'Monthly on the', 'woocommerce-multilingual' ),
                'key_placeholder'   => __( 'Enter API key', 'woocommerce-multilingual' ),
                'key_required'      => __( 'API key (required)', 'woocommerce-multilingual' ),
                'fixerio_warning'   => __( 'WARNING! Minor limitations include 1000 requests/month limit and EUR being the only available base currency for customers using a free account. If you need more than 1000 requests per month or want to use all 170 available base currencies, you’ll need to choose one of the paid plans starting at only $10 per month.', 'woocommerce-multilingual' ),
                'nonce'             => wp_create_nonce( 'update-exchange-rates' ),
                'updated_time'      => sprintf(
                                        __('Last updated: %s', 'woocommerce-multilingual' ),
                                            '<span class="time">' . $last_updated . '</span>'
                                        ),
                'updated_success'   => __( 'Exchange rates updated successfully', 'woocommerce-multilingual' ),
                'visit_website'     => __( 'Visit website', 'woocommerce-multilingual' )

            ),

            'services'              => $this->services,
            'settings'              => $this->settings,

            'secondary_currencies'  => $this->woocommerce_wpml->multi_currency->get_currencies(),

        );

        return $model;
    }

    protected function init_template_base_dir() {
        $this->template_paths = array(
            WCML_PLUGIN_PATH . '/templates/multi-currency/',
        );
    }

    public function get_template() {
        return 'exchange-rates.twig';
    }

}