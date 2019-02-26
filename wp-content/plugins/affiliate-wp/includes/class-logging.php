<?php

class Affiliate_WP_Logging {

	public $is_writable = true;
	private $filename   = '';
	private $file       ='';

	/**
	 * Get things started
	 *
	 * @since 1.7.15
	 */
	public function __construct() {

		$this->init();

	}

	/**
	 * Get things started
	 *
	 * @since 1.7.15
	 * @return void
	 */
	public function init() {

		$upload_dir       = wp_upload_dir( null, false );
		$this->filename   = 'affwp-debug.log';
		$this->file       = trailingslashit( $upload_dir['basedir'] ) . $this->filename;

		if ( ! is_writeable( $upload_dir['basedir'] ) ) {
			$this->is_writable = false;
		}

	}

	/**
	 * Retrieve the log data
	 *
	 * @since 1.7.15
	 * @return string
	 */
	public function get_log() {
		return $this->get_file();
	}

	/**
	 * Log message to file
	 *
	 * @since 1.7.15
	 * @return void
	 */
	public function log( $message = '' ) {
		$message = date( 'Y-n-d H:i:s' ) . ' - ' . $message . "\r\n";
		$this->write_to_log( $message );

	}

	/**
	 * Retrieve the file data is written to
	 *
	 * @since 1.7.15
	 * @return string
	 */
	protected function get_file() {

		$file = '';

		if ( @file_exists( $this->file ) ) {

			if ( ! is_writeable( $this->file ) ) {
				$this->is_writable = false;
			}

			$file = @file_get_contents( $this->file );

		} else {

			@file_put_contents( $this->file, '' );
			@chmod( $this->file, 0664 );

		}

		return $file;
	}

	/**
	 * Write the log message
	 *
	 * @since 1.7.15
	 * @return void
	 */
	protected function write_to_log( $message = '' ) {
		$file = $this->get_file();
		$file .= $message;
		@file_put_contents( $this->file, $file );
	}

	/**
	 * Write the log message
	 *
	 * @since 1.7.15
	 * @return void
	 */
	public function clear_log() {
		@unlink( $this->file );
	}

}
