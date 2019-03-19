<?php

class AffiliateWP_Lifetime_Commissions_Dashboard {

	public function __construct() {

		// Load CSS.
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );

		// Display the affiliate lifetime customers count on the Statistics page.
		add_action( 'affwp_affiliate_dashboard_after_campaign_stats', array( $this, 'stats' ) );

		// Add a th to the referrals table in the affiliate area.
		add_action( 'affwp_referrals_dashboard_th', array( $this, 'lifetime_referral_th' ) );

		// Add a td to the referrals table in the affiliate area.
		add_action( 'affwp_referrals_dashboard_td', array( $this, 'lifetime_referral_td' ) );
	}

	/**
	 * Frontend CSS
	 *
	 * @since 1.3
	 */
	public function frontend_scripts() {

		affwp_enqueue_style( 'dashicons', 'referrals' );

	}

	/**
	 * Display the affiliate lifetime customers count.
	 *
	 * @since 1.3
	 */
	public function stats( $affiliate_id ) {

		$lc_enabled = affiliate_wp_lifetime_commissions()->integrations->can_receive_lifetime_commissions( $affiliate_id );

		if ( ! $lc_enabled || $this->get_affiliate_lifetime_customers_count() === 0 ) {
			return;
		}

		?>
		<table class="affwp-table affwp-table-responsive">
			<thead>
				<tr>
					<th colspan="5"><?php _e( 'Lifetime Customers', 'affiliate-wp-lifetime-commissions' ); ?></th>
				</tr>
			</thead>

			<tbody>
				<tr>
					<td data-th="<?php _e( 'Lifetime Customers', 'affiliate-wp-lifetime-commissions' ); ?>" colspan="5"><?php echo $this->get_affiliate_lifetime_customers_count( $affiliate_id ); ?></td>
				</tr>
			</tbody>
		</table>

		<?php
	}

	/**
	 * Th for the lifetime referral column.
	 *
	 * @since 1.3
	 */
	public function lifetime_referral_th() {

		$affiliate_id = affwp_get_affiliate_id();
		$lc_enabled   = affiliate_wp_lifetime_commissions()->integrations->can_receive_lifetime_commissions( $affiliate_id );

		if ( ! $lc_enabled || $this->get_affiliate_lifetime_customers_count( $affiliate_id ) === 0 ) {
			return;
		}

		?>
		<th class="lifetime-commission"><?php _e( 'Lifetime Referral', 'affiliate-wp-lifetime-commissions' ); ?></th>
		<?php
	}

	/**
	 * Display icon to indicate lifetime referral.
	 *
	 * @since 1.3
	 */
	public function lifetime_referral_td( $referral ) {

		$affiliate_id = $referral->affiliate_id;
		$lc_enabled   = affiliate_wp_lifetime_commissions()->integrations->can_receive_lifetime_commissions( $affiliate_id );

		if ( ! $lc_enabled || $this->get_affiliate_lifetime_customers_count( $affiliate_id ) === 0 ) {
			return;
		}

		$custom = maybe_unserialize( $referral->custom );

		?>

		<td class="lifetime-referral" data-th="<?php _e( 'Lifetime Referral', 'affiliate-wp-lifetime-commissions' ); ?>">

			<?php if ( $custom && in_array( 'lifetime_referral', $custom ) ): ?>
				<span class="dashicons dashicons-yes" aria-label="<?php _e( 'Lifetime Referral', 'affiliate-wp-lifetime-commissions' ); ?>"></span>
			<?php endif; ?>

		</td>

		<?php

	}

	/**
	 * Get the lifetime customers count for an affiliate.
	 *
	 * @since 1.3
	 */
	public function get_affiliate_lifetime_customers_count( $affiliate_id = 0 ) {

		global $wpdb;

		$customers_count = 0;
		$table           = affiliate_wp()->customer_meta->table_name;
		$customer_ids    = $wpdb->get_col( $wpdb->prepare( "SELECT affwp_customer_id FROM {$table} WHERE meta_key = 'affiliate_id' AND meta_value = %d ORDER BY meta_id ASC;", $affiliate_id ) );
		$customer_ids    = array_map( 'absint', $customer_ids );

		if ( ! empty( $customer_ids ) ) {

			$customers_count = count( array_unique( $customer_ids ) );

		}

		return $customers_count;
	}

}
new AffiliateWP_Lifetime_Commissions_Dashboard;