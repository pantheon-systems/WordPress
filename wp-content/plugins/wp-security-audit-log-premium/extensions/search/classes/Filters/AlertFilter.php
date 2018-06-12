<?php
/**
 * Class: Alert Filter
 *
 * Filter for alert codes.
 *
 * @since 1.0.0
 * @package search-wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WSAL_AS_Filters_AlertFilter
 *
 * @package search-wsal
 */
class WSAL_AS_Filters_AlertFilter extends WSAL_AS_Filters_AbstractFilter {

	public function GetName() {
		return __( 'Alert', 'wp-security-audit-log' );
	}

	public function IsApplicable( $query ) {
		return strtolower( substr( trim( $query ), 0, 5 ) ) == 'alert';
	}

	public function GetPrefixes() {
		return array(
			'alert',
		);
	}

	public function GetWidgets() {
		$wgt = new WSAL_AS_Filters_SingleSelectWidget( $this, 'alert', 'Alert' );
		foreach ( WpSecurityAuditLog::GetInstance()->alerts->GetCategorizedAlerts() as $catg => $group ) {
			foreach ( $group as $subname => $alerts ) {
				// Skip CPTs and Pages alerts.
				if ( 'Custom Post Types' === $subname || 'Pages' === $subname ) {
					continue;
				}

				// Add Group.
				$grp = $wgt->AddGroup( $subname );
				foreach ( $alerts as $alert ) {
					$grp->Add( str_pad( $alert->type, 4, '0', STR_PAD_LEFT ) . ' - ' . $alert->desc, $alert->type );
				}
			}
		}
		return array( $wgt );
	}

	public function ModifyQuery( $query, $prefix, $value ) {
		switch ( $prefix ) {
			case 'alert':
				$query->addORCondition(
					array(
						'alert_id = %s' => $value,
					)
				);
				break;
			default:
				throw new Exception( 'Unsupported filter "' . $prefix . '".' );
		}
	}
}
