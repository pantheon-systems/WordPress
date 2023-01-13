<?php
$oe_svg_support_active_status = get_option( 'oe_svg_support_active_status', 'no' );
?>
<h3 class="oceanwp-tp-block-description"><?php esc_html_e( 'Enable SVG files support for your WordPress Media Library.', 'ocean-extra' ); ?></h3>
<div id="ocean-svg-support-disable" class="oceanwp-tp-switcher column-wrap">
	<label for="oceanwp-switch-svg-support-disable" class="column-name">
		<input type="checkbox" role="checkbox" name="svg-support-disable-bulk" value="true" id="oceanwp-switch-svg-support-disable" <?php checked( $oe_svg_support_active_status == 'yes' ); ?> />
		<span class="slider round"></span>
	</label>
</div>
