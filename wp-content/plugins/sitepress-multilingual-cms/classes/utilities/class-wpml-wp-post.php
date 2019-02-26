<?php

class WPML_WP_Post {
	/** @var WPDB $wpdb */
	public $wpdb;

	/** @var int */
	private $post_id;

	/**
	 * @param WPDB $wpdb
	 * @param int $post_id
	 */
	public function __construct( WPDB $wpdb, $post_id ) {
		$this->wpdb = $wpdb;
		$this->post_id = $post_id;
	}

	public function update( Array $post_data_array, $direct_db_update = false) {
		if ( $direct_db_update ) {
			$this->wpdb->update( $this->wpdb->posts, $post_data_array, array( 'ID' => $this->post_id ) );
			clean_post_cache( $this->post_id );
		} else {
			$post_data_array['ID'] = $this->post_id;
			wp_update_post( $post_data_array );
		}
	}
}