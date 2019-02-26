<?php

class WPML_ST_MO_Dictionary {
	/** @var WPML_ST_MO_Dictionary_Storage */
	private $storage;

	/**
	 * @param WPML_ST_MO_Dictionary_Storage $storage
	 */
	public function __construct( WPML_ST_MO_Dictionary_Storage $storage ) {
		$this->storage = $storage;
	}

	/**
	 * @param $mo_file_path
	 *
	 * @return WPML_ST_MO_File
	 */
	public function find_file_info_by_path( $mo_file_path ) {
		$result = $this->storage->find( $mo_file_path );
		if ( $result ) {
			return current( $result );
		}

		return null;
	}

	/**
	 * @param WPML_ST_MO_File $file
	 */
	public function save( WPML_ST_MO_File $file ) {
		$this->storage->save( $file );
	}

	/**
	 * @return WPML_ST_MO_File[]
	 */
	public function get_not_imported_mo_files() {
		return $this->storage->find( null, array( WPML_ST_MO_File::NOT_IMPORTED, WPML_ST_MO_File::PARTLY_IMPORTED ) );
	}

	/**
	 * @return WPML_ST_MO_File[]
	 */
	public function get_imported_mo_files() {
		return $this->storage->find( null, WPML_ST_MO_File::IMPORTED );
	}
}
