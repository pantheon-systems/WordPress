<?php if ( ! empty( $cards ) ) : ?>

	<h3><?php esc_html_e( 'Your saved credit cards', 'woocommerce-gateway-ebanx' ); ?></h3>

	<p><?php esc_html_e( 'The following credit cards will be listed on the checkout page. To delete a credit card, just check it and submit.', 'woocommerce-gateway-ebanx' ); ?></p>

	<form method="post" action="" class="ebanx-credit-cards-form">
		<div class="ebanx-credit-cards">
			<?php foreach ( $cards as $card ) : ?>
				<label class="ebanx-credit-card">
					<input type="checkbox" name="credit-card-delete[]" value="<?php echo esc_attr( $card->masked_number ); ?>" class="ebanx-delete-input">
					<div class="ebanx-credit-card-info">
						<div>
							<img src="<?php echo esc_url( WC_EBANX_PLUGIN_DIR_URL . "assets/images/icons/$card->brand.png" ); ?>" height="20" style="height: 20px; margin-left: 0; margin-right: 7px; float: none;" alt="<?php echo esc_attr( $card->brand ); ?>" class="ebanx-credit-card-brand">
							<span class="ebanx-credit-card-brand-name"><?php echo esc_html( ucfirst( $card->brand ) ); ?></span>
						</div>
						<p class="ebanx-credit-card-bin">&bull;&bull;&bull;&bull; <?php echo esc_html( substr( $card->masked_number, -4 ) ); ?></p>
					</div>
				</label>
			<?php endforeach ?>
		</div>

		<input type="submit" class="button" value="<?php esc_html_e( 'Delete cards', 'woocommerce-gateway-ebanx' ); ?>">
	</form>

<?php else : ?>
	<h3><?php esc_html_e( 'No credit cards found', 'woocommerce-gateway-ebanx' ); ?></h3>

	<p><?php esc_html_e( 'To save a credit card, pay an order on checkout using credit card as payment method.', 'woocommerce-gateway-ebanx' ); ?></p>
<?php endif ?>
