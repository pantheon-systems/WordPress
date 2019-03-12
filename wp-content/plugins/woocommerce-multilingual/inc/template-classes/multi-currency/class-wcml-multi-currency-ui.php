<?php

class WCML_Multi_Currency_UI extends WPML_Templates_Factory {

    /**
     * @var woocommerce_wpml
     */
    private $woocommerce_wpml;
    /**
     * @var SitePress
     */
    private $sitepress;
    /**
     * @var array
     */
    private $currencies;
    /**
     * @var array
     */
    private $wc_currencies;
    /**
     * @var string
     */
    private $wc_currency;

	/** @var WCML_Tracking_Link */
	private $tracking_link;

    function __construct( &$woocommerce_wpml, &$sitepress ){

        $functions = array(
            new Twig_SimpleFunction( 'get_flag_url', array( $this, 'get_flag_url' ) ),
            new Twig_SimpleFunction( 'is_currency_on', array( $this, 'is_currency_on' ) ),
            new Twig_SimpleFunction( 'get_language_currency', array( $this, 'get_language_currency' ) ),
            new Twig_SimpleFunction( 'get_currency_symbol', array( $this, 'get_currency_symbol' ) ),
            new Twig_SimpleFunction( 'get_currency_name', array( $this, 'get_currency_name' ) ),
            new Twig_SimpleFunction( 'wp_do_action', array( $this, 'wp_do_action' ) ),
            new Twig_SimpleFunction( 'get_weekday', array( $this, 'get_weekday' ) )
        );

        parent::__construct( $functions );
        $this->woocommerce_wpml = $woocommerce_wpml;
        $this->sitepress = $sitepress;

        $this->currencies       = $this->woocommerce_wpml->multi_currency->get_currencies();
        $this->wc_currencies    = get_woocommerce_currencies();
        $this->wc_currency      = get_option( 'woocommerce_currency' );

        $this->load_custom_currency_option_boxes();
        $this->load_curency_switcher_option_boxes();

        $this->tracking_link = new WCML_Tracking_Link();
    }

