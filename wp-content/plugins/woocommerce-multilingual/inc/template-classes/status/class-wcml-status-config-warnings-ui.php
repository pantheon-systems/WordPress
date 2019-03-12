<?php

class WCML_Status_Config_Warnings_UI extends WPML_Templates_Factory {

    private $woocommerce_wpml;
    private $sitepress;
    private $sitepress_settings;

    function __construct( &$sitepress, &$woocommerce_wpml, &$sitepress_settings ){
        parent::__construct();

        $this->woocommerce_wpml = $woocommerce_wpml;
        $this->sitepress = $sitepress;
        $this->sitepress_settings = $sitepress_settings;

    }

    public function init_twig_functions() {
        $function = new Twig_SimpleFunction( 'get_flag_url', array( $this, 'get_flag_url' ) );
        $this->get_twig()->addFunction( $function );
    }

    public function get_model() {
        $this->init_twig_functions();

        $model = array(
            'default_language' => $this->sitepress->get_default_language(),
            'miss_slug_lang' => $this->get_missed_product_slug_translations_languages(),
            'prod_slug' => $this->woocommerce_wpml->strings->product_permalink_slug(),
            'dismiss_non_default' => isset( $this->woocommerce_wpml->settings['dismiss_non_default_language_warning'] ) ? true : false,
            'xml_config_errors' => isset( $this->woocommerce_wpml->dependencies->xml_config_errors ) ? $this->woocommerce_wpml->dependencies->xml_config_errors : false,
            'slugs_tab' => admin_url( 'admin.php?page=wpml-wcml&tab=slugs' ),
            'st_lang' => $this->sitepress_settings['st']['strings_language'],
            'not_en_doc_page' => 'https://wpml.org/?page_id=355545',
            'strings' => array(
                'conf' => __( 'Configuration Warnings', 'woocommerce-multilingual' ),
                'base_not_trnsl' => __( 'Your product permalink base is not translated to:', 'woocommerce-multilingual' ),
                'trsl_urls' => __( 'Translate URLs', 'woocommerce-multilingual' ),
                'run_not_en' => __( 'Running WooCommerce multilingual with default language other than English.', 'woocommerce-multilingual' ),
                'url_problems' => __( 'This may cause problems with URLs in different languages.', 'woocommerce-multilingual' ),
                'change_def_lang' => __( 'Change default language', 'woocommerce-multilingual' ),
                'attent_sett' => __( 'There are some settings that require careful attention.', 'woocommerce-multilingual' ),
                'over_sett' => __( 'Some settings from the WooCommerce Multilingual wpml-config.xml file have been overwritten.', 'woocommerce-multilingual' ),
                'check_conf' => __( 'You should check WPML configuration files added by other plugins or manual settings on the %s section.', 'woocommerce-multilingual' ),
                'cont_set' => '<a href="' . admin_url( 'admin.php?page=' . WPML_TM_FOLDER . '/menu/main.php&sm=mcsetup' ) . '">'. __( 'Multilingual Content Setup', 'woocommerce-multilingual' ) .'</a>'
            ),
            'nonces' => array(
                'fix_strings' => wp_create_nonce( 'wcml_fix_strings_language' )
            )
        );

        return $model;

    }

    private function get_missed_product_slug_translations_languages(){

        $slug_settings = $this->sitepress->get_setting( 'posts_slug_translation' );
        $miss_slug_lang = array();

        if ( ! empty( $slug_settings['on'] ) ) {
            $slug = $this->woocommerce_wpml->strings->product_permalink_slug();

            if (has_filter('wpml_slug_translation_available')) {

                if (version_compare(WPML_ST_VERSION, '2.2.6', '>')) {
                    $slug_translation_languages = apply_filters('wpml_get_slug_translation_languages', array(), 'product');


                } else {
                    $slug_translation_languages = apply_filters('wpml_get_slug_translation_languages', array(), $slug);
                }

            } else {
                $string_id = icl_get_string_id($slug, $this->woocommerce_wpml->url_translation->url_strings_context(), $this->woocommerce_wpml->url_translation->url_string_name('product'));
                $slug_translations = icl_get_string_translations_by_id($string_id);
            }


            $string_language = $this->woocommerce_wpml->strings->get_string_language($slug, $this->woocommerce_wpml->url_translation->url_strings_context(), $this->woocommerce_wpml->url_translation->url_string_name('product'));

            foreach ($this->sitepress->get_active_languages() as $lang_info) {
                if (
                    (
                        (isset($slug_translations) && !array_key_exists($lang_info['code'], $slug_translations)) ||
                        (isset($slug_translation_languages) && !in_array($lang_info['code'], $slug_translation_languages))
                    ) && $lang_info['code'] != $string_language
                ) {
                    $miss_slug_lang[] = $lang_info;
                }
            }
        }

        return $miss_slug_lang;
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
        return 'conf-warn.twig';
    }

}