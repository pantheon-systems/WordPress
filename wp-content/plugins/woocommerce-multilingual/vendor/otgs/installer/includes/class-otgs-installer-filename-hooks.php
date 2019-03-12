<?php

class OTGS_Installer_Filename_Hooks {

	/**
	 * @var OTGS_Installer_PHP_Functions
	 */
	private $built_in_functions;

	public function __construct( OTGS_Installer_PHP_Functions $built_in_functions ) {
		$this->built_in_functions = $built_in_functions;
	}

	public function add_hooks() {
		if ( in_array( $this->built_in_functions->constant( 'PHP_OS' ), array( 'WIN32', 'WINNT', 'Windows' ), true ) ) {
			add_filter( 'wp_unique_filename', array( $this, 'fix_filename_for_win' ), 10, 3 );
		}
	}

	/**
	 * @param string $filename
	 * @param string $ext
	 * @param string $dir
	 *
	 * @return string
	 */
	public function fix_filename_for_win( $filename, $ext, $dir ) {
		if ( $dir === get_temp_dir() ) {
			return md5( $filename . $this->built_in_functions->time() ) . 'tmp';
		}
		return $filename;
	}
}