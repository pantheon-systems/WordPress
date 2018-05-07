<?php
/**
 * Extensions Manager Class
 *
 * Class file for extensions management.
 *
 * @since 3.0.0
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! class_exists( 'WSAL_Extension_Manager' ) ) :

	/**
	 * WSAL_Extension_Manager.
	 *
	 * Extension manager class.
	 *
	 * @since 3.0.0
	 */
	class WSAL_Extension_Manager {

		/**
		 * Extensions.
		 *
		 * @var array
		 */
		public $extensions;

		/**
		 * WSAL Instance.
		 *
		 * @var object
		 */
		public $_wsal;

		/**
		 * Method: Constructor.
		 *
		 * @param object $wsal - Instance of WpSecurityAuditLog.
		 * @since 3.0.0
		 */
		public function __construct( WpSecurityAuditLog $wsal ) {

			$this->_wsal = $wsal;

			// Include extension files.
			$this->include_extensions();

			// Initialize the extensions.
			$this->init();

		}

		/**
		 * Method: Include extensions.
		 *
		 * @since 3.0.0
		 */
		protected function include_extensions() {

			// Extensions for BASIC and above plans.
			if ( wsal_freemius()->is_plan__premium_only( 'starter' ) ) {
				/**
				 * Search.
				 *
				 * @since 3.0.0
				 */
				if ( file_exists( WSAL_BASE_DIR . '/extensions/search/search-init.php' ) ) {
					require_once( WSAL_BASE_DIR . '/extensions/search/search-init.php' );
				}

				/**
				 * Email Notifications.
				 *
				 * @since 3.0.0
				 */
				if ( file_exists( WSAL_BASE_DIR . '/extensions/email-notifications/email-notifications.php' ) ) {
					require_once( WSAL_BASE_DIR . '/extensions/email-notifications/email-notifications.php' );
				}
			}

			// Extensions for PROFESSIONAL and above plans.
			if ( wsal_freemius()->is_plan__premium_only( 'professional' ) ) {
				/**
				 * Reports
				 *
				 * @since 3.0.0
				 */
				if ( file_exists( WSAL_BASE_DIR . '/extensions/reports/reports-init.php' ) ) {
					require_once( WSAL_BASE_DIR . '/extensions/reports/reports-init.php' );
				}

				/**
				 * Users Sessions Management.
				 *
				 * @since 3.0.0
				 */
				if ( file_exists( WSAL_BASE_DIR . '/extensions/users-sessions-management/users-sessions-management.php' ) ) {
					require_once( WSAL_BASE_DIR . '/extensions/users-sessions-management/users-sessions-management.php' );
				}

				/**
				 * External DB
				 *
				 * @since 3.0.0
				 */
				if ( file_exists( WSAL_BASE_DIR . '/extensions/external-db/external-db-init.php' ) ) {
					require_once( WSAL_BASE_DIR . '/extensions/external-db/external-db-init.php' );
				}
			}

		}

		/**
		 * Method: Initialize the extensions.
		 *
		 * @since 3.0.0
		 */
		protected function init() {

			// Basic package extensions.
			if ( wsal_freemius()->is_plan__premium_only( 'starter' ) ) {

				// Search filters.
				if ( class_exists( 'WSAL_SearchExtension' ) ) {
					$this->extensions[] = new WSAL_SearchExtension();
				}

				// Email Notifications.
				if ( class_exists( 'WSAL_NP_Plugin' ) ) {
					$this->extensions[] = new WSAL_NP_Plugin();
				}
			}

			// Professional package extensions.
			if ( wsal_freemius()->is_plan__premium_only( 'professional' ) ) {

				// Reports.
				if ( class_exists( 'WSAL_Rep_Plugin' ) ) {
					$this->extensions[] = new WSAL_Rep_Plugin();
				}

				// Users Sessions Management.
				if ( class_exists( 'WSAL_User_Management_Plugin' ) ) {
					$this->extensions[] = new WSAL_User_Management_Plugin();
				}

				// External DB.
				if ( class_exists( 'WSAL_Ext_Plugin' ) ) {
					$this->extensions[] = new WSAL_Ext_Plugin();
				}
			}

		}

		/**
		 * Method: Activation hook function of Sessions Management add-on.
		 *
		 * @since 3.0
		 */
		public function activate_sessions_management() {
			if ( class_exists( 'WpSecurityAuditLog' ) && class_exists( 'WSAL_User_Management_Plugin' ) ) {
				$users_sessions = new WSAL_User_Management_Plugin();
				$users_sessions->destroy_on_activation();
			}
		}

	}

endif;
