<?php

class WPML_ST_MO_Components_Find_Plugin implements WPML_ST_MO_Components_Find {
	/** @var WPML_Debug_BackTrace */
	private $debug_backtrace;

	/** @var string */
	private $plugin_dir;

	/** @var array */
	private $plugin_ids;

	/**
	 * @param WPML_Debug_BackTrace $debug_backtrace
	 */
	public function __construct( WPML_Debug_BackTrace $debug_backtrace ) {
		$this->debug_backtrace = $debug_backtrace;
		$this->plugin_dir = realpath( WP_PLUGIN_DIR );
	}

	public function find_id( $mo_file ) {
		$directory = $this->find_plugin_directory( $mo_file );
		if ( ! $directory ) {
			return null;
		}

		return $this->get_plugin_id_by_directory( $directory );
	}

	private function find_plugin_directory( $mo_file ) {
		if ( false !== strpos( $mo_file, $this->plugin_dir ) ) {
			return $this->extract_plugin_directory( $mo_file );
		}

		return $this->find_plugin_directory_in_backtrace();
	}

	private function find_plugin_directory_in_backtrace() {
		$file = $this->find_file_in_backtrace();
		if ( ! $file ) {
			return null;
		}

		return $this->extract_plugin_directory( $file );
	}

	private function find_file_in_backtrace() {
		$stack = $this->debug_backtrace->get_backtrace();

		foreach ( $stack as $call ) {
			if ( isset( $call['function'] ) && 'load_plugin_textdomain' === $call['function'] ) {
				return $call['file'];
			}
		}

		return null;
	}

	/**
	 * @param $file_path
	 *
	 * @return mixed
	 */
	private function extract_plugin_directory( $file_path ) {
		$dir = ltrim( str_replace( $this->plugin_dir, '', $file_path ), DIRECTORY_SEPARATOR );
		$dir = explode( DIRECTORY_SEPARATOR, $dir );

		return trim( $dir[0], DIRECTORY_SEPARATOR );
	}

	/**
	 * @param string $directory
	 *
	 * @return string|null
	 */
	private function get_plugin_id_by_directory( $directory ) {
		foreach ( $this->get_plugin_ids() as $plugin_id ) {
			if ( 0 === strpos( $plugin_id, $directory . '/' ) ) {
				return $plugin_id;
			}
		}

		return null;
	}

	/**
	 * @return string[]
	 */
	private function get_plugin_ids() {
		if ( null === $this->plugin_ids ) {
			$this->plugin_ids = array_keys( get_plugins() );
		}

		return $this->plugin_ids;
	}
}