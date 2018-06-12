<?php
/**
 * Abstract class used in the Logger.
 *
 * @see Loggers/Database.php
 * @package Wsal
 */
abstract class WSAL_AbstractLogger {

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
	 * @since  1.0.0
	 */
	public function __construct( WpSecurityAuditLog $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Log alert abstract.
	 *
	 * @param integer $type - Alert code.
	 * @param array   $data - Metadata.
	 * @param integer $date (Optional) - Created on.
	 * @param integer $siteid (Optional) - Site id.
	 * @param bool    $migrated (Optional) - Is migrated.
	 */
	public abstract function Log( $type, $data = array(), $date = null, $siteid = null, $migrated = false );
}
