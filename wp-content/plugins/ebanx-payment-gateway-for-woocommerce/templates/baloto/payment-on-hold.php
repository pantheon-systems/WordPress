<?php
/**
 * Baloto - Payment instructions.
 *
 * @author  EBANX.com
 * @package WooCommerce_EBANX/Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<hr>
<div class="banking-ticket__desc">
	<p class="woocommerce-thankyou-order-received">Su boleta Baloto fue generado con éxito.</p>
	<p>Una copia de la boleta fue enviada al correo electrónico <strong><?php echo esc_html( $customer_email ); ?></strong>.</p>
	<p>Si tienes dudas, por favor escribe a <a href="mailto:soporte@ebanx.com">soporte@ebanx.com</a>.</p>
</div>

<hr>
<div class="banking-ticket__actions">
	<div class="ebanx-button--group ebanx-button--group-two">
		<a href="<?php echo esc_attr( $url_pdf ); ?>" target="_blank" class="button banking-ticket__action">Guardar como PDF</a><a href="<?php echo esc_attr( $url_print ); ?>" target="_blank" class="button banking-ticket__action">Imprimir Baloto</a>
	</div>
</div>
<hr>

<div>
	<iframe src="<?php echo esc_attr( $url_iframe ); ?>" style="width: 100%; height: 1000px; border: 0px;"></iframe>
</div>
