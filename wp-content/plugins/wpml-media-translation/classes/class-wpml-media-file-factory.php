<?php

/**
 * Class WPML_Media_File_Factory
 */
class WPML_Media_File_Factory {

	/**
	 * @param $attachment_id
	 *
	 * @return WPML_Media_File
	 */
	public function create( $attachment_id ) {
		global $wpdb;

		return new WPML_Media_File( $attachment_id, $this->get_wp_filesystem(), $wpdb );
	}

	private function get_wp_filesystem() {
		global $wp_filesystem;
		if ( null === $wp_filesystem ) {
			WP_Filesystem();
		}

		return $wp_filesystem;
	}


}