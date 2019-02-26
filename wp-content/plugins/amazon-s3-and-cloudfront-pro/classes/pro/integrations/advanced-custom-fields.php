<?php

namespace DeliciousBrains\WP_Offload_Media\Pro\Integrations;

use WP_Error;

class Advanced_Custom_Fields extends Integration {

	/**
	 * Is installed?
	 *
	 * @return bool
	 */
	public function is_installed() {
		if ( class_exists( 'acf' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Init integration.
	 */
	public function init() {
		/*
		 * Content Filtering
		 */
		add_filter( 'acf/load_value/type=text', array( $this->as3cf->filter_local, 'filter_post' ) );
		add_filter( 'acf/load_value/type=textarea', array( $this->as3cf->filter_local, 'filter_post' ) );
		add_filter( 'acf/load_value/type=wysiwyg', array( $this->as3cf->filter_local, 'filter_post' ) );
		add_filter( 'acf/update_value/type=text', array( $this->as3cf->filter_provider, 'filter_post' ) );
		add_filter( 'acf/update_value/type=textarea', array( $this->as3cf->filter_provider, 'filter_post' ) );
		add_filter( 'acf/update_value/type=wysiwyg', array( $this->as3cf->filter_provider, 'filter_post' ) );

		/*
		 * Image Crop Add-on
		 * https://en-gb.wordpress.org/plugins/acf-image-crop-add-on/
		 */
		if ( class_exists( 'acf_field_image_crop' ) ) {
			add_filter( 'wp_get_attachment_metadata', array( $this, 'download_image' ), 10, 2 );
			add_filter( 'sanitize_file_name', array( $this, 'remove_original_after_download' ) );
		}
	}

	/**
	 * Copy back the S3 image for cropping
	 *
	 * @param array $data
	 * @param int   $post_id
	 *
	 * @return array
	 */
	public function download_image( $data, $post_id ) {
		$this->maybe_download_image( $post_id );

		return $data;
	}

	/**
	 * Copy back the S3 image
	 *
	 * @param int $post_id
	 *
	 * @return bool|WP_Error
	 */
	public function maybe_download_image( $post_id ) {
		if ( false === $this->as3cf->plugin_compat->maybe_process_on_action( 'acf_image_crop_perform_crop', true ) ) {
			return $this->as3cf->_throw_error( 1, 'Not ACF crop process' );
		}

		$file = get_attached_file( $post_id, true );

		if ( file_exists( $file ) ) {
			return $this->as3cf->_throw_error( 2, 'File already exists' );
		}

		if ( ! ( $provider_object = $this->as3cf->get_attachment_provider_info( $post_id ) ) ) {
			return $this->as3cf->_throw_error( 3, 'Attachment not offloaded' );
		}

		$callers = debug_backtrace();
		foreach ( $callers as $caller ) {
			if ( isset( $caller['function'] ) && 'image_downsize' === $caller['function'] ) {
				// Don't copy when downsizing the image, which would result in bringing back
				// the newly cropped image from S3.
				return $this->as3cf->_throw_error( 4, 'Copying back cropped file' );
			}
		}

		// Copy back the original file for cropping
		$result = $this->as3cf->plugin_compat->copy_provider_file_to_server( $provider_object, $file );

		if ( false === $result ) {
			return $this->as3cf->_throw_error( 5, 'Copy back failed' );
		}

		// Mark the attachment so we know to remove it later after the crop
		$provider_object['acf_cropped_to_remove'] = true;
		update_post_meta( $post_id, 'amazonS3_info', $provider_object );

		return true;
	}

	/**
	 * Remove the original image downloaded for the cropping after it has been processed
	 *
	 * @param $filename
	 *
	 * @return mixed
	 */
	public function remove_original_after_download( $filename ) {
		$this->maybe_remove_original_after_download();

		return $filename;
	}

	/**
	 * Remove the original image from the server
	 *
	 * @return string
	 */
	public function maybe_remove_original_after_download() {
		if ( false === $this->as3cf->plugin_compat->maybe_process_on_action( 'acf_image_crop_perform_crop', true ) ) {
			return $this->as3cf->_throw_error( 1, 'Not ACF crop process' );
		}

		$original_attachment_id = $this->as3cf->filter_input( 'id', INPUT_POST, FILTER_VALIDATE_INT );

		if ( ! isset( $original_attachment_id ) ) {
			// Can't find the original attachment id
			return $this->as3cf->_throw_error( 6, 'Attachment ID not available' );
		}

		if ( ! ( $provider_object = $this->as3cf->get_attachment_provider_info( $original_attachment_id ) ) ) {
			// Original attachment not on S3
			return $this->as3cf->_throw_error( 3, 'Attachment not offloaded' );
		}

		if ( ! isset( $provider_object['acf_cropped_to_remove'] ) ) {
			// Original attachment should exist locally, no need to delete
			return $this->as3cf->_throw_error( 7, 'Attachment not to be removed from server' );
		}

		// Remove the original file from the server
		$original_file = get_attached_file( $original_attachment_id, true );
		$this->as3cf->remove_local_files( array( $original_file ) );

		// Remove marker
		unset( $provider_object['acf_cropped_to_remove'] );
		update_post_meta( $original_attachment_id, 'amazonS3_info', $provider_object );

		return true;
	}

}