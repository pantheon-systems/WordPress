<?php

class WPML_ST_MO_Scan_Db_Table_List {
	/** @var wpdb */
	private $wpdb;

	/**
	 * @param wpdb $wpdb
	 */
	public function __construct( wpdb $wpdb ) {
		$this->wpdb = $wpdb;
	}

	/**
	 * @return array
	 */
	public function get_tables() {
		return array(
			$this->wpdb->prefix . 'icl_strings',
			$this->wpdb->prefix . 'icl_string_translations',
		);
	}
}