<?php

class WPML_Flags_Factory {
	/** @var  wpdb */
	private $wpdb;

	/**
	 * @param wpdb $wpdb
	 */
	public function __construct( wpdb $wpdb ) {
		$this->wpdb = $wpdb;
	}

	/**
	 * @return WPML_Flags
	 */
	public function create() {
		if ( ! class_exists( 'WP_Filesystem_Direct' ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
			require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
		}

		return new WPML_Flags( $this->wpdb, new icl_cache( 'flags', true ), new WP_Filesystem_Direct( null ) );
	}
}
