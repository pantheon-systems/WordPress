<?php
/**
 * Notice: Currency not supported.
 *
 * @package WooCommerce_EBANX/Admin/Notices
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="error inline">
	<p><strong><?php esc_html_e( 'EBANX Disabled', 'woocommerce-gateway-ebanx' ); ?></strong>: <?php printf( wp_kses( __( 'Currency %s is not supported. Works only with Brazilian Real.', 'woocommerce-gateway-ebanx' ), array( 'code' => array() ) ), '<code>' . esc_html( get_woocommerce_currency() ) . '</code>' ); ?>
	</p>
</div>

<?php
