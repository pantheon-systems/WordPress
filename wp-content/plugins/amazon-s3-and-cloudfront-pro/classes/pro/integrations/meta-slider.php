<?php

namespace DeliciousBrains\WP_Offload_Media\Pro\Integrations;

use Amazon_S3_And_CloudFront;
use AS3CF_Error;
use AS3CF_Utils;
use Exception;

class Meta_Slider extends Integration {

	/**
	 * @var int
	 */
	private $post_id;

	/**
	 * Is installed?
	 *
	 * @return bool
	 */
	public function is_installed() {
		if ( class_exists( 'MetaSliderPlugin' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Init integration.
	 */
	public function init() {
		add_filter( 'metaslider_attachment_url', array( $this, 'metaslider_attachment_url' ), 10, 2 );
		add_filter( 'sanitize_post_meta_amazonS3_info', array( $this, 'layer_slide_sanitize_post_meta' ), 10, 3 );
		add_filter( 'as3cf_pre_update_attachment_metadata', array( $this, 'layer_slide_abort_upload' ), 10, 4 );
		add_filter( 'as3cf_remove_attachment_paths', array( $this, 'layer_slide_remove_attachment_paths' ), 10, 4 );
		add_action( 'shutdown', array( $this, 'layer_slide_remove_local_files' ) );
		add_action( 'add_post_meta', array( $this, 'add_post_meta' ), 10, 3 );
		add_action( 'update_post_meta', array( $this, 'update_post_meta' ), 10, 4 );
	}

	/**
	 * Use the S3 URL for a Meta Slider slide image
	 *
	 * @param string $url
	 * @param int    $slide_id
	 *
	 * @return string
	 */
	public function metaslider_attachment_url( $url, $slide_id ) {
		$provider_url = $this->as3cf->get_attachment_url( $slide_id );

		if ( ! is_wp_error( $provider_url ) && false !== $provider_url ) {
			return $provider_url;
		}

		return $url;
	}

	/**
	 * Layer slide sanitize post meta.
	 *
	 * This fixes issues with 'Layer Slides', which uses `get_post_custom` to retrieve
	 * attachment meta, but does not unserialize the data. This results in the `amazonS3_info`
	 * key being double serialized when inserted into the database.
	 *
	 * @param mixed  $meta_value
	 * @param string $meta_key
	 * @param string $object_type
	 *
	 * @return mixed
	 */
	public function layer_slide_sanitize_post_meta( $meta_value, $meta_key, $object_type ) {
		if ( ! $this->is_layer_slide() ) {
			return $meta_value;
		}

		return maybe_unserialize( $meta_value );
	}

	/**
	 * Layer slide abort upload.
	 *
	 * 'Layer Slide' duplicates an attachment in the Media Library, but uses the same
	 * file as the original. This prevents us trying to upload a new version to S3.
	 *
	 * @param bool  $pre
	 * @param mixed $data
	 * @param int   $post_id
	 * @param mixed $old_provider_object
	 *
	 * @return bool
	 */
	public function layer_slide_abort_upload( $pre, $data, $post_id, $old_provider_object ) {
		if ( ! $this->is_layer_slide() ) {
			return $pre;
		}

		if ( $this->as3cf->get_setting( 'remove-local-file' ) ) {
			// Download full size image locally so that custom sizes can be generated
			$file = get_attached_file( $post_id, true );
			$this->as3cf->plugin_compat->copy_provider_file_to_server( $old_provider_object, $file );

			$this->post_id = $post_id;
		}

		return true;
	}

	/**
	 * Layer slide remove attachment paths.
	 *
	 * Because 'Layer Slide' duplicates an attachment in the Media Library, but uses the same
	 * file as the original we don't want to remove them from S3. Only the backup sizes
	 * should be removed.
	 *
	 * @param array $paths
	 * @param int   $post_id
	 * @param array $provider_object
	 * @param bool  $remove_backup_sizes
	 *
	 * @return array
	 */
	public function layer_slide_remove_attachment_paths( $paths, $post_id, $provider_object, $remove_backup_sizes ) {
		$slider = get_post_meta( $post_id, 'ml-slider_type', true );

		if ( 'html_overlay' !== $slider ) {
			// Not a layer slide, return
			return $paths;
		}

		$meta = get_post_meta( $post_id, '_wp_attachment_metadata', true );

		unset( $paths['full'] );

		if ( isset( $meta['sizes'] ) ) {
			foreach ( $meta['sizes'] as $size => $details ) {
				unset( $paths[ $size ] );
			}
		}

		return $paths;
	}

	/**
	 * Layer slide remove local files.
	 */
	public function layer_slide_remove_local_files() {
		if ( is_null( $this->post_id ) ) {
			return;
		}

		$file    = get_attached_file( $this->post_id, true );
		$backups = get_post_meta( $this->post_id, '_wp_attachment_backup_sizes', true );

		@unlink( $file );

		foreach ( $backups as $backup ) {
			@unlink( $backup['path'] );
		}
	}

	/**
	 * Is layer slide.
	 *
	 * @return bool
	 */
	private function is_layer_slide() {
		if ( 'create_html_overlay_slide' === filter_input( INPUT_POST, 'action' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Add post meta
	 *
	 * @param int    $object_id
	 * @param string $meta_key
	 * @param mixed  $_meta_value
	 */
	public function add_post_meta( $object_id, $meta_key, $_meta_value ) {
		$this->maybe_upload_attachment_backup_sizes( $object_id, $meta_key, $_meta_value );
	}

	/**
	 * Update post meta
	 *
	 * @param int    $meta_id
	 * @param int    $object_id
	 * @param string $meta_key
	 * @param mixed  $_meta_value
	 */
	public function update_post_meta( $meta_id, $object_id, $meta_key, $_meta_value ) {
		$this->maybe_upload_attachment_backup_sizes( $object_id, $meta_key, $_meta_value );
	}

	/**
	 * Maybe upload attachment backup sizes
	 *
	 * @param int    $object_id
	 * @param string $meta_key
	 * @param mixed  $data
	 */
	private function maybe_upload_attachment_backup_sizes( $object_id, $meta_key, $data ) {
		if ( '_wp_attachment_backup_sizes' !== $meta_key ) {
			return;
		}

		if ( 'resize_image_slide' !== filter_input( INPUT_POST, 'action' ) && ! $this->is_layer_slide() ) {
			return;
		}

		if ( ! $this->as3cf->is_plugin_setup( true ) ) {
			return;
		}

		if ( ! ( $provider_object = $this->as3cf->get_attachment_provider_info( $object_id ) ) && ! $this->as3cf->get_setting( 'copy-to-s3' ) ) {
			// Abort if not already uploaded to S3 and the copy setting is off
			return;
		}

		$this->upload_attachment_backup_sizes( $object_id, $provider_object, $data );
	}

	/**
	 * Upload attachment backup sizes
	 *
	 * @param int   $object_id
	 * @param array $provider_object
	 * @param mixed $data
	 */
	private function upload_attachment_backup_sizes( $object_id, $provider_object, $data ) {
		$region = '';
		$prefix = trailingslashit( dirname( $provider_object['key'] ) );

		if ( isset( $provider_object['region'] ) ) {
			$region = $provider_object['region'];
		}

		$provider_client = $this->as3cf->get_provider_client( $region, true );

		$acl = $this->as3cf->get_provider()->get_default_acl();

		if ( isset( $provider_object['acl'] ) ) {
			$acl = $provider_object['acl'];
		}

		foreach ( $data as $file ) {
			if ( ! isset( $file['path'] ) ) {
				continue;
			}

			if ( $this->is_remote_file( $file['path'] ) ) {
				continue;
			}

			$args = array(
				'Bucket'       => $provider_object['bucket'],
				'Key'          => $prefix . $file['file'],
				'ACL'          => $acl,
				'SourceFile'   => $file['path'],
				'CacheControl' => 'max-age=31536000',
				'Expires'      => date( 'D, d M Y H:i:s O', time() + 31536000 ),
			);
			$size = AS3CF_Utils::get_intermediate_size_from_filename( $object_id, wp_basename( $file['file'] ) );
			$args = apply_filters( 'as3cf_object_meta', $args, $object_id, $size, false );

			try {
				$provider_client->upload_object( $args );
			} catch ( Exception $e ) {
				AS3CF_Error::log( 'Error offloading ' . $args['SourceFile'] . ' to the bucket: ' . $e->getMessage(), 'META_SLIDER' );
			}
		}
	}

	/**
	 * Is remote file
	 *
	 * @param string $path
	 *
	 * @return bool
	 */
	private function is_remote_file( $path ) {
		if ( preg_match( '@^s3[a-z0-9]*:\/\/@', $path ) ) {
			return true;
		}

		return false;
	}
}
