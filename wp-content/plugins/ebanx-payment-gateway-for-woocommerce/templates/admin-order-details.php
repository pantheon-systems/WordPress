<div class="form-field form-field-wide">
	<h3><?php esc_html_e( 'EBANX Order Details', 'woocommerce-gateway-ebanx' ); ?></h3>
	<p>
		<?php esc_html_e( 'Dashboard Payment Link', 'woocommerce-gateway-ebanx' ); ?>
		<br>
		<a href="<?php echo esc_url( $dashboard_link ); ?>" class="ebanx-text-overflow" target="_blank"><?php echo esc_url( $dashboard_link ); ?></a>
	</p>
	<p>
		<?php esc_html_e( 'Payment Hash', 'woocommerce-gateway-ebanx' ); ?>
		<br>
		<input type="text" value="<?php echo esc_attr( $payment_hash ); ?>" onfocus="this.select();" onmouseup="return false;" readonly>
	</p>
	<?php if ( 'pending' === $order->status && $payment_checkout_url ) : ?>
		<p>
			<strong><?php esc_html_e( 'Customer Payment Link', 'woocommerce-gateway-ebanx' ); ?></strong>
			<br>
			<input type="text" value="<?php echo esc_url( $payment_checkout_url ); ?>" onfocus="this.select();" onmouseup="return false;" readonly>
		</p>
	<?php endif ?>
</div>


<style>
	.ebanx-text-overflow {
		text-overflow: ellipsis;
		white-space: nowrap;
		width: 100%;
		overflow: hidden;
		display: block;
	}
</style>
