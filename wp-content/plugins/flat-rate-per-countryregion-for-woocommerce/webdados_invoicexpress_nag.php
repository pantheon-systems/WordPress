<?php

	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

	// Add InvoiceXpress for WooCommerce nag
	add_action( 'admin_notices', 'webdados_invoicexpress_nag' );
	function webdados_invoicexpress_nag() {
		?>
		<script type="text/javascript">
		jQuery(function($) {
			$( document ).on( 'click', '#webdados_invoicexpress_nag .notice-dismiss', function () {
				//AJAX SET TRANSIENT FOR 1 MONTH
				$.ajax( ajaxurl, {
					type: 'POST',
					data: {
						action: 'dismiss_webdados_invoicexpress_nag',
					}
				});
			});
		});
		</script>
		<div id="webdados_invoicexpress_nag" class="notice notice-info is-dismissible">
			<p style="line-height: 1.4em;">
				<img src="https://invoicexpress-woocommerce.com/wp-content/uploads/2018/12/invoicexpress-woocommerce-logo.png" style="float: left; max-width: 100px; height: auto; margin-right: 1em;"/>
				<strong><?php _e( 'Are you already issuing automatic invoices on your WooCommerce store?', 'flat-rate-per-countryregion-for-woocommerce'); ?></strong>
				<br/>
				<?php echo sprintf(
					__( 'If not, get to know our new plugin: %1$sInvoicing with InvoiceXpress for WooCommerce%2$s', 'flat-rate-per-countryregion-for-woocommerce' ),
					sprintf(
						'<a href="%s" target="_blank">',
						esc_url( __( 'https://invoicexpress-woocommerce.com/', 'flat-rate-per-countryregion-for-woocommerce' ) )
					),
					'</a>'
				); ?>
				<br/>
				<?php _e( 'Use the coupon <strong>webdados</strong> for 10% discount!', 'flat-rate-per-countryregion-for-woocommerce' ); ?>
			</p>
		</div>
		<?php
	}
	add_action( 'wp_ajax_dismiss_webdados_invoicexpress_nag', 'dismiss_webdados_invoicexpress_nag' );
	function dismiss_webdados_invoicexpress_nag() {
		$days = 30;
		$expiration = $days * DAY_IN_SECONDS;
		set_transient( 'webdados_invoicexpress_nag', 1, $expiration );
		wp_die();
	}