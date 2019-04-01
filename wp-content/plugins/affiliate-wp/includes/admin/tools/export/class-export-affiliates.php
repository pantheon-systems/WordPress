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

				/**
				 * Filters an individual line of affiliate data to be exported.
				 *
				 * @since 2.1.17
				 *
				 * @param array           $affiliate_data {
				 *     Single line of exported affiliate data
				 *
				 *     @type int    $affiliate_id      Affiliate ID.
				 *     @type string $email             Affiliate email.
				 *     @type string $name              Affiliate name.
				 *     @type string $payment_email     Affiliate payment email.
				 *     @type string $username          Affiliate username.
				 *     @type string $rate              Affiliate referral rate.
				 *     @type string $rate_type         Affiliate rate type.
				 *     @type string $earnings          Affiliate earnings.
				 *     @type string $referrals         Number of referrals.
				 *     @type string $visits            Number of visits.
				 *     @type string $conversion_rate   Affiliate conversion rate.
				 *     @type string $status            Affiliate status.
				 *     @type float  $date_registered   Date the affiliate was registered.
				 * }
				 * @param \AffWP\Affiliate $affiliate Affiliate object.
				 */
				$affiliate_data = apply_filters( 'affwp_affiliate_export_get_data_line', array(
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
				), $affiliate );

				// Add slashing.
				$data[] = array_map( function( $column ) {
					return addslashes( preg_replace( "/\"/","'", $column ) );
				}, $affiliate_data );

				unset( $affiliate_data );
			}

		}

		/** This filter is documented in includes/admin/tools/export/class-export.php */
		$data = apply_filters( 'affwp_export_get_data', $data );

		/** This filter is documented in includes/admin/tools/export/class-export.php */
		$data = apply_filters( 'affwp_export_get_data_' . $this->export_type, $data );

		return $data;
	}

}
