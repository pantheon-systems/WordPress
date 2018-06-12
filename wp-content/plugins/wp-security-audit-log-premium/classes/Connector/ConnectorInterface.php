<?php
/**
 * Class: Connection Interface
 *
 * Interface used by the WSAL_Connector.
 *
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Interface used by the WSAL_Connector.
 *
 * @package Wsal
 */
interface WSAL_Connector_ConnectorInterface {

	/**
	 * Gets the adapter.
	 *
	 * @param string $class_name - Class name.
	 */
	public function getAdapter( $class_name );

	/**
	 * Get the connection.
	 */
	public function getConnection();

	/**
	 * Close the connection.
	 */
	public function closeConnection();

	/**
	 * Is installed?
	 */
	public function isInstalled();

	/**
	 * Can migrate?
	 */
	public function canMigrate();

	/**
	 * Install all.
	 */
	public function installAll();

	/**
	 * Uninstall all.
	 */
	public function uninstallAll();
}
