<?php
/**
 * TEF - Payment instructions.
 *
 * @author  EBANX.com
 * @package WooCommerce_EBANX/Templates
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="woocommerce-message">
	<span><?php printf( wp_kses( __( 'Payment successfully made using %1$s multicaja in %2$s.', 'woocommerce-gateway-ebanx' ), array( 'strong' => array() ) ), '' ); ?></span>
</div>
