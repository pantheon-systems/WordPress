<?php
/**
 * TEF - Checkout form.
 *
 * @author  EBANX.com
 * @package WooCommerce_EBANX/Templates
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div id="ebanx-tef-payment" class="ebanx-payment-container ebanx-language-br">
	<?php require WC_EBANX::get_templates_path() . 'compliance-fields-br.php'; ?>

	<p>
		<label class="ebanx-label">
			<input type="radio" name="tef" value="itau" checked> Itaú
		</label>
	</p>
	<p>
		<label class="ebanx-label">
			<input type="radio" name="tef" value="bradesco"> Bradesco
		</label>
	</p>
	<p>
		<label class="ebanx-label">
			<input type="radio" name="tef" value="bancodobrasil"> Banco do Brasil
		</label>
	</p>
	<p>
		<label class="ebanx-label">
			<input type="radio" name="tef" value="banrisul"> Banrisul
		</label>
	</p>
</div>
