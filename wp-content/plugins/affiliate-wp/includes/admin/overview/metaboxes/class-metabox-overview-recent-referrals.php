<?php
namespace AffWP\Admin\Overview\Meta_Box;

use AffWP\Admin\Meta_Box;

/**
 * Implements a Recent Referrals meta box for the Overview screen.
 *
 * The meta box displays recent referrals.
 *
 * @since 1.9
 * @see   \AffWP\Admin\Meta_Box
 */
class Recent_Referrals extends Meta_Box implements Meta_Box\Base {

	/**
	 * Initialize.
	 *
	 * Define the meta box name, meta box id,
	 * and the action on which to hook the meta box here.
	 *
	 * Example:
	 *
	 * $this->action        = 'affwp_overview_meta_boxes';
	 * $this->meta_box_name = __( 'Name of the meta box', 'affiliate-wp' );
	 *
	 * @access  public
	 * @return  void
	 * @since   1.9
	 */
	public function init() {
		$this->action        = 'affwp_overview_meta_boxes';
		$this->meta_box_name = __( 'Recent Referrals', 'affiliate-wp' );
		$this->meta_box_id   = 'overview-recent-referrals';
		$this->context       = 'secondary';
	}

	/**
	 * Displays the content of the metabox.
	 *
	 * @return mixed content  The metabox content.
	 * @since  1.9
	 */
	public function content() {

		$referrals = affiliate_wp()->referrals->get_referrals(
			/**
	 		 * Filter the get_referrals() query.
	 		 *
	 		 * @param array The query arguments for get_referrals().
	 		 *              By default, this query shows the five
	 		 *              most recent unpaid referrals.
	 		 * @since 1.9
	 		 *
	 		 */
			apply_filters( 'affwp_overview_recent_referrals',
				array(
					'number' => 5,
					'status' => 'unpaid'
				)
			)
		); ?>

		<table class="affwp_table">

			<thead>

				<tr>
					<th><?php _ex( 'Affiliate', 'Affiliate column table header', 'affiliate-wp' ); ?></th>
					<th><?php _ex( 'Amount', 'Amount column table header', 'affiliate-wp' ); ?></th>
					<th><?php _ex( 'Description', 'Description column table header', 'affiliate-wp' ); ?></th>
				</tr>

			</thead>

			<tbody>
			<?php if( $referrals ) : ?>
				<?php foreach( $referrals as $referral  ) : ?>
					<tr>
						<td><?php echo affiliate_wp()->affiliates->get_affiliate_name( $referral->affiliate_id ); ?></td>
						<td><?php echo affwp_currency_filter( affwp_format_amount( $referral->amount ) ); ?></td>
						<td><?php echo ! empty( $referral->description ) ? esc_html( $referral->description ) : ''; ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="3"><?php _e( 'No referrals recorded yet', 'affiliate-wp' ); ?></td>
				</tr>
			<?php endif; ?>
			</tbody>

		</table>
	<?php }
}
