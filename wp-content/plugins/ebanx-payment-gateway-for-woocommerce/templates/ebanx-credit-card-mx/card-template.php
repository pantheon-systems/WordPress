<div class="ebanx-credit-card-template ebanx-language-es">
	<section class="ebanx-form-row">
		<label for="ebanx-card-holder-name">Titular de la tarjeta<span class="required">*</span></label>
		<input id="ebanx-card-holder-name" class="wc-credit-card-form-card-name input-text" type="text" autocomplete="off" />
	</section>
	<div class="clear"></div>
	<section class="ebanx-form-row">
		<label for="ebanx-card-number">Número de la tarjeta<span class="required">*</span></label>
		<input id="ebanx-card-number" class="input-text wc-credit-card-form-card-number" type="tel" maxlength="20" autocomplete="off" placeholder="&bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull;" />
	</section>
	<div class="clear"></div>
	<section class="ebanx-form-row ebanx-form-row-first">
		<label for="ebanx-card-expiry">Fecha de expiración (MM / AA)<span class="required">*</span></label>
		<input id="ebanx-card-expiry" class="input-text wc-credit-card-form-card-expiry" type="tel" autocomplete="off" placeholder="MM / AA" maxlength="7" />
	</section>
	<section class="ebanx-form-row ebanx-form-row-last">
		<label for="ebanx-card-cvv">Código de verificación<span class="required">*</span></label>
		<input id="ebanx-card-cvv" class="input-text wc-credit-card-form-card-cvc" type="tel" autocomplete="off" placeholder="CVV" />
	</section>

	<?php require WC_EBANX::get_templates_path() . 'instalments.php'; ?>

	<?php if ( $place_order_enabled ) : ?>
		<section class="ebanx-form-row">
			<label for="ebanx-save-credit-card">
				<?php
				$input_type     = 'checkbox';
				$save_card_text = 'Guarda esta tarjeta para compras futuras.';
				if ( WC_EBANX_Helper::checkout_contains_subscription() ) {
					$input_type     = 'hidden';
					$save_card_text = null;
				}
				?>
				<input id="ebanx-save-credit-card" name="ebanx-save-credit-card" class="wc-credit-card-form-save" type="<?php echo esc_attr( $input_type ); ?>" style="width: auto; display: inline-block;" value="yes" checked />
				<?php echo esc_html( $save_card_text ); ?>
			</label>
		</section>
		<div class="clear"></div>
	<?php endif; ?>
</div>
