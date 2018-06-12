<?php
/**
 * Filter: User Role Filter
 *
 * User Role filter for search.
 *
 * @since 3.1
 * @package wsal/search
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WSAL_AS_Filters_UserRoleFilter' ) ) :

	/**
	 * WSAL_AS_Filters_UserRoleFilter.
	 *
	 * User Role filter class.
	 *
	 * @since 3.1
	 */
	class WSAL_AS_Filters_UserRoleFilter extends WSAL_AS_Filters_AbstractFilter {

		/**
		 * Instance of WpSecurityAuditLog.
		 *
		 * @var WpSecurityAuditLog
		 */
		public $wsal;

		/**
		 * Method: Constructor.
		 *
		 * @param object $search_wsal – Instance of main plugin.
		 * @since 3.1.0
		 */
		public function __construct( $search_wsal ) {
			$this->wsal = $search_wsal->wsal;
		}

		/**
		 * Method: Get Name.
		 */
		public function GetName() {
			return esc_html__( 'User Role', 'wp-security-audit-log' );
		}

		/**
		 * Method: Get Prefixes.
		 */
		public function GetPrefixes() {
			return array(
				'userrole',
			);
		}

		/**
		 * Method: Returns true if this filter has suggestions for this query.
		 *
		 * @param string $query - Part of query to check.
		 */
		public function IsApplicable( $query ) {
			// Get WP User Roles.
			$wp_user_roles = wp_roles()->roles;
			$user_roles = array();

			foreach ( $wp_user_roles as $role => $details ) {
				$user_roles[ $role ] = translate_user_role( $details['name'] );
			}

			// Search for the post status in query from available post statuses.
			$key = array_search( $query, $user_roles );

			if ( ! empty( $key ) ) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Method: Get Widgets.
		 */
		public function GetWidgets() {
			// Intialize single select widget class.
			$widget = new WSAL_AS_Filters_SingleSelectWidget( $this, 'userrole', 'User Role' );

			// Get WP User Roles.
			$wp_user_roles = wp_roles()->roles;
			$user_roles = array();
			foreach ( $wp_user_roles as $role => $details ) {
				$user_roles[ $role ] = translate_user_role( $details['name'] );
			}

			// Add select options to widget.
			foreach ( $user_roles as $key => $role ) {
				$widget->Add( $role, $role );
			}
			return array( $widget );
		}

		/**
		 * Allow this filter to change the DB query according to the search value.
		 *
		 * @param WSAL_DB_Query $query - Database query for selecting occurrenes.
		 * @param string        $prefix - The filter name (filter string prefix).
		 * @param string        $value - The filter value (filter string suffix).
		 * @throws Exception - Unsupported filter throw.
		 */
		public function ModifyQuery( $query, $prefix, $value ) {
			// Get DB connection array.
			$connection = $this->wsal->getConnector()->getAdapter( 'Occurrence' )->get_connection();
			$connection->set_charset( $connection->dbh, 'utf8mb4', 'utf8mb4_general_ci' );

			// Tables.
			$meta = new WSAL_Adapters_MySQL_Meta( $connection );
			$table_meta = $meta->GetTable(); // Metadata.
			$occurrence = new WSAL_Adapters_MySQL_Occurrence( $connection );
			$table_occ = $occurrence->GetTable(); // Occurrences.

			// User role search condition.
			$sql = "( EXISTS(SELECT 1 FROM $table_meta as meta WHERE meta.occurrence_id = $table_occ.id AND meta.name='CurrentUserRoles' AND replace(replace(replace(meta.value, ']', ''), '[', ''), '\\'', '') REGEXP %s) )";

			switch ( $prefix ) {
				case 'userrole':
					$query->addORCondition(
						array(
							$sql => $value,
						)
					);
					break;
				default:
					throw new Exception( 'Unsupported filter "' . $prefix . '".' );
			}
		}

	}

endif;
