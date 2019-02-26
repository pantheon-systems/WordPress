<?php

class WPML_ST_Upgrade_DB_Longtext_String_Value implements IWPML_St_Upgrade_Command {
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
		if ( count( $this->wpdb->get_results( "SHOW TABLES LIKE '{$table_name}'" ) ) ) {
			$sql = "
				ALTER TABLE {$table_name}
				MODIFY COLUMN `value` LONGTEXT NOT NULL;
			";

			$result = false !== $this->wpdb->query( $sql );
		}

		$table_name = $this->wpdb->prefix . 'icl_string_translations';
		if ( count( $this->wpdb->get_results( "SHOW TABLES LIKE '{$table_name}'" ) ) ) {
			$sql = "
				ALTER TABLE {$table_name}
				MODIFY COLUMN `value` LONGTEXT NULL DEFAULT NULL,
				MODIFY COLUMN `mo_string` LONGTEXT NULL DEFAULT NULL;
			";

			$result = ( false !== $this->wpdb->query( $sql ) ) && $result;
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
		return __CLASS__;
	}
}
