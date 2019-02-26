<?php
class WCML_Requests{
    
    function __construct(){
        
        add_action('init', array($this, 'run') );
        
    }
    
    function run(){
        global $woocommerce_wpml;

        $nonce = filter_input( INPUT_POST, 'wcml_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $settings_needs_update = false;

        if( isset( $_GET[ 'wcml_action' ] ) ){
            $settings_needs_update = true;

            if( $_GET['wcml_action'] == 'dismiss' ){
                $woocommerce_wpml->settings['dismiss_doc_main'] = 1;
            }elseif( $_GET['wcml_action'] == 'dismiss_tm_warning' ){
                $woocommerce_wpml->settings['dismiss_tm_warning'] = 1;
            }elseif( $_GET['wcml_action'] == 'dismiss_cart_warning' ){
                $woocommerce_wpml->settings['dismiss_cart_warning'] = 1;
            }else{
                $settings_needs_update = false;
            }
        }

        if(isset($_POST['wcml_save_settings']) && wp_verify_nonce($nonce, 'wcml_save_settings_nonce')){
            global $sitepress,$sitepress_settings;

            $woocommerce_wpml->settings['trnsl_interface'] = filter_input( INPUT_POST, 'trnsl_interface', FILTER_SANITIZE_NUMBER_INT );

            $woocommerce_wpml->settings['products_sync_date'] = empty($_POST['products_sync_date']) ? 0 : 1;
            $woocommerce_wpml->settings['products_sync_order'] = empty($_POST['products_sync_order']) ? 0 : 1;

            $wcml_file_path_sync = filter_input( INPUT_POST, 'wcml_file_path_sync', FILTER_SANITIZE_NUMBER_INT );

            $woocommerce_wpml->settings['file_path_sync'] = $wcml_file_path_sync;

	        if ( isset( $_POST['cart_sync_lang'] ) && isset( $_POST['cart_sync_currencies'] ) ) {
		        $woocommerce_wpml->settings['cart_sync']['lang_switch']     = (int) filter_input( INPUT_POST, 'cart_sync_lang', FILTER_SANITIZE_NUMBER_INT );
		        $woocommerce_wpml->settings['cart_sync']['currency_switch'] = (int) filter_input( INPUT_POST, 'cart_sync_currencies', FILTER_SANITIZE_NUMBER_INT );
	        }

            $new_value = $wcml_file_path_sync == 0 ? 2 :$wcml_file_path_sync;
            $sitepress_settings['translation-management']['custom_fields_translation']['_downloadable_files'] = $new_value;
            $sitepress_settings['translation-management']['custom_fields_translation']['_file_paths'] = $new_value;

            $sitepress->save_settings($sitepress_settings);

            $message = array(
                'id'            => 'wcml-settings-saved',
                'text'          => __('Your settings have been saved.', 'woocommerce-multilingual' ),
                'group'         => 'wcml-settings',
                'admin_notice'  => true,
                'limit_to_page' => true,
                'classes'       => array('updated', 'notice', 'notice-success'),
                'show_once'     => true
            );
            ICL_AdminNotifier::add_message( $message );

            $settings_needs_update = true;
        }

        if( $settings_needs_update ){
            $woocommerce_wpml->update_settings();
        }

        add_action('wp_ajax_wcml_ignore_warning', array( $this, 'update_settings_from_warning') );

        // Override cached widget id
        add_filter( 'woocommerce_cached_widget_id', array( $this, 'override_cached_widget_id' ) );
    }

    function update_settings_from_warning(){
        $nonce = filter_input( INPUT_POST, 'wcml_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        if(!$nonce || !wp_verify_nonce($nonce, 'wcml_ignore_warning')){
            die('Invalid nonce');
        }
        global $woocommerce_wpml;

        $woocommerce_wpml->settings[$_POST['setting']] = 1;
        $woocommerce_wpml->update_settings();

    }

    public function override_cached_widget_id( $widget_id ){

        if( defined( 'ICL_LANGUAGE_CODE' ) ){
            $widget_id .= ':' . ICL_LANGUAGE_CODE;
        }

        return $widget_id;
    }

}