<?php
namespace AffWP\Utils;

/**
 * Core class that implements privacy tools and controls for GDPR compliance.
 *
 * @since 2.2
 */
class Privacy_Tools {

	/**
	 * Set up hook callbacks for connecting to WordPress' privacy API.
	 *
	 * @since 2.2
	 */
	public function __construct() {
		add_filter( 'wp_privacy_personal_data_exporters', array( $this, 'register_exporters' ) );
		add_filter( 'wp_privacy_personal_data_erasers',   array( $this, 'register_data_erasers' ) );
	}

	/**
	 * Registers the privacy exporters.
	 *
	 * @since 2.2
	 *
	 * @param array $exporters Existing exporters.
	 * @return array Modified exporters.
	 */
	public function register_exporters( $exporters ) {

		$exporters[] = array(
			'exporter_friendly_name' => __( 'Affiliate Record', 'affiliate-wp' ),
			'callback'               => array( $this, 'affiliate_record_exporter' ),
		);

		$exporters[] = array(
			'exporter_friendly_name' => __( 'Affiliate Customer Record', 'affiliate-wp' ),
			'callback'               => array( $this, 'affiliate_customer_record_exporter' ),
		);

		return $exporters;

	}
	/**
	 * Registers the data erasers.
	 *
	 * @since 2.2
	 *
	 * @param array $erasers Existing erasers.
	 * @return array Modified erasers.
	 */
	public function register_data_erasers( $erasers ) {

		$erasers[] = array(
			'eraser_friendly_name' => __( 'Affiliate Record', 'affiliate-wp' ),
			'callback'             => array( $this, 'affiliate_record_eraser' ),
		);

		$erasers[] = array(
			'eraser_friendly_name' => __( 'Affiliate Customer Record', 'affiliate-wp' ),
			'callback'             => array( $this, 'affiliate_customer_record_eraser' ),
		);

		return $erasers;

	}

	/**
	 * Retrieves the affiliate record for the Privacy Data Exporter
	 *
	 * @since 2.2
	 *
	 * @param string $email_address Affiliate email address.
	 * @param int    $page          Page number.
	 * @return array Affiliate data to export.
	 */
	public function affiliate_record_exporter( $email_address, $page ) {

		$export_data = array();
		$user        = get_user_by( 'email', $email_address );

		if( $user ) {

			$affiliate = affwp_get_affiliate( $user->user_login );

			if ( ! empty( $affiliate->affiliate_id ) ) {

				$export_data = array(
					'group_id'    => 'affwp-affiliate-record',
					'group_label' => __( 'Affiliate Record', 'affiliate-wp' ),
					'item_id'     => "affwp-affiliate-record-{$affiliate->affiliate_id}",
					'data'        => array(
						array(
							'name'  => __( 'Customer ID', 'affiliate-wp' ),
							'value' => $affiliate->affiliate_id
						),
						array(
							'name'  => __( 'Primary Email', 'affiliate-wp' ),
							'value' => $user->user_email
						),
						array(
							'name'  => __( 'Payment Email', 'affiliate-wp' ),
							'value' => empty( $affiliate->payment_email ) ? $user->user_email : $affiliate->payment_email
						),
						array(
							'name'  => __( 'Name', 'affiliate-wp' ),
							'value' => affwp_get_affiliate_name( $affiliate->affiliate_id )
						),
						array(
							'name'  => __( 'Date Created', 'affiliate-wp' ),
							'value' => $affiliate->date
						),
					)
				);
			}
		}

		if( ! $user || empty( $affiliate->affiliate_id ) ) {

			$export_data = array(
				'group_id'    => 'affwp-affiliate-record',
				'group_label' => __( 'Affiliate Record', 'affiliate-wp' ),
				'item_id'     => "affwp-affiliate-record-$email_address",
				'data'        => array(
					array(
						'name'  => __( 'Customer ID', 'affiliate-wp' ),
						'value' => __( 'No records found', 'affiliate-wp' )
					)
				)
			);

		}

		return array( 'data' => array( $export_data ), 'done' => true );
	}

