<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Scripts
 *
 * @since 1.0
 * @return void
 */
function affwp_mlm_admin_scripts() {

	if ( ! affwp_is_admin_page() ) {
		return;
	}

	wp_enqueue_script( 'affwp-mlm-select2', AFFWP_MLM_PLUGIN_URL . 'lib/select2/select2.min.js', array( 'jquery' ), '3.5.2' );
	wp_enqueue_script( 'affwp-mlm-admin', AFFWP_MLM_PLUGIN_URL . 'assets/js/admin.js', array( 'jquery', 'affwp-mlm-select2' ), '0.1.0' );
	wp_enqueue_style( 'affwp-mlm-select2', AFFWP_MLM_PLUGIN_URL . 'lib/select2/select2.css', array(), '3.5.2' );
	
	wp_enqueue_style( 'affwp-mlm-frontend', AFFWP_MLM_PLUGIN_URL . 'assets/css/mlm.css', AFFWP_MLM_VERSION );

}
add_action( 'admin_enqueue_scripts', 'affwp_mlm_admin_scripts' );


/**
 *  Load the frontend styles
 *  
 *  @since 1.1
 *  @return void
 */
function affwp_mlm_frontend_styles() {
	
	wp_enqueue_style( 'affwp-mlm-frontend', AFFWP_MLM_PLUGIN_URL . 'assets/css/mlm.css', AFFWP_MLM_VERSION );
	
	// FontAwesome
	wp_enqueue_style( 'affwp-mlm-fa', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' );


}
add_action( 'wp_enqueue_scripts', 'affwp_mlm_frontend_styles' );