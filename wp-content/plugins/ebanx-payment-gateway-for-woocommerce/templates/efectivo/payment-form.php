<?php
/**
 * Efectivo - Checkout form.
 *
 * @author  EBANX.com
 * @package WooCommerce_EBANX/Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div id="ebanx-efectivo-payment" class="ebanx-payment-container ebanx-language-es">
	<?php require WC_EBANX::get_templates_path() . 'compliance-fields-ar.php'; ?>

	<div class="ebanx-form-row">
		<label class="ebanx-label">
			<input type="radio" name="efectivo" value="rapipago" checked> <img src="<?php echo esc_url( WC_EBANX_PLUGIN_DIR_URL ); ?>assets/images/ebanx-rapipago.png" style="height: 20px" /> Rapipago
		</label>
	</div>
	<div class="ebanx-form-row">
		<label class="ebanx-label">
			<input type="radio" name="efectivo" value="pagofacil"> <img src="<?php echo esc_url( WC_EBANX_PLUGIN_DIR_URL ); ?>assets/images/ebanx-pagofacil.png" style="height: 20px" /> Pagofacil
		</label>
	</div>
	<div class="ebanx-form-row">
		<label class="ebanx-label">
			<input type="radio" name="efectivo" value="cupon"> <img src="<?php echo esc_url( WC_EBANX_PLUGIN_DIR_URL ); ?>assets/images/ebanx-cupon.png" style="height: 20px" /> Otros Cupones
		</label>
	</div>
</div>
