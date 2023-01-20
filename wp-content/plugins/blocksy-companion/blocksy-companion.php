<?php

/*
Plugin Name: Blocksy Companion
Description: This plugin is the companion for the Blocksy theme, it runs and adds its enhacements only if the Blocksy theme is installed and active.
Version: 1.8.67
Author: CreativeThemes
Author URI: https://creativethemes.com
Text Domain: blocksy-companion
Domain Path: /languages/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly.
}

register_activation_hook( __FILE__, function () {
    
    if ( class_exists( '\\Blocksy\\Plugin' ) && !function_exists( 'blc_fs' ) ) {
        $to_deactivate = plugin_basename( str_replace( '-pro/', '/', __FILE__ ) );
        if ( is_plugin_active( $to_deactivate ) ) {
            deactivate_plugins( $to_deactivate );
        }
    }
    
    if ( isset( $_REQUEST['action'] ) && 'activate-selected' === $_REQUEST['action'] && isset( $_POST['checked'] ) && count( $_POST['checked'] ) > 1 ) {
        return;
    }
    add_option( 'blc_activation_redirect', wp_get_current_user()->ID );
} );

if ( function_exists( 'blc_fs' ) || class_exists( '\\Blocksy\\Plugin' ) ) {
    if ( function_exists( 'blc_fs' ) ) {
        blc_fs()->set_basename( false, __FILE__ );
    }
} else {
    
    if ( !function_exists( 'blc_fs' ) && file_exists( dirname( __FILE__ ) . '/freemius/start.php' ) ) {
        global  $blc_fs ;
        
        if ( !isset( $blc_fs ) ) {
            if ( !defined( 'WP_FS__PRODUCT_5115_MULTISITE' ) ) {
                define( 'WP_FS__PRODUCT_5115_MULTISITE', true );
            }
            require_once dirname( __FILE__ ) . '/freemius/start.php';
            $has_account = true;
            $instance = \Freemius::instance( 5115, 'blocksy-companion', true );
            
            if ( in_array( 'white-label', get_option( 'blocksy_active_extensions', [] ) ) && $instance->is_plan( 'agency' ) ) {
                $settings = get_option( 'blocksy_ext_white_label_settings', [] );
                if ( $settings && isset( $settings['hide_billing_account'] ) && $settings['hide_billing_account'] ) {
                    $has_account = false;
                }
            }
            
            $blc_fs = fs_dynamic_init( array(
                'id'             => '5115',
                'slug'           => 'blocksy-companion',
                'premium_slug'   => 'blocksy-companion-pro',
                'type'           => 'plugin',
                'public_key'     => 'pk_b00a5cbae90b2e948015a7d0710f5',
                'premium_suffix' => 'PRO',
                'is_premium'     => false,
                'has_addons'     => false,
                'has_paid_plans' => true,
                'menu'           => ( true ? [
                'slug'    => 'ct-dashboard',
                'support' => false,
                'contact' => false,
                'pricing' => true,
                'account' => $has_account,
            ] : [
                'support' => false,
                'contact' => false,
                'pricing' => false,
                'account' => false,
            ] ),
                'is_live'        => true,
            ) );
            function blc_fs()
            {
                global  $blc_fs ;
                return $blc_fs;
            }
            
            blc_fs();
            do_action( 'blc_fs_loaded' );
        }
    
    }
    
    define( 'BLOCKSY__FILE__', __FILE__ );
    define( 'BLOCKSY_PLUGIN_BASE', plugin_basename( BLOCKSY__FILE__ ) );
    define( 'BLOCKSY_PATH', plugin_dir_path( BLOCKSY__FILE__ ) );
    define( 'BLOCKSY_URL', plugin_dir_url( BLOCKSY__FILE__ ) );
    add_action( 'init', function () {
        /**
         * Load Blocksy textdomain.
         *
         * Load gettext translate for Blocksy text domain.
         */
        load_plugin_textdomain( 'blocksy-companion', false, dirname( BLOCKSY_PLUGIN_BASE ) . '/languages' );
    } );
    
    if ( !version_compare( PHP_VERSION, '7.0', '>=' ) ) {
        add_action( 'admin_notices', 'blc_fail_php_version' );
    } elseif ( !version_compare( get_bloginfo( 'version' ), '5.0', '>=' ) ) {
        add_action( 'admin_notices', 'blc_fail_wp_version' );
    } else {
        require BLOCKSY_PATH . 'plugin.php';
    }
    
    /**
     * Blocksy admin notice for minimum PHP version.
     *
     * Warning when the site doesn't have the minimum required PHP version.
     */
    function blc_fail_php_version()
    {
        /* translators: %s: PHP version */
        $message = sprintf( esc_html__( 'Blocksy requires PHP version %s+, plugin is currently NOT RUNNING.', 'blocksy-companion' ), '7.0' );
        $html_message = sprintf( '<div class="error">%s</div>', wpautop( $message ) );
        echo  wp_kses_post( $html_message ) ;
    }
    
    /**
     * Blocksy admin notice for minimum WordPress version.
     *
     * Warning when the site doesn't have the minimum required WordPress version.
     */
    function blc_fail_wp_version()
    {
        /* translators: %s: WordPress version */
        $message = sprintf( esc_html__( 'Blocksy requires WordPress version %s+. Because you are using an earlier version, the plugin is currently NOT RUNNING.', 'blocksy-companion' ), '5.0' );
        $html_message = sprintf( '<div class="error">%s</div>', wpautop( $message ) );
        echo  wp_kses_post( $html_message ) ;
    }

}
