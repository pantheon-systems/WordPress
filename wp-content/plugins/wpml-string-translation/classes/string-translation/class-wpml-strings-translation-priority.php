<?php

class WPML_Strings_Translation_Priority {
	/**
	 * @var wpdb
	 */
	private $wpdb;

	/**
	 * @param wpdb $wpdb
	 */
	public function __construct( wpdb $wpdb ) {
		$this->wpdb = $wpdb;
	}

	/**
	 * @param int[] $strings
	 * @param string $priority
	 *
	 * @return array
	 */
	public function change_translation_priority_of_strings( $strings, $priority ) {

		$update_query   = "UPDATE {$this->wpdb->prefix}icl_strings SET translation_priority=%s WHERE id IN (" . wpml_prepare_in( $strings, '%d' ) . ")";
		$update_prepare = $this->wpdb->prepare( $update_query, $priority );
		$this->wpdb->query( $update_prepare );

	}
}

