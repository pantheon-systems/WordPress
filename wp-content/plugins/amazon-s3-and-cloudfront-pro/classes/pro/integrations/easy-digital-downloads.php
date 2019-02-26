<?php

namespace DeliciousBrains\WP_Offload_Media\Pro\Integrations;

use Amazon_S3_And_CloudFront;

class Easy_Digital_Downloads extends Integration {

	/**
	 * Is installed?
	 *
	 * @return bool
	 */
	public function is_installed() {
		if ( class_exists( 'Easy_Digital_Downloads' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Init integration.
	 */
	public function init() {
		// Set download method to redirect
		add_filter( 'edd_file_download_method', array( $this, 'set_download_method' ) );
		// Disable using symlinks for download.
		add_filter( 'edd_symlink_file_downloads', array( $this, 'disable_symlink_file_downloads' ) );
		// Hook into edd_requested_file to swap in the S3 secure URL
		add_filter( 'edd_requested_file', array( $this, 'get_download_url' ), 10, 3 );
		// Hook into the save download files metabox to apply the private ACL
		add_filter( 'edd_metabox_save_edd_download_files', array( $this, 'make_edd_files_private_on_provider' ), 11 );
	}

	/**
	 * Set download method
	 *
	 * @param string $method
	 *
	 * @return string
	 */
	public function set_download_method( $method ) {
		return 'redirect';
	}

	/**
	 * Disable symlink file downloads
	 *
	 * @param bool $use_symlink
	 *
	 * @return bool
	 */
	public function disable_symlink_file_downloads( $use_symlink ) {
		return false;
	}

	/**
	 * Uses the secure S3 url for downloads of a file
	 *
	 * @param $file
	 * @param $download_files
	 * @param $file_key
	 *
	 * @return mixed
	 */
	public function get_download_url( $file, $download_files, $file_key ) {
		global $edd_options;

		$file_data = $download_files[ $file_key ];
		$file_name = $file_data['file'];
		$post_id   = $file_data['attachment_id'];
		$expires   = apply_filters( 'as3cf_edd_download_expires', 5 );
		$headers   = apply_filters( 'as3cf_edd_download_headers', array(
			'ResponseContentDisposition' => 'attachment',
		), $file_data );

		// Our S3 upload
		if ( $this->as3cf->get_attachment_provider_info( $post_id ) ) {
			return $this->as3cf->get_secure_attachment_url( $post_id, $expires, null, $headers, true );
		}

		// Official EDD S3 addon upload - path should not start with '/', 'http', 'https' or 'ftp' or contain AWSAccessKeyId
		$url = parse_url( $file_name );

		if ( ( '/' !== $file_name[0] && false === isset( $url['scheme'] ) ) || false !== ( strpos( $file_name, 'AWSAccessKeyId' ) ) ) {
			$bucket     = ( isset( $edd_options['edd_amazon_s3_bucket'] ) ) ? trim( $edd_options['edd_amazon_s3_bucket'] ) : $this->as3cf->get_setting( 'bucket' );
			$expires    = time() + $expires;
			$secure_url = $this->as3cf->get_provider_client()->get_object_url( $bucket, $file_name, $expires, $headers );

			return $secure_url;
		}

		// None S3 upload
		return $file;
	}

	/**
	 * Apply ACL to files uploaded outside of EDD on save of EDD download files
	 *
	 * @param $files
	 *
	 * @return mixed
	 */
	public function make_edd_files_private_on_provider( $files ) {
		global $post;

		// get existing files attached to download
		$old_files          = edd_get_download_files( $post->ID );
		$old_attachment_ids = wp_list_pluck( $old_files, 'attachment_id' );
		$new_attachment_ids = array();

		if ( is_array( $files ) ) {
			foreach ( $files as $key => $file ) {
				$new_attachment_ids[] = $file['attachment_id'];

				if ( ! ( $provider_object = $this->as3cf->get_attachment_provider_info( $file['attachment_id'] ) ) ) {
					// not S3 upload ignore
					continue;
				}

				if ( $this->as3cf->is_pro_plugin_setup( true ) ) {
					$provider_object = $this->as3cf->set_attachment_acl_on_provider( $file['attachment_id'], $provider_object, $this->as3cf->get_provider()->get_private_acl() );
					if ( $provider_object && ! is_wp_error( $provider_object ) ) {
						$this->as3cf->make_acl_admin_notice( $provider_object );
					}
				}
			}
		}

		// determine which attachments have been removed and maybe set to public
		$removed_attachment_ids = array_diff( $old_attachment_ids, $new_attachment_ids );
		$this->maybe_make_removed_edd_files_public( $removed_attachment_ids, $post->ID );

		return $files;
	}

	/**
	 * Remove public ACL from attachments removed from a download
	 * as long as they are not attached to any other downloads
	 *
	 * @param $attachment_ids
	 * @param $download_id
	 */
	function maybe_make_removed_edd_files_public( $attachment_ids, $download_id ) {
		global $wpdb;

		foreach ( $attachment_ids as $id ) {
			if ( ! ( $provider_object = $this->as3cf->get_attachment_provider_info( $id ) ) ) {
				// not an S3 attachment, ignore
				continue;
			}

			$length = strlen( $id );
			// check the attachment isn't used by other downloads
			$sql = "
				SELECT COUNT(*)
				FROM `{$wpdb->prefix}postmeta`
				WHERE `{$wpdb->prefix}postmeta`.`meta_key` = 'edd_download_files'
				AND `{$wpdb->prefix}postmeta`.`post_id` != {$download_id}
				AND `{$wpdb->prefix}postmeta`.`meta_value` LIKE '%s:13:\"attachment_id\";s:{$length}:\"{$id}\"%'
			";

			if ( $wpdb->get_var( $sql ) > 0 ) {
				// used for another download, ignore
				continue;
			}

			// set acl to public
			$provider_object = $this->as3cf->set_attachment_acl_on_provider( $id, $provider_object, $this->as3cf->get_provider()->get_default_acl() );
			if ( $provider_object && ! is_wp_error( $provider_object ) ) {
				$this->as3cf->make_acl_admin_notice( $provider_object );
			}
		}
	}
}
