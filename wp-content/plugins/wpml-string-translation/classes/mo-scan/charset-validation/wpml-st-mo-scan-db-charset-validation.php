<?php

class WPML_ST_MO_Scan_Db_Charset_Validation implements WPML_ST_MO_Scan_Charset_Validation {
	/** @var wpdb */
	private $wpdb;

	/** @var WPML_ST_MO_Scan_Db_Table_List */
	private $table_list;

	/**
	 * @param wpdb $wpdb
	 * @param WPML_ST_MO_Scan_Db_Table_List $table_list
	 */
	public function __construct( wpdb $wpdb, WPML_ST_MO_Scan_Db_Table_List $table_list ) {
		$this->wpdb       = $wpdb;
		$this->table_list = $table_list;
	}


	/**
	 * @return bool
	 */
	public function is_valid() {
		if ( ! $this->wpdb->has_cap( 'utf8mb4' ) ) {
			return false;
		}

		$chunks     = array();
		foreach ( $this->table_list->get_tables() as $table ) {
			$chunks[] = $this->get_unique_collation_list_from_table( $table );
		}

		$collations = call_user_func_array( 'array_merge', $chunks );
		$collations = array_unique( $collations );

		return count( $collations ) < 2;
	}



	/**
	 * @param string $table
	 *
	 * @return array
	 */
	private function get_unique_collation_list_from_table( $table ) {
		$columns = $this->wpdb->get_results( "SHOW FULL COLUMNS FROM `{$table}` WHERE Collation LIKE 'utf8mb4%'" );

		return array_unique( wp_list_pluck( $columns, 'Collation' ) );
	}
}
