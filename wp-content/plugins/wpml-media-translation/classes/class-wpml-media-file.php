<?php

class WPML_Media_File {

	/**
	 * @var int
	 */
	private $attachment_id;
	/**
	 * @var WP_Filesystem_Base
	 */
	private $wp_filesystem;
	/**
	 * @var wpdb
	 */
	private $wpdb;

	public function __construct( $attachment_id, WP_Filesystem_Base $wp_filesystem, wpdb $wpdb ) {
		$this->wp_filesystem = $wp_filesystem;
		$this->attachment_id = $attachment_id;
		$this->wpdb          = $wpdb;
	}

	public function delete() {
		$relative_file_path = get_post_meta( $this->attachment_id, '_wp_attached_file', true );

		if ( $relative_file_path && ! $this->file_is_shared( $relative_file_path, $this->attachment_id ) ) {

			$file_path = $this->get_full_file_upload_path( $relative_file_path );

			$this->wp_filesystem->delete( $file_path, false, 'f' );

			$attachment_meta_data = wp_get_attachment_metadata( $this->attachment_id );
			if ( $attachment_meta_data ) {
				$subdir = dirname( $attachment_meta_data['file'] );
				foreach ( $attachment_meta_data['sizes'] as $key => $size ) {
					$file_path = $this->get_full_file_upload_path( $subdir . '/' . $size['file'] );
					$this->wp_filesystem->delete( $file_path, false, 'f' );
				}
			}
		}

	}

	private function get_full_file_upload_path( $relative_file_path ) {
		$upload_dir         = wp_upload_dir();
		$relative_file_path = trim( $relative_file_path, ' /' );
		$file_path          = $upload_dir['basedir'] . '/' . $relative_file_path;

		return $file_path;
	}

	private function file_is_shared( $relative_file_path, $attachment_id ) {

		$sql = "SELECT post_id FROM {$this->wpdb->postmeta} 
				WHERE post_id <> %d AND meta_key='_wp_attached_file' AND meta_value=%s";

		return $this->wpdb->get_var( $this->wpdb->prepare( $sql, $attachment_id, $relative_file_path ) );
	}

}