	/**
	 * Retrieves the affiliate record for the Privacy Data Exporter
	 *
	 * @since 2.2
	 *
	 * @param string $email_address Affiliate customer email address.
	 * @param int    $page          Page number.
	 * @return array Affiliate customer data to export.
	 */
	public  function affiliate_customer_record_exporter( $email_address, $page ) {

		$export_data = array();
		$customer    = affwp_get_customer( $email_address );

		if ( ! empty( $customer->customer_id ) ) {

			$export_data = array(
				'group_id'    => 'affwp-affiliate-customer-record',
				'group_label' => __( 'Affiliate Customer Record', 'affiliate-wp' ),
				'item_id'     => "affwp-affiliate-customer-record-{$customer->customer_id}",
				'data'        => array(
					array(
						'name'  => __( 'Customer ID', 'affiliate-wp' ),
						'value' => $customer->customer_id
					),
					array(
						'name'  => __( 'Email', 'affiliate-wp' ),
						'value' => $customer->email
					),
					array(
						'name'  => __( 'First Name', 'affiliate-wp' ),
						'value' => $customer->first_name
					),
					array(
						'name'  => __( 'Last Name', 'affiliate-wp' ),
						'value' => $customer->last_name
					),
					array(
						'name'  => __( 'Date Created', 'affiliate-wp' ),
						'value' => $customer->date
					),
				)
			);

		} else {

			$export_data = array(
				'group_id'    => 'affwp-affiliate-customer-record',
				'group_label' => __( 'Affiliate Customer Record', 'affiliate-wp' ),
				'item_id'     => "affwp-affiliate-customer-record-$email_address",
				'data'        => array(
					array(
						'name'  => __( 'Customer ID', 'affiliate-wp' ),
						'value' => __( 'No records found', 'affiliate-wp' )
					)
				)
			);

		}

		return array( 'data' => array( $export_data ), 'done' => true );
	}

	/**
	 * Erases an affiliate record.
	 *
	 * @since 2.2
	 *
	 * @param string $email_address Affiliate email address.
	 * @param int    $page          Page number.
	 * @return array Affiliate data to erase.
	 */
	public function affiliate_record_eraser( $email_address, $page ) {

		$user = get_user_by( 'email', $email_address );

		if( $user ) {

			$affiliate = affwp_get_affiliate( $user->user_login );

		}

		if ( empty( $email_address ) || empty( $affiliate->affiliate_id ) ) {
			return array(
				'items_removed'  => false,
				'items_retained' => false,
				'messages'       => array(),
				'done'           => true,
			);
		}

		$items_removed = affwp_delete_affiliate( $affiliate );

		return array(
			'items_removed'  => $items_removed,
			'items_retained' => false,
			'messages'       => array( __( 'Affiliate record has been deleted.', 'affiliate-wp' ) ),
			'done'           => true,
		);
	}

	/**
	 * Erases an affiliate customer record.
	 *
	 * @since 2.2
	 *
	 * @param string $email_address Affiliate customer email address.
	 * @param int    $page          Page number.
	 *
	 * @return array
	 */
	public  function affiliate_customer_record_eraser( $email_address, $page ) {

		$customer = affwp_get_customer( $email_address );

		if ( empty( $email_address ) || empty( $customer->customer_id ) ) {
			return array(
				'items_removed'  => false,
				'items_retained' => false,
				'messages'       => array(),
				'done'           => true,
			);
		}

		$items_removed = affwp_delete_customer( $customer );

		return array(
			'items_removed'  => $items_removed,
			'items_retained' => false,
			'messages'       => array( __( 'Affiliate customer record has been deleted.', 'affiliate-wp' ) ),
			'done'           => true,
		);
	}

}