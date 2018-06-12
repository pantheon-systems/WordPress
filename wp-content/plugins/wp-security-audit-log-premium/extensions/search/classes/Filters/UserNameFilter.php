<?php
/**
 * Class: Username Filter
 *
 * Username Filter for search extension.
 *
 * @since 1.0.0
 * @package search-wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WSAL_AS_Filters_UserFilter
 *
 * @package search-wsal
 */
class WSAL_AS_Filters_UserNameFilter extends WSAL_AS_Filters_AbstractFilter {

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
		return esc_html__( 'User', 'wp-security-audit-log' );
	}

	/**
	 * Method: Returns true if this filter has suggestions for this query.
	 *
	 * @param string $query - Part of query to check.
	 */
	public function IsApplicable( $query ) {
		global $wpdb;
		$args = array( esc_sql( $query ) . '%', esc_sql( $query ) . '%' );
		return ! ! $wpdb->count( 'SELECT COUNT(*) FROM wp_user WHERE name LIKE %s OR username LIKE %s', $args );
	}

	/**
	 * Method: Get Prefixes.
	 */
	public function GetPrefixes() {
		return array(
			'username',
		);
	}

	/**
	 * Method: Get Widgets.
	 */
	public function GetWidgets() {
		return array( new WSAL_AS_Filters_UserNameWidget( $this, 'username', 'Username' ) );
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

		switch ( $prefix ) {
			case 'username':
				$user = get_user_by( 'login', $value[0] );
				$user_id = $user ? $user->ID : -1;
				if ( -1 == $user_id ) {
					$user = get_user_by( 'slug', $value[0] );
					$user_id = $user ? $user->ID : -1;
				}

				$query->addORCondition(
					array(
						"( EXISTS(SELECT 1 FROM $table_meta as meta WHERE meta.occurrence_id = $table_occ.id AND meta.name='CurrentUserID' AND find_in_set(meta.value, %s) > 0 ) )" => $user_id,
						"( EXISTS(SELECT 1 FROM $table_meta as meta WHERE meta.occurrence_id = $table_occ.id AND meta.name='Username' AND find_in_set(replace(meta.value, '\"', ''), %s) > 0 ) )" => $value,
					)
				);

				break;
			default:
				throw new Exception( 'Unsupported filter "' . $prefix . '".' );
		}
	}
}
