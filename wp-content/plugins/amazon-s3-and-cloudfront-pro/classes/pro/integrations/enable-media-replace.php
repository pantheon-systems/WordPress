<?php

namespace DeliciousBrains\WP_Offload_Media\Pro\Integrations;

class Enable_Media_Replace extends Integration {

	/**
	 * Is installed?
	 *
	 * @return bool
	 */
	public function is_installed() {
		if ( function_exists( 'enable_media_replace_init' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Init integration.
	 */
	public function init() {
		add_filter( 'as3cf_get_attached_file', array( $this, 'download_file' ), 10, 4 );
		add_filter( 'update_attached_file', array( $this, 'maybe_process_provider_replacement' ), 101, 2 );
		add_filter( 'as3cf_update_attached_file', array( $this, 'process_provider_replacement' ), 10, 2 );
		add_filter( 'as3cf_get_attachment_provider_info', array( $this, 'update_file_prefix_on_replace' ), 10, 2 );
		add_filter( 'as3cf_pre_update_attachment_metadata', array( $this, 'remove_existing_provider_files_before_replace' ), 10, 4 );
		add_filter( 'emr_unfiltered_get_attached_file', '__return_false' );
		add_filter( 'emr_unique_filename', array( $this, 'ensure_unique_filename' ), 10, 3 );
	}

	/**
	 * Allow the Enable Media Replace plugin to copy the S3 file back to the local
	 * server when the file is missing on the server via get_attached_file()
	 *
	 * @param string $url
	 * @param string $file
	 * @param int    $attachment_id
	 * @param array  $provider_object
	 *
	 * @return string
	 */
	function download_file( $url, $file, $attachment_id, $provider_object ) {
		return $this->as3cf->plugin_compat->copy_image_to_server_on_action( 'media_replace_upload', false, $url, $file, $provider_object );
	}

	/**
	 * Allow the Enable Media Replace plugin to remove old images from S3 when performing a replace
	 *
	 * @param bool  $pre
	 * @param array $data
	 * @param int   $post_id
	 * @param array $provider_object
	 *
	 * @return bool
	 */
	function remove_existing_provider_files_before_replace( $pre, $data, $post_id, $provider_object = array() ) {
		if ( ! $this->is_replacing_media() ) {
			return $pre;
		}

		if ( $provider_object ) {
			// Only remove old attachment files if they exist on S3
			$this->as3cf->remove_attachment_files_from_provider( $post_id, $provider_object );
		}

		// abort the rest of the update_attachment_metadata hook,
		// as we will process via update_attached_file
		return true;
	}

	/**
	 * Process the file replacement on a local only file if we are now
	 * offloading to S3.
	 *
	 * @param string $file
	 * @param int    $attachment_id
	 *
	 * @return string
	 */
	public function maybe_process_provider_replacement( $file, $attachment_id ) {
		if ( ! $this->is_replacing_media() ) {
			return $file;
		}

		if ( ! $this->as3cf->is_plugin_setup( true ) ) {
			return $file;
		}

		if ( ! ( $provider_object = $this->as3cf->get_attachment_provider_info( $attachment_id ) ) ) {
			// Process the replacement for a local file
			return $this->process_provider_replacement( $file, $attachment_id );
		}

		return $file;
	}

	/**
	 * Allow the Enable Media Replace to use update_attached_file() so it can
	 * replace the file on S3.
	 *
	 * @param string $file
	 * @param int    $attachment_id
	 *
	 * @return string
	 */
	function process_provider_replacement( $file, $attachment_id ) {
		if ( ! $this->is_replacing_media() ) {
			return $file;
		}

		if ( $this->as3cf->get_attachment_provider_info( $attachment_id ) ) {
			$this->as3cf->upload_attachment( $attachment_id, null, $file );
		}

		return $file;
	}

	/**
	 * Are we doing a media replacement?
	 *
	 * @return bool
	 */
	public function is_replacing_media() {
		$action = filter_input( INPUT_GET, 'action' );

		if ( empty( $action ) ) {
			return false;
		}

		return ( 'media_replace_upload' === sanitize_key( $action ) );
	}

	/**
	 * Update the file prefix in the S3 meta
	 *
	 * @param array|string $provider_object
	 * @param int          $attachment_id
	 *
	 * @return array|string
	 */
	public function update_file_prefix_on_replace( $provider_object, $attachment_id ) {
		if ( ! $this->is_replacing_media() ) {
			// Not replacing using EMR
			return $provider_object;
		}

		if ( '' === $provider_object ) {
			// First time upload to S3
			return $provider_object;
		}

		if ( ! $this->as3cf->get_setting( 'object-versioning' ) ) {
			// Not using object versioning
			return $provider_object;
		}

		$is_doing_upload = false;
		$callers         = debug_backtrace();

		foreach ( $callers as $caller ) {
			if ( isset( $caller['function'] ) && 'upload_attachment' === $caller['function'] ) {
				$is_doing_upload = true;
				break;
			}
		}

		if ( ! $is_doing_upload ) {
			return $provider_object;
		}

		// Get attachment folder time
		$time = $this->as3cf->get_attachment_folder_time( $attachment_id );
		$time = date( 'Y/m', $time );

		// Update the file prefix to generate new object versioning string
		$prefix   = $this->as3cf->get_file_prefix( $time );
		$filename = wp_basename( $provider_object['key'] );

		$provider_object['key'] = $prefix . $filename;

		return $provider_object;
	}

	/**
	 * Ensure the generated filename for an image replaced with a new image is unique.
	 *
	 * @param string $filename File name that should be unique.
	 * @param string $path     Absolute path to where the file will go.
	 * @param int    $id       Attachment ID.
	 *
	 * @return string
	 */
	public function ensure_unique_filename( $filename, $path, $id ) {
		return $this->as3cf->filter_unique_filename( $filename, $id );
	}
}
