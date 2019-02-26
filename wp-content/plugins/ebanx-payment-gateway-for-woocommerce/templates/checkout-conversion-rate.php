<?php
/**
 * Checkout Conversion Rate.
 *
 * @author  EBANX.com
 * @package WooCommerce_EBANX/Templates
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="ebanx-payment-converted-amount" style="overflow: hidden">
	<span style="float: left; margin-bottom: 5px"><?php echo $exchange_rate_message; // phpcs:ignore WordPress.XSS.EscapeOutput ?></span>
	<span id="converted-amount" style="float: left;"><?php echo $message; // phpcs:ignore WordPress.XSS.EscapeOutput ?></span>
	<img
		class="ebanx-spinner" 
		src="<?php echo esc_url( admin_url( '/images/spinner-2x.gif' ) ); ?>" 
		width="20"
		height="20"
		style="display: none; margin-left: 10px; float: left;"
	/>
</div>
