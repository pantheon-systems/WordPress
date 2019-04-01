<?php
namespace AffWP\Admin\Overview\Meta_Box;

use AffWP\Admin\Meta_Box;

/**
 * Implements a Totals meta box for the Overview screen.
 *
 * The meta box displays an overview of recent affiliate
 * earnings activity, and related totals during
 * various date ranges.
 *
 * @since 1.9
 * @see   \AffWP\Admin\Meta_Box
 */
class Totals extends Meta_Box implements Meta_Box\Base {

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
		$this->meta_box_name = __( 'Totals', 'affiliate-wp' );
		$this->meta_box_id   = 'overview-totals';
		$this->context       = 'primary';
	}

	/**
	 * Displays the content of the metabox.
	 *
	 * @return mixed content The metabox content.
	 * @since  1.9
	 */
	public function content() { ?>

		<table class="affwp_table">

			<thead>

				<tr>

					<th><?php _ex( 'Paid earnings', 'Paid earnings column table header', 'affiliate-wp' ); ?></th>
					<th><?php _ex( 'Paid earnings this month', 'Paid earnings this month column table header', 'affiliate-wp' ); ?></th>
					<th><?php _ex( 'Paid earnings today', 'Paid earnings today column table header', 'affiliate-wp' ); ?></th>

				</tr>

			</thead>

			<tbody>

				<tr>
					<td><?php echo affiliate_wp()->referrals->paid_earnings(); ?></td>
					<td><?php echo affiliate_wp()->referrals->paid_earnings( 'month' ); ?></td>
					<td><?php echo affiliate_wp()->referrals->paid_earnings( 'today' ); ?></td>
				</tr>

			</tbody>

		</table>

		<table class="affwp_table">

			<thead>

				<tr>

					<th><?php _ex( 'Unpaid referrals', 'Unpaid referrals column table header', 'affiliate-wp' ); ?></th>
					<th><?php _ex( 'Unpaid referrals this month', 'Unpaid referrals this month column table header', 'affiliate-wp' ); ?></th>
					<th><?php _ex( 'Unpaid referrals today', 'Unpaid referrals today column table header', 'affiliate-wp' ); ?></th>

				</tr>

			</thead>

			<tbody>

				<tr>
					<td><?php echo affiliate_wp()->referrals->unpaid_count(); ?></td>
					<td><?php echo affiliate_wp()->referrals->unpaid_count( 'month' ); ?></td>
					<td><?php echo affiliate_wp()->referrals->unpaid_count( 'today' ); ?></td>
				</tr>

			</tbody>

		</table>
		<table class="affwp_table">

			<thead>

				<tr>

					<th><?php _ex( 'Unpaid earnings', 'Unpaid earnings column table header', 'affiliate-wp' ); ?></th>
					<th><?php _ex( 'Unpaid earnings this month', 'Unpaid earnings this month', 'affiliate-wp' ); ?></th>
					<th><?php _ex( 'Unpaid earnings today', 'Unpaid earnings today column table header', 'affiliate-wp' ); ?></th>

				</tr>

			</thead>

			<tbody>

				<tr>
					<td><?php echo affiliate_wp()->referrals->unpaid_earnings(); ?></td>
					<td><?php echo affiliate_wp()->referrals->unpaid_earnings( 'month' ); ?></td>
					<td><?php echo affiliate_wp()->referrals->unpaid_earnings( 'today' ); ?></td>
				</tr>

			</tbody>

		</table>
<?php }
}
