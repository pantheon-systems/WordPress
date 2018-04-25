<?php
/**
 * Class: Abstract Connector Factory.
 *
 * Abstract class used for create the connector, only MySQL is implemented.
 *
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WSAL_Connector_ConnectorFactory.
 *
 * Abstract class used for create the connector, only MySQL is implemented.
 *
 * @todo Add other adapters.
 * @package Wsal
 */
abstract class WSAL_Connector_ConnectorFactory {

	/**
	 * Connector.
	 *
	 * @var array
	 */
	public static $connector;

	/**
	 * Default Connector.
	 *
	 * @var bool
	 */
	public static $defaultConnector;

	/**
	 * Adapter.
	 *
	 * @var string
	 */
	public static $adapter;

	/**
	 * Returns the a default WPDB connector for saving options
	 */
	public static function GetDefaultConnector() {
		return new WSAL_Connector_MySQLDB();
	}

	/**
	 * Returns a connector singleton
	 *
	 * @param array $config - Connection config.
	 * @param bool  $reset - True if reset.
	 * @return WSAL_Connector_ConnectorInterface
	 */
	public static function GetConnector( $config = null, $reset = false ) {
		if ( ! empty( $config ) ) {
			$connection_config = $config;
		} else {
			$connection_config = self::GetConfig();
		}

		// TO DO: Load connection config.
		if ( null == self::$connector || ! empty( $config ) || $reset ) {
			switch ( strtolower( $connection_config['type'] ) ) {
				// TO DO: Add other connectors.
				case 'mysql':
				default:
					// Use config.
					self::$connector = new WSAL_Connector_MySQLDB( $connection_config );
			}
		}
		return self::$connector;
	}

	/**
	 * Get the adapter config stored in the DB
	 *
	 * @return array|null adapter config
	 */
	public static function GetConfig() {
		$conf = new WSAL_Settings( WpSecurityAuditLog::GetInstance() );
		$type = $conf->GetAdapterConfig( 'adapter-type' );
		if ( empty( $type ) ) {
			return null;
		} else {
			return array(
				'type' => $conf->GetAdapterConfig( 'adapter-type' ),
				'user' => $conf->GetAdapterConfig( 'adapter-user' ),
				'password' => $conf->GetAdapterConfig( 'adapter-password' ),
				'name' => $conf->GetAdapterConfig( 'adapter-name' ),
				'hostname' => $conf->GetAdapterConfig( 'adapter-hostname' ),
				'base_prefix' => $conf->GetAdapterConfig( 'adapter-base-prefix' ),
			);
		}
	}

	/**
	 * Check the adapter config with a test connection.
	 *
	 * @param string $type - Adapter type.
	 * @param string $user - Adapter user.
	 * @param string $password - Adapter password.
	 * @param string $name - Adapter name.
	 * @param string $hostname - Adapter hostname.
	 * @param string $base_prefix - Adapter base_prefix.
	 * @return boolean true|false
	 */
	public static function CheckConfig( $type, $user, $password, $name, $hostname, $base_prefix ) {
		$result = false;
		$config = self::GetConfigArray( $type, $user, $password, $name, $hostname, $base_prefix );
		switch ( strtolower( $type ) ) {
			// TO DO: Add other connectors.
			case 'mysql':
			default:
				$test = new WSAL_Connector_MySQLDB( $config );
				$result = $test->TestConnection();
		}
		return $result;
	}

	/**
	 * Create array config.
	 *
	 * @param string $type - Adapter type.
	 * @param string $user - Adapter user.
	 * @param string $password - Adapter password.
	 * @param string $name - Adapter name.
	 * @param string $hostname - Adapter hostname.
	 * @param string $base_prefix - Adapter base_prefix.
	 * @return array config
	 */
	public static function GetConfigArray( $type, $user, $password, $name, $hostname, $base_prefix ) {
		return array(
			'type' => $type,
			'user' => $user,
			'password' => $password,
			'name' => $name,
			'hostname' => $hostname,
			'base_prefix' => $base_prefix,
		);
	}
}
