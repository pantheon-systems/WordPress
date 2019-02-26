<?php

class WPML_ST_MO_Components_Find_Theme implements WPML_ST_MO_Components_Find {
	/** @var WPML_Debug_BackTrace */
	private $debug_backtrace;

	/** @var WPML_File $file */
	private $file;

	/** @var string */
	private $theme_dir;

	/**
	 * @param WPML_Debug_BackTrace $debug_backtrace
	 * @param WPML_File            $file
	 */
	public function __construct( WPML_Debug_BackTrace $debug_backtrace, WPML_File $file ) {
		$this->debug_backtrace = $debug_backtrace;
		$this->file            = $file;

		$this->theme_dir = $this->file->fix_dir_separator( get_theme_root() );
	}

	public function find_id( $mo_file ) {
		return $this->find_theme_directory( $mo_file );
	}

	private function find_theme_directory( $mo_file ) {
		if ( false !== strpos( $mo_file, $this->theme_dir ) ) {
			return $this->extract_theme_directory( $mo_file );
		}

		return $this->find_theme_directory_in_backtrace();
	}

	private function find_theme_directory_in_backtrace() {
		$file = $this->find_file_in_backtrace();
		if ( ! $file ) {
			return null;
		}

		return $this->extract_theme_directory( $file );
	}

	private function find_file_in_backtrace() {
		$stack = $this->debug_backtrace->get_backtrace();

		foreach ( $stack as $call ) {
			if ( isset( $call['function'] ) && 'load_theme_textdomain' === $call['function'] ) {
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
	private function extract_theme_directory( $file_path ) {
		$file_path = $this->file->fix_dir_separator( $file_path );
		$dir       = ltrim( str_replace( $this->theme_dir, '', $file_path ), DIRECTORY_SEPARATOR );
		$dir       = explode( DIRECTORY_SEPARATOR, $dir );

		return trim( $dir[0], DIRECTORY_SEPARATOR );
	}
}