    public function get_model(){

        $currencies_positions = array();
        foreach ( $this->currencies as $code => $currency ){
            $currencies_positions[$code] = $this->price_position_format( $currency['position'], $code );
        }

        $exchange_rates_ui = new WCML_Exchange_Rates_UI( $this->woocommerce_wpml );

        $model = array(
            'strings' => array(
                'headers' => array(
                    'enable_disable'    => __( 'Enable/disable', 'woocommerce-multilingual' ),
                    'currencies'        => __( 'Currencies', 'woocommerce-multilingual' ),
                ),
                'add_currency_button'   => __( 'Add currency', 'woocommerce-multilingual' ),
                'currencies_table' => array(
                    'head_currency'     => __('Currency', 'woocommerce-multilingual'),
                    'head_rate'         => __('Rate', 'woocommerce-multilingual'),
                    'default'           => __( 'default', 'woocommerce-multilingual' ),
                    'edit'              => __( 'Edit', 'woocommerce-multilingual' ),
                    'default_currency'  => __( 'Default currency', 'woocommerce-multilingual' ),
                    'default_cur_tip'   => __( 'Switch to this currency when switching language in the front-end', 'woocommerce-multilingual' ),
                    'keep_currency'     => __( 'Keep', 'woocommerce-multilingual' ),
                    'delete'            => __( 'Delete', 'woocommerce-multilingual' ),
                    'help_title'        => __( 'Currencies to display for each language', 'woocommerce-multilingual' ),
                    'enable_for'        => __('Enable %s for %s', 'woocommerce-multilingual'),
                    'disable_for'       => __('Disable %s for %s', 'woocommerce-multilingual')
                )

            ),
            'currencies'            => $this->currencies,
            'currencies_positions'  => $currencies_positions,
            'wc_currency'           => $this->wc_currency,
            'wc_currencies'         => $this->wc_currencies,
            'positioned_price'      => sprintf( __( ' (%s)', 'woocommerce-multilingual' ), $this->get_positioned_price( $this->wc_currency ) ) ,

            'active_languages'      => $this->sitepress->get_active_languages(),

            'multi_currency_on'     => $this->woocommerce_wpml->settings['enable_multi_currency'] == WCML_MULTI_CURRENCIES_INDEPENDENT,

            'wc_currency_empty_warn' => sprintf(__('The multi-currency mode cannot be enabled as a specific currency was not set. Go to the %sWooCommerce settings%s page and select the default currency for your store.',
                                        'woocommerce-multilingual'), '<a href="' . admin_url('admin.php?page=wc-settings') . '">', '</a>'),
            'wcml_settings' => $this->woocommerce_wpml->settings,
            'form' => array(
                'action'                    => $_SERVER['REQUEST_URI'],
                'nonce'                     => wp_nonce_field( 'wcml_mc_options', 'wcml_nonce', true, false ),
                'save_currency_nonce'       => wp_create_nonce( 'save_currency' ),
                'del_currency_nonce'        => wp_create_nonce( 'wcml_delete_currency' ),
                'multi_currency_option'     => WCML_MULTI_CURRENCIES_INDEPENDENT,
                'mco_disabled'              => empty($wc_currency),
                'label_mco'                 => __( "Enable the multi-currency mode", 'woocommerce-multilingual' ),
                'label_mco_learn_url'       => $this->tracking_link->generate( 'https://wpml.org/documentation/related-projects/woocommerce-multilingual/multi-currency-support-woocommerce/', 'multi-currency-support-woocommerce', 'documentation' ),
                'label_mco_learn_txt'       => __( 'Learn more', 'woocommerce-multilingual' ),
                'update_currency_lang_nonce'=> wp_create_nonce( 'wcml_update_currency_lang' ),
                'wpdate_default_cur_nonce'  => wp_create_nonce( 'wcml_update_default_currency' ),
                'custom_prices_select'      => array(
                    'checked'   => $this->woocommerce_wpml->settings['display_custom_prices'] == 1,
                    'label'     => __( 'Show only products with custom prices in secondary currencies', 'woocommerce-multilingual' ),
                    'tip'       => __( 'When this option is on, when you switch to a secondary currency on the front end, only the products with custom prices in that currency are being displayed. Products with prices determined based on the exchange rate are hidden.', 'woocommerce-multilingual' )
                ),
                'submit'        => __( 'Save changes', 'woocommerce-multilingual' ),
                'navigate_warn' => __( 'The changes you made will be lost if you navigate away from this page.', 'woocommerce-multilingual' ),
                'cur_lang_warn' => __( 'At least one currency must be enabled for this language!', 'woocommerce-multilingual' )

            ),

            'currency_switcher' => array(
                'headers' => array(
                    'main'            => __('Currency switcher options', 'woocommerce-multilingual'),
                    'main_desc'       => __('All currency switchers in your site are affected by the settings in this section.', 'woocommerce-multilingual'),
                    'order'           => __( 'Order of currencies', 'woocommerce-multilingual' ),
                    'additional_css'  => __('Additional CSS', 'woocommerce-multilingual'),
                    'widget'          => __('Widget Currency Switcher', 'woocommerce-multilingual'),
                    'product_page'    => __('Product page Currency Switcher', 'woocommerce-multilingual'),
                    'preview'         => __('Preview', 'woocommerce-multilingual'),
                    'position'        => __('Position', 'woocommerce-multilingual'),
                    'actions'         => __('Actions', 'woocommerce-multilingual'),
                    'action'          => __('Action', 'woocommerce-multilingual'),
                    'delete'          => __('Delete', 'woocommerce-multilingual'),
                    'edit'            => __('Edit currency switcher', 'woocommerce-multilingual'),
                    'add_widget'      => __('Add a new currency switcher to a widget area', 'woocommerce-multilingual'),
                ),
                'preview'       => $this->get_currency_switchers_preview(),
                'widget_currency_switchers' => $this->widget_currency_switchers(),
                'available_sidebars'    => $this->woocommerce_wpml->multi_currency->currency_switcher->get_available_sidebars(),
                'preview_text'  => __( 'Currency switcher preview', 'woocommerce-multilingual' ),
                'order'             => !isset( $this->woocommerce_wpml->settings['currencies_order'] ) ?
                                        $this->woocommerce_wpml->multi_currency->get_currency_codes() :
                                        $this->woocommerce_wpml->settings['currencies_order'],
                'order_nonce'       => wp_create_nonce( 'set_currencies_order_nonce' ),
                'delete_nonce'      => wp_create_nonce( 'delete_currency_switcher' ),
                'order_tip'         => __( 'Drag and drop the currencies to change their order', 'woocommerce-multilingual' ),
                'visibility_label'  => __('Show a currency selector on the product page template', 'woocommerce-multilingual'),
                'visibility_on'     => isset($this->woocommerce_wpml->settings['currency_switcher_product_visibility']) ?
                                        $this->woocommerce_wpml->settings['currency_switcher_product_visibility']:1,
                'additional_css'     => isset($this->woocommerce_wpml->settings['currency_switcher_additional_css']) ?
                                        $this->woocommerce_wpml->settings['currency_switcher_additional_css']:''
            ),
            'exchange_rates'        => $exchange_rates_ui->get_model()
        );

        return $model;

    }

