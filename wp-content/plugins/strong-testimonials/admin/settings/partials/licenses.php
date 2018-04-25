<?php
/**
 * Licenses
 *
 * @since 2.1
 * @since 2.18 As a partial file. Using action hook for add-ons to append their info.
 *
 * TODO Add link to member account on website.
 */
?>
<div class="tab-header">
<p><?php _e( 'Valid license keys allow you to receive automatic updates and priority support.', 'strong-testimonials' ); ?></p>
<p><?php _e( 'To transfer a license to another site or to uninstall the add-on, please deactivate the license here first.', 'strong-testimonials' ); ?></p>
<p><?php printf( __( '<a href="%s" target="_blank">Access your downloads and license keys</a>.', 'strong-testimonials' ), esc_url( 'https://strongplugins.com/account/' ) ); ?></p>
</div>
<table class="form-table">
	<thead>
	<tr>
		<th><?php _e( 'Add-on', 'strong-testimonials' ); ?></th>
		<th class="for-license-key"><?php _e( 'License Key', 'strong-testimonials' ); ?></th>
		<th class="for-license-status"><?php _e( 'Status', 'strong-testimonials' ); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php do_action( 'wpmtst_licenses' ); ?>
	</tbody>
</table>
