<?php

namespace DeliciousBrains\WP_Offload_Media\Pro\Background_Processes;

class Remove_Local_Files_Process extends Background_Tool_Process {

	/**
	 * @var string
	 */
	protected $action = 'remove_local_files';

	/**
	 * @var int
	 */
	protected $blog_id;

	/**
	 * @var array
	 */
	protected $paths = array();

	/**
	 * Process attachments chunk.
	 *
	 * @param array $attachments
	 * @param int   $blog_id
	 */
	protected function process_attachments_chunk( $attachments, $blog_id ) {
		$this->blog_id = $blog_id;

		foreach ( $attachments as $key => $attachment_id ) {
			if ( ! $this->as3cf->is_attachment_served_by_provider( $attachment_id, true ) || ! $this->attachment_exist_locally( $attachment_id ) ) {
				unset( $attachments[ $key ] );
			}
		}

		$keys = $this->get_provider_keys( $attachments );

		foreach ( $attachments as $attachment_id ) {
			$this->delete_local_attachment( $attachment_id, $keys );
		}
	}

	/**
	 * Does attachment exist locally?
	 *
	 * @param int $attachment_id
	 *
	 * @return bool
	 */
	protected function attachment_exist_locally( $attachment_id ) {
		$paths = $this->get_attachment_local_paths( $attachment_id );

		foreach ( $paths as $path ) {
			if ( file_exists( $path ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Delete local attachment files.
	 *
	 * @param int   $attachment_id
	 * @param array $keys
	 */
	protected function delete_local_attachment( $attachment_id, $keys ) {
		$paths = $this->get_attachment_local_paths( $attachment_id );
		$keys  = isset( $keys[ $attachment_id ] ) ? $keys[ $attachment_id ] : array();

		foreach ( $paths as $path ) {
			if ( file_exists( $path ) && $this->file_exists_on_provider( $path, $keys ) ) {
				$files_to_remove[] = $path;
			}
		}

		// Delete the files and record original file's size before removal.
		if ( ! empty( $files_to_remove ) ) {
			// Get original file's size if still on disk.
			$filesize = file_exists( $paths[0] ) ? filesize( $paths[0] ) : 0;

			$this->as3cf->remove_local_files( $files_to_remove, $attachment_id );

			// Store filesize in the attachment meta data for use by WP
			if ( 0 < $filesize && ( $data = wp_get_attachment_metadata( $attachment_id ) ) ) {
				if ( empty( $data['filesize'] ) ) {
					$data['filesize'] = $filesize;

					// Update metadata with filesize
					update_post_meta( $attachment_id, '_wp_attachment_metadata', $data );
				}
			}
		}
	}

	/**
	 * Does file local file exist on S3?
	 *
	 * @param string $path
	 * @param array  $keys
	 *
	 * @return bool
	 */
	protected function file_exists_on_provider( $path, $keys ) {
		foreach ( $keys as $key ) {
			if ( pathinfo( $path, PATHINFO_BASENAME ) === pathinfo( $key, PATHINFO_BASENAME ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get attachment local paths.
	 *
	 * @param int $attachment_id
	 *
	 * @return array
	 */
	protected function get_attachment_local_paths( $attachment_id ) {
		if ( isset( $this->paths[ $this->blog_id ][ $attachment_id ] ) ) {
			return $this->paths[ $this->blog_id ][ $attachment_id ];
		}

		$file    = get_attached_file( $attachment_id, true );
		$parts   = pathinfo( $file );
		$meta    = wp_get_attachment_metadata( $attachment_id );
		$backups = get_post_meta( $attachment_id, '_wp_attachment_backup_sizes', true );
		$paths   = array( $file );

		if ( ! empty( $meta['sizes'] ) ) {
			foreach ( $meta['sizes'] as $size ) {
				$paths[] = path_join( $parts['dirname'], $size['file'] );
			}
		}

		if ( ! empty( $backups ) ) {
			foreach ( $backups as $size ) {
				$paths[] = path_join( $parts['dirname'], $size['file'] );
			}
		}

		$this->paths[ $this->blog_id ][ $attachment_id ] = $paths;

		return $paths;
	}

	/**
	 * Get complete notice message.
	 *
	 * @return string
	 */
	protected function get_complete_message() {
		return __( '<strong>WP Offload Media</strong> &mdash; Finished removing media files from local server.', 'amazon-s3-and-cloudfront' );
	}
}
