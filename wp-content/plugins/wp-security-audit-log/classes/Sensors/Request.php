<?php
/**
 * Sensor: Request
 *
 * Request sensor class file.
 *
 * @since 1.0.0
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Writes the Request.log.php file.
 *
 * @package Wsal
 * @subpackage Sensors
 */
class WSAL_Sensors_Request extends WSAL_AbstractSensor {

	/**
	 * Environment Variables
	 *
	 * @var array
	 */
	protected static $envvars = array();

	/**
	 * Listening to events using WP hooks.
	 */
	public function HookEvents() {
		if ( $this->plugin->settings->IsRequestLoggingEnabled() ) {
			add_action( 'shutdown', array( $this, 'EventShutdown' ) );
		}
	}

	/**
	 * Fires just before PHP shuts down execution.
	 */
	public function EventShutdown() {
		// Filter global arrays for security.
		$post_array = filter_input_array( INPUT_POST );
		$server_array = filter_input_array( INPUT_SERVER );

		$upload_dir = wp_upload_dir();
		$uploads_dir_path = trailingslashit( $upload_dir['basedir'] ) . 'wp-security-audit-log/';
		if ( ! $this->CheckDirectory( $uploads_dir_path ) ) {
			wp_mkdir_p( $uploads_dir_path );
		}

		$file = $uploads_dir_path . 'Request.log.php';

		$request_method = isset( $server_array['REQUEST_METHOD'] ) ? $server_array['REQUEST_METHOD'] : false;
		$request_uri = isset( $server_array['REQUEST_URI'] ) ? $server_array['REQUEST_URI'] : false;

		$line = '[' . date( 'Y-m-d H:i:s' ) . '] '
			. $request_method . ' '
			. $request_uri . ' '
			. ( ! empty( $post_array ) ? str_pad( PHP_EOL, 24 ) . json_encode( $post_array ) : '')
			. ( ! empty( self::$envvars ) ? str_pad( PHP_EOL, 24 ) . json_encode( self::$envvars ) : '')
			. PHP_EOL;

		if ( ! file_exists( $file ) && ! file_put_contents( $file, '<' . '?php die(\'Access Denied\'); ?>' . PHP_EOL ) ) {
			return $this->LogError(
				'Could not initialize request log file', array(
					'file' => $file,
				)
			);
		}

		$f = fopen( $file, 'a' );
		if ( $f ) {
			if ( ! fwrite( $f, $line ) ) {
				$this->LogWarn(
					'Could not write to log file', array(
						'file' => $file,
					)
				);
			}
			if ( ! fclose( $f ) ) {
				$this->LogWarn(
					'Could not close log file', array(
						'file' => $file,
					)
				);
			}
		} else {
			$this->LogWarn(
				'Could not open log file', array(
					'file' => $file,
				)
			);
		}
	}

	/**
	 * Sets $envvars element with key and value.
	 *
	 * @param mixed $name - Key name of the variable.
	 * @param mixed $value - Value of the variable.
	 */
	public static function SetVar( $name, $value ) {
		self::$envvars[ $name ] = $value;
	}

	/**
	 * Copy data array into $envvars array.
	 *
	 * @param array $data - Data array.
	 */
	public static function SetVars( $data ) {
		foreach ( $data as $name => $value ) {
			self::SetVar( $name, $value );
		}
	}
}
