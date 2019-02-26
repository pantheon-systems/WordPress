<?php
/**
 * Safetypay - Checkout form.
 *
 * @author  EBANX.com
 * @package WooCommerce_EBANX/Templates
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div id="ebanx-safetypay-payment" class="ebanx-payment-container ebanx-language-es">
	<?php require WC_EBANX::get_templates_path() . 'compliance-fields-pe.php'; ?>

	<div class="ebanx-form-row">
		<label class="ebanx-label">
			<input type="radio" name="safetypay" value="cash" checked> Cash
		</label>
	</div>
	<div class="ebanx-form-row">
		<label class="ebanx-label">
			<input type="radio" name="safetypay" value="online"> Online
		</label>
	</div>
</div>
