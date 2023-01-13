<?php
    /**
     * @package     Freemius Migration
     * @copyright   Copyright (c) 2016, Freemius, Inc.
     * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
     * @since       1.0.3
     */

    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

    if ( ! class_exists( 'FS_Client_License_Abstract_v2' ) ) {
        require_once dirname( __FILE__ ) . '/class-fs-client-license-abstract.php';
    }

    if ( ! class_exists( 'FS_EDD_Client_Migration_v2' ) ) {
        require_once dirname( __FILE__ ) . '/class-fs-edd-client-migration.php';
    }

    /**
     * You should use your own unique CLASS name, and be sure to replace it
     * throughout this file. For example, if your product's name is "Awesome Product"
     * then you can rename it to "Awesome_Product_EDD_License_Key".
     */
    class OceanWP_EDD_License_Key extends FS_Client_License_Abstract_v2 {
        /**
         * @var array<string,string>
         */
        public static $paid_addons = array(
            'Ocean_Cookie_Notice'     => array(
                'name'         => 'Cookie Notice',
                'fs_id'        => '3765',
                'fs_shortcode' => 'ocean_cookie_notice_fs',
            ),
            'Ocean_Elementor_Widgets' => array(
                'name'         => 'Elementor Widgets',
                'fs_id'        => '3757',
                'fs_shortcode' => 'ocean_elementor_widgets_fs',
            ),
            'Ocean_Footer_Callout'    => array(
                'name'         => 'Footer Callout',
                'fs_id'        => '3754',
                'fs_shortcode' => 'ocean_footer_callout_fs',
            ),
            'Ocean_Full_Screen'       => array(
                'name'         => 'Full Screen',
                'fs_id'        => '3766',
                'fs_shortcode' => 'ocean_full_screen_fs',
            ),
            'Ocean_Hooks'             => array(
                'name'         => 'Ocean Hooks',
                'fs_id'        => '3758',
                'fs_shortcode' => 'oh_fs',
            ),
            /*'Ocean_Instagram'         => array(
                'name'         => 'Instagram',
                'fs_id'        => '3763',
                'fs_shortcode' => 'ocean_instagram_fs',
            ),*/
            'Ocean_Popup_Login'       => array(
                'name'         => 'Popup Login',
                'fs_id'        => '3764',
                'fs_shortcode' => 'ocean_popup_login_fs',
            ),
            'Ocean_Portfolio'         => array(
                'name'         => 'Portfolio',
                'fs_id'        => '3761',
                'fs_shortcode' => 'ocean_portfolio_fs',
            ),
            'Ocean_Pro_Demos'         => array(
                'name'         => 'Pro Demos',
                'fs_id'        => '3797',
                'fs_shortcode' => 'ocean_pro_demos_fs',
            ),
            'Ocean_Side_Panel'        => array(
                'name'         => 'Side Panel',
                'fs_id'        => '3756',
                'fs_shortcode' => 'ocean_side_panel_fs',
            ),
            'Ocean_Sticky_Footer'     => array(
                'name'         => 'Sticky Footer',
                'fs_id'        => '3759',
                'fs_shortcode' => 'ocean_sticky_footer_fs',
            ),
            'Ocean_Sticky_Header'     => array(
                'name'         => 'Sticky Header',
                'fs_id'        => '3755',
                'fs_shortcode' => 'ocean_sticky_header_fs',
            ),
            'Ocean_White_Label'       => array(
                'name'         => 'White Label',
                'fs_id'        => '3762',
                'fs_shortcode' => 'ocean_white_label_fs',
            ),
            'Ocean_Woo_Popup'         => array(
                'name'         => 'Woo Popup',
                'fs_id'        => '3760',
                'fs_shortcode' => 'ocean_woo_popup_fs',
            ),
        );

        public static $separate_addons = array(
            'Ocean_eCommerce'         => array(
                'name'         => 'Ocean Treasure Box',
                'fs_id'        => '11449',
                'fs_shortcode' => 'oet_fs',
            ),
            'Ocean_Gutenberg_Blocks'         => array(
                'name'         => 'Ocean Gutenberg Blocks',
                'fs_id'        => '9081',
                'fs_shortcode' => 'ocean_gutenberg_blocks_fs',
            ),
        );

        private $_is_valid;
        private $_is_bundle;
        private $_addon_class;
        private $_addon_license_index;
        private $_logger;

        /**
         * OceanWP_EDD_License_Key constructor.
         *
         * @param bool   $is_bundle
         * @param string $addon_class
         * @param string $addon_name
         */
        function __construct( $is_bundle, $addon_class = '', $addon_name = '' ) {
            $this->_is_bundle           = $is_bundle;
            $this->_addon_class         = $addon_class;
            $this->_addon_license_index = sprintf(
                'oceanwp_%s_license_key',
                preg_replace( '/[^a-zA-Z0-9_\s]/', '', str_replace( ' ', '_', strtolower( $addon_name ) ) )
            );
            $this->_is_valid            = true;

            if ( ! $is_bundle ) {
                $this->_is_valid = (
                    isset( self::$paid_addons[ $addon_class ] ) &&
                    $addon_name === self::$paid_addons[ $addon_class ]['name']
                );
            }

            $this->_logger = FS_Logger::get_logger(
                WP_FS__SLUG . '_oceanwp_migration_' . ($is_bundle ? 'bundle' : $addon_class),
                WP_FS__DEBUG_SDK,
                WP_FS__ECHO_DEBUG_SDK
            );
        }

        /**
         * @author   Vova Feldman (@svovaf)
         * @since    1.0.3
         *
         * @param int|null $blog_id
         *
         * @return string
         */
        function get( $blog_id = null ) {
            if ( ! $this->_is_valid ) {
                return '';
            }

            if ( $this->_is_bundle ) {
                return trim( get_option( 'oceanwp_bundle_key', '' ) );
            }

            $oceanwp_options = get_option( 'oceanwp_options', '' );

            if ( ! is_array( $oceanwp_options ) || ! isset( $oceanwp_options['licenses'] ) ) {
                return '';
            }

            return isset( $oceanwp_options['licenses'][ $this->_addon_license_index ] ) ?
                trim( $oceanwp_options['licenses'][ $this->_addon_license_index ] ) :
                '';
        }

        /**
         * When migrating a bundle license and the sales platform creates a different
         * license key for every product in the bundle which is the key that actually
         * used for activation, this method should return the collection of all
         * child license keys that were activated on the current website.
         *
         * @author   Vova Feldman (@svovaf)
         * @since    1.1.0
         *
         * @param int|null $blog_id
         *
         * @return string[]
         */
        function get_children( $blog_id = null ) {
            if ( ! $this->_is_valid ) {
                return array();
            }

            $oceanwp_options = get_option( 'oceanwp_options', '' );

            if ( ! is_array( $oceanwp_options ) || ! isset( $oceanwp_options['licenses'] ) ) {
                return array();
            }

            $children_license_keys = array();
            foreach ( self::$paid_addons as $class_name => $data ) {
                $index = sprintf(
                    'oceanwp_%s_license_key',
                    preg_replace( '/[^a-zA-Z0-9_\s]/', '', str_replace( ' ', '_', strtolower( $data['name'] ) ) )
                );;

                if ( isset( $oceanwp_options['licenses'][ $index ] ) ) {
                    $children_license_keys[] = trim( $oceanwp_options['licenses'][ $index ] );
                }
            }

            return $children_license_keys;
        }

        /**
         * @author   Vova Feldman (@svovaf)
         * @since    1.0.3
         *
         * @param string   $license_key
         * @param int|null $blog_id
         *
         * @return bool True if successfully updated.
         */
        function set( $license_key, $blog_id = null ) {
            if ( ! $this->_is_valid ) {
                return false;
            }

            if ( $this->_is_bundle ) {
                $option_name  = 'oceanwp_bundle_key';
                $option_value = $license_key;
            } else {
                $option_name = 'oceanwp_options';

                $oceanwp_options = get_option( $option_name, '' );

                if ( ! is_array( $oceanwp_options ) ) {
                    $oceanwp_options = array( 'licenses' => array() );
                } else if ( ! isset( $oceanwp_options['licenses'] ) ) {
                    $oceanwp_options['licenses'] = array();
                }

                $oceanwp_options['licenses'][ $this->_addon_license_index ] = $license_key;

                $option_value = $oceanwp_options;
            }

            if ( ! is_multisite() ) {
                return update_option( $option_name, $option_value );
            }

            $blog_ids = FS_Client_Migration_Abstract_v2::get_blog_ids();

            $is_updated = false;
            foreach ( $blog_ids as $blog_id ) {
                switch_to_blog( $blog_id );
                $is_updated = $is_updated || update_option( $option_name, $option_value );
                restore_current_blog();
            }

            return $is_updated;
        }

        /**
         * Override this only when the product supports a network level integration.
         *
         * @author   Vova Feldman (@svovaf)
         * @since    1.1.0
         *
         * @return bool
         */
        public function is_network_migration() {
            /**
             * Comment the line below if you'd like to support network level licenses migration.
             * This is only relevant if you have a special network level integration with your plugin
             * and you're utilizing the Freemius SDK's multisite network integration mode.
             */
            return false;
        }

        /**
         * This method is only relevant when you're using the network level migration mode.
         * The method should return true only if you restrict a network level license activation
         * to apply the exact same license for the products network wide.
         *
         * For example, if a network with 5-sites can have license1 on sub-sites 1-3,
         * and license2 on sub-sites 4-5, then the result of this method should be set to `false`.
         * BUT, if you the only way to activate the license is that it will be the same license on
         * all sub-sites 1-5, then this method should return `true`.
         *
         * @return bool
         */
        public function are_licenses_network_identical() {
            return false;
        }

        /**
         * Activates a bundle license on the installed child products, after successfully migrating the license.
         *
         * @author   Vova Feldman (@svovaf)
         * @since    2.0.0
         *
         * @param \FS_User    $user
         * @param string|null $bundle_license_key
         */
        public function activate_bundle_license_after_migration( FS_User $user, $bundle_license_key = null ) {
            $this->_logger->entrance("bundle_license_key=" . var_export( $bundle_license_key, true ));

            if ( $this->_is_bundle || empty( $bundle_license_key ) ) {
                $bundle_license_key = $this->get();
            }

            // Iterate over the installed add-ons and try to activate the bundle's license for each add-on.
            foreach ( self::$paid_addons as $class_name => $data ) {
                if ( ! class_exists( $class_name ) ) {
                    $this->_logger->log( "Class {$class_name} does not exist." );

                    continue;
                }

                if ( ! function_exists( $data['fs_shortcode'] ) ) {
                    $this->_logger->log( "Function {$data['fs_shortcode']} does not exist." );

                    continue;
                }

                /**
                 * Initiate the Freemius instance before migrating.
                 *
                 * @var Freemius $addon_fs
                 */
                $addon_fs = call_user_func( $data['fs_shortcode'] );

                $this->_logger->log( 'Starting activation of the migrated license for ' . str_replace( '_', ' ', $class_name) . '.' );

                $addon_fs->activate_migrated_license( $bundle_license_key );
            }
        }
    }

    if ( ! class_exists( 'FS_Client_Addon_Migration_Abstract_v2' ) ) {
        require_once dirname( __FILE__ ) . '/class-fs-client-addon-migration-abstract.php';
    }

    /**
     * @todo For add-ons migration change the if condition from `false` to `true` an update the class according to the inline instructions.
     *
     * @author   Vova Feldman (@svovaf)
     * @since    2.0.0
     */
    if ( true ) {
        /**
         * You should use your own unique CLASS name, and be sure to replace it
         * throughout this file. For example, if your product's name is "Awesome Product"
         * then you can rename it to "Awesome_Product_EDD_Addon_Migration".
         *
         * @author   Vova Feldman (@svovaf)
         * @since    2.0.0
         *
         * Class OceanWP_EDD_Addon_Migration
         */
        class OceanWP_EDD_Addon_Migration extends FS_Client_Addon_Migration_Abstract_v2 {

            #region Singleton

            /**
             * @var FS_Client_Addon_Migration_Abstract_v2[]
             */
            protected static $_INSTANCES = array();

            /**
             * @param string $addon_shortcode
             *
             * @return FS_Client_Addon_Migration_Abstract_v2
             */
            public static function instance( $addon_shortcode ) {
                if ( ! isset( self::$_INSTANCES[ $addon_shortcode ] ) ) {
                    self::$_INSTANCES[ $addon_shortcode ] = new self( $addon_shortcode );
                }

                return self::$_INSTANCES[ $addon_shortcode ];
            }

            /**
             * OceanWP_EDD_Addon_Migration constructor.
             *
             * @param string $addon_shortcode
             */
            private function __construct( $addon_shortcode ) {
                $this->_addon_shortcode = $addon_shortcode;
            }

            #endregion

            /**
             * The parent product's shortcode.
             *
             * @author   Vova Feldman (@svovaf)
             * @since    2.0.0
             *
             * @return string
             */
            protected function get_parent_shortcode() {
                return 'owp_fs';
            }

            /**
             * @author   Vova Feldman (@svovaf)
             * @since    2.0.0
             *
             * @return bool
             */
            protected function is_parent_included() {
                return class_exists( 'OCEANWP_Theme_Class' );
            }

            /**
             * @author   Vova Feldman (@svovaf)
             * @since    2.0.0
             *
             * @return array
             */
            protected function get_addons_sdk_init_common_config() {
                return array(
                    'type'            => 'plugin',
                    'is_premium'      => true,
                    'is_premium_only' => true,
                    'has_paid_plans'  => true,
                    'parent'          => array(
                        'id'         => '3752',
                        'slug'       => 'oceanwp',
                        'public_key' => 'pk_043077b34f20f5e11334af3c12493',
                        'name'       => 'OceanWP',
                    ),
                    'menu'            => array(
                        'first-path' => 'plugins.php',
                        'support'    => false,
                    ),
                );
            }

            /**
             * @author   Vova Feldman (@svovaf)
             * @since    2.0.0
             *
             * @param bool   $is_bundle
             * @param string $addon_class
             * @param string $addon_name
             *
             * @return FS_Client_License_Abstract_v2
             */
            protected function get_new_license_key_manager( $is_bundle, $addon_class = '', $addon_name = '' ) {
                return new OceanWP_EDD_License_Key( $is_bundle, $addon_class, $addon_name );
            }

            /**
             * @todo This should point to your EDD store root URL.
             *
             * @author   Vova Feldman (@svovaf)
             * @since    2.0.0
             *
             * @return string
             */
            protected function get_store_url() {
                return 'https://oceanwp.org';
            }
        }
    }

    $is_migration_debug = FS_Client_Addon_Migration_Abstract_v2::is_migration_debug();

    if ( FS_Client_Addon_Migration_Abstract_v2::is_migration_on() ) {
        if ( ! $is_migration_debug ||
             ( ! FS_Client_Addon_Migration_Abstract_v2::is_wp_ajax() && ! FS_Client_Addon_Migration_Abstract_v2::is_wp_cron() )
        ) {
            $bundle_license_manager = new OceanWP_EDD_License_Key( true );

            // @todo We need to make sure that if there's both a bundle license and individual add-on license, it first migrates the bundle’s license, and only later migrate the individual license, but only if the bundle’s migration failed.

            if ( empty( $bundle_license_manager->get() ) ) {
                // Bundle license is not set, try to migrate per add-on.
                do_action( 'owp_fs_client_migration_loaded' );
            } else {
                // Bundle license is set, try to migrate the bundle's license.
                new FS_EDD_Client_Migration_v2(
                // This should be replaced with your custom Freemius shortcode.
                    owp_fs(),

                    // This should point to your EDD store root URL.
                    'https://oceanwp.org',

                    // The bundle's download ID.
                    '37394',

                    $bundle_license_manager,

                    // Migration type.
                    FS_Client_Migration_Abstract_v2::TYPE_BUNDLE_TO_BUNDLE,

                    // Freemius was NOT included in the previous (last) version of the product.
                    true,

                    // For testing, you can change that argument to TRUE to trigger the migration in the same HTTP request.
                    $is_migration_debug
                );
            }
        }
    }