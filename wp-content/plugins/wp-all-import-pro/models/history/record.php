<?php

class PMXI_History_Record extends PMXI_Model_Record {	
	
	/**
	 * Initialize model instance
	 * @param array[optional] $data Array of record data to initialize object with
	 */
	public function __construct($data = array()) {
		parent::__construct($data);
		$this->setTable(PMXI_Plugin::getInstance()->getTablePrefix() . 'history');
	}

	public function delete( $db = true ) {
		if ($this->id) { // delete history file first

			$uploads = wp_upload_dir();

			$history_file_path = wp_all_import_secure_file( $uploads['basedir'] . DIRECTORY_SEPARATOR . PMXI_Plugin::LOGS_DIRECTORY, $this->id, false, false ) . DIRECTORY_SEPARATOR . $this->id . '.html';
			if ( @file_exists($history_file_path) ){
				wp_all_import_remove_source($history_file_path);
			}
		}
		return ($db) ? parent::delete() : true;
	}
	
}