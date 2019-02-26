<?php

class WPML_ST_Upgrade_String_Index {
	/** @var wpdb */
	private $wpdb;

	const OPTION_NAME = 'wpml_string_table_ok_for_mo_import';

	/**
	 * @param wpdb $wpdb
	 */
	public function __construct( wpdb $wpdb ) {
		$this->wpdb = $wpdb;
	}

	public function is_uc_domain_name_context_index_unique() {
		$key_exists = get_option( self::OPTION_NAME );
		if ( ! $key_exists ) {
			$sql = "SHOW KEYS FROM  {$this->wpdb->prefix}icl_strings WHERE Key_name='uc_domain_name_context_md5' AND Non_unique = 0";

			$key_exists = 0 < count( $this->wpdb->get_results( $sql ) ) ? 'yes' : 'no';
			update_option( self::OPTION_NAME, $key_exists, true );
		}

		return 'yes' === $key_exists;
	}
}
