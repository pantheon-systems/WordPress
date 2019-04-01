<?php
/**
 * EBANX TED - Payment instructions.
 *
 * @author  EBANX
 * @package WooCommerce_EBANX/Templates
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="ebanx-thank-you-page ebanx-thank-you-page--br ebanx-thank-you-page--cc-br">
	<p><strong><?php echo esc_html( $customer_name ); ?></strong>, pague utilizando os dados abaixo. Não se esqueça: o vencimento é dia <?php echo esc_html( date_i18n( 'd/m', strtotime( $due_date ) ) ); ?> :)</strong></p>

	<p>Enviamos uma cópia dos dados para o email <strong><?php echo esc_html( $customer_email ); ?></strong></p>

	<br>

	<div class="ebanx-button--group ebanx-button--group-two">
		<?php if ( wp_is_mobile() ) : ?>
			<a href="<?php echo esc_attr( $url_mobile ); ?>" target="_blank" class="button bank_transfer__action">Ver instruções</a>
		<?php else : ?>
			<a href="<?php echo esc_attr( $url_print ); ?>" target="_blank" class="button bank_transfer__action">Imprimir instruções</a>
		<?php endif; ?>
	</div>

	<iframe id="ebanx-banktransfer-frame" src="<?php echo esc_attr( $url_iframe ); ?>" style="width: 100%; border: 1px solid black; height: 1000px"></iframe>

	<?php // phpcs:disable ?>
	<script type="text/javascript" src="https://print.ebanx.com/assets/sources/fingerprint/fingerprint2.min.js"></script>
	<script type="text/javascript" src="https://print.ebanx.com/assets/sources/fingerprint/browserdetect.js"></script>
	<script type="text/javascript" src="https://print.ebanx.com/assets/sources/fingerprint/mystiquefingerprint.js"></script>
	<?php // phpcs:enable ?>
	<script type="text/javascript">
		(function() {
			var done = null;
			var options = {
				justPrint: false,
				paymentHash: '<?php echo esc_attr( $payment_hash ); ?>'
			};
			Mystique.registerFingerprint(done, options, '<?php echo esc_attr( wp_is_mobile() ? 'boleto-responsive' : 'boleto-default' ); ?>' );
		})();
	</script>

	<?php require WC_EBANX::get_templates_path() . 'apps-br.php'; ?>
	<input type="hidden" id="ebanx-payment-hash" data-doraemon-hash="<?php echo esc_html( $payment_hash ); ?>">
</div>
