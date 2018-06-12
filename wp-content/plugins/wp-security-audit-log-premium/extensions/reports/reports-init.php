<?php
/**
 * Extension: Reports
 *
 * Reports extension for wsal.
 *
 * @since 1.0.0
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Holds the name of the cache key if cache available
 */
define( 'WSAL_CACHE_KEY_2', '__NOTIF_CACHE__' );

/**
 * Class WSAL_Rep_Plugin
 *
 * @package report-wsal
 */
class WSAL_Rep_Plugin {

	/**
	 * Instance of WpSecurityAuditLog.
	 *
	 * @var object
	 */
	protected $wsal = null;

	/**
	 * Method: Constructor
	 *
	 * @since  1.0.0
	 */
	public function __construct() {
		// Function to hook at `wsal_init`.
		add_action( 'wsal_init', array( $this, 'wsal_init' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_action_links' ) );
	}

	/**
	 * Triggered when the main plugin is loaded.
	 *
	 * @param object $wsal - Instance of WpSecurityAuditLog.
	 * @see WpSecurityAuditLog::load()
	 */
	public function wsal_init( WpSecurityAuditLog $wsal ) {
		// Autoload files in /classes.
		$wsal->autoloader->Register( 'WSAL_Rep_', dirname( __FILE__ ) . '/classes' );

		// Initialize utility classes.
		$wsal->reporting = new stdClass();
		$wsal->reporting->common = new WSAL_Rep_Common( $wsal );
		$wsal->views->AddFromClass( 'WSAL_Rep_Views_Main' );
		$wsal->repPlugin = $this;
	}

	/**
	 * Add action links in the plugins page.
	 *
	 * @param array $links - Existing links.
	 * @return array all the links after merging the new
	 */
	public function add_action_links( $links ) {
		$new_links = array(
			'<a href="' . admin_url( 'admin.php?page=wsal-rep-views-main' ) . '">Generate Report</a>',
			'<a href="' . admin_url( 'admin.php?page=wsal-rep-views-main#tab-summary' ) . '">Email Summary Reports</a>',
		);
		return array_merge( $new_links, $links );
	}
}
