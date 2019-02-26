<?php

class WCML_Resources {

    private static $page;
    private static $tab;
    private static $is_wpml_wcml_page;
    private static $pagenow;

    private static $woocommerce_wpml;
    private static $sitepress;

    public static function set_up_resources( &$woocommerce_wpml, &$sitepress ) {
        global $pagenow;

        self::$woocommerce_wpml =& $woocommerce_wpml;
        self::$sitepress =& $sitepress;

        self::$page = isset($_GET['page']) ? $_GET['page'] : null;
        self::$tab = isset($_GET['tab']) ? $_GET['tab'] : null;
        self::$is_wpml_wcml_page = self::$page == 'wpml-wcml';
        self::$pagenow = $pagenow;

        self::load_css();
        self::load_common_scripts();

        $is_edit_product = self::$pagenow == 'post.php' && isset($_GET['post']) && get_post_type( $_GET['post'] ) == 'product';
        $is_original_product = isset( $_GET['post'] ) && !is_array( $_GET['post'] ) && self::$woocommerce_wpml->products->is_original_product( $_GET['post'] );
        $is_new_product = self::$pagenow == 'post-new.php' && isset($_GET['source_lang']) && isset($_GET['post_type']) && $_GET['post_type'] == 'product';

        if ( ($is_edit_product && !$is_original_product) || $is_new_product && !self::$woocommerce_wpml->settings['trnsl_interface'] ) {
            add_action( 'init', array(__CLASS__, 'load_lock_fields_js') );
            add_action( 'admin_footer', array(__CLASS__, 'hidden_label') );
        }
    }

    private static function load_css() {
        
        if ( self::$is_wpml_wcml_page || self::$page == WPML_TM_FOLDER . '/menu/translations-queue.php' ) {

            self::load_management_css();

            if ( in_array( self::$tab, array('multi-currency', 'slugs') ) ) {
                wp_register_style( 'wcml-dialogs', WCML_PLUGIN_URL . '/res/css/dialogs.css', array('wpml-dialog'), WCML_VERSION );
                wp_enqueue_style( 'wcml-dialogs' );
            }

            wp_enqueue_style( 'wp-color-picker' );
        }

        if ( self::$pagenow == 'options-permalink.php' ) {
            wp_register_style( 'wcml_op', WCML_PLUGIN_URL . '/res/css/options-permalink.css', null, WCML_VERSION );
            wp_enqueue_style( 'wcml_op' );
        }

        if( is_admin() ){
            wp_register_style( 'wcml_admin', WCML_PLUGIN_URL . '/res/css/admin.css', array( 'wp-pointer' ), WCML_VERSION );
            wp_enqueue_style( 'wcml_admin' );
        }
    }

    public static function load_management_css() {
        wp_register_style( 'wpml-wcml', WCML_PLUGIN_URL . '/res/css/management.css', array(), WCML_VERSION );
        wp_enqueue_style( 'wpml-wcml' );
    }

    public static function load_taxonomy_translation_scripts(){
	    wp_register_script( 'wcml-taxonomy-translation-scripts', WCML_PLUGIN_URL . '/res/js/taxonomy_translation' . WCML_JS_MIN . '.js', array( 'jquery' ), WCML_VERSION );
	    wp_enqueue_script( 'wcml-taxonomy-translation-scripts' );
    }

    private static function load_common_scripts() {

        if ( self::$is_wpml_wcml_page ) {

	        wp_register_script( 'wcml-scripts', WCML_PLUGIN_URL . '/res/js/scripts' . WCML_JS_MIN . '.js', array(
		        'jquery',
		        'jquery-ui-core',
		        'jquery-ui-resizable'
	        ), WCML_VERSION );

    		self::load_taxonomy_translation_scripts();

            wp_register_script( 'jquery-cookie', WCML_PLUGIN_URL . '/res/js/jquery.cookie' . WCML_JS_MIN . '.js', array('jquery'), WCML_VERSION );
            wp_register_script( 'wcml-dialogs', WCML_PLUGIN_URL . '/res/js/dialogs' . WCML_JS_MIN . '.js', array('jquery', 'jquery-ui-core', 'jquery-ui-dialog'), WCML_VERSION );
            wp_register_script( 'wcml-troubleshooting', WCML_PLUGIN_URL . '/res/js/troubleshooting' . WCML_JS_MIN . '.js', array('jquery'), WCML_VERSION );
            wp_register_script( 'wcml-translation-interface-dialog-warning', WCML_PLUGIN_URL . '/res/js/trnsl_interface_dialog_warning' . WCML_JS_MIN . '.js', array('jquery'), WCML_VERSION );

	        wp_enqueue_script( 'wcml-scripts' );
            wp_enqueue_script( 'wp-color-picker');
            wp_enqueue_script( 'wcml-dialogs' );
            wp_enqueue_script( 'jquery-cookie' );
            wp_enqueue_script( 'wcml-troubleshooting' );
            wp_enqueue_script( 'wcml-translation-interface-dialog-warning' );

            wp_localize_script( 'wcml-scripts', 'wcml_settings',
                array(
                    'nonce' => wp_create_nonce( 'woocommerce_multilingual' )
                )
            );

            self::load_tooltip_resources();
        }

        if ( self::$page == WPML_TM_FOLDER . '/menu/main.php' ) {
            wp_register_script( 'wpml_tm', WCML_PLUGIN_URL . '/res/js/wpml_tm' . WCML_JS_MIN . '.js', array('jquery'), WCML_VERSION );
            wp_enqueue_script( 'wpml_tm' );
        }

        if ( self::$pagenow == 'widgets.php' ) {
            wp_register_script( 'wcml_widgets', WCML_PLUGIN_URL . '/res/js/widgets' . WCML_JS_MIN . '.js', array('jquery'), WCML_VERSION );
            wp_enqueue_script( 'wcml_widgets' );
        }

        if ( self::$page == 'wpml-wcml' && self::$tab == 'multi-currency' ) {
            wp_register_script( 'multi-currency', WCML_PLUGIN_URL . '/res/js/multi-currency' . WCML_JS_MIN . '.js', array('jquery', 'jquery-ui-sortable'), WCML_VERSION, true );
            wp_enqueue_script( 'multi-currency' );

            wp_register_script( 'currency-switcher-settings', WCML_PLUGIN_URL . '/res/js/currency-switcher-settings' . WCML_JS_MIN . '.js', array('jquery', 'jquery-ui-sortable'), WCML_VERSION, true );
            wp_enqueue_script( 'currency-switcher-settings' );
            wp_localize_script( 'currency-switcher-settings', 'settings', array(
                'pre_selected_colors' => WCML_Currency_Switcher_Options_Dialog::currency_switcher_pre_selected_colors()
            ) );

            wp_register_script( 'exchange-rates', WCML_PLUGIN_URL . '/res/js/exchange-rates' . WCML_JS_MIN . '.js', array('jquery'), WCML_VERSION, true );
            wp_enqueue_script( 'exchange-rates' );
        }

        if ( self::$page == 'wpml-wcml' && self::$tab == 'product-attributes' ) {
            wp_register_script( 'product-attributes', WCML_PLUGIN_URL . '/res/js/product-attributes' . WCML_JS_MIN . '.js', array('jquery'), WCML_VERSION, true );
            wp_enqueue_script( 'product-attributes' );
        }

	    if ( self::$page == 'wpml-wcml' && self::$tab == 'custom-taxonomies' ) {
		    wp_register_script( 'custom-taxonomies', WCML_PLUGIN_URL . '/res/js/product-custom-taxonomies' . WCML_JS_MIN . '.js', array('jquery'), WCML_VERSION, true );
		    wp_enqueue_script( 'custom-taxonomies' );
	    }

        if ( !is_admin() && self::$pagenow != 'wp-login.php' ) {

            wp_register_script( 'wcml-front-scripts', WCML_PLUGIN_URL . '/res/js/front-scripts' . WCML_JS_MIN . '.js', array(
                'jquery'
            ), WCML_VERSION );
            wp_enqueue_script( 'wcml-front-scripts' );

            $referer = isset( $_SERVER[ 'HTTP_REFERER' ] ) ? $_SERVER[ 'HTTP_REFERER' ] : '';

            wp_register_script( 'cart-widget', WCML_PLUGIN_URL . '/res/js/cart_widget' . WCML_JS_MIN . '.js', array('jquery'), WCML_VERSION );
            wp_enqueue_script( 'cart-widget' );
            wp_localize_script( 'cart-widget', 'actions', array(
                'is_lang_switched' => self::$sitepress->get_language_from_url( $referer ) !=  self::$sitepress->get_current_language() ? 1 : 0,
                'force_reset' => apply_filters( 'wcml_force_reset_cart_fragments', 0 )
            ) );
        } elseif( is_admin() ) {

            wp_enqueue_script(
                'wcml-pointer',
                WCML_PLUGIN_URL . '/res/js/pointer' . WCML_JS_MIN . '.js',
                array( 'wp-pointer' ),
                WCML_VERSION,
                true
            );

            wp_register_script( 'wcml-messages', WCML_PLUGIN_URL . '/res/js/wcml-messages' . WCML_JS_MIN . '.js', array('jquery'), WCML_VERSION );
            wp_enqueue_script( 'wcml-messages' );
        }

        $is_attr_page = apply_filters( 'wcml_is_attributes_page', self::$page == 'product_attributes' && isset( $_GET[ 'post_type' ] ) && $_GET[ 'post_type' ] == 'product' );

        if( $is_attr_page ){
            wp_register_script( 'wcml-attributes', WCML_PLUGIN_URL . '/res/js/wcml-attributes' . WCML_JS_MIN . '.js', array( 'jquery' ), WCML_VERSION );
            wp_enqueue_script( 'wcml-attributes' );
        }

        if( self::$page == WPML_TM_FOLDER . '/menu/translations-queue.php' ) {

            self::load_tooltip_resources();
            wp_enqueue_media();
            wp_register_script( 'wcml-editor', WCML_PLUGIN_URL . '/res/js/wcml-translation-editor' . WCML_JS_MIN . '.js', array('jquery', 'jquery-ui-core'), WCML_VERSION);
            wp_enqueue_script( 'wcml-editor' );
            wp_localize_script( 'wcml-editor', 'wcml_settings',
                array(
                    'strings' => array(
                        'choose'        => __( 'Choose a file', 'woocommerce-multilingual' ),
                        'save_tooltip'  => __( 'At least one of these fields is required: title, content or excerpt', 'woocommerce-multilingual' ),
                        'resign_tooltip'=> __( 'This translation job will no longer be assigned to you. Other translators will be able take it and continue the translation.', 'woocommerce-multilingual')
                    ),
                    'hide_resign' => self::$woocommerce_wpml->products->is_hide_resign_button()
                )
            );
        }

        if ( isset( $_GET['post_type'] ) && 'product' === $_GET['post_type'] && 'edit.php' === self::$pagenow ) {
            self::load_tooltip_resources();
            wp_enqueue_script( 'products-screen-options', WCML_PLUGIN_URL . '/res/js/products-screen-option.js', array( 'jquery', 'wcml-tooltip-init' ), WCML_VERSION );
            wp_localize_script( 'products-screen-options', 'products_screen_option', array( 'nonce' => wp_create_nonce( 'products-screen-option-action' ) ) );
        }
    }

    public static function load_tooltip_resources() {

        if ( class_exists( 'WooCommerce' ) && function_exists( 'WC' ) ) {
            wp_register_script( 'jquery-tiptip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip.min.js', array('jquery'), WC_VERSION, true );
            wp_register_script( 'wcml-tooltip-init', WCML_PLUGIN_URL . '/res/js/tooltip_init' . WCML_JS_MIN . '.js', array('jquery'), WCML_VERSION );
            wp_enqueue_script( 'jquery-tiptip' );
            wp_enqueue_script( 'wcml-tooltip-init' );
            wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_VERSION );
        }

    }

    public static function load_lock_fields_js() {
        global $pagenow;

        wp_register_script( 'wcml-lock-script', WCML_PLUGIN_URL . '/res/js/lock_fields' . WCML_JS_MIN . '.js', array('jquery'), WCML_VERSION );
        wp_enqueue_script( 'wcml-lock-script' );

        $file_path_sync = self::$woocommerce_wpml->settings['file_path_sync'];

        $product_id = false;
        if( $pagenow === 'post.php' && isset( $_GET['post'] ) ){
            $product_id =  $_GET['post'];
        }elseif( isset( $_POST['product_id'] ) ){
            $product_id = $_POST['product_id'];
        }

        if( $product_id ){
            $original_id = self::$woocommerce_wpml->products->get_original_product_id( $product_id );
            $custom_product_sync = get_post_meta( $original_id, 'wcml_sync_files', true );
            if( $custom_product_sync && $custom_product_sync == 'self' ) {
                $file_path_sync = false;
            }elseif( $custom_product_sync && $custom_product_sync == 'auto' ){
                $file_path_sync = true;
            }
        }

        wp_localize_script( 'wcml-lock-script', 'unlock_fields', array(
                'menu_order' => self::$woocommerce_wpml->settings['products_sync_order'],
                'file_paths' => $file_path_sync
            )
        );
        wp_localize_script( 'wcml-lock-script', 'non_standard_fields', array(
            'ids' => apply_filters( 'wcml_js_lock_fields_ids', array() ),
            'classes' => apply_filters( 'wcml_js_lock_fields_classes', array() ),
            'input_names' => apply_filters( 'wcml_js_lock_fields_input_names', array() )
        ) );

        do_action( 'wcml_after_load_lock_fields_js' );

    }

    public static function hidden_label() {
        global $sitepress;

        echo '<img src="' . WCML_PLUGIN_URL . '/res/images/locked.png" class="wcml_lock_img wcml_lock_icon" alt="' .
            __( 'This field is locked for editing because WPML will copy its value from the original language.', 'woocommerce-multilingual' ) .
            '" title="' . __( 'This field is locked for editing because WPML will copy its value from the original language.', 'woocommerce-multilingual' ) .
            '" style="display: none;position:relative;left:2px;top:2px;">';

        if ( isset($_GET['post']) ) {
            $original_id = self::$woocommerce_wpml->products->get_original_product_id( sanitize_text_field( $_GET['post'] ) );
        } elseif ( isset($_GET['trid']) ) {
            $original_id = $sitepress->get_original_element_id_by_trid( sanitize_text_field( $_GET['trid'] ) );
        }

        if ( isset($_GET['lang']) ) {
            $language = $_GET['lang'];
        } else {
            return;
        }

        echo '<h3 class="wcml_prod_hidden_notice">' .
            sprintf( __( "This is a translation of %s. Some of the fields are not editable. It's recommended to use the %s for translating products.",
                'woocommerce-multilingual' ),
                '<a href="' . get_edit_post_link( $original_id ) . '" >' . get_the_title( $original_id ) . '</a>',
                '<a data-action="product-translation-dialog" class="js-wcml-dialog-trigger" data-id="' . $original_id . '" data-job_id="" data-language="' . $language . '">' .
                __( 'WooCommerce Multilingual products translator', 'woocommerce-multilingual' ) . '</a>' ) . '</h3>';
    }
}