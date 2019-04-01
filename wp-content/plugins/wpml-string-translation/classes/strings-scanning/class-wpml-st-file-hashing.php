<?php

class WPML_ST_File_Hashing {

	const OPTION_NAME = 'wpml-scanning-files-hashing';

	/** @var array */
	private $hashes;

	public function __construct() {
		$this->hashes = $this->get_hashes();
	}

	/**
	 * @param string $file
	 */
	private function set_hash( $file ) {
		$this->hashes[ $file ] = md5_file( $file );
	}

	/**
	 * @param string $file
	 *
	 * @return bool
	 */
	public function hash_changed( $file ) {
		return ! array_key_exists( $file, $this->hashes ) || md5_file( $file ) !== $this->hashes[ $file ];
	}

	public function save_hash() {
		$needs_to_save = false;
		if (array_key_exists( 'files', $_POST ) ) {
			foreach ( $_POST['files'] as $file_path ) {
				if ( realpath( $file_path ) ) {
					$this->set_hash( $file_path );
					$needs_to_save = true;
				}
			}
		}

		if ( $needs_to_save ) {
			update_option( self::OPTION_NAME, $this->hashes );
		}

		wp_send_json_success();
	}

	/**
	 * @return array
	 */
	private function get_hashes() {
		return get_option( self::OPTION_NAME ) ? get_option( self::OPTION_NAME ) : array();
	}

	public function clean_hashes() {
		delete_option( self::OPTION_NAME );
	}
}