    protected function init_template_base_dir() {
        $this->template_paths = array(
            WCML_PLUGIN_PATH . '/templates/multi-currency/',
        );
    }

    public function get_template() {
        return 'multi-currency.twig';
    }

    protected function get_positioned_price( $wc_currency ){

        $woocommerce_currency_pos = get_option( 'woocommerce_currency_pos' );
        $positioned_price = '';

        switch ( $woocommerce_currency_pos ) {
            case 'left':
                $positioned_price = sprintf( '%s99.99', get_woocommerce_currency_symbol( $wc_currency ) );
                break;
            case 'right':
                $positioned_price = sprintf( '99.99%s', get_woocommerce_currency_symbol( $wc_currency ) );
                break;
            case 'left_space':
                $positioned_price = sprintf( '%s 99.99', get_woocommerce_currency_symbol( $wc_currency ) );
                break;
            case 'right_space':
                $positioned_price = sprintf( '99.99 %s', get_woocommerce_currency_symbol( $wc_currency ) );
                break;
        }

        return $positioned_price;

    }

    protected function price_position_format( $position, $code ){

        $positioned_price = '';
        switch ( $position ) {
            case 'left':
                $positioned_price = sprintf( '%s99.99', get_woocommerce_currency_symbol( $code ) );
                break;
            case 'right':
                $positioned_price = sprintf( '99.99%s', get_woocommerce_currency_symbol( $code ) );
                break;
            case 'left_space':
                $positioned_price = sprintf( '%s 99.99', get_woocommerce_currency_symbol( $code ) );
                break;
            case 'right_space':
                $positioned_price = sprintf( '99.99 %s', get_woocommerce_currency_symbol( $code ) );
                break;
        }

        return $positioned_price;

    }

    public function get_flag_url( $code ){
        return $this->sitepress->get_flag_url( $code );
    }

    public function is_currency_on($currency, $language) {
        return $this->woocommerce_wpml->settings['currency_options'][ $currency ]['languages'][ $language ];
    }

    public function get_language_currency( $language ) {
        return $this->woocommerce_wpml->settings['default_currencies'][ $language ];
    }

    public function get_currency_symbol( $code ) {
        return get_woocommerce_currency_symbol( $code );
    }
    public function get_currency_name( $code ){
        return $this->wc_currencies[$code];
    }


    public function load_custom_currency_option_boxes(){

        $args = array(
            'title'             => __('Add new currency', 'woocommerce-multilingual'),
            'default_currency'  => $this->wc_currency,
            'currencies'        => $this->currencies,
            'wc_currencies'     => $this->wc_currencies,
            'currency_code'     => '',
            'currency_name'     => '',
            'currency_symbol'   => '',
            'currency'          => array(
                'rate' => 1,
                'position'              => 'left',
                'thousand_sep'          => ',',
                'decimal_sep'           => '.',
                'num_decimals'          => 2,
                'rounding'              => 'disabled',
                'rounding_increment'    => 1,
                'auto_subtract'         => 0,
                'updated'               => 0
            ),
            'current_currency'  => current( array_diff( array_keys( $this->wc_currencies ), array_keys( $this->currencies ), array ( $this->wc_currency ) ) )
        );

        new WCML_Custom_Currency_Options($args, $this->woocommerce_wpml);

        foreach($this->currencies as $code => $currency){
            $args['currency_code'] 		= $code;
            $args['currency_name'] 		= $args['wc_currencies'][$args['currency_code']];
            $args['currency_symbol'] 	= get_woocommerce_currency_symbol( $args['currency_code'] );
            $args['currency']			= $currency;
            $args['title'] = sprintf( __( 'Update settings for %s', 'woocommerce-multilingual' ), $args['currency_name'] . ' (' . $args['currency_symbol'] . ')' );

            $args['current_currency'] = $args['currency_code'];

            new WCML_Custom_Currency_Options($args, $this->woocommerce_wpml);

        }


    }


