<?php
/**
 * Manager: Sensor
 *
 * Sensor manager class file.
 *
 * @since 1.0.0
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sensor Manager.
 *
 * This class loads all the sensors and initialize them.
 *
 * @package Wsal
 */
final class WSAL_SensorManager extends WSAL_AbstractSensor {

	/**
	 * Array of sensors.
	 *
	 * @var WSAL_AbstractSensor[]
	 */
	protected $sensors = array();

	/**
	 * Method: Constructor.
	 *
	 * @param WpSecurityAuditLog $plugin - Instance of WpSecurityAuditLog.
	 */
	public function __construct( WpSecurityAuditLog $plugin ) {
		parent::__construct( $plugin );

		foreach ( glob( dirname( __FILE__ ) . '/Sensors/*.php' ) as $file ) {
			$this->AddFromFile( $file );
		}

		/**
		 * Load Custom Sensor files from /wp-content/uploads/wp-security-audit-log/custom-sensors/
		 */
		$upload_dir = wp_upload_dir();
		$uploads_dir_path = trailingslashit( $upload_dir['basedir'] ) . 'wp-security-audit-log' . DIRECTORY_SEPARATOR . 'custom-sensors' . DIRECTORY_SEPARATOR;

		// Check directory.
		if ( is_dir( $uploads_dir_path ) && is_readable( $uploads_dir_path ) ) {
			foreach ( glob( $uploads_dir_path . '*.php' ) as $file ) {
				// Include custom sensor file.
				require_once( $file );
				$file = substr( $file, 0, -4 );
				$sensor = str_replace( $uploads_dir_path, '', $file );

				// Skip if the file is index.php for security.
				if ( 'index' === $sensor ) {
					continue;
				}

				// Generate and initiate custom sensor file.
				$class = 'WSAL_Sensors_' . $sensor;
				$this->AddFromClass( $class );
			}
		}
	}

	/**
	 * Method: Hook events of the sensors.
	 */
	public function HookEvents() {
		foreach ( $this->sensors as $sensor ) {
			$sensor->HookEvents();
		}
	}

	/**
	 * Method: Get the sensors.
	 */
	public function GetSensors() {
		return $this->sensors;
	}

	/**
	 * Add new sensor from file inside autoloader path.
	 *
	 * @param string $file Path to file.
	 */
	public function AddFromFile( $file ) {
		$this->AddFromClass( $this->plugin->GetClassFileClassName( $file ) );
	}

	/**
	 * Add new sensor given class name.
	 *
	 * @param string $class Class name.
	 */
	public function AddFromClass( $class ) {
		$this->AddInstance( new $class( $this->plugin ) );
	}

	/**
	 * Add newly created sensor to list.
	 *
	 * @param WSAL_AbstractSensor $sensor The new sensor.
	 */
	public function AddInstance( WSAL_AbstractSensor $sensor ) {
		$this->sensors[] = $sensor;
	}
}
