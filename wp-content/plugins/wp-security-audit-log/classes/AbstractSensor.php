<?php
/**
 * Abstract class used in all the sensors.
 *
 * @see Sensors/*.php
 * @package Wsal
 */
abstract class WSAL_AbstractSensor {

	/**
	 * Instance of WpSecurityAuditLog.
	 *
	 * @var object
	 */
	protected $plugin;

	/**
	 * Method: Constructor.
	 *
	 * @param  object $plugin - Instance of WpSecurityAuditLog.
	 */
	public function __construct( WpSecurityAuditLog $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Whether we are running on multisite or not.
	 *
	 * @return boolean
	 */
	protected function IsMultisite() {
		return function_exists( 'is_multisite' ) && is_multisite();
	}

	/**
	 * Method: Hook events related to sensor.
	 */
	abstract function HookEvents();

	/**
	 * Method: Log the message for sensor.
	 *
	 * @param int    $type - Type of alert.
	 * @param string $message - Alert message.
	 * @param mix    $args - Message arguments.
	 */
	protected function Log( $type, $message, $args ) {
		$this->plugin->alerts->Trigger(
			$type, array(
				'Message' => $message,
				'Context' => $args,
				'Trace'   => debug_backtrace(),
			)
		);
	}

	/**
	 * Method: Log error message for sensor.
	 *
	 * @param string $message - Alert message.
	 * @param mix    $args - Message arguments.
	 */
	protected function LogError( $message, $args ) {
		$this->Log( 0001, $message, $args );
	}

	/**
	 * Method: Log warning message for sensor.
	 *
	 * @param string $message - Alert message.
	 * @param mix    $args - Message arguments.
	 */
	protected function LogWarn( $message, $args ) {
		$this->Log( 0002, $message, $args );
	}

	/**
	 * Method: Log info message for sensor.
	 *
	 * @param string $message - Alert message.
	 * @param mix    $args - Message arguments.
	 */
	protected function LogInfo( $message, $args ) {
		$this->Log( 0003, $message, $args );
	}

	/**
	 * Check to see whether or not the specified directory is accessible
	 *
	 * @param string $dir_path - Directory path.
	 * @return boolean
	 */
	protected function CheckDirectory( $dir_path ) {
		if ( ! is_dir( $dir_path ) ) {
			return false;
		}
		if ( ! is_readable( $dir_path ) ) {
			return false;
		}
		if ( ! is_writable( $dir_path ) ) {
			return false;
		}
		return true;
	}
}
