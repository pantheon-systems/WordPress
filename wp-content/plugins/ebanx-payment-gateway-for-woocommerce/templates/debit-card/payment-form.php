<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div id="ebanx-debit-cart-form" class="ebanx-payment-container ebanx-language-es">
	<?php require WC_EBANX::get_templates_path() . 'compliance-fields-mx.php'; ?>

	<div id="ebanx-container-new-debit-card">
		<section class="ebanx-form-row">
			<label for="ebanx-debit-card-holder-name">Titular de la tarjeta<span class="required">*</span></label>
			<input id="ebanx-debit-card-holder-name" class="wc-credit-card-form-card-name input-text" type="text" autocomplete="off" />
		</section>
		<section class="ebanx-form-row">
			<label for="ebanx-debit-card-number">Número de la tarjeta<span class="required">*</span></label>
			<input id="ebanx-debit-card-number" class="input-text wc-credit-card-form-card-number" type="tel" maxlength="20" autocomplete="off" placeholder="&bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull;" />
		</section>
		<div class="clear"></div>
		<section class="ebanx-form-row ebanx-form-row-first">
			<label for="ebanx-debit-card-expiry">Fecha de expiración (MM / AA)<span class="required">*</span></label>
			<input id="ebanx-debit-card-expiry" class="input-text wc-credit-card-form-card-expiry" type="tel" autocomplete="off" placeholder="MM / AA" maxlength="7" />
		</section>
		<section class="ebanx-form-row ebanx-form-row-last">
			<label for="ebanx-debit-card-cvv">Código de verificación<span class="required">*</span></label>
			<input id="ebanx-debit-card-cvv" class="input-text wc-credit-card-form-card-cvc" type="tel" autocomplete="off" placeholder="CVV" />
		</section>

		<div class="clear"></div>
	</div>
</div>
