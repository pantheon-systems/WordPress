<?php
use \AffWP\Utils\Importer;

/**
 * Process a settings import from a json file
 *
 * @since 1.0
 * @return void
 */
function affwp_process_settings_import() {

	if( empty( $_POST['affwp_import_nonce'] ) )
		return;

	if( ! wp_verify_nonce( $_POST['affwp_import_nonce'], 'affwp_import_nonce' ) )
		return;

	if( ! current_user_can( 'manage_affiliate_options' ) )
		return;

	$extension = end( explode( '.', $_FILES['import_file']['name'] ) );

    if( $extension != 'json' ) {
        wp_die( __( 'Please upload a valid .json file', 'affiliate-wp' ), __( 'Error', 'affiliate-wp' ), array( 'response' => 400 ) );
    }

	$import_file = $_FILES['import_file']['tmp_name'];

	if( empty( $import_file ) ) {
		wp_die( __( 'Please upload a file to import', 'affiliate-wp' ), __( 'Error', 'affiliate-wp' ), array( 'response' => 400 ) );
	}

	// Retrieve the settings from the file and convert the json object to an array
	$settings = new Importer\Settings( $import_file );

	// Run the import.
	$settings->import();

	wp_safe_redirect( affwp_admin_url( 'tools', array( 'tab' => 'export_import', 'affwp_notice' => 'settings-imported' ) ) );
	exit;

}
add_action( 'affwp_import_settings', 'affwp_process_settings_import' );