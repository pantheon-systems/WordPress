<?php
/**
 * SPEI - Payment EBANX Pending.
 *
 * @author  EBANX.com
 * @package WooCommerce_EBANX/Templates
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<hr>
<div class="banking-ticket__desc">
	<p class="woocommerce-thankyou-order-received">¡Listo <?php echo esc_html( $customer_name ); ?>! Tu recibo EBANX de pago en SPEI ha sido generada.</p>
	<p>Enviamos una copia a <strong><?php echo esc_html( $customer_email ); ?></strong>.</p>
	<p>No lo olvides: tu boleta vence el día <strong><?php echo esc_html( date_i18n( 'd/m', strtotime( $due_date ) ) ); ?></strong>.</p>
	<p>¿Dudas? Con gusto te <a href="https://www.ebanx.com/mx/ayuda/pagos/boleta" target="_blank">ayudaremos</a>.</p>
</div>

<hr>
<div class="banking-ticket__actions">
	<div class="ebanx-button--group ebanx-button--group-two">
		<a href="<?php echo esc_url( $url_pdf ); ?>" target="_blank" class="button banking-ticket__action">Guardar como PDF</a><a href="<?php echo esc_url( $url_print ); ?>" target="_blank" class="button banking-ticket__action">Imprimir SPEI</a>
	</div>
</div>
<hr>

<div>
	<iframe src="<?php echo esc_url( $url_iframe ); ?>" style="width: 100%; height: 1000px; border: 0px;"></iframe>
</div>
