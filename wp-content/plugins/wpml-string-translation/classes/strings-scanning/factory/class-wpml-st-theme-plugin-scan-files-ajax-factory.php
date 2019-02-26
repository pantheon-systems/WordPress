<?php

class WPML_ST_Theme_Plugin_Scan_Files_Ajax_Factory extends WPML_AJAX_Base_Factory implements IWPML_Backend_Action_Loader {

	const AJAX_ACTION = 'wpml_st_scan_chunk';
	const NONCE       = 'wpml-scan-files-nonce';

	/** @return null|WPML_ST_Theme_Plugin_Scan_Files_Ajax */
	public function create() {
		$hooks = null;
		$scan_factory = '';

		if ( $this->is_valid_action( self::AJAX_ACTION ) ) {
			if ( array_key_exists( 'theme', $_POST ) ) {
				$scan_factory = new WPML_ST_Theme_String_Scanner_Factory();
			} elseif( array_key_exists( 'plugin', $_POST ) ) {
				$scan_factory = new WPML_ST_Plugin_String_Scanner_Factory();
			}

			if ( $scan_factory ) {
				$scan = $scan_factory->create();
				$hooks = new WPML_ST_Theme_Plugin_Scan_Files_Ajax( $scan );
			}
		}

		return $hooks;
	}
}