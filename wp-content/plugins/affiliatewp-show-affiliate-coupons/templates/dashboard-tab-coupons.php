<?php
$affwp_sad = affiliatewp_show_affiliate_coupons();
$coupons   = $affwp_sad->get_coupons();
?>

<div id="affwp-affiliate-dashboard-coupons" class="affwp-tab-content">

	<h4><?php _e( 'Coupons', 'affiliatewp-show-affiliate-coupons' ); ?></h4>

	<?php if ( $coupons ) : ?>
	<table class="affwp-table affwp-table-responsive">
		<thead>
			<tr>
				<th><?php _e( 'Coupon Code', 'affiliatewp-show-affiliate-coupons' ); ?></th>
				<th><?php _e( 'Amount', 'affiliatewp-show-affiliate-coupons' ); ?></th>
			</tr>
		</thead>
		<tbody>

		<?php if ( $coupons ) : ?>
			<?php foreach ( $coupons as $coupon ) :	?>
				<tr>
					<td data-th="<?php _e( 'Coupon Code', 'affiliatewp-show-affiliate-coupons' ); ?>"><?php echo $coupon['code']; ?></td>
					<td data-th="<?php _e( 'Amount', 'affiliatewp-show-affiliate-coupons' ); ?>"><?php echo $coupon['amount']; ?></td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>

		</tbody>
	</table>
	<?php else : ?>
		<p><?php _e( 'There are currently no discount codes to display.', 'affiliatewp-show-affiliate-coupons' ); ?></p>
	<?php endif; ?>

</div>