<?php
/**
 * Class: IP Filter
 *
 * IP Filter for search extension.
 *
 * @since 1.0.0
 * @package search-wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WSAL_AS_Filters_IpFilter
 *
 * @package search-wsal
 */
class WSAL_AS_Filters_IpFilter extends WSAL_AS_Filters_AbstractFilter {

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
		return __( 'IP', 'wp-security-audit-log' );
	}

	/**
	 * Method: Returns true if this filter has suggestions for this query.
	 *
	 * @param string $query - Part of query to check.
	 */
	public function IsApplicable( $query ) {
		$query = explode( ':', $query );

		if ( count( $query ) > 1 ) {
			// maybe IPv6?
			// TODO do IPv6 validation.
		}
		$query = explode( '.', $query[0] );

		if ( count( $query ) > 1 ) {
			// maybe IPv4?
			foreach ( $query as $part ) {
				if ( ! is_numeric( $part ) || $part < 0 || $part > 255 ) {
					return false;
				}
			}
			return true;
		}
		return false; // All validations failed.
	}

	/**
	 * Method: Get Prefixes.
	 */
	public function GetPrefixes() {
		return array(
			'ip',
		);
	}

	/**
	 * Method: Get Widgets.
	 */
	public function GetWidgets() {
		$wgt = new WSAL_AS_Filters_AutoCompleteWidget( $this, 'ip', 'IP' );
		$wgt->SetDataLoader( array( $this, 'GetMatchingIPs' ) );
		return array( $wgt );
	}

	public function GetMatchingIPs( WSAL_AS_Filters_AutoCompleteWidget $wgt ) {
		$tmp = new WSAL_Models_Meta();
		$ips = $tmp->getAdapter()->GetMatchingIPs();
		foreach ( $ips as $ip ) {
			$wgt->Add( $ip, $ip );
		}
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

		// IP search condition.
		$sql = "( EXISTS(SELECT 1 FROM $table_meta as meta WHERE meta.occurrence_id = $table_occ.id AND meta.name='ClientIP' AND find_in_set(meta.value, %s) > 0) )";

		// Check prefix.
		switch ( $prefix ) {
			case 'ip':
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

	/**
	 * Renders filter widgets.
	 */
	public function Render() {
		if ( $this->IsTitled ) {
			?><strong><?php echo $this->GetName(); ?></strong>
			<?php
		}
		foreach ( $this->GetWidgets() as $widget ) :
		?>
			<div class="wsal-as-filter-widget">
				<?php $widget->Render(); ?>
				<button id="wsal-add-ip-filter" class="wsal-add-button dashicons-before dashicons-plus"></button>
			</div>
			<?php
		endforeach;
	}
}
