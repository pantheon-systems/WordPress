<?php
/**
 * EBANX Banking Ticket - Payment instructions.
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
	<p><strong><?php echo esc_html( $customer_name ); ?>, seu boleto foi gerado e a data de vencimento é dia <?php echo esc_html( date_i18n( 'd/m', strtotime( $due_date ) ) ); ?> :)</strong></p>

	<p>Enviamos uma cópia para o email <strong><?php echo esc_html( $customer_email ); ?></strong></p>

	<p>Pague o boleto no Internet Banking de seu banco apenas copiando o código de barras! Você também pode imprimir o boleto e pagar em casas lotéricas e no caixa do seu banco.</p>

	<div>
		<h3><?php echo $barcode_fraud['boleto1']; ?>.<?php echo $barcode_fraud['boleto2']; ?> <?php echo $barcode_fraud['boleto3']; ?>.<?php echo $barcode_fraud['boleto4']; ?> <?php echo $barcode_fraud['boleto5']; ?>.<?php echo $barcode_fraud['boleto6']; ?> <?php echo $barcode_fraud['boleto7']; ?> <?php echo $barcode_fraud['boleto8']; // phpcs:ignore WordPress.XSS.EscapeOutput ?></h3>
		<div class="banking-ticket__barcode-copy">
			<button type="button" class="button ebanx-button--copy" data-clipboard-text="<?php echo esc_attr( $barcode ); ?>">Copiar</button>
		</div>
	</div>

	<br>
	<p>Dica: Pagar seu boleto até às 21h de dias úteis, faz com que o pagamento tenha a chance de ser confirmado mais rápido :)</p>

	<div class="ebanx-button--group ebanx-button--group-two">
		<a href="<?php echo esc_attr( $url_pdf ); ?>" target="_blank" class="button banking-ticket__action">Salvar em PDF</a>
		<?php if ( wp_is_mobile() ) : ?>
			<a href="<?php echo esc_attr( $url_mobile ); ?>" target="_blank" class="button banking-ticket__action">Ver boleto</a>
		<?php else : ?>
			<a href="<?php echo esc_attr( $url_print ); ?>" target="_blank" class="button banking-ticket__action">Imprimir boleto</a>
		<?php endif; ?>
	</div>

	<?php if ( ! wp_is_mobile() ) : ?>
		<iframe id="ebanx-boleto-frame" src="<?php echo esc_attr( $url_iframe ); ?>" style="width: 100%; border: 0px; height: 1000px"></iframe>
	<?php endif; ?>

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
				paymentHash: '<?php echo esc_attr( $boleto_hash ); ?>'
			};
			Mystique.registerFingerprint(done, options, '<?php echo esc_attr( wp_is_mobile() ? 'boleto-responsive' : 'boleto-default' ); ?>' );
		})();
	</script>

	<?php require WC_EBANX::get_templates_path() . 'apps-br.php'; ?>
	<input type="hidden" id="ebanx-payment-hash" data-doraemon-hash="<?php echo esc_html( $hash ); ?>">
</div>
