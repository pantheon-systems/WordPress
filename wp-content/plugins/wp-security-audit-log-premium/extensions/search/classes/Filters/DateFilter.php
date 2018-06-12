<?php
/**
 * Class: Date Filter
 *
 * Date filter for search extension.
 *
 * @since 1.0.0
 * @package search-wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WSAL_AS_Filters_DateFilter
 *
 * @package search-wsal
 */
class WSAL_AS_Filters_DateFilter extends WSAL_AS_Filters_AbstractFilter {

	public function GetName() {
		return __( 'Date', 'wp-security-audit-log' );
	}

	public function IsApplicable( $query ) {
		return false;
	}

	public function GetPrefixes() {
		return array(
			'from',
			'to',
		);
	}

	public function GetWidgets() {
		return array(
			new WSAL_AS_Filters_DateWidget( $this, 'from', 'From' ),
			new WSAL_AS_Filters_DateWidget( $this, 'to', 'To' ),
		);
	}

	public function ModifyQuery( $query, $prefix, $value ) {
		$date_format = WpSecurityAuditLog::GetInstance()->settings->GetDateFormat();
		$date = DateTime::createFromFormat( $date_format, $value[0] );
		$date->setTime( 0, 0 ); // Reset time to 00:00:00.
		$dateString = $date->format( 'U' );
		switch ( $prefix ) {
			case 'from':
				$query->addCondition( 'created_on >= %s', $dateString );
				break;
			case 'to':
				$query->addCondition( 'created_on <= %s', strtotime( '+1 day -1 minute', $dateString ) );
				break;
			default:
				throw new Exception( 'Unsupported filter "' . $prefix . '".' );
		}
	}
}
