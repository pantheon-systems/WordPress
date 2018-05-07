<?php
/**
 * Filter: Post Type Filter
 *
 * Post type filter for search.
 *
 * @since   1.1.7
 * @package search-wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WSAL_AS_Filters_PostTypeFilter' ) ) :

	/**
	 * WSAL_AS_Filters_PostTypeFilter.
	 *
	 * Post type filter class.
	 *
	 * @since 1.1.7
	 */
	class WSAL_AS_Filters_PostTypeFilter extends WSAL_AS_Filters_AbstractFilter {

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
			return esc_html__( 'Post Type' );
		}

		/**
		 * Method: Get Prefixes.
		 *
		 * @since  1.1.7
		 */
		public function GetPrefixes() {
			return array(
				'posttype',
			);
		}

		/**
		 * Method: Returns true if this filter has suggestions for this query.
		 *
		 * @param string $query - Part of query to check.
		 * @since 1.1.7
		 */
		public function IsApplicable( $query ) {

			$args = array(
				'public'   => true,
			);
			$output     = 'names'; // names or objects, note names is the default
			$operator   = 'and'; // Conditions: and, or.
			$post_types = get_post_types( $args, $output, $operator );

			// Search for the post type in query from available post types.
			$key = array_search( $query, $post_types );

			if ( ! empty( $key ) ) {
				return true;
			} else {
				return false;
			}

		}

		/**
		 * Method: Get Widgets.
		 *
		 * @since  1.1.7
		 */
		public function GetWidgets() {
			// Intialize single select widget class.
			$widget = new WSAL_AS_Filters_SingleSelectWidget( $this, 'posttype', 'Post Type' );

			// Get the post types.
			$args = array(
				'public'   => true,
			);
			$output     = 'names'; // names or objects, note names is the default
			$operator   = 'and'; // Conditions: and, or.
			$post_types = get_post_types( $args, $output, $operator );

			// Search and remove attachment type.
			$key = array_search( 'attachment', $post_types, true );
			if ( false !== $key ) {
				unset( $post_types[ $key ] );
			}

			// Add select options to widget.
			foreach ( $post_types as $post_type ) {
				$widget->Add( ucwords( $post_type ), $post_type );
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

			// Post type search condition.
			$sql = "( EXISTS(SELECT 1 FROM $table_meta as meta WHERE meta.occurrence_id = $table_occ.id AND meta.name='PostType' AND find_in_set(meta.value, %s) > 0) )";

			// Check prefix.
			switch ( $prefix ) {
				case 'posttype':
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
