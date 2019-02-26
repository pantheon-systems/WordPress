<?php
/**
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

!defined( 'ABSPATH' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_Debug' ) ) {
    /**
     * YITH_Debug
     *
     * manages debug
     *
     * @class       YITH_Debug
     * @package     YITH
     * @since       1.0.0
     * @author      Leanza Francesco <leanzafrancesco@gmail.com>
     *
     */
    class YITH_Debug {

        /** @var YITH_Debug */
        private static $_instance;

        public static function get_instance() {
            return isset( self::$_instance ) ? self::$_instance : self::$_instance = new self();
        }

        /**
         * @access private
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        private function __construct() {
            add_action( 'init', array( $this, 'init' ) );
        }

        /**
         * fired on init
         */
        public function init() {
            if ( !is_admin() || defined( 'DOING_AJAX' ) )
                return;

            $is_debug = apply_filters( 'yith_plugin_fw_is_debug', isset( $_GET[ 'yith-debug' ] ) );

            if ( $is_debug ) {
                add_action( 'admin_bar_menu', array( $this, 'add_debug_in_admin_bar' ), 99 );
            }
        }

        /**
         * add debug node in admin bar
         *
         * @param $wp_admin_bar
         */
        public function add_debug_in_admin_bar( $wp_admin_bar ) {
            $args = array(
                'id'    => 'yith-debug-admin-bar',
                'title' => 'YITH Debug',
                'href'  => '',
                'meta'  => array(
                    'class' => 'yith-debug-admin-bar'
                )
            );
            $wp_admin_bar->add_node( $args );

            $subnodes = array();

            foreach ( $this->get_debug_information() as $key => $information ) {
                $label = $information[ 'label' ];
                $value = $information[ 'value' ];
                $url   = !empty( $information[ 'url' ] ) ? $information[ 'url' ] : '';

                if ( !!$value ) {
                    $title = "<strong>$label:</strong> $value";
                } else {
                    $title = "<strong>$label</strong>";
                }

                $subnodes[] = array(
                    'id'     => 'yith-debug-admin-bar-' . $key,
                    'parent' => 'yith-debug-admin-bar',
                    'title'  => $title,
                    'href'   => $url,
                    'meta'   => array(
                        'class' => 'yith-debug-admin-bar-' . $key
                    )
                );

                if ( isset( $information[ 'subsub' ] ) ) {
                    foreach ( $information[ 'subsub' ] as $sub_key => $sub_value ) {
                        $title      = isset( $sub_value[ 'title' ] ) ? $sub_value[ 'title' ] : '';
                        $html       = isset( $sub_value[ 'html' ] ) ? $sub_value[ 'html' ] : '';
                        $subnodes[] = array(
                            'id'     => 'yith-debug-admin-bar-' . $key . '-' . $sub_key,
                            'parent' => 'yith-debug-admin-bar-' . $key,
                            'title'  => $title,
                            'href'   => '',
                            'meta'   => array(
                                'class' => 'yith-debug-admin-bar-' . $key . '-' . $sub_key,
                                'html'  => $html,
                            )
                        );
                    }
                }
            }

            foreach ( $subnodes as $subnode ) {
                $wp_admin_bar->add_node( $subnode );
            }
        }


        /**
         * return an array of debug information
         *
         * @return array
         */
        public function get_debug_information() {
            $debug = array(
                'plugin-fw-info'       => array(
                    'label' => 'Framework',
                    'value' => $this->get_plugin_framework_info()
                ),
                'yith-premium-plugins' => array(
                    'label'  => 'YITH Premium Plugins',
                    'value'  => '',
                    'subsub' => $this->get_premium_plugins_info()
                ),
                'wc-version'           => array(
                    'label' => 'WooCommerce',
                    'value' => $this->get_woocommerce_version_info()
                ),
                'theme'                => array(
                    'label' => 'Theme',
                    'value' => $this->get_theme_info()
                ),
                'screen-id'            => array(
                    'label' => 'Screen ID',
                    'value' => $this->get_current_screen_info()
                ),
                'post-meta'            => array(
                    'label' => 'Post Meta',
                    'value' => '',
                    'url'   => add_query_arg( array( 'yith-debug-post-meta' => 'all' ) )
                ),
                'option'               => array(
                    'label' => 'Option',
                    'value' => '',
                    'url'   => add_query_arg( array( 'yith-debug-option' => '' ) )
                ),
            );

            // Post Meta debug -------------
            global $post;
            if ( !empty( $_GET[ 'yith-debug-post-meta' ] ) && $post ) {
                $meta_key   = $_GET[ 'yith-debug-post-meta' ];
                $meta_value = 'all' !== $meta_key ? get_post_meta( $post->ID, $meta_key, true ) : get_post_meta( $post->ID );

                ob_start();
                echo '<pre>';
                var_dump( $meta_value );
                echo '</pre>';
                $meta_value_html = ob_get_clean();

                $debug[ 'post-meta' ][ 'value' ]  = $meta_key;
                $debug[ 'post-meta' ][ 'subsub' ] = array( array( 'html' => $meta_value_html ) );
            }

            // Option debug -------------

            if ( !empty( $_GET[ 'yith-debug-option' ] ) ) {
                $option_key   = $_GET[ 'yith-debug-option' ];
                $option_value = get_option( $option_key );

                ob_start();
                echo '<pre>';
                var_dump( $option_value );
                echo '</pre>';
                $option_value_html = ob_get_clean();

                $debug[ 'option' ][ 'value' ]  = $option_key;
                $debug[ 'option' ][ 'subsub' ] = array( array( 'html' => $option_value_html ) );
            }

            return $debug;
        }

        /** -----------------------------------------------------------
         *                          GETTER INFO
         *  -----------------------------------------------------------
         */


        /**
         * return the current screen id
         *
         * @return string
         */
        public function get_current_screen_info() {
            $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : false;

            return !!$screen ? $screen->id : 'null';
        }

        /**
         * return the current theme name and version
         *
         * @return string
         */
        public function get_theme_info() {
            $theme = function_exists( 'wp_get_theme' ) ? wp_get_theme() : false;

            return !!$theme ? $theme->get( 'Name' ) . ' (' . $theme->get( 'Version' ) . ')' : 'null';
        }

        /**
         * return the WooCommerce version if active
         *
         * @return string
         */
        public function get_woocommerce_version_info() {
            return function_exists( 'WC' ) ? WC()->version : 'not active';
        }

        /**
         * return plugin framework information (version and loaded_by)
         *
         * @return string
         */
        public function get_plugin_framework_info() {
            $plugin_fw_version   = yith_plugin_fw_get_version();
            $plugin_fw_loaded_by = basename( dirname( YIT_CORE_PLUGIN_PATH ) );

            return "$plugin_fw_version (by $plugin_fw_loaded_by)";
        }

        /**
         * return premium plugins list with versions
         *
         * @return array
         */
        public function get_premium_plugins_info() {
            $plugins      = YIT_Plugin_Licence()->get_products();
            $plugins_info = array();

            if ( !!$plugins ) {
                foreach ( $plugins as $plugin ) {
                    $plugins_info[ $plugin[ 'product_id' ] ] = array( 'title' => $plugin[ 'Name' ] . ' (' . $plugin[ 'Version' ] . ')' );
                }

                sort( $plugins_info );
            }

            return $plugins_info;
        }
    }
}
if ( !function_exists( 'YITH_Debug' ) ) {
    function YITH_Debug() {
        return YITH_Debug::get_instance();
    }

    YITH_Debug();
}