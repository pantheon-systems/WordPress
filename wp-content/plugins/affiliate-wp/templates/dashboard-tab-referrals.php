<?php
$affiliate_id = affwp_get_affiliate_id();
?>

<div id="affwp-affiliate-dashboard-referrals" class="affwp-tab-content">

	<h4><?php _e( 'Referrals', 'affiliate-wp' ); ?></h4>

	<?php
	$per_page  = 30;
	$page      = affwp_get_current_page_number();
	$pages     = absint( ceil( affwp_count_referrals( $affiliate_id ) / $per_page ) );
	$referrals = affiliate_wp()->referrals->get_referrals(
		array(
			'number'       => $per_page,
			'offset'       => $per_page * ( $page - 1 ),
			'affiliate_id' => $affiliate_id,
			'status'       => array( 'paid', 'unpaid', 'rejected' ),
		)
	);
	?>

	<?php
	/**
	 * Fires before the referrals dashbaord data able within the referrals template.
	 *
	 * @param int $affiliate_id Affiliate ID.
	 */
	do_action( 'affwp_referrals_dashboard_before_table', $affiliate_id );
	?>

	<table id="affwp-affiliate-dashboard-referrals" class="affwp-table affwp-table-responsive">
		<thead>
			<tr>
				<th class="referral-amount"><?php _e( 'Reference', 'affiliate-wp' ); ?></th>
				<th class="referral-amount"><?php _e( 'Amount', 'affiliate-wp' ); ?></th>
				<th class="referral-description"><?php _e( 'Description', 'affiliate-wp' ); ?></th>
				<th class="referral-status"><?php _e( 'Status', 'affiliate-wp' ); ?></th>
				<th class="referral-date"><?php _e( 'Date', 'affiliate-wp' ); ?></th>
				<?php
				/**
				 * Fires in the dashboard referrals template, within the table header element.
				 */
				do_action( 'affwp_referrals_dashboard_th' );
				?>
			</tr>
		</thead>

		<tbody>
			<?php if ( $referrals ) : ?>

				<?php foreach ( $referrals as $referral ) : ?>
					<tr>
						<td class="referral-reference" data-th="<?php _e( 'Reference', 'affiliate-wp' ); ?>"><?php echo $referral->reference; ?></td>
						<td class="referral-amount" data-th="<?php _e( 'Amount', 'affiliate-wp' ); ?>"><?php echo affwp_currency_filter( affwp_format_amount( $referral->amount ) ); ?></td>
						<td class="referral-description" data-th="<?php _e( 'Description', 'affiliate-wp' ); ?>"><?php echo wp_kses_post( nl2br( $referral->description ) ); ?></td>
						<td class="referral-status <?php echo $referral->status; ?>" data-th="<?php _e( 'Status', 'affiliate-wp' ); ?>"><?php echo affwp_get_referral_status_label( $referral ); ?></td>
						<td class="referral-date" data-th="<?php _e( 'Date', 'affiliate-wp' ); ?>"><?php echo esc_html( $referral->date_i18n( 'datetime' ) ); ?></td>
						<?php
						/**
						 * Fires within the table data of the dashboard referrals template.
						 *
						 * @param \AffWP\Referral $referral Referral object.
						 */
						do_action( 'affwp_referrals_dashboard_td', $referral ); ?>
					</tr>
				<?php endforeach; ?>

			<?php else : ?>

				<tr>
					<td class="affwp-table-no-data" colspan="5"><?php _e( 'You have not made any referrals yet.', 'affiliate-wp' ); ?></td>
				</tr>

			<?php endif; ?>
		</tbody>
	</table>

	<?php
	/**
	 * Fires after the data table within the affiliate area referrals template.
	 *
	 * @param int $affiliate_id Affiliate ID.
	 */
	do_action( 'affwp_referrals_dashboard_after_table', $affiliate_id );
	?>

	<?php if ( $pages > 1 ) : ?>

		<p class="affwp-pagination">
			<?php
			echo paginate_links(
				array(
					'current'      => $page,
					'total'        => $pages,
					'add_fragment' => '#affwp-affiliate-dashboard-referrals',
					'add_args'     => array(
						'tab' => 'referrals',
					),
				)
			);
			?>
		</p>

	<?php endif; ?>

</div>
