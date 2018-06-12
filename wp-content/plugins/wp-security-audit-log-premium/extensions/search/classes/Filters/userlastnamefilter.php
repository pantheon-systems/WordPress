<?php
/**
 * Filter: User Last Name Filter
 *
 * User last name filter for search.
 *
 * @since 	1.1.7
 * @package search-wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WSAL_AS_Filters_UserLastNameFilter' ) ) :

	/**
	 * WSAL_AS_Filters_UserLastNameFilter.
	 *
	 * User last name filter class.
	 *
	 * @since 1.1.7
	 */
	class WSAL_AS_Filters_UserLastNameFilter extends WSAL_AS_Filters_AbstractFilter {

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
		 *
		 * @since  1.1.7
		 */
		public function GetName() {
	        return esc_html__( 'User' );
	    }

	    /**
		 * Method: Get Prefixes.
		 *
		 * @since  1.1.7
		 */
	    public function GetPrefixes() {
	        return array(
	            'lastname',
	        );
	    }

	    /**
	     * Method: Returns true if this filter has suggestions for this query.
	     *
	     * @param string $query - Part of query to check.
	     * @since 1.1.7
	     */
	    public function IsApplicable( $query ) {

	    	global $wpdb;
	        $args = array( esc_sql( $query ) . '%', esc_sql( $query ) . '%' );
	        return ! ! $wpdb->count( 'SELECT COUNT(*) FROM wp_user WHERE name LIKE %s OR username LIKE %s', $args );

	    }

	    /**
		 * Method: Get Widgets.
		 *
		 * @since  1.1.7
		 */
	    public function GetWidgets() {
	        return array( new WSAL_AS_Filters_UserLastNameWidget( $this, 'lastname', 'Last Name' ) );
	    }

	    /**
	     * Allow this filter to change the DB query according to the search value.
	     *
	     * @param WSAL_DB_Query $query - Database query for selecting occurrenes.
	     * @param string 		$prefix - The filter name (filter string prefix).
	     * @param string 		$value - The filter value (filter string suffix).
	     * @throws object - Unsupported filter throw.
	     * @since 1.1.7
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
				case 'lastname':
					$users = array();
					foreach ( $value as $last_name ) {
						$users_array = get_users(
							array(
								'meta_key'      => 'last_name',
								'meta_value'    => $last_name,
								'fields'        => array( 'ID', 'user_login' ),
								'meta_compare'  => 'LIKE',
							)
						);

						foreach ( $users_array as $user ) {
							$users[] = $user;
						}
					}

	            	$usernames 	= array();
	            	$user_ids 	= array();
	                if ( ! empty( $users ) ) {
	                	foreach ( $users as $user ) {
	                		$usernames[]	= $user->user_login;
	                		$user_ids[]		= $user->ID;
	                	}
	                }

	                $query->addORCondition( array(
	                    "( EXISTS(SELECT 1 FROM $table_meta as meta WHERE meta.occurrence_id = $table_occ.id AND meta.name='Username' AND find_in_set(replace(meta.value, '\"', ''), %s) > 0 ) )" => $usernames,
						"( EXISTS(SELECT 1 FROM $table_meta as meta WHERE meta.occurrence_id = $table_occ.id AND meta.name='CurrentUserID' AND find_in_set(meta.value, %s) > 0 ) )" => $user_ids,
					) );

	                break;
	            default:
	                throw new Exception( 'Unsupported filter "' . $prefix . '".' );
	        }
	    }
	}

endif;
