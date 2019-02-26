<?php

class PMXI_File_Record extends PMXI_Model_Record {
	/**
	 * Initialize model instance
	 * @param array[optional] $data Array of record data to initialize object with
	 */
	public function __construct($data = array()) {
		parent::__construct($data);
		$this->setTable(PMXI_Plugin::getInstance()->getTablePrefix() . 'files');
	}
	
	/**
	 * @see PMXI_Model_Record::insert()
	 */
	public function insert() {
		$file_contents = NULL;
		if ($this->offsetExists('contents')) {
			$file_contents = $this['contents'];
			unset($this->contents);
		}
				
		parent::insert();

		$uploads = wp_upload_dir();

		if (isset($this->id) and ! is_null($file_contents)) {
			file_put_contents($uploads['basedir']  . '/wpallimport/history/' . $this->id, $file_contents);
		}
		
		$list = new PMXI_File_List();
		$list->sweepHistory();
		return $this;
	}
	
	/**
	 * @see PMXI_Model_Record::update()
	 */
	public function update() {
		$file_contents = NULL;
		if ($this->offsetExists('contents')) {
			$file_contents = $this['contents'];
			unset($this->contents);
		}
				
		parent::update();

		if (isset($this->id) and ! is_null($file_contents)) {
			$uploads = wp_upload_dir();
			file_put_contents($uploads['basedir']  . DIRECTORY_SEPARATOR . PMXI_Plugin::HISTORY_DIRECTORY . DIRECTORY_SEPARATOR . $this->id, $file_contents);
		}
		
		return $this;
	}
	
	public function __isset($field) {
		if ('contents' == $field and ! $this->offsetExists($field)) {
			$uploads = wp_upload_dir();
			return isset($this->id) and file_exists($uploads['basedir']  . DIRECTORY_SEPARATOR . PMXI_Plugin::HISTORY_DIRECTORY . DIRECTORY_SEPARATOR . $this->id);
		}
		return parent::__isset($field);
	}
	
	public function __get($field) {
		if ('contents' == $field and ! $this->offsetExists('contents')) {
			if (isset($this->contents)) {
				$uploads = wp_upload_dir();
				$this['contents'] = file_get_contents($uploads['basedir']  . DIRECTORY_SEPARATOR . PMXI_Plugin::HISTORY_DIRECTORY . DIRECTORY_SEPARATOR . $this->id);
			} else {
				$this->contents = NULL;
			}
		}
		return parent::__get($field);
	}
	
	public function delete( $unlink = true ) {		
		$import_file_path = wp_all_import_get_absolute_path($this->path);
		if ( @file_exists($import_file_path) and $unlink ){ 
			wp_all_import_remove_source($import_file_path);				
		}
		return parent::delete();
	}
}