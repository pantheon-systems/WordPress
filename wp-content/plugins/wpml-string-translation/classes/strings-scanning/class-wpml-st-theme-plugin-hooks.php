<?php

class WPML_ST_Theme_Plugin_Hooks {

	/**
	 * @var WPML_ST_File_Hashing
	 */
	private $file_hashing;

	public function __construct( WPML_ST_File_Hashing $file_hashing ) {
		$this->file_hashing = $file_hashing;
	}

	public function add_hooks() {
		add_action( 'icl_st_unregister_string_multi', array( $this->file_hashing, 'clean_hashes' ) );
	}
}