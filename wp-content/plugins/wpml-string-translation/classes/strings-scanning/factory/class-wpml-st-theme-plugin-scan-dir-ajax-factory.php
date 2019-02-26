<?php

class WPML_ST_Theme_Plugin_Scan_Dir_Ajax_Factory extends WPML_AJAX_Base_Factory implements IWPML_Backend_Action_Loader {

	const AJAX_ACTION = 'wpml_get_files_to_scan';
	const NONCE       = 'wpml-get-files-to-scan-nonce';

	/** @return null|WPML_ST_Theme_Plugin_Scan_Dir_Ajax */
	public function create() {
		$hooks = null;

		if ( $this->is_valid_action( self::AJAX_ACTION ) ) {
			$scan_dir = new WPML_ST_Scan_Dir();
			$file_hashing = new WPML_ST_File_Hashing();
			$hooks = new WPML_ST_Theme_Plugin_Scan_Dir_Ajax( $scan_dir, $file_hashing );
		}

		return $hooks;
	}
}