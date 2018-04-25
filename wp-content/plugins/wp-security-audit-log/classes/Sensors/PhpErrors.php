<?php
/**
 * Sensor: PHP Errors
 *
 * PHP Errors sensor file.
 *
 * @since 1.0.0
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * PHP Errors sensor.
 *
 * 0001 PHP error
 * 0002 PHP warning
 * 0003 PHP notice
 * 0004 PHP exception
 * 0005 PHP shutdown error
 *
 * @package Wsal
 * @subpackage Sensors
 */
class WSAL_Sensors_PhpErrors extends WSAL_AbstractSensor {

	/**
	 * Avoid Recursive Errors
	 *
	 * @var boolean
	 */
	protected $_avoid_error_recursion = false;

	/**
	 * Last Error
	 *
	 * @var string
	 */
	protected $_maybe_last_error = null;

	/**
	 * Error Types
	 *
	 * @var array
	 */
	protected $_error_types = array(
		0001 => array( 1, 4, 16, 64, 256, 4096 ),     // Errors.
		0002 => array( 2, 32, 128, 512 ),             // Warnings.
		0003 => array( 8, 1024, 2048, 8192, 16384 ),  // Notices.
		0004 => array(),                              // Exceptions.
		0005 => array(),                              // Shutdown.
	);

	/**
	 * Listening to Php events.
	 */
	public function HookEvents() {
		if ( $this->plugin->settings->IsPhpErrorLoggingEnabled() ) {
			set_error_handler( array( $this, 'EventError' ), E_ALL );
			set_exception_handler( array( $this, 'EventException' ) );
			register_shutdown_function( array( $this, 'EventShutdown' ) );
		}
	}

	/**
	 * Get the hash of the error.
	 *
	 * @param integer $code - Error code.
	 * @param string  $mesg - Error message.
	 * @param string  $file - File name.
	 * @param integer $line - Line number.
	 */
	protected function GetErrorHash( $code, $mesg, $file, $line ) {
		return md5( implode( ':', func_get_args() ) );
	}

	/**
	 * PHP error, warning or notice.
	 *
	 * @param integer $errno - Error code.
	 * @param string  $errstr - Error message.
	 * @param string  $errfile - File name.
	 * @param integer $errline - Line number.
	 * @param array   $errcontext - Error context.
	 */
	public function EventError( $errno, $errstr, $errfile = 'unknown', $errline = 0, $errcontext = array() ) {
		if ( $this->_avoid_error_recursion ) {
			return;
		}

		$errbacktrace = 'No Backtrace';
		if ( $this->plugin->settings->IsBacktraceLoggingEnabled() ) {
			ob_start();
			debug_print_backtrace();
			$errbacktrace = ob_get_clean();
		}

		$data = array(
			'Code'    => $errno,
			'Message' => $errstr,
			'File'    => $errfile,
			'Line'    => $errline,
			'Context' => $errcontext,
			'Trace'   => $errbacktrace,
		);

		$type = 0002; // Default — middle ground.
		foreach ( $this->_error_types as $temp => $codes ) {
			if ( in_array( $errno, $codes ) ) {
				$type = $temp;
			}
		}

		$this->_maybe_last_error = $this->GetErrorHash( $errno, $errstr, $errfile, $errline );
		$this->_avoid_error_recursion = true;
		$this->plugin->alerts->Trigger( $type, $data );
		$this->_avoid_error_recursion = false;
	}

	/**
	 * PHP exception.
	 *
	 * @param Exception $ex - Instance of Exception.
	 */
	public function EventException( Exception $ex ) {
		if ( $this->_avoid_error_recursion ) {
			return;
		}

		$errbacktrace = 'No Backtrace';
		if ( $this->plugin->settings->IsBacktraceLoggingEnabled() ) {
			$errbacktrace = $ex->getTraceAsString();
		}

		$data = array(
			'Class'   => get_class( $ex ),
			'Code'    => $ex->getCode(),
			'Message' => $ex->getMessage(),
			'File'    => $ex->getFile(),
			'Line'    => $ex->getLine(),
			'Trace'   => $errbacktrace,
		);

		if ( method_exists( $ex, 'getContext' ) ) {
			$data['Context'] = $ex->getContext();
		}

		$this->_avoid_error_recursion = true;
		$this->plugin->alerts->Trigger( 0004, $data );
		$this->_avoid_error_recursion = false;
	}

	/**
	 * PHP shutdown error.
	 */
	public function EventShutdown() {
		if ( $this->_avoid_error_recursion ) {
			return;
		}

		if ( ! ! ($e = error_get_last()) && ($this->_maybe_last_error != $this->GetErrorHash( $e['type'], $e['message'], $e['file'], $e['line'] )) ) {
			$data = array(
				'Code'    => $e['type'],
				'Message' => $e['message'],
				'File'    => $e['file'],
				'Line'    => $e['line'],
			);

			$this->_avoid_error_recursion = true;
			$this->plugin->alerts->Trigger( 0005, $data );
			$this->_avoid_error_recursion = false;
		}
	}
}