    public function load_curency_switcher_option_boxes(){

        $wcml_settings = $this->woocommerce_wpml->get_settings();
        $currency_switchers = isset( $wcml_settings[ 'currency_switchers' ] ) ? $wcml_settings[ 'currency_switchers' ] : array();

        //add empty dialog for new sidebar currency switcher
        $currency_switchers[ 'new_widget' ] = array(
            'switcher_style' => 'wcml-dropdown',
            'widget_title'  =>  '',
            'switcher_templates' => $this->woocommerce_wpml->cs_templates->get_templates(),
            'template' => '%name% (%symbol%) - %code%',
            'color_scheme' => array(
                'font_current_normal'       => '',
                'font_current_hover'        => '',
                'background_current_normal' => '',
                'background_current_hover'  => '',
                'font_other_normal'         => '',
                'font_other_hover'          => '',
                'background_other_normal'   => '',
                'background_other_hover'    => '',
                'border_normal'             => ''
            )
        );

        if( !isset( $currency_switchers[ 'product' ] ) ){
            $currency_switchers[ 'product' ] = $currency_switchers[ 'new_widget' ];
        }

        $widget_currency_switchers = $this->widget_currency_switchers();

        foreach( $currency_switchers as $switcher_id => $currency_switcher ){

            if ( 'new_widget' !== $switcher_id && !$this->woocommerce_wpml->cs_properties->is_currency_switcher_active( $switcher_id, $wcml_settings ) ) continue;

            if( $switcher_id == 'product'){
                $dialog_title = __('Edit Product Currency Switcher', 'woocommerce-multilingual');
            }elseif( $switcher_id == 'new_widget' ){
                $dialog_title = __('New Widget Area Currency Switcher', 'woocommerce-multilingual');
            }else{
                $dialog_title = sprintf( __('Edit %s Currency Switcher', 'woocommerce-multilingual'), $widget_currency_switchers[ $switcher_id ]['name'] );
            }

            $args = array(
                'title'             => $dialog_title,
                'currency_switcher'  => $switcher_id,
                'switcher_style'  =>  $currency_switcher[ 'switcher_style' ],
                'widget_title'  =>  $currency_switcher[ 'widget_title' ],
                'switcher_templates' => $this->woocommerce_wpml->cs_templates->get_templates(),
                'template'  => $currency_switcher[ 'template' ],
                'template_default'  => '%name% (%symbol%) - %code%',
                'options'  => $currency_switcher[ 'color_scheme' ]
            );

            new WCML_Currency_Switcher_Options_Dialog( $args, $this->woocommerce_wpml );
        }
    }

    public function get_currency_switchers_preview(){
        $preview = array(
            'product' => $this->woocommerce_wpml->multi_currency->currency_switcher->wcml_currency_switcher( array( 'switcher_id' => 'product', 'echo' => false ) )
        );

        foreach( $this->widget_currency_switchers() as $switcher ){
            $preview[ $switcher['id'] ] = $this->woocommerce_wpml->multi_currency->currency_switcher->wcml_currency_switcher( array( 'switcher_id' => $switcher['id'], 'echo' => false ) );
        }

        return $preview;
    }

    public function wp_do_action( $hook ){
        do_action( $hook );
    }

    public function get_weekday( $day_index ){
        global $wp_locale;
        return $wp_locale->get_weekday( $day_index );
    }

    public function widget_currency_switchers(){
        $wcml_settings = $this->woocommerce_wpml->get_settings();
        $currency_switchers = isset( $wcml_settings[ 'currency_switchers' ] ) ? $wcml_settings[ 'currency_switchers' ] : array();
        $sidebars = $this->woocommerce_wpml->multi_currency->currency_switcher->get_registered_sidebars();
        foreach( $sidebars as $key => $sidebar ){
            if( !isset( $currency_switchers[ $key ] ) ){
                unset( $sidebars[ $key ] );
            }
        }

        return $sidebars;
    }

}