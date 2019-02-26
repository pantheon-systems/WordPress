<?php

class WCML_Languages_Upgrader{



    function __construct(){

        add_action( 'icl_update_active_languages', array( $this, 'download_woocommerce_translations_for_active_languages' ) );
        add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_for_update' ), 11 );
        add_filter( 'upgrader_pre_download', array( $this, 'version_update' ), 10, 2 );
        add_action( 'admin_notices', array( $this, 'translation_upgrade_notice' ) );
        add_action( 'wp_ajax_hide_wcml_translations_message', array($this, 'hide_wcml_translations_message') );

        $this->load_js();
    }

    /**
     * Automatically download translations for WC ( when user install WCML ( from 3.3.3) / add new language in WPML )
     *
     * @param  string $lang_code Language code
     *
     */
    function download_woocommerce_translations( $lang_code, $wc_version ){
        global $sitepress;

        $locale = $sitepress->get_locale( $lang_code );

        if( $locale != 'en_US' && $this->has_available_update( $locale, $wc_version ) ){

            $wc_version = $wc_version ? $wc_version : WC_VERSION;

            include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            require_once( ABSPATH . 'wp-admin/includes/template.php' );

            $url = 'update-core.php?action=do-translation-upgrade';
            $nonce = 'upgrade-translations';
            $title = '';
            $context = WP_LANG_DIR;

            $upgrader = new Language_Pack_Upgrader( new Automatic_Upgrader_Skin( compact( 'url', 'nonce', 'title', 'context' ) ) ); // use Language_Pack_Upgrader_Skin instead of Automatic_Upgrader_Skin to display upgrade process

            $upgr_object = array();
            $upgr_object[0] = new stdClass();
            $upgr_object[0]->type = 'plugin';
            $upgr_object[0]->slug = 'woocommerce';
            $upgr_object[0]->language = $locale;
            $upgr_object[0]->version = $wc_version;
            $upgr_object[0]->updated = date('Y-m-d H:i:s');
            $upgr_object[0]->package = $this->get_language_pack_uri( $locale, $wc_version );
            $upgr_object[0]->autoupdate = 1;

            $ob_level_before = ob_get_level();

            $upgrader->bulk_upgrade( $upgr_object );

            // Close a potential unclosed output buffer
            $ob_level_after  = ob_get_level();
            if( $ob_level_after > $ob_level_before ){
                ob_end_clean();
            }


            $this->save_translation_version( $locale, false, $wc_version );
        }

    }


    /*
     * Automatically download translations for WC for active languages
     *
     */
    function download_woocommerce_translations_for_active_languages( $wc_version = false ){
        global $sitepress, $woocommerce_wpml;

        $active_languages = $sitepress->get_active_languages();

        $current_language = $sitepress->get_current_language();

        foreach( $active_languages as $language ){

            $this->download_woocommerce_translations( $language['code'], $wc_version );

        }

        $sitepress->switch_lang( $current_language );

        if( isset( $woocommerce_wpml->url_translation ) ){
            $woocommerce_wpml->url_translation->register_product_and_taxonomy_bases();
        }

    }


    /*
     * Check for WC language updates
     *
     * @param  object $data Transient update data
     *
     * @return object
     */
    function check_for_update( $data ){
        global $sitepress;

        $active_languages = $sitepress->get_active_languages();
        $current_language = $sitepress->get_current_language();

        foreach( $active_languages as $language ){
            if( $language['code'] == 'en' )
                continue;

            $locale = $sitepress->get_locale( $language['code'] );

            if ( $this->has_available_update( $locale ) && isset( $data->translations ) ) {

                $data->translations[] = array(
                    'type'       => 'plugin',
                    'slug'       => 'woocommerce',
                    'language'   => $locale,
                    'version'    => WC_VERSION,
                    'updated'    => date( 'Y-m-d H:i:s' ),
                    'package'    => $this->get_language_pack_uri( $locale ),
                    'autoupdate' => 1
                );

            }

        }

        return $data;
    }


    function get_language_pack_uri( $locale, $version = false ){

        if( !$version ){
            $version = WC_VERSION;
        }

        if( version_compare( $version, '2.5.0', '<') ){
            $repo = 'https://github.com/woothemes/woocommerce-language-packs/raw/v';
            return $repo . $version . '/packages/' . $locale . '.zip';

        }else{
            $repo = 'https://downloads.wordpress.org/translation/plugin/woocommerce/';
            return $repo . $version . '/' . $locale . '.zip';
        }

    }

