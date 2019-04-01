<?php

class WPML_ST_String_Dependencies_Records {

	/** @var wpdb $wpdb */
	private $wpdb;

	public function __construct( wpdb $wpdb ) {
		$this->wpdb = $wpdb;
	}

	/**
	 * @param string $type
	 * @param int    $id
	 *
	 * @return int
	 */
	public function get_parent_id_from( $type, $id ) {
		switch ( $type ) {
			case 'package':
				$query = "SELECT post_id FROM {$this->wpdb->prefix}icl_string_packages WHERE ID = %d";
				break;

			case 'string':
				$query = "SELECT string_package_id FROM {$this->wpdb->prefix}icl_strings WHERE id = %d";
				break;

			default:
				return 0;
		}

		return (int) $this->wpdb->get_var( $this->wpdb->prepare( $query, $id ) );
	}

	/**
	 * @param string $type
	 * @param int    $id
	 *
	 * @return array
	 */
	public function get_child_ids_from( $type, $id ) {
		switch ( $type ) {
			case 'post':
				$query = "SELECT id FROM {$this->wpdb->prefix}icl_string_packages WHERE post_id = %d";
				break;

			case 'package':
				$query = "SELECT ID FROM {$this->wpdb->prefix}icl_strings WHERE string_package_id = %d";
				break;

			default:
				return array();
		}

		$ids = $this->wpdb->get_col( $this->wpdb->prepare( $query, $id ) );

		return array_map( 'intval', $ids );
	}
}
