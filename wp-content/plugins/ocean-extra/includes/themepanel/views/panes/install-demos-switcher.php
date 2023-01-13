<?php
$oe_install_demos_active = get_option( 'oe_install_demos_active', 'yes' );
?>
<h3 class="oceanwp-tp-block-description">
	<?php esc_html_e( 'Access freemium OceanWP full website WordPress templates and start building immediately.', 'ocean-extra' ); ?>
</h3>
<p class="oceanwp-tp-block-description">
	<?php
	echo sprintf(
		esc_html__( 'If this is your first time working with templates or you wish to switch to a different template, make sure to check out %1$sthis document%2$s first.', 'ocean-extra' ),
		'<a href="https://docs.oceanwp.org/article/728-how-to-switch-different-demo" target="_blank">',
		'</a>'
	);
	?>
</p>
<p class="oceanwp-tp-block-description">
	<?php
	echo sprintf(
		esc_html__( 'Check out %1$sour documentation and full video guide%2$s on how easy it is to import website template demos, both free and pro.', 'ocean-extra' ),
		'<a href="https://docs.oceanwp.org/article/463-how-to-import-a-pro-demo" target="_blank">',
		'</a>'
	);
	?>
</p>
<div id="ocean-install-demos-active" class="column-wrap clr">
	<label for="oceanwp-switch-install-demos-active" class="oceanwp-tp-switcher column-name clr">
		<input type="checkbox" role="checkbox" name="oe_install_demos_active" value="true" id="oceanwp-switch-install-demos-active" <?php checked( $oe_install_demos_active == 'yes' ); ?>>
		<span class="slider round"></span>
	</label>
</div>
