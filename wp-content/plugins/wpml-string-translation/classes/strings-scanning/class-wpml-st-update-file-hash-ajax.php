<?php

class WPML_ST_Update_File_Hash_Ajax implements IWPML_Action {

	/** @var WPML_ST_File_Hashing */
	private $file_hashing;

	/**
	 * WPML_ST_Update_File_Hash_Ajax constructor.
	 *
	 * @param WPML_ST_File_Hashing $file_hashing
	 */
	public function __construct( WPML_ST_File_Hashing $file_hashing ) {
		$this->file_hashing = $file_hashing;
	}

	public function add_hooks() {
		add_action( 'wp_ajax_update_file_hash', array( $this->file_hashing, 'save_hash' ) );
	}
}