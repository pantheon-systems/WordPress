<?php

class AS3CF_S3_To_Local extends AS3CF_Filter {

	/**
	 * Init.
	 */
	protected function init() {
		// EDD
		add_filter( 'edd_metabox_save_edd_download_files', array( $this, 'filter_edd_download_files' ) );
		// Customizer
		add_filter( 'pre_set_theme_mod_background_image', array( $this, 'filter_customizer_image' ), 10, 2 );
		add_filter( 'pre_set_theme_mod_header_image', array( $this, 'filter_customizer_image' ), 10, 2 );
		add_filter( 'pre_set_theme_mod_header_image_data', array( $this, 'filter_header_image_data' ), 10, 2 );
		add_filter( 'update_custom_css_data', array( $this, 'filter_update_custom_css_data' ), 10, 2 );
		// Posts
		add_filter( 'content_save_pre', array( $this, 'filter_post' ) );
		add_filter( 'excerpt_save_pre', array( $this, 'filter_post' ) );
		add_filter( 'as3cf_filter_post_s3_to_local', array( $this, 'filter_post' ) ); // Backwards compatibility
		add_filter( 'as3cf_filter_post_provider_to_local', array( $this, 'filter_post' ) );
		// Widgets
		add_filter( 'widget_update_callback', array( $this, 'filter_widget_save' ), 10, 4 );
	}

	/**
	 * Filter update custom CSS data.
	 *
	 * @param array $data
	 * @param array $args
	 *
	 * @return array
	 */
	public function filter_update_custom_css_data( $data, $args ) {
		$data['css'] = $this->filter_custom_css( $data['css'], $args['stylesheet'] );

		return $data;
	}

	/**
	 * Filter widget on save.
	 *
	 * @param array     $instance
	 * @param array     $new_instance
	 * @param array     $old_instance
	 * @param WP_Widget $class
	 *
	 * @return array
	 *
	 */
	public function filter_widget_save( $instance, $new_instance, $old_instance, $class ) {
		return $this->handle_widget( $instance, $class );
	}

	/**
	 * Should filter content.
	 *
	 * @return bool
	 */
	protected function should_filter_content() {
		return true;
	}

	/**
	 * Does URL need replacing?
	 *
	 * @param string $url
	 *
	 * @return bool
	 */
	protected function url_needs_replacing( $url ) {
		$uploads  = wp_upload_dir();
		$base_url = AS3CF_Utils::remove_scheme( $uploads['baseurl'] );

		if ( false !== strpos( $url, $base_url ) ) {
			// Local URL, no replacement needed
			return false;
		}

		// Remote URL, perform replacement
		return true;
	}

	/**
	 * Get URL
	 *
	 * @param int         $attachment_id
	 * @param null|string $size
	 *
	 * @return bool|string
	 */
	protected function get_url( $attachment_id, $size = null ) {
		return $this->as3cf->get_attachment_local_url_size( $attachment_id, $size );
	}

	/**
	 * Get base URL.
	 *
	 * @param int $attachment_id
	 *
	 * @return string|false
	 */
	protected function get_base_url( $attachment_id ) {
		return $this->as3cf->get_attachment_url( $attachment_id );
	}

	/**
	 * Get attachment ID from URL.
	 *
	 * @param string $url
	 *
	 * @return bool|int
	 */
	protected function get_attachment_id_from_url( $url ) {
		global $wpdb;

		$full_url = AS3CF_Utils::remove_size_from_filename( $url );

		if ( isset( $this->query_cache[ $full_url ] ) ) {
			// ID already cached, return
			return $this->query_cache[ $full_url ];
		}

		$parts = AS3CF_Utils::parse_url( $full_url );
		$path  = $this->as3cf->decode_filename_in_path( ltrim( $parts['path'], '/' ) );

		if ( false !== strpos( $path, '/' ) ) {
			// Remove the first directory to cater for bucket in path domain settings
			$path = explode( '/', $path );
			array_shift( $path );
			$path = implode( '/', $path );
		}

		$sql = $wpdb->prepare( "
 				SELECT * FROM {$wpdb->postmeta}
 				WHERE meta_key = %s
 				AND meta_value LIKE %s;
 			", 'amazonS3_info', '%' . $path . '%' );

		$results = $wpdb->get_results( $sql );

		if ( empty( $results ) ) {
			// No attachment found, return false
			return false;
		}

		if ( 1 === count( $results ) ) {
			// Attachment matched, return ID
			$this->query_cache[ $full_url ] = $results[0]->post_id;

			return $results[0]->post_id;
		}

		$path = ltrim( $parts['path'], '/' );

		foreach ( $results as $result ) {
			$meta = maybe_unserialize( $result->meta_value );

			if ( ! isset( $meta['bucket'] ) || ! isset( $meta['key'] ) ) {
				// Can't determine S3 bucket or key, continue
				continue;
			}

			if ( false !== strpos( $path, $meta['bucket'] ) ) {
				// Bucket in path, remove
				$path = ltrim( str_replace( $meta['bucket'], '', $path ), '/' );
			}

			if ( $path === $meta['key'] ) {
				// Exact match, return ID
				$this->query_cache[ $full_url ] = $results[0]->post_id;

				return $result->post_id;
			}
		}

		// Can't determine ID, return false
		$this->query_cache[ $full_url ] = false;

		return false;
	}

	/**
	 * Get attachment IDs from URLs.
	 *
	 * @param array $urls
	 *
	 * @return array url => attachment ID (or false)
	 */
	protected function get_attachment_ids_from_urls( $urls ) {
		$results = array();

		if ( empty( $urls ) ) {
			return $results;
		}

		if ( ! is_array( $urls ) ) {
			$urls = array( $urls );
		}

		foreach ( $urls as $url ) {
			$results[ $url ] = $this->get_attachment_id_from_url( $url );
		}

		return $results;
	}

	/**
	 * Normalize find value.
	 *
	 * @param string $url
	 *
	 * @return string
	 */
	protected function normalize_find_value( $url ) {
		return $this->as3cf->encode_filename_in_path( $url );
	}

	/**
	 * Normalize replace value.
	 *
	 * @param string $url
	 *
	 * @return string
	 */
	protected function normalize_replace_value( $url ) {
		return $this->as3cf->decode_filename_in_path( $url );
	}

	/**
	 * Post process content.
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	protected function post_process_content( $content ) {
		return $this->remove_aws_query_strings( $content );
	}

	/**
	 * Pre replace content.
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	protected function pre_replace_content( $content ) {
		return $content;
	}
}
