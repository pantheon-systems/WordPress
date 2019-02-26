<?php

class WPML_ST_Upgrade_MO_Scanning implements IWPML_St_Upgrade_Command {
	/** @var WPDB $wpdb */
	private $wpdb;

	private static $sql = "
		CREATE TABLE `PREFIXicl_mo_files_domains` (
		  `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
		  `file_path` varchar(250) NOT NULL,
		  `file_path_md5` varchar(32) NOT NULL,
		  `domain` varchar(45) NOT NULL,
		  `status` varchar(20) NOT NULL DEFAULT %s,
		  `num_of_strings` int(11) NOT NULL DEFAULT '0',
		  `last_modified` int(11) NOT NULL,
		  `component_type` enum('plugin','theme','other') NOT NULL DEFAULT 'other',
  		  `component_id` varchar(100) DEFAULT NULL,
		  UNIQUE KEY `file_path_md5_UNIQUE` (`file_path_md5`)
		)
	";

	/**
	 * @param WPDB $wpdb
	 */
	public function __construct( WPDB $wpdb ) {
		$this->wpdb = $wpdb;
	}


	public function run() {
		return $this->create_table() && $this->add_mo_value_field_if_does_not_exist();
	}

	private function create_table() {
		$table_name = $this->wpdb->prefix . 'icl_mo_files_domains';
		$this->wpdb->query( "DROP TABLE IF EXISTS `{$table_name}`" );

		$sql = str_replace( 'PREFIX', $this->wpdb->prefix, self::$sql );
		$sql = $this->wpdb->prepare(
			$sql,
			array( WPML_ST_MO_File::NOT_IMPORTED )
		);

		$sql .= $this->get_charset_collate();

		return false !== $this->wpdb->query( $sql );
	}

	private function add_mo_value_field_if_does_not_exist() {
		$result = true;

		$table_name = $this->wpdb->prefix . 'icl_string_translations';
		if ( 0 === count( $this->wpdb->get_results( "SHOW COLUMNS FROM `{$table_name}` LIKE 'mo_string'" ) ) ) {
			$sql = "
				ALTER TABLE {$table_name} 
				ADD COLUMN `mo_string` TEXT NULL DEFAULT NULL AFTER `value`;
			";

			$result = false !== $this->wpdb->query( $sql );
		}

		return $result;
	}

	public function run_ajax() {
		return $this->run();
	}

	public function run_frontend() {
		return $this->run();
	}

	public static function get_command_id() {
		return __CLASS__ . '_3' ;
	}

	/**
	 * @return string
	 */
	private function get_charset_collate() {
		$charset_collate = '';
		if ( method_exists( $this->wpdb, 'has_cap' ) && $this->wpdb->has_cap( 'collation' ) ) {
			$charset_collate = $this->wpdb->get_charset_collate();
		}

		return $charset_collate;
	}
}
