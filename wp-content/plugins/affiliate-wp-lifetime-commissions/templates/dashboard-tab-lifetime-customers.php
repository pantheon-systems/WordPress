<?php
$affiliate_id = affwp_get_affiliate_id();
$customers    = affiliate_wp_lifetime_commissions()->integrations->get_customers_for_affiliate( $affiliate_id );
?>

<div id="affwp-affiliate-dashboard-lifetime-customers" class="affwp-tab-content">

	<h4><?php _e( 'Lifetime Customers', 'affiliate-wp-lifetime-commissions' ); ?></h4>

	<?php if ( $customers ) : ?>

		<table class="affwp-table affwp-table-responsive">
			<thead>
				<tr>
					<th class="customer-first-name"><?php _e( 'First Name', 'affiliate-wp-lifetime-commissions' ); ?></th>
					<th class="customer-last-name"><?php _e( 'Last Name', 'affiliate-wp-lifetime-commissions' ); ?></th>
					<th class="customer-email"><?php _e( 'Email', 'affiliate-wp-lifetime-commissions' ); ?></th>
				</tr>
			</thead>

			<tbody>
			<?php foreach ( $customers as $customer ) : ?>

				<?php if ( $customer ): ?>

					<tr>
						<td class="customer-first-name" data-th="<?php _e( 'First Name', 'affiliate-wp-lifetime-commissions' ); ?>"><?php echo $customer->first_name; ?></td>
						<td class="customer-last-name" data-th="<?php _e( 'Last Name', 'affiliate-wp-lifetime-commissions' ); ?>"><?php echo $customer->last_name; ?></td>
						<td class="customer-email" data-th="<?php _e( 'Email', 'affiliate-wp-lifetime-commissions' ); ?>"><?php echo $customer->email; ?></td>
					</tr>

				<?php endif; ?>

			<?php endforeach; ?>
			</tbody>
		</table>

	<?php else : ?>
		<p><?php _e( 'You don\'t have any lifetime customers yet.', 'affiliate-wp-lifetime-commissions' ); ?></p>
	<?php endif; ?>

</div>