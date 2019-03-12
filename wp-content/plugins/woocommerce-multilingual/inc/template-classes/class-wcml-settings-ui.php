<?php

class WCML_Settings_UI extends WPML_Templates_Factory {

    /** @var woocommerce_wpml */
    private $woocommerce_wpml;
    /** @var Sitepress */
    private $sitepress;

    /**
     * WCML_Settings_UI constructor.
     *
     * @param woocommerce_wpml $woocommerce_wpml
     * @param SitePress $sitepress
     */
    function __construct( woocommerce_wpml $woocommerce_wpml, Sitepress $sitepress ){
        parent::__construct();

        $this->woocommerce_wpml = $woocommerce_wpml;
        $this->sitepress        = $sitepress;

    }

    public function get_model(){

        $model = array(
            'form' => array(
                'action' => $_SERVER['REQUEST_URI'],

                'translation_interface' => array(
                    'heading'   => __('Product Translation Interface','woocommerce-multilingual'),
                    'tip'       => __( 'The recommended way is using the WPML Translation Editor. It is streamlined for making the translation process much easier while also providing a much better integration with various WooCommerce extensions.',
                        'woocommerce-multilingual' ),
                    'wcml'      => array(
                        'label' => __('WPML Translation Editor', 'woocommerce-multilingual'),

                    ),
                    'native'    => array(
                        'label' => __('Native WooCommerce product editing screen' , 'woocommerce-multilingual'),

                    ),
                    'controls_value' => $this->woocommerce_wpml->settings['trnsl_interface'],
                    'pb_warning' => __("If you are using a page builder to design WooCommerce products, you should only use WPML's Translation Editor.", 'woocommerce-multilingual'),
                    'pb_warning_ok_button' => __('OK (translate with the WordPress editor)', 'woocommerce-multilingual'),
                    'pb_warning_cancel_button' => __('Cancel (stay with the Translation Editor)', 'woocommerce-multilingual'),
                ),

                'synchronization' => array(
                    'heading'   => __('Products Synchronization', 'woocommerce-multilingual'),
                    'tip'       => __( 'Configure specific product properties that should be synced to translations.', 'woocommerce-multilingual' ),
                    'sync_date' => array(
                        'value' => $this->woocommerce_wpml->settings['products_sync_date'],
                        'label' => __('Sync publishing date for translated products.', 'woocommerce-multilingual')
                    ),
                    'sync_order'=> array(
                        'value' => $this->woocommerce_wpml->settings['products_sync_order'],
                        'label' => __('Sync products and product taxonomies order.', 'woocommerce-multilingual')
                    ),
                ),

                'file_sync' => array(
                    'heading'       => __('Products Download Files', 'woocommerce-multilingual'),
                    'tip'           => __( 'If you are using downloadable products, you can choose to have their paths
                                            synchronized, or seperate for each language.', 'woocommerce-multilingual' ),
                    'value'         => $this->woocommerce_wpml->settings['file_path_sync'],
                    'label_same'    => __('Use the same files for translations', 'woocommerce-multilingual'),
                    'label_diff'    => __('Add separate download files for translations when translating products', 'woocommerce-multilingual'),
                ),

                'cart_sync' => array(
                    'tip'             => __('You can choose to clear the cart contents when you change language or currency in case you have problems in cart or checkout page', 'woocommerce-multilingual'),
                    'heading'         => __('Cart', 'woocommerce-multilingual'),
                    'lang_switch'     => array(
                        'heading'     => __('Switching languages when there are items in the cart', 'woocommerce-multilingual'),
                        'sync_label'  => __('Synchronize cart content when switching languages', 'woocommerce-multilingual'),
                        'clear_label' => __('Prompt for a confirmation and reset the cart', 'woocommerce-multilingual'),
                        'value' => $this->woocommerce_wpml->settings['cart_sync']['lang_switch']
                    ),
                    'currency_switch' => array(
                        'heading'     => __('Switching currencies when there are items in the cart', 'woocommerce-multilingual'),
                        'sync_label'  => __('Synchronize cart content when switching currencies', 'woocommerce-multilingual'),
                        'clear_label' => __('Prompt for a confirmation and reset the cart', 'woocommerce-multilingual'),
                        'value'       => $this->woocommerce_wpml->settings['cart_sync']['currency_switch']
                    ),
                    'wpml_cookie_enabled' => $this->sitepress->get_setting( WPML_Cookie_Setting::COOKIE_SETTING_FIELD ),
                    'cookie_not_enabled_message' => sprintf( __( 'This feature was disabled. Please enable %sWPML cookies%s to continue.',
                        'woocommerce-multilingual' ),
                        '<a href="'. admin_url( '?page='.WPML_PLUGIN_FOLDER.'/menu/languages.php#cookie' ) .'" target="_blank">', '</a>'
                    ),
                    'doc_link' => sprintf( __( 'Not sure which option to choose? Read about %spotential issues when switching languages and currencies while the cart has items%s.',
                        'woocommerce-multilingual' ),
                        '<a href="https://wpml.org/documentation/related-projects/woocommerce-multilingual/clearing-cart-contents-when-language-or-currency-change/" target="_blank">', '</a>'
                    ),
                ),

                'nonce'             => wp_nonce_field('wcml_save_settings_nonce', 'wcml_nonce', true, false),
                'save_label'        => __( 'Save changes', 'woocommerce-multilingual' ),

            ),

            'native_translation'  => WCML_TRANSLATION_METHOD_MANUAL,
            'wpml_translation'    => WCML_TRANSLATION_METHOD_EDITOR,

            'wcml_cart_sync'     => WCML_CART_SYNC,
            'wcml_cart_clear'    => WCML_CART_CLEAR,

            'troubleshooting' => array(
                'url'   => admin_url( 'admin.php?page=wpml-wcml&tab=troubleshooting' ),
                'label' => __( 'Troubleshooting', 'woocommerce-multilingual' )
            )
        );

        return $model;

    }

    protected function init_template_base_dir() {
        $this->template_paths = array(
            WCML_PLUGIN_PATH . '/templates/',
        );
    }

    public function get_template() {
        return 'settings-ui.twig';
    }


}