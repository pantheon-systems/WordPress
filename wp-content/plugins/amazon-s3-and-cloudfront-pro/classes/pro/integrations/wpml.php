<?php

namespace DeliciousBrains\WP_Offload_Media\Pro\Integrations;

class Wpml extends Integration {

	/**
	 * Is installed?
	 *
	 * @return bool
	 */
	public function is_installed() {
		if ( class_exists( 'SitePress' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Init integration.
	 */
	public function init() {
		add_action( 'wpml_media_create_duplicate_attachment', array( $this, 'store_duplicate_attachment_ids' ), 10, 2 );
		add_action( 'as3cf_post_upload_attachment', array( $this, 'duplicate_provider_meta' ), 10, 2 );
		add_filter( 'as3cf_pre_upload_attachment', array( $this, 'duplicate_provider_meta_on_upload' ), 10, 2 );
	}

	/**
	 * Store duplicated attachment IDs for other languages when an attachment is created
	 * or WPML is updating old attachments
	 *
	 * @param int $attachment_id
	 * @param int $duplicated_attachment_id
	 */
	public function store_duplicate_attachment_ids( $attachment_id, $duplicated_attachment_id ) {
		if ( ( $old_provider_object = $this->as3cf->get_attachment_provider_info( $attachment_id ) ) ) {
			// Attachment already uploaded to S3, duplicate
			update_post_meta( $duplicated_attachment_id, 'amazonS3_info', $old_provider_object );
			$this->duplicate_filesize_total( $attachment_id, $duplicated_attachment_id );

			return;
		}

		if ( ! $this->as3cf->get_setting( 'copy-to-s3' ) ) {
			// abort if we aren't uploading to S3
			return;
		}

		$language_duplicates = get_post_meta( $attachment_id, 'as3cf_wpml_duplicates', true );
		if ( ! is_array( $language_duplicates ) ) {
			$language_duplicates = array();
		}

		$language_duplicates[] = $duplicated_attachment_id;

		// Store the duplicate attachment IDs because at this point the created attachment
		// does not have our S3 metadata yet
		update_post_meta( $attachment_id, 'as3cf_wpml_duplicates', $language_duplicates );
	}

	/**
	 * Duplicate our S3 metadata when WPML Media duplicates an attachment for other languages
	 *
	 * @param int   $post_id
	 * @param array $provider_object
	 */
	public function duplicate_provider_meta( $post_id, $provider_object ) {
		if ( ! ( $language_duplicates = get_post_meta( $post_id, 'as3cf_wpml_duplicates', true ) ) ) {
			// No languages to duplicate for
			return;
		}

		foreach ( $language_duplicates as $duplicated_id ) {
			update_post_meta( $duplicated_id, 'amazonS3_info', $provider_object );
			$this->duplicate_filesize_total( $post_id, $duplicated_id );
		}

		// Cleanup our cache of duplicated IDs
		delete_post_meta( $post_id, 'as3cf_wpml_duplicates' );
	}

	/**
	 * Duplicate 'as3cf_filesize_total' meta if exists for an attachment
	 *
	 * @param int $attachment_id
	 * @param int $new_attachment_id
	 */
	private function duplicate_filesize_total( $attachment_id, $new_attachment_id ) {
		if ( ! ( $filesize = get_post_meta( $attachment_id, 'as3cf_filesize_total', true ) ) ) {
			// No filezie to duplicate
			return;
		}

		update_post_meta( $new_attachment_id, 'as3cf_filesize_total', $filesize );
	}

	/**
	 * Don't upload attachments to S3 when they are WPML language duplicates
	 * where the S3 metadata hasn't been copied during the attachment duplication.
	 * Instead find the original attachment and copy the S3 metadata from it.
	 *
	 * @param bool $pre
	 * @param int  $attachment_id
	 *
	 * @return bool
	 */
	public function duplicate_provider_meta_on_upload( $pre, $attachment_id ) {
		if ( ! get_post_meta( $attachment_id, 'wpml_media_processed', true ) ) {
			// Attachment hasn't been duplicated by WPML, carry on.
			return $pre;
		}

		if ( $this->as3cf->get_attachment_provider_info( $attachment_id ) ) {
			// Attachment already uploaded to S3, carry on.
			return $pre;
		}

		// Find original attachment from language duplicate
		$file = get_post_meta( $attachment_id, '_wp_attached_file', true );

		global $wpdb;

		$sql = "SELECT p.ID, pm2.meta_value
				FROM {$wpdb->posts} p
				INNER JOIN {$wpdb->postmeta} pm1
				ON p.ID = pm1.post_id
				AND pm1.meta_key = '_wp_attached_file'
				INNER JOIN {$wpdb->postmeta} pm2
				ON p.ID = pm2.post_id
				AND pm2.meta_key = 'amazonS3_info'
				WHERE pm1.meta_value = %s
				AND p.ID != %d
				LIMIT 1";

		$sql = $wpdb->prepare( $sql, $file, $attachment_id );

		$original_attachment = $wpdb->get_row( $sql );

		if ( is_null( $original_attachment ) ) {
			// Can't find original, attempt normal upload
			return $pre;
		}

		// Duplicate the S3 meta and filesize for the new attachment
		update_post_meta( $attachment_id, 'amazonS3_info', maybe_unserialize( $original_attachment->meta_value ) );
		$this->duplicate_filesize_total( $original_attachment->ID, $attachment_id );

		// Abort upload
		return true;
	}
}