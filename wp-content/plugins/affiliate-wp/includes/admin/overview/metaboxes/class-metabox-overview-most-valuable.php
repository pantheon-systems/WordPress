<?php
namespace AffWP\Admin\Overview\Meta_Box;

use AffWP\Admin\Meta_Box;

/**
 * Implements a Most Valuable Affiliates meta box for the Overview screen.
 *
 * The meta box displays the "most valuable" affiliates, determined by showing the highest:
 *
 * - Earnings
 * - Referrals generated
 * - Visits generated
 *
 * @since 1.9
 * @see   \AffWP\Admin\Meta_Box
 */
class Most_Valuable_Affiliates extends Meta_Box implements Meta_Box\Base {

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
		$this->meta_box_id   = 'overview-most-valuable';
		$this->context       = 'secondary';
		$this->meta_box_name = __( 'Most Valuable Affiliates', 'affiliate-wp' );
	}

	/**
	 * Defines the content of the metabox.
	 *
	 * @return mixed content  The metabox content.
	 * @since  1.9
	 */
	public function content() {

		$affiliates = affiliate_wp()->affiliates->get_affiliates(
			/**
	 		 * Filter the get_affiliates() query.
	 		 *
	 		 * @param array The query arguments for get_affiliates().
	 		 *              By default, this query shows the five highest
	 		 *              earning affiliates, in descending order.
	 		 * @since 1.9
	 		 *
	 		 */
			apply_filters( 'affwp_overview_most_valuable_affiliates_query_args',
				array(
					'number'  => 5,
					'orderby' => 'earnings',
					'order'   => 'DESC'
				)
			)
		); ?>

		<table class="affwp_table">

			<thead>

				<tr>
					<th><?php _ex( 'Affiliate', 'Affiliate column table header', 'affiliate-wp' ); ?></th>
					<th><?php _ex( 'Earnings', 'Earnings column table header', 'affiliate-wp' ); ?></th>
					<th><?php _ex( 'Referrals', 'Referrals column table header', 'affiliate-wp' ); ?></th>
					<th><?php _ex( 'Visits', 'Visits column table header', 'affiliate-wp' ); ?></th>
				</tr>

			</thead>

			<tbody>
			<?php if( $affiliates ) : ?>
				<?php foreach( $affiliates as $affiliate  ) : ?>
					<tr>
						<td><?php echo affiliate_wp()->affiliates->get_affiliate_name( $affiliate->affiliate_id ); ?></td>
						<td><?php echo affwp_currency_filter( affwp_format_amount( $affiliate->earnings ) ); ?></td>
						<td><?php echo affwp_format_amount( absint( $affiliate->referrals ), false ); ?></td>
						<td><?php echo affwp_format_amount( absint( $affiliate->visits ), false ); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="4"><?php _e( 'No registered affiliates', 'affiliate-wp' ); ?></td>
				</tr>
			<?php endif; ?>
			</tbody>

		</table>
	<?php }
}
