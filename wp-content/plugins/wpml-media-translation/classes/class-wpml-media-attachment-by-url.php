<?php

class WPML_Media_Attachment_By_URL {

	/**
	 * @var wpdb
	 */
	private $wpdb;
	/**
	 * @var string
	 */
	private $url;
	/**
	 * @var string
	 */
	private $language;

	const SIZE_SUFFIX_REGEXP = '/-([0-9]+)x([0-9]+)\.([a-z]{3,4})$/';

	const CACHE_KEY_PREFIX = 'attachment-id-from-guid-';
	const CACHE_GROUP = 'wpml-media-setup';
	const CACHE_EXPIRATION = 1800;

	public $cache_hit_flag = null;

	/**
	 * WPML_Media_Attachment_By_URL constructor.
	 *
	 * @param wpdb $wpdb
	 * @param string $url
	 * @param string $language
	 */
	public function __construct( wpdb $wpdb, $url, $language ) {
		$this->url      = $url;
		$this->language = $language;
		$this->wpdb     = $wpdb;
	}

	public function get_id() {
		$cache_key = self::CACHE_KEY_PREFIX . md5( $this->language . '#' . $this->url );

		$attachment_id = wp_cache_get( $cache_key, self::CACHE_GROUP, false, $this->cache_hit_flag );
		if ( ! $this->cache_hit_flag ) {
			$attachment_id = $this->get_id_from_guid();
			if ( ! $attachment_id ) {
				$attachment_id = $this->get_id_from_meta();
			}

			wp_cache_add( $cache_key, $attachment_id, self::CACHE_GROUP, self::CACHE_EXPIRATION );
		}

		return $attachment_id;
	}

	private function get_id_from_guid() {
		$attachment_id = $this->wpdb->get_var( $this->wpdb->prepare( "
		SELECT ID FROM {$this->wpdb->posts} p
		JOIN {$this->wpdb->prefix}icl_translations t ON t.element_id = p.ID
		WHERE t.element_type='post_attachment' AND t.language_code=%s AND p.guid=%s
		", $this->language, $this->url ) );

		return $attachment_id;
	}

	private function get_id_from_meta() {

		$uploads_dir   = wp_get_upload_dir();
		$relative_path = ltrim( preg_replace( '@^' . $uploads_dir['baseurl'] . '@', '', $this->url ), '/' );

		// using _wp_attached_file
		$attachment_id = $this->wpdb->get_var( $this->wpdb->prepare( "
			SELECT post_id 
			FROM {$this->wpdb->postmeta} p 
			JOIN {$this->wpdb->prefix}icl_translations t ON t.element_id = p.post_id 
			WHERE p.meta_key='_wp_attached_file' AND p.meta_value=%s 
				AND t.element_type='post_attachment' AND t.language_code=%s
			", $relative_path, $this->language ) );

		// using attachment meta (fallback)
		if ( ! $attachment_id && preg_match( self::SIZE_SUFFIX_REGEXP, $relative_path ) ) {
			$attachment_id = $this->get_attachment_image_from_meta_fallback( $relative_path );
		}

		return $attachment_id;
	}

	private function get_attachment_image_from_meta_fallback( $relative_path ) {
		$attachment_id = null;

		$relative_path_original = preg_replace( self::SIZE_SUFFIX_REGEXP, '.$3', $relative_path );
		$attachment_id_original = $this->wpdb->get_var( $this->wpdb->prepare( "
			SELECT p.post_id 
			FROM {$this->wpdb->postmeta} p
			JOIN {$this->wpdb->prefix}icl_translations t ON t.element_id = p.post_id
			WHERE p.meta_key='_wp_attached_file' AND p.meta_value=%s 
				AND t.element_type='post_attachment' AND t.language_code=%s
			", $relative_path_original, $this->language ) );
		// validate size
		if ( $attachment_id_original ) {
			$attachment_meta_data = wp_get_attachment_metadata( $attachment_id_original );
			if ( $this->validate_image_size( $relative_path, $attachment_meta_data ) ) {
				$attachment_id = $attachment_id_original;
			}
		}

		return $attachment_id;
	}

	private function validate_image_size( $path, $attachment_meta_data ) {
		$valid     = false;
		$file_name = basename( $path );

		foreach ( $attachment_meta_data['sizes'] as $size ) {
			if ( $file_name === $size['file'] ) {
				$valid = true;
				break;
			}
		}

		return $valid;
	}

}