<?php
/**
 * Baloto - Checkout form.
 *
 * @author  EBANX.com
 * @package WooCommerce_EBANX/Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div id="ebanx-baloto-payment" class="ebanx-payment-container ebanx-language-es">
	<?php require WC_EBANX::get_templates_path() . 'compliance-fields-co.php'; ?>
</div>
