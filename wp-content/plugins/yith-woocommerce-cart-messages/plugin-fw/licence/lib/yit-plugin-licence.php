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

if ( !class_exists( 'YIT_Plugin_Licence' ) ) {
    /**
     * YIT Plugin Licence Panel
     *
     * Setting Page to Manage Plugins
     *
     * @class      YIT_Plugin_Licence
     * @package    YITH
     * @since      1.0
     * @author     Andrea Grillo      <andrea.grillo@yithemes.com>
     */
    class YIT_Plugin_Licence extends YIT_Licence {

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
        protected $_licence_option = 'yit_plugin_licence_activation';

        /**
         * @var string product type
         * @since 1.0
         */
        protected $_product_type = 'plugin';

        /**
         * Constructor
         *
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function __construct() {
            parent::__construct();

            if ( !is_admin() ) {
                return;
            }

            $this->_settings = array(
                'parent_page' => 'yith_plugin_panel',
                'page_title'  => __( 'License Activation', 'yith-plugin-fw' ),
                'menu_title'  => __( 'License Activation', 'yith-plugin-fw' ),
                'capability'  => 'manage_options',
                'page'        => 'yith_plugins_activation',
            );

            add_action( 'admin_menu', array( $this, 'add_submenu_page' ), 99 );
            add_action( "wp_ajax_yith_activate-{$this->_product_type}", array( $this, 'activate' ) );
            add_action( "wp_ajax_yith_deactivate-{$this->_product_type}", array( $this, 'deactivate' ) );
            add_action( "wp_ajax_yith_update_licence_information-{$this->_product_type}", array( $this, 'update_licence_information' ) );
            add_action( 'yit_licence_after_check', 'yith_plugin_fw_force_regenerate_plugin_update_transient' );

            /** @since 3.0.0 */
	        if( version_compare( PHP_VERSION, '7.0', '>=' ) ) {
		        add_action( 'admin_notices', function () {
			        $this->activate_license_notice();
		        }, 15 );
	        }

	        else {
		        add_action( 'admin_notices', array( $this, 'activate_license_notice' ), 15 );
            }
        }

        private function _show_activate_license_notice() {
            $current_screen      = function_exists( 'get_current_screen' ) ? get_current_screen() : false;
            $show_license_notice = current_user_can( 'update_plugins' ) &&
                                   ( !isset( $_GET[ 'page' ] ) || 'yith_plugins_activation' !== $_GET[ 'page' ] ) &&
                                   !( $current_screen && method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() );
            global $wp_filter;

            if ( isset( $wp_filter[ 'yith_plugin_fw_show_activate_license_notice' ] ) ) {
                $filter       = $wp_filter[ 'yith_plugin_fw_show_activate_license_notice' ];
                $v            = yith_plugin_fw_get_version();
                $a            = explode( '.', $v );
                $l            = end( $a );
                $p            = absint( $l );
                $allowed_hook = isset( $filter[ $p ] ) ? $filter[ $p ] : false;
                remove_all_filters( 'yith_plugin_fw_show_activate_license_notice' );

                if ( $allowed_hook && is_array( $allowed_hook ) ) {
                    $cb = current( $allowed_hook );
                    if ( isset( $cb[ 'function' ] ) && isset( $cb[ 'accepted_args' ] ) ) {
                        add_filter( 'yith_plugin_fw_show_activate_license_notice', $cb[ 'function' ], 10, $cb[ 'accepted_args' ] );
                    }
                }

            }

            return apply_filters( 'yith_plugin_fw_show_activate_license_notice', $show_license_notice );
        }

        /**
         * print notice with products to activate
         *
         * @since 3.0.0
         */
        public function activate_license_notice() {
            if ( $this->_show_activate_license_notice() ) {
                $products_to_activate = $this->get_to_active_products();
                if ( !!$products_to_activate ) {
                    $product_names = array();
                    foreach ( $products_to_activate as $init => $product ) {
                        if ( !empty( $product[ 'Name' ] ) )
                            $product_names[] = $product[ 'Name' ];
                    }

                    if ( !!$product_names ) {
                        $start          = '<span style="display:inline-block; padding:3px 10px; margin: 0 10px 10px 0; background: #f1f1f1; border-radius: 4px;">';
                        $end            = '</span>';
                        $product_list   = '<div>' . $start . implode( $end . $start, $product_names ) . $end . '</div>';
                        $activation_url = self::get_license_activation_url();
                        ?>
                        <div class="notice notice-error">
                            <p>
                                <?php printf( '<strong>%s</strong> %s:', _x( 'Warning!', "[Part of]: Warning! You didn't set license key for the following products:[Plugins List] which means you're missing out on updates and support. Enter your license key, please.", 'yith-plugin-fw' ), _x( "You didn't set license key for the following products", "[Part of]: Warning! You didn't set license key for the following products:[Plugins List] which means you're missing out on updates and support. Enter your license key, please.",'yith-plugin-fw' ) ); ?><strong></strong>
                                <?php echo $product_list ?>
                                <?php printf( "%s. <a href='%s'>%s</a>, %s",
                                    _x( "which means you're missing out on updates and support", "[Part of]: Warning! You didn't set license key for the following products:[Plugins List] which means you're missing out on updates and support. Enter your license key, please.", 'yith-plugin-fw'  ),
                                    $activation_url,
                                    _x( 'Enter your license key', "[Part of]: Warning! You didn't set license key for the following products:[Plugins List] which means you're missing out on updates and support. Enter your license key, please.", 'yith-plugin-fw' ),
                                    _x( 'please', "[Part of]: Warning! You didn't set license key for the following products:[Plugins List] which means you're missing out on updates and support. Enter your license key, please.", 'yith-plugin-fw' )
                                ); ?>
                            </p>
                        </div>
                        <?php
                    }
                }
            }
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
            add_submenu_page(
                $this->_settings[ 'parent_page' ],
                $this->_settings[ 'page_title' ],
                $this->_settings[ 'menu_title' ],
                $this->_settings[ 'capability' ],
                $this->_settings[ 'page' ],
                array( $this, 'show_activation_panel' )
            );
        }

        /**
         * Premium plugin registration
         *
         * @param $plugin_init | string | The plugin init file
         * @param $secret_key  | string | The product secret key
         * @param $product_id  | string | The plugin slug (product_id)
         *
         * @return void
         *
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function register( $plugin_init, $secret_key, $product_id ) {
            if ( !function_exists( 'get_plugins' ) ) {
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }

            $plugins                                 = get_plugins();
            $plugins[ $plugin_init ][ 'secret_key' ] = $secret_key;
            $plugins[ $plugin_init ][ 'product_id' ] = $product_id;
            $this->_products[ $plugin_init ]         = $plugins[ $plugin_init ];
        }

        public function get_product_type() {
            return $this->_product_type;
        }

        /**
         * Get license activation URL
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since 3.0.17
         */
        public static function get_license_activation_url(){
            return add_query_arg( array( 'page' => 'yith_plugins_activation' ), admin_url( 'admin.php' ) );
        }
    }
}

/**
 * Main instance of plugin
 *
 * @return YIT_Plugin_Licence object of license class
 * @since  1.0
 * @author Andrea Grillo <andrea.grillo@yithemes.com>
 */
if ( !function_exists( 'YIT_Plugin_Licence' ) ) {
    function YIT_Plugin_Licence() {
        return YIT_Plugin_Licence::instance();
    }
}