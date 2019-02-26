<?php

class WPML_ST_Upgrade_DB_String_Name_Index implements IWPML_St_Upgrade_Command {
	/** @var wpdb */
	private $wpdb;

	/**
	 * @param wpdb $wpdb
	 */
	public function __construct( wpdb $wpdb ) {
		$this->wpdb = $wpdb;
	}

	public function run() {
		$result = true;

		$table_name = $this->wpdb->prefix . 'icl_strings';
		if ( 0 !== count( $this->wpdb->get_results( "SHOW TABLES LIKE '{$table_name}'" ) ) ) {
			$sql = "SHOW KEYS FROM  {$table_name} WHERE Key_name='icl_strings_name'";
			if ( 0 === count( $this->wpdb->get_results( $sql ) ) ) {
				$sql = "
				ALTER TABLE {$this->wpdb->prefix}icl_strings 
				ADD INDEX `icl_strings_name` (`name` ASC);
				";

				$result = false !== $this->wpdb->query( $sql );
			}
		}

		return $result;
	}

	public function run_ajax() {
		$this->run();
	}

	public function run_frontend() {
		$this->run();
	}

	public static function get_command_id() {
		return __CLASS__ . '_2';
	}
}
