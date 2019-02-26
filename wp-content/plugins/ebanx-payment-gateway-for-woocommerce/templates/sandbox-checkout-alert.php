<?php
if ( ! $is_sandbox_mode ) {
	return;
}
?>
<div class="sandbox-alert-box">
	<img class="sandbox-alert-icon" style="max-height: 100%; float: left;" src="<?php echo esc_html( WC_EBANX_PLUGIN_DIR_URL ); ?>assets/images/icons/warning-icon.svg" />
	<div class="sandbox-alert-message"><?php echo esc_html( $message ); ?></div>
</div>
