<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YIT_Theme_Licence' ) ) {
    /**
     * YIT Plugin Licence Panel
     *
     * Setting Page to Manage Plugins
     *
     * @class      YIT_Theme_Licence
     * @package    YITH
     * @since      1.0
     * @author     Andrea Grillo      <andrea.grillo@yithemes.com>
     */
    class YIT_Theme_Licence extends YIT_Licence {

        /**
         * @var array The settings require to add the submenu page "Activation"
         * @since 1.0
         */
        protected $_settings = array();

        /**
         * @var object The single instance of the class
         * @since 1.0
         */
        protected static $_instance = null;

        /**
         * @var string Option name
         * @since 1.0
         */
        protected $_licence_option = 'yit_theme_licence_activation';

        /**
         * @var string product type
         * @since 1.0
         */
        protected $_product_type = 'theme';

        /**
         * @var string Old theme licence works until 28 January 2016
         * @since 1.0
         */
        protected $_old_licence_expires = 1453939200; //28 January 2016

        /**
         * Constructor
         *
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function __construct() {
            parent::__construct();

            $this->_settings = array(
                'parent_page' => 'yit_product_panel',
                'page_title'  => __( 'License Activation', 'yith-plugin-fw' ),
                'menu_title'  => __( 'License Activation', 'yith-plugin-fw' ),
                'capability'  => 'manage_options',
                'page'        => 'yith_plugins_activation',
            );

            add_action( 'admin_menu', array( $this, 'add_submenu_page' ), 99 );
            add_action( "wp_ajax_yith_activate-{$this->_product_type}", array( $this, 'activate' ) );
            add_action( "wp_ajax_yith_deactivate-{$this->_product_type}", array( $this, 'deactivate' ) );
            add_action( "wp_ajax_yith_update_licence_information-{$this->_product_type}", array( $this, 'update_licence_information' ) );
        }

        /**
         * Main plugin Instance
         *
         * @static
         * @return object Main instance
         *
         * @since  1.0
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }

            return self::$_instance;
        }

        /**
         * Add "Activation" submenu page under YITH Plugins
         *
         * @return void
         * @since  1.0
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function add_submenu_page() {

            $admin_tree = array(
                'parent_slug' => apply_filters( 'yit_licence_parent_slug', 'yit_panel' ),
                'page_title'  => __( 'License Activation', 'yith-plugin-fw' ),
                'menu_title'  => __( 'License Activation', 'yith-plugin-fw' ),
                'capability'  => 'manage_options',
                'menu_slug'   => 'yit_panel_license',
                'function'    => 'show_activation_panel'
            );

            add_submenu_page( $admin_tree['parent_slug'],
                sprintf( __( '%s', 'yith-plugin-fw' ), $admin_tree['page_title'] ),
                sprintf( __( '%s', 'yith-plugin-fw' ), $admin_tree['menu_title'] ),
                $admin_tree['capability'],
                $admin_tree['menu_slug'],
                array( $this, $admin_tree['function'] )
            );
        }

        /**
         * Premium product registration
         *
         * @param $product_init | string | The product init file
         * @param $secret_key   | string | The product secret key
         * @param $product_id   | string | The product slug (product_id)
         *
         * @return void
         *
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function register( $product_init, $secret_key, $product_id ) {
            $theme                                 = wp_get_theme();
            $products[$product_init]['Name']       = $theme->Name;
            $products[$product_init]['secret_key'] = $secret_key;
            $products[$product_init]['product_id'] = $product_id;
            $this->_products[$product_init]        = $products[$product_init];
        }

        /**
         * Check for old licence
         *
         * @return bool True for old licence period, false otherwise
         * @since  2.2
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function show_old_licence_message() {
            return time() < $this->_old_licence_expires;
        }

        public function get_old_licence_message() {
            ob_start(); ?>
            <div class="activation-faq">
                <h3><?php _e( 'I cannot find the license key for activating the theme I have bought some time ago. Where can I find it?', 'yith-plugin-fw' ) ?></h3>

                <p>
                    <?php
                    _e( 'If you have purchased one of our products before 27 January 2015, you can benefit from support and updates (the services offered with the license)
                    until 27 January 2016 and you do not have to purchase it again to get a new license key, because, before this date, your license used to be activated automatically by our system.
                    After 27 January 2016, instead, if you want to benefit from support and updates you have to buy a new license and activate it through the license key you will be
                    provided with and that you can find in your YITH account, in section "My licenses".', 'yith-plugin-fw' )
                    ?>
                </p>
            </div>
            <?php
            echo ob_get_clean();
        }

        public function get_product_type() {
            return $this->_product_type;
        }
    }
}

/**
 * Main instance
 *
 * @return object
 * @since  1.0
 * @author Andrea Grillo <andrea.grillo@yithemes.com>
 */
if ( !function_exists( 'YIT_Theme_Licence' ) ) {
    function YIT_Theme_Licence() {
        return YIT_Theme_Licence::instance();
    }
}