<?php

class WPML_ST_Update_File_Hash_Ajax_Factory extends WPML_AJAX_Base_Factory implements IWPML_Backend_Action_Loader {

	const AJAX_ACTION = 'update_file_hash';
	const NONCE       = 'wpml-update-file-hash-nonce';

	/** @return null|WPML_ST_Update_File_Hash_Ajax */
	public function create() {
		$hooks = null;

		if ( $this->is_valid_action( self::AJAX_ACTION ) ) {
			$hooks = new WPML_ST_Update_File_Hash_Ajax( new WPML_ST_File_Hashing() );
		}
		return $hooks;
	}
}