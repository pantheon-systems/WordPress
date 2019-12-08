<?php

class WPCFM_Ajax
{

    function __construct() {
        add_action( 'wp_ajax_wpcfm_load', array( $this, 'load_settings' ) );
        add_action( 'wp_ajax_wpcfm_save', array( $this, 'save_settings' ) );
        add_action( 'wp_ajax_wpcfm_push', array( $this, 'push_settings' ) );
        add_action( 'wp_ajax_wpcfm_pull', array( $this, 'pull_settings' ) );
        add_action( 'wp_ajax_wpcfm_diff', array( $this, 'load_diff' ) );
    }


    /**
     * Load admin settings
     */
    function load_settings() {
        if ( current_user_can( 'manage_options' ) ) {
            $bundles = WPCFM()->helper->get_bundles();
            echo json_encode( array( 'bundles' => $bundles ) );
        }
        exit;
    }


    /**
     * Save admin settings
     */
    function save_settings() {
        if ( current_user_can( 'manage_options' ) ) {
            $settings = stripslashes( $_POST['data'] );

            // Save the option
            WPCFM()->options->update( 'wpcfm_settings', $settings );

            // Delete orphan bundles
            $file_bundles = array_keys( WPCFM()->helper->get_file_bundles() );
            $new_bundles = WPCFM()->helper->get_bundles();

            foreach ( $file_bundles as $bundle_name ) {
                if ( ! isset( $new_bundles[ $bundle_name ] ) || false === $new_bundles[ $bundle_name ]['is_db'] ) {
                    WPCFM()->readwrite->delete_file( $bundle_name );
                }
            }

            echo __( 'Settings saved', 'wpcfm' );
        }
        exit;
    }


    function load_diff() {
        if ( current_user_can( 'manage_options' ) ) {
            $bundle_name = stripslashes( $_POST['data']['bundle_name'] );
            $comparison = WPCFM()->readwrite->compare_bundle( $bundle_name );

            // The pretty-text-diff.js will do its best on these print_r()s.
            if ( isset( $comparison['file'] ) ) {
                $comparison['file'] = print_r( $comparison['file'], true );
            }
            if ( isset( $comparison['db'] ) ) {
                $comparison['db'] = print_r( $comparison['db'], true );
            }

            echo json_encode( $comparison );
        }
        exit;
    }


    /**
     * Push settings to filesystem
     */
    function push_settings() {
        if ( current_user_can( 'manage_options' ) ) {
            $bundle_name = stripslashes( $_POST['data']['bundle_name'] );
            WPCFM()->readwrite->push_bundle( $bundle_name );
            echo __( 'Push successful', 'wpcfm' );
        }
        exit;
    }


    /**
     * Pull settings into DB
     */
    function pull_settings() {
        if ( current_user_can( 'manage_options' ) ) {
            $bundle_name = stripslashes( $_POST['data']['bundle_name'] );
            WPCFM()->readwrite->pull_bundle( $bundle_name );
            echo __( 'Pull successful', 'wpcfm' );
        }
        exit;
    }
}
