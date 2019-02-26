<?php
/**
 * Affiliates Export Class
 *
 * This class handles exporting affiliate data.
 *
 * @package     AffiliateWP
 * @subpackage  Admin/Export
 * @copyright   Copyright (c) 2014, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.3
 */

use AffWP\Utils\Exporter;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Affiliate_WP_Export Class
 *
 * @since 1.3
 */
class Affiliate_WP_Affiliate_Export extends Affiliate_WP_Export implements Exporter\CSV {

	/**
	 * Our export type. Used for export-type specific filters/actions
	 * @var string
	 * @since 1.3
	 */
	public $export_type = 'affiliates';

	/**
	 * Status
	 * @var string
	 * @since 1.3
	 */
	public $status;

	/**
	 * Set the CSV columns
	 *
	 * @access public
	 * @since 1.3
	 * @return array $cols All the columns
	 */
	public function csv_cols() {
		$cols = array(
			'affiliate_id'    => __( 'Affiliate ID', 'affiliate-wp' ),
			'email'           => __( 'Email', 'affiliate-wp' ),
			'name'            => __( 'Name', 'affiliate-wp' ),
			'payment_email'   => __( 'Payment Email', 'affiliate-wp' ),
			'username'        => __( 'Username', 'affiliate-wp' ),
			'rate'            => __( 'Rate', 'affiliate-wp' ),
			'rate_type'       => __( 'Rate Type', 'affiliate-wp' ),
			'earnings'        => __( 'Earnings', 'affiliate-wp' ),
			'referrals'       => __( 'Referrals', 'affiliate-wp' ),
			'visits'          => __( 'Visits', 'affiliate-wp' ),
			'conversion_rate' => __( 'Conversion Rate', 'affiliate-wp' ),
			'status'          => __( 'Status', 'affiliate-wp' ),
			'date_registered' => __( 'Date Registered', 'affiliate-wp' )
		);
		return $cols;
	}

	/**
	 * Retrieves the data being exported.
	 *
	 * @access public
	 * @since  1.3
	 *
	 * @return array $data Data for Export
	 */
	public function get_data() {

		$args = array(
			'status' => $this->status,
			'number' => -1
		);

		$data       = array();
		$affiliates = affiliate_wp()->affiliates->get_affiliates( $args );

		if( $affiliates ) {

			foreach( $affiliates as $affiliate ) {

				$data[] = array(
					'affiliate_id'    => $affiliate->affiliate_id,
					'email'           => affwp_get_affiliate_email( $affiliate->affiliate_id ),
					'name'            => affwp_get_affiliate_name( $affiliate->affiliate_id ),
					'payment_email'   => affwp_get_affiliate_payment_email( $affiliate->affiliate_id ),
					'username'        => affwp_get_affiliate_login( $affiliate->affiliate_id ),
					'rate'            => affwp_get_affiliate_rate( $affiliate->affiliate_id ),
					'rate_type'       => affwp_get_affiliate_rate_type( $affiliate->affiliate_id ),
					'earnings'        => $affiliate->earnings,
					'referrals'       => $affiliate->referrals,
					'visits'          => $affiliate->visits,
					'conversion_rate' => affwp_get_affiliate_conversion_rate( $affiliate->affiliate_id ),
					'status'          => $affiliate->status,
					'date_registered' => $affiliate->date_i18n( 'datetime' ),
				);

			}

		}

		/** This filter is documented in includes/admin/tools/export/class-export.php */
		$data = apply_filters( 'affwp_export_get_data', $data );

		/** This filter is documented in includes/admin/tools/export/class-export.php */
		$data = apply_filters( 'affwp_export_get_data_' . $this->export_type, $data );

		return $data;
	}

}
