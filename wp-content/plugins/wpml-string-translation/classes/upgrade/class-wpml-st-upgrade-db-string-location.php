<?php

/**
 * Class WPML_ST_Upgrade_DB_String_Location
 */
class WPML_ST_Upgrade_DB_String_Location implements IWPML_St_Upgrade_Command {
	private $wpdb;

	/**
	 * WPML_ST_Upgrade_DB_String_Location constructor.
	 *
	 * @param wpdb $wpdb
	 */
	public function __construct( wpdb $wpdb ) {
		$this->wpdb = $wpdb;
	}

	public function run() {
		$result = false;
		$sql_get_st_table_name = "SHOW TABLES LIKE '{$this->wpdb->prefix}icl_strings'";
		$sql_get_location_column_from_st = "SHOW COLUMNS FROM {$this->wpdb->prefix}icl_strings LIKE 'location'";

		$st_table_exist = $this->wpdb->get_var( $sql_get_st_table_name ) === "{$this->wpdb->prefix}icl_strings";
		$location_column_exists = $st_table_exist ? $this->wpdb->get_var( $sql_get_location_column_from_st ) === 'location' : false;
		if ( $st_table_exist && ! $location_column_exists ) {
			$sql = "ALTER TABLE {$this->wpdb->prefix}icl_strings
				ADD COLUMN `location` BIGINT unsigned NULL";
			$result = $this->wpdb->query( $sql );
		}

		return false !== $result;
	}

	public function run_ajax() {
	}

	public function run_frontend() {
	}

	/**
	 * @return string
	 */
	public static function get_command_id() {
		return __CLASS__;
	}
}
