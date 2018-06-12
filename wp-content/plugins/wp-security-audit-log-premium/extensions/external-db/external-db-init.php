<?php
/**
 * Extension: External DB
 *
 * External DB extension for WSAL.
 *
 * @since 1.0.0
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WSAL_Ext_Plugin
 *
 * @package Wsal
 */
class WSAL_Ext_Plugin {

	/**
	 * Instance of WpSecurityAuditLog.
	 *
	 * @var object
	 */
	protected $wsal = null;

	/**
	 * Method: Constructor
	 *
	 * @author Ashar Irfan
	 * @since  1.0.0
	 */
	public function __construct() {
		// Function to hook at `wsal_init`.
		add_action( 'wsal_init', array( $this, 'wsal_init' ) );
		if ( class_exists( 'WpSecurityAuditLog' ) ) {
			// register_deactivation_hook( __FILE__, array( $this, 'remove_config' ) );
			// Register freemius uninstall event.
			// wsal_freemius()->add_action( 'after_uninstall', array( $this, 'remove_config' ) );
		}
		add_filter( 'cron_schedules', array( $this, 'my_add_intervals' ) );
	}

	/**
	 * Triggered when the main plugin is loaded.
	 *
	 * @param object $wsal - Instance of WpSecurityAuditLog.
	 * @see WpSecurityAuditLog::load()
	 */
	public function wsal_init( WpSecurityAuditLog $wsal ) {
		$wsal->autoloader->Register( 'WSAL_Ext_', dirname( __FILE__ ) . '/classes' );
		$wsal_common_class = new WSAL_Ext_Common( $wsal );
		$wsal->wsalCommonClass = $wsal_common_class;
		$wsal->views->AddFromClass( 'WSAL_Ext_Settings' );
		$this->wsal = $wsal;

		// Cron job archiving.
		if ( $this->wsal->wsalCommonClass->IsArchivingEnabled() ) {
			if ( ! $this->wsal->wsalCommonClass->IsArchivingStop() ) {
				add_action( 'run_archiving', array( $this, 'archiving_alerts' ) );
				$every = strtolower( $this->wsal->wsalCommonClass->GetArchivingRunEvery() );
				if ( ! wp_next_scheduled( 'run_archiving' ) ) {
					wp_schedule_event( time(), $every, 'run_archiving' );
				}
			}
		}

		// Cron job mirroring.
		if ( $this->wsal->wsalCommonClass->IsMirroringEnabled() ) {
			if ( ! $this->wsal->wsalCommonClass->IsMirroringStop() ) {
				add_action( 'run_mirroring', array( $this, 'mirroring_alerts' ) );
				$every = strtolower( $this->wsal->wsalCommonClass->GetMirroringRunEvery() );
				if ( ! wp_next_scheduled( 'run_mirroring' ) ) {
					wp_schedule_event( time(), $every, 'run_mirroring' );
				}
			}
		}
	}

	/**
	 * Remove External DB config and recreate DB tables on WP.
	 */
	public function remove_config() {
		$wsalCommonClass = $this->wsal->wsalCommonClass;
		$wsalCommonClass->RemoveConfig();
		$wsalCommonClass->RecreateTables();
	}

	/**
	 * Archiving alerts
	 */
	public function archiving_alerts() {
		$this->wsal->wsalCommonClass->archiving_alerts();
	}

	/**
	 * Mirroring alerts
	 */
	public function mirroring_alerts() {
		$this->wsal->wsalCommonClass->mirroring_alerts();
	}

	/**
	 * Method: Add time intervals for scheduling.
	 *
	 * @param  array $schedules - Array of schedules.
	 * @return array
	 * @author Ashar Irfan
	 * @since  1.0.0
	 */
	public function my_add_intervals( $schedules ) {
		$schedules['fortyfiveminutes'] = array(
			'interval' => 2700,
			'display' => __( 'Every 45 minutes', 'wp-security-audit-log' ),
		);
		$schedules['thirtyminutes'] = array(
			'interval' => 1800,
			'display' => __( 'Every 30 minutes', 'wp-security-audit-log' ),
		);
		$schedules['tenminutes'] = array(
			'interval' => 600,
			'display' => __( 'Every 10 minutes', 'wp-security-audit-log' ),
		);
		$schedules['oneminute'] = array(
			'interval' => 60,
			'display' => __( 'Every 1 minute', 'wp-security-audit-log' ),
		);
		return $schedules;
	}
}
