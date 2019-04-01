<?php

class WPML_ST_Word_Count_Package_Records {

	/** @var wpdb */
	private $wpdb;

	public function __construct( wpdb $wpdb ) {
		$this->wpdb = $wpdb;
	}

	/** @return array */
	public function get_all_package_ids() {
		return array_map(
			'intval',
			$this->wpdb->get_col( "SELECT ID FROM {$this->wpdb->prefix}icl_string_packages"	)
		);
	}

	/** @return array */
	public function get_packages_ids_without_word_count() {
		return array_map(
			'intval',
			$this->wpdb->get_col(
				"SELECT ID FROM {$this->wpdb->prefix}icl_string_packages WHERE word_count IS NULL"
			)
		);
	}

	/** @return array */
	public function get_word_counts( $post_id ) {
		return $this->wpdb->get_col(
			$this->wpdb->prepare(
				"SELECT word_count FROM {$this->wpdb->prefix}icl_string_packages WHERE post_id = %d",
				$post_id
			)
		);
	}

	/**
	 * @param int    $package_id
	 * @param string $word_count
	 */
	public function set_word_count( $package_id, $word_count ) {
		$this->wpdb->update(
			$this->wpdb->prefix . 'icl_string_packages',
			array( 'word_count' => $word_count ),
			array( 'ID' => $package_id )
		);
	}

	/**
	 * @param int $package_id
	 *
	 * @return null|string
	 */
	public function get_word_count( $package_id ) {
		return $this->wpdb->get_var(
			$this->wpdb->prepare(
				"SELECT word_count FROM {$this->wpdb->prefix}icl_string_packages WHERE ID = %d",
				$package_id
			)
		);
	}

	public function reset_all( array $package_kinds ) {
		if ( ! $package_kinds ) {
			return;
		}

		$query = "UPDATE {$this->wpdb->prefix}icl_string_packages SET word_count = NULL
				  WHERE kind_slug IN(" . wpml_prepare_in( $package_kinds ) . ")";

		$this->wpdb->query( $query );
	}

	/**
	 * @param array $kinds
	 *
	 * @return array
	 */
	public function get_ids_from_kind_slugs( array $kinds ) {
		if ( ! $kinds ) {
			return array();
		}

		$query = "SELECT ID FROM {$this->wpdb->prefix}icl_string_packages
				  WHERE kind_slug IN(" . wpml_prepare_in( $kinds ) . ")";

		return array_map( 'intval', $this->wpdb->get_col( $query ) );
	}

	/**
	 * @param array $post_types
	 *
	 * @return array
	 */
	public function get_ids_from_post_types( array $post_types ) {
		if ( ! $post_types ) {
			return array();
		}

		$query = "SELECT sp.ID FROM {$this->wpdb->prefix}icl_string_packages AS sp
				  LEFT JOIN {$this->wpdb->posts} AS p
				  	ON p.ID = sp.post_id
				  WHERE p.post_type IN(" . wpml_prepare_in( $post_types ) . ")";

		return array_map( 'intval', $this->wpdb->get_col( $query ) );
	}

	/**
	 * @param string $kind_slug
	 *
	 * @return int
	 */
	public function count_items_by_kind_not_part_of_posts( $kind_slug ) {
		$query = "SELECT COUNT(*) FROM {$this->wpdb->prefix}icl_string_packages
				  WHERE kind_slug = %s AND post_id IS NULL";

		return (int) $this->wpdb->get_var( $this->wpdb->prepare( $query, $kind_slug ) );
	}

	/**
	 * @param string $kind_slug
	 *
	 * @return int
	 */
	public function count_word_counts_by_kind( $kind_slug ) {
		$query = "SELECT COUNT(*) FROM {$this->wpdb->prefix}icl_string_packages
				  WHERE kind_slug = %s AND word_count IS NOT NULL
				  	AND post_id IS NULL";

		return (int) $this->wpdb->get_var( $this->wpdb->prepare( $query, $kind_slug ) );
	}

	/**
	 * @param string $kind_slug
	 *
	 * @return array
	 */
	public function get_word_counts_by_kind( $kind_slug ) {
		$query = "SELECT word_count FROM {$this->wpdb->prefix}icl_string_packages
				  WHERE kind_slug = %s";

		return $this->wpdb->get_col( $this->wpdb->prepare( $query, $kind_slug ) );
	}
}
