<?php

class WPML_ST_MO_Scan_Hooks {
	/** @var WPML_ST_MO_Queue */
	private $mo_queue;

	/** @var WPML_ST_MO_Dictionary */
	private $mo_dictionary;

	/** @var WPML_File */
	private $wpml_file;

	/**
	 * @param WPML_ST_MO_Queue $mo_queue
	 * @param WPML_ST_MO_Dictionary $mo_dictionary
	 * @param WPML_File $wpml_file
	 */
	public function __construct(
		WPML_ST_MO_Queue $mo_queue,
		WPML_ST_MO_Dictionary $mo_dictionary,
		WPML_File $wpml_file
	) {
		$this->mo_queue          = $mo_queue;
		$this->mo_dictionary     = $mo_dictionary;
		$this->wpml_file         = $wpml_file;
	}

	public function add_hooks() {
		add_filter( 'override_load_textdomain', array( $this, 'block_loading_of_imported_mo_files' ), PHP_INT_MAX, 3 );
		add_action( 'shutdown', array( $this, 'import_mo_files' ), 10, 0 );
	}

	public function import_mo_files() {
		if ( ! $this->mo_queue->is_locked() ) {
			$this->mo_queue->lock();
			$this->mo_queue->import();
			$this->mo_queue->unlock();
		}
	}

	public function block_loading_of_imported_mo_files( $override, $domain, $mo_file ) {
		$relative_path = $this->wpml_file->get_relative_path( $mo_file );
		$file = $this->mo_dictionary->find_file_info_by_path( $relative_path );

		$statuses = array( WPML_ST_MO_File::IMPORTED, WPML_ST_MO_File::FINISHED );

		return $file && in_array( $file->get_status(), $statuses, true );
	}
}