    /*
     * Update the WC language version in database
     *
     *
     * @param  bool   $reply   Whether to bail without returning the package (default: false)
     * @param  string $package Package URL
     *
     * @return bool
     */
    function version_update( $reply, $package ) {

        $notices = maybe_unserialize( get_option( 'wcml_translations_upgrade_notice' ) );

        if( !is_array( $notices ) ){
            return $reply;
        }

        foreach( $notices as $key => $locale){
            if( strstr( $package, 'woocommerce') && strstr( $package, $locale) ){

                $this->save_translation_version( $locale, $key );

            }
        }

        return $reply;
    }


    function save_translation_version( $locale, $key = false, $wc_version = false ){

        $notices = maybe_unserialize( get_option( 'wcml_translations_upgrade_notice' ) );

        // Update the language pack version
        update_option( 'woocommerce_language_pack_version_'.$locale, array( $wc_version ? $wc_version : WC_VERSION, $locale ) );

        if( is_array( $notices ) ){

            if( !$key )
                $key = array_search( $locale, $notices );

            // Remove the translation upgrade notice
            unset( $notices[ $key ] );

            update_option( 'wcml_translations_upgrade_notice', $notices );

        }

    }

    /*
     * Check if has available translation update
     *
     * @param string $locale Locale code
     *
     * @return bool
     */
    function has_available_update( $locale, $wc_version = false ) {
        $wc_version = $wc_version ? $wc_version : WC_VERSION;

        $version = get_option( 'woocommerce_language_pack_version_'.$locale, array( '0', $locale ) );

        $is_new_version = !is_array( $version ) || version_compare( $version[0], $wc_version, '<' ) || $version[1] !== $locale;
        $mo_file_absent = !file_exists( sprintf( WP_LANG_DIR . '/plugins/woocommerce-%s.mo', $locale ) );

        $notices = maybe_unserialize( get_option( 'wcml_translations_upgrade_notice' ) );

        if ( 'en_US' !== $locale && ( $is_new_version || $mo_file_absent ) ) {
            if ( $this->check_if_language_pack_exists( $locale, $wc_version ) ){

                if( !$notices || !in_array( $locale, $notices )){
                    $notices[] = $locale;

                    update_option( 'wcml_translations_upgrade_notice', $notices );
                    update_option( 'hide_wcml_translations_message', 0 );
                }

                return true;
            } else {
                // Updated the woocommerce_language_pack_version to avoid searching translations for this release again
                update_option( 'woocommerce_language_pack_version_'.$locale, array( $wc_version, $locale ) );
            }
        }

        return false;
    }


    /**
     * Check if language pack exists
     *
     * @return bool
     */
    function check_if_language_pack_exists( $locale, $wc_version ) {

        $response = wp_safe_remote_get( $this->get_language_pack_uri( $locale, $wc_version ), array( 'timeout' => 60 ) );

        if ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 && $response['body'] != '404 File not found' ) {
            return true;
        } else {
            return false;
        }
    }


    /*
     * Display Translations upgrade notice message
     */
    function translation_upgrade_notice(){
        global $woocommerce_wpml;

        $screen = get_current_screen();
        $notices = maybe_unserialize( get_option( 'wcml_translations_upgrade_notice' ) );

        if ( 'update-core' !== $screen->id && !empty ( $notices ) && !get_option( 'hide_wcml_translations_message' ) ) {

            $lang_notices = new WCML_Languages_Upgrade_Notice( $notices );
            $lang_notices->show();
        }
    }

    /*
     * Hide Translations upgrade notice message ( update option in DB )
     */
    function hide_wcml_translations_message(){
        $nonce = filter_input( INPUT_POST, 'wcml_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        if(!$nonce || !wp_verify_nonce($nonce, 'hide_wcml_translations_message' ) ){
            die('Invalid nonce');
        }
        update_option( 'hide_wcml_translations_message', true );

        die();
    }

    public function load_js(){

        wp_register_script( 'wcml-lang-notice', WCML_PLUGIN_URL . '/res/js/languages_notice' . WCML_JS_MIN . '.js', array( 'jquery' ), WCML_VERSION );
        wp_enqueue_script( 'wcml-lang-notice');

        wp_localize_script( 'wcml-lang-notice', 'wcml_language_upgrade_notices',
            array(
                'dont_close' => esc_html__( "Downloading translations... Please don't close this page.", 'woocommerce-multilingual' )
            )
        );

    }

}