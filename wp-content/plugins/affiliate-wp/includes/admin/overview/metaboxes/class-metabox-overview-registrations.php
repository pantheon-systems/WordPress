<?php
namespace AffWP\Admin\Overview\Meta_Box;

use AffWP\Admin\Meta_Box;

/**
 * Implements an Affiliate Registrations meta box for the Overview screen.
 *
 * The meta box displays recent affiliate registrations.
 *
 * @since 1.9
 * @see   \AffWP\Admin\Meta_Box
 */
class Affiliate_Registrations extends Meta_Box implements Meta_Box\Base {

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
		$this->meta_box_name = __( 'Latest Affiliate Registrations', 'affiliate-wp' );
		$this->meta_box_id   = 'overview-registrations';
		$this->context       = 'primary';
	}

	/**
	 * Displays the content of the metabox.
	 *
	 * @return mixed content  The metabox content.
	 * @since  1.9
	 */
	public function content() { ?>

		<?php $affiliates = affiliate_wp()->affiliates->get_affiliates(
			/**
			 * Filter the get_affiliates() query.
			 *
			 * @param array The query arguments for get_affiliates().
			 *              By default, this query shows the five
			 *              most recent affiliate registrations.
			 * @since 1.9
			 *
			 */
			apply_filters( 'affwp_overview_latest_affiliate_registrations',
				array(
				'number' => 5
				)
			)
		); ?>
		<table class="affwp_table">

			<thead>

				<tr>
					<th><?php _ex( 'Affiliate', 'Affiliate column table header', 'affiliate-wp' ); ?></th>
					<th><?php _ex( 'Status', 'Status column table header', 'affiliate-wp' ); ?></th>
					<th><?php _ex( 'Actions', 'Actions column table header', 'affiliate-wp' ); ?></th>
				</tr>

			</thead>

			<tbody>
				<?php if( $affiliates ) : ?>
					<?php foreach( $affiliates as $affiliate  ) : ?>
						<tr>
							<td><?php echo affiliate_wp()->affiliates->get_affiliate_name( $affiliate->affiliate_id ); ?></td>
							<td><?php echo affwp_get_affiliate_status_label( $affiliate ); ?></td>
							<td>
								<?php
								if( 'pending' == $affiliate->status ) {
									echo affwp_admin_link( 'affiliates', __( 'Review', 'affiliate-wp' ), array( 'action' => 'review_affiliate', 'affiliate_id' => $affiliate->affiliate_id ) );
								} else {
									echo affwp_admin_link( 'affiliates', __( 'View Report', 'affiliate-wp' ), array( 'action' => 'view_affiliate', 'affiliate_id' => $affiliate->affiliate_id ) );
								}
								?>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
					<tr>
						<td colspan="3"><?php _e( 'No affiliate registrations yet', 'affiliate-wp' ); ?></td>
					</tr>
				<?php endif; ?>
			</tbody>

		</table>
<?php }
}
