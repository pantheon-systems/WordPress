<?php

class WPML_ST_Upgrade_DB_Strings_Add_Translation_Priority_Field implements IWPML_St_Upgrade_Command {
	/** @var wpdb */
	private $wpdb;

	/**
	 * @param wpdb $wpdb
	 */
	public function __construct( wpdb $wpdb ) {
		$this->wpdb = $wpdb;
	}

	public function run() {
		$result = null;

		$table_name = $this->wpdb->prefix . 'icl_strings';
		if ( 0 !== count( $this->wpdb->get_results( "SHOW TABLES LIKE '{$table_name}'" ) ) ) {
			$sql = "SHOW FIELDS FROM  {$table_name} WHERE FIELD = 'translation_priority'";
			if ( 0 === count( $this->wpdb->get_results( $sql ) ) ) {
				$sql = "ALTER TABLE {$this->wpdb->prefix}icl_strings 
				ADD COLUMN `translation_priority` varchar(160) NOT NULL";

				$result = false !== $this->wpdb->query( $sql );
			}

			if ( false !== $result ) {
				$sql = "SHOW KEYS FROM  {$table_name} WHERE Key_name='icl_strings_translation_priority'";
				if ( 0 === count( $this->wpdb->get_results( $sql ) ) ) {
					$sql = "
					ALTER TABLE {$this->wpdb->prefix}icl_strings 
					ADD INDEX `icl_strings_translation_priority` ( `translation_priority` ASC )
					";

					$result = false !== $this->wpdb->query( $sql );
				}
			}
		}

		return (bool) $result;
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
