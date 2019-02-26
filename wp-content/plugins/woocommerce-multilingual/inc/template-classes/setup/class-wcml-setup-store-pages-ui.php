<?php

class WCML_Setup_Store_Pages_UI extends WPML_Templates_Factory {

    private $woocommerce_wpml;
    private $sitepress;
    private $next_step_url;

    function __construct( &$woocommerce_wpml, &$sitepress, $next_step_url ){
        parent::__construct();

        $this->woocommerce_wpml = &$woocommerce_wpml;
        $this->sitepress = &$sitepress;
        $this->next_step_url = $next_step_url;

    }

    public function get_model(){

        $WCML_Status_Store_Pages_UI = new WCML_Status_Store_Pages_UI($this->sitepress , $this->woocommerce_wpml);
        $store_pages_view = $WCML_Status_Store_Pages_UI->get_view();


        if( 'non_exist' == $this->woocommerce_wpml->store->get_missing_store_pages() ){

            $store_pages_view = '<p><i class="otgs-ico-warning"></i> ' . __('One or more WooCommerce pages have not been created') . '<p>';

            $store_pages_view .=
                '<label><input type="checkbox" name="install_missing_pages" value="1" checked="checked">&nbsp;' .
                __('Install missing WooCommerce pages and create translations.', 'woocommerce-multilingual') .
                '</label>';
        }else{

            $store_pages_view = preg_replace('@<form [^>]+>@', '', $store_pages_view);
            $store_pages_view = preg_replace('@</form>@', '', $store_pages_view);
            $store_pages_view = preg_replace('@<button [^>]+>([^<]+)</button>@',
                '<label><input type="checkbox" name="create_pages" value="1" checked="checked">&nbsp;$1</label>', $store_pages_view);

        }

        $store_pages_view .= '<input type="hidden" name="next_step_url" value="' . esc_url( $this->next_step_url ) .  '">';

        $model = array(
            'strings' => array(
                'step_id'       => 'store_pages_step',
                'heading'       => __('Translate Store Pages', 'woocommerce-multilingual'),
                'description'   => __("All store pages must be translated in the languages configured on the site.", 'woocommerce-multilingual'),
                'continue'      => __('Continue', 'woocommerce-multilingual'),
            ),
            'store_pages'       => $store_pages_view,
            'continue_url'  => $this->next_step_url
        );

        return $model;

    }

    protected function init_template_base_dir() {
        $this->template_paths = array(
            WCML_PLUGIN_PATH . '/templates/',
        );
    }

    public function get_template() {
        return '/setup/store-pages.twig';
    }


}