<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

!defined( 'ABSPATH' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YIT_Assets' ) ) {
    /**
     * YIT Assets
     *
     * @class      YIT_Assets
     * @package    YITH
     * @since      3.0.0
     * @author     Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YIT_Assets {
        /** @var string */
        public $version = '1.0.0';

        /** @var YIT_Assets */
        private static $_instance;

        /** @return YIT_Assets */
        public static function instance() {
            return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
        }

        /**
         * Constructor
         *
         * @since      1.0
         * @author     Leanza Francesco <leanzafrancesco@gmail.com>
         */
        private function __construct() {
            add_action( 'admin_enqueue_scripts', array( $this, 'register_styles_and_scripts' ) );
        }

        /**
         * Register styles and scripts
         */
        public function register_styles_and_scripts() {
            global $wp_scripts, $woocommerce;

            $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

            //scripts
            wp_register_script( 'yith-plugin-fw-fields', YIT_CORE_PLUGIN_URL . '/assets/js/yith-fields' . $suffix . '.js', array( 'jquery', 'jquery-ui-datepicker', 'wp-color-picker', 'codemirror', 'codemirror-javascript', 'jquery-ui-slider' ), $this->version, true );
            wp_register_script( 'yit-metabox', YIT_CORE_PLUGIN_URL . '/assets/js/metabox' . $suffix . '.js', array( 'jquery', 'wp-color-picker' ), '1.0.0', true );
            wp_register_script( 'yit-plugin-panel', YIT_CORE_PLUGIN_URL . '/assets/js/yit-plugin-panel' . $suffix . '.js', array( 'jquery', 'wp-color-picker', 'jquery-ui-sortable' ), $this->version, true );
            wp_register_script( 'codemirror', YIT_CORE_PLUGIN_URL . '/assets/js/codemirror/codemirror.js', array( 'jquery' ), $this->version, true );
            wp_register_script( 'codemirror-javascript', YIT_CORE_PLUGIN_URL . '/assets/js/codemirror/javascript.js', array( 'jquery', 'codemirror' ), $this->version, true );
            wp_register_script( 'colorbox', YIT_CORE_PLUGIN_URL . '/assets/js/jquery.colorbox' . $suffix . '.js', array( 'jquery' ), '1.6.3', true );
            wp_register_script( 'yith_how_to', YIT_CORE_PLUGIN_URL . '/assets/js/how-to' . $suffix . '.js', array( 'jquery' ), $this->version, true );

            //styles
            $jquery_version = isset( $wp_scripts->registered[ 'jquery-ui-core' ]->ver ) ? $wp_scripts->registered[ 'jquery-ui-core' ]->ver : '1.9.2';
            wp_register_style( 'codemirror', YIT_CORE_PLUGIN_URL . '/assets/css/codemirror/codemirror.css' );
            wp_register_style( 'yit-plugin-style', YIT_CORE_PLUGIN_URL . '/assets/css/yit-plugin-panel.css', array(), $this->version );
            wp_register_style( 'raleway-font', '//fonts.googleapis.com/css?family=Raleway:400,500,600,700,800,100,200,300,900' );
            wp_register_style( 'yit-jquery-ui-style', '//code.jquery.com/ui/' . $jquery_version . '/themes/smoothness/jquery-ui.css', array(), $jquery_version );
            wp_register_style( 'colorbox', YIT_CORE_PLUGIN_URL . '/assets/css/colorbox.css', array(), $this->version );
            wp_register_style( 'yit-upgrade-to-pro', YIT_CORE_PLUGIN_URL . '/assets/css/yit-upgrade-to-pro.css', array( 'colorbox' ), $this->version );
            wp_register_style( 'yit-plugin-metaboxes', YIT_CORE_PLUGIN_URL . '/assets/css/metaboxes.css' );
            wp_register_style( 'yith-plugin-fw-fields', YIT_CORE_PLUGIN_URL . '/assets/css/yith-fields.css', false, $this->version );

            $wc_version_suffix = '';
            if ( function_exists( 'WC' ) || !empty( $woocommerce ) ) {
                $woocommerce_version = function_exists( 'WC' ) ? WC()->version : $woocommerce->version;
                $wc_version_suffix   = version_compare( $woocommerce_version, '3.0.0', '>=' ) ? '' : '-wc-2.6';

                wp_register_style( 'woocommerce_admin_styles', $woocommerce->plugin_url() . '/assets/css/admin.css', array(), $woocommerce_version );
            } else {
                wp_register_script( 'select2', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js', array( 'jquery' ), '4.0.3', true );
                wp_register_style( 'yith-select2-no-wc', YIT_CORE_PLUGIN_URL . '/assets/css/yith-select2-no-wc.css', false, $this->version );
            }

            wp_register_script( 'yith-enhanced-select', YIT_CORE_PLUGIN_URL . '/assets/js/yith-enhanced-select' . $wc_version_suffix . $suffix . '.js', array( 'jquery', 'select2' ), '1.0.0', true );
            wp_localize_script( 'yith-enhanced-select', 'yith_framework_enhanced_select_params', array(
                'ajax_url'           => admin_url( 'admin-ajax.php' ),
                'search_posts_nonce' => wp_create_nonce( 'search-posts' ),
                'search_terms_nonce' => wp_create_nonce( 'search-terms' ),
            ) );

            wp_enqueue_style( 'yith-plugin-fw-admin', YIT_CORE_PLUGIN_URL . '/assets/css/admin.css', array(), $this->version );

        }
    }
}

YIT_Assets::instance();