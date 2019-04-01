<?php

/**
 * Class WPML_Flags
 *
 * @package wpml-core
 */
class WPML_Flags {
	/** @var icl_cache  */
	private $cache;

	/** @var WPDB $wpdb */
	private $wpdb;

	/** @var WP_Filesystem_Direct */
	private $filesystem;

	/**
	 * @param WPDB $wpdb
	 * @param icl_cache $cache
	 * @param WP_Filesystem_Direct $filesystem
	 */
	public function __construct( $wpdb, icl_cache $cache, WP_Filesystem_Direct $filesystem ) {
		$this->wpdb = $wpdb;
		$this->cache = $cache;
		$this->filesystem = $filesystem;
	}

	/**
	 * @param $lang_code
	 *
	 * @return bool|object
	 */
	public function get_flag( $lang_code ) {
		$flag = $this->cache->get( $lang_code );

		if ( ! $flag ) {
			$flag = $this->wpdb->get_row( $this->wpdb->prepare( "SELECT flag, from_template
                                                    FROM {$this->wpdb->prefix}icl_flags
                                                    WHERE lang_code=%s", $lang_code ) );

			$this->cache->set( $lang_code, $flag );
		}

		return $flag;
	}

	public function get_flag_url( $lang_code ) {
		$flag = $this->get_flag( $lang_code );
		if ( ! $flag ) {
			return '';
		}

		$path = '';
		if ( $flag->from_template ) {
			$wp_upload_dir = wp_upload_dir();
			$base_url      = $wp_upload_dir['baseurl'];
			$path          = 'flags/';
		} else {
			$base_url = $this->get_wpml_flags_url();
		}
		$path .= $flag->flag;

		return $this->append_path_to_url( $base_url, $path );
	}

	public function clear() {
		$this->cache->clear();
	}

	/**
	 * @param array $allowed_file_types
	 *
	 * @return string[]
	 */
	public function get_wpml_flags( $allowed_file_types = null ) {
		if ( null === $allowed_file_types ) {
			$allowed_file_types = array( 'gif', 'jpeg', 'png', 'svg' );
		}

		$files = array_keys( $this->filesystem->dirlist( $this->get_wpml_flags_directory(), false ) );

		$result = $this->filter_flag_files( $allowed_file_types, $files );
		sort( $result );

		return $result;
	}

	/**
	 * @return string
	 */
	public final function get_wpml_flags_directory() {
		return WPML_PLUGIN_PATH . '/res/flags/';
	}

	/**
	 * @return string
	 */
	public final function get_wpml_flags_url() {
		return ICL_PLUGIN_URL . '/res/flags/';
	}

	/**
	 * @param array $allowed_file_types
	 * @param array $files
	 *
	 * @return array
	 */
	private function filter_flag_files( $allowed_file_types, $files ) {
		$result = array();
		foreach ( $files as $file ) {
			$path = $this->get_wpml_flags_directory() . $file;
			if ( $this->filesystem->exists( $path ) ) {
				$ext = pathinfo( $path, PATHINFO_EXTENSION );
				if ( in_array( $ext, $allowed_file_types, true ) ) {
					$result[] = $file;
				}
			}
		}

		return $result;
	}

	/**
	 * @param $base_url
	 * @param $path
	 *
	 * @return string
	 */
	private function append_path_to_url( $base_url, $path ) {
		$base_url_parts = wp_parse_url( $base_url );

		$base_url_path_components = '';
		if ( array_key_exists( 'path', $base_url_parts ) ) {
			$base_url_path_components = explode( '/', untrailingslashit( $base_url_parts['path'] ) );
		}

		$sub_dir_path_components = explode( '/', trim( $path, '/' ) );
		foreach ( $sub_dir_path_components as $sub_dir_path_part ) {
			$base_url_path_components[] = $sub_dir_path_part;
		}

		$base_url_parts['path'] = implode( '/', $base_url_path_components );

		return http_build_url( $base_url_parts );
	}
}
