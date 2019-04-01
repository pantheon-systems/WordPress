<?php
$affiliate_id = affwp_get_affiliate_id();
?>
<div id="affwp-affiliate-dashboard-referral-counts" class="affwp-tab-content">

	<h4><?php _e( 'Statistics', 'affiliate-wp' ); ?></h4>

	<table class="affwp-table affwp-table-responsive">
		<thead>
			<tr>
				<th><?php _e( 'Unpaid Referrals', 'affiliate-wp' ); ?></th>
				<th><?php _e( 'Paid Referrals', 'affiliate-wp' ); ?></th>
				<th><?php _e( 'Visits', 'affiliate-wp' ); ?></th>
				<th><?php _e( 'Conversion Rate', 'affiliate-wp' ); ?></th>
			</tr>
		</thead>

		<tbody>
			<tr>
				<td data-th="<?php _e( 'Unpaid Referrals', 'affiliate-wp' ); ?>"><?php echo affwp_count_referrals( $affiliate_id, 'unpaid' ); ?></td>
				<td data-th="<?php _e( 'Paid Referrals', 'affiliate-wp' ); ?>"><?php echo affwp_count_referrals( $affiliate_id, 'paid' ); ?></td>
				<td data-th="<?php _e( 'Visits', 'affiliate-wp' ); ?>"><?php echo affwp_count_visits( $affiliate_id ); ?></td>
				<td data-th="<?php _e( 'Conversion Rate', 'affiliate-wp' ); ?>"><?php echo affwp_get_affiliate_conversion_rate( $affiliate_id ); ?></td>
			</tr>
		</tbody>
	</table>

	<?php
	/**
	 * Fires immediately after stats counts in the affiliate area.
     *
  	 * @param int $affiliate_id Affiliate ID of the currently logged-in affiliate.
	 */
	do_action( 'affwp_affiliate_dashboard_after_counts', $affiliate_id );
	?>

</div>

<div id="affwp-affiliate-dashboard-earnings-stats" class="affwp-tab-content">
	<table class="affwp-table affwp-table-responsive">
		<thead>
			<tr>
				<th><?php _e( 'Unpaid Earnings', 'affiliate-wp' ); ?></th>
				<th><?php _e( 'Paid Earnings', 'affiliate-wp' ); ?></th>
				<th><?php _e( 'Commission Rate', 'affiliate-wp' ); ?></th>
			</tr>
		</thead>

		<tbody>
			<tr>
				<td data-th="<?php _e( 'Unpaid Earnings', 'affiliate-wp' ); ?>"><?php echo affwp_get_affiliate_unpaid_earnings( $affiliate_id, true ); ?></td>
				<td data-th="<?php _e( 'Paid Earnings', 'affiliate-wp' ); ?>"><?php echo affwp_get_affiliate_earnings( $affiliate_id, true ); ?></td>
				<td data-th="<?php _e( 'Commission Rate', 'affiliate-wp' ); ?>"><?php echo affwp_get_affiliate_rate( $affiliate_id, true ); ?></td>
			</tr>
		</tbody>
	</table>

	<?php
	/**
	 * Fires immediately after earnings stats in the affiliate area.
     *
  	 * @param int $affiliate_id Affiliate ID of the currently logged-in affiliate.
	 */
	do_action( 'affwp_affiliate_dashboard_after_earnings', $affiliate_id );
	?>

</div>

<div id="affwp-affiliate-dashboard-campaign-stats" class="affwp-tab-content">
	<table class="affwp-table affwp-table-responsive">
		<thead>
			<tr>
				<th><?php _e( 'Campaign', 'affiliate-wp' ); ?></th>
				<th><?php _e( 'Visits', 'affiliate-wp' ); ?></th>
				<th><?php _e( 'Unique Links', 'affiliate-wp' ); ?></th>
				<th><?php _e( 'Converted', 'affiliate-wp' ); ?></th>
				<th><?php _e( 'Conversion Rate', 'affiliate-wp' ); ?></th>
			</tr>
		</thead>

		<tbody>
			<?php if( $campaigns = affwp_get_affiliate_campaigns( $affiliate_id ) ) : ?>
				<?php foreach( $campaigns as $campaign ) : ?>
					<tr>
						<td data-th="<?php _e( 'Campaign', 'affiliate-wp' ); ?>"><?php echo ! empty( $campaign->campaign ) ? esc_html( $campaign->campaign ) : __( 'None set', 'affiliate-wp' ); ?></td>
						<td data-th="<?php _e( 'Visits', 'affiliate-wp' ); ?>"><?php echo esc_html( $campaign->visits ); ?></td>
						<td data-th="<?php _e( 'Unique Links', 'affiliate-wp' ); ?>"><?php echo esc_html( $campaign->unique_visits ); ?></td>
						<td data-th="<?php _e( 'Converted', 'affiliate-wp' ); ?>"><?php echo esc_html( $campaign->referrals ); ?></td>
						<td data-th="<?php _e( 'Conversion Rate', 'affiliate-wp' ); ?>"><?php echo esc_html( affwp_format_amount( $campaign->conversion_rate ) ); ?>%</td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td class="affwp-table-no-data" data-th="<?php _e( 'Campaigns', 'affiliate-wp' ); ?>" colspan="5"><?php _e( 'You have no referrals or visits that included a campaign name.', 'affiliate-wp' ); ?></td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>

	<?php
	/**
	 * Fires immediately after campaign stats in the affiliate area.
     *
  	 * @param int $affiliate_id Affiliate ID of the currently logged-in affiliate.
	 */
	do_action( 'affwp_affiliate_dashboard_after_campaign_stats', $affiliate_id );
	?>

</div>
