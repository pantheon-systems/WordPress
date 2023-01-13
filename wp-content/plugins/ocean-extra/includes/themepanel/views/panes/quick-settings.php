<h3 class="oceanwp-tp-block-description">
	<?php esc_html_e( 'Access specific Customizer control settings quickly.', 'ocean-extra' ); ?>
</h3>
<div class="quick-settings-block clr">
	<div class="quick-settings clr">
		<?php
		if ( function_exists ( 'owp_get_customizer_options' ) ) {
			$options = owp_get_customizer_options();
		} else {
			$options = get_options();
		}

		// Loop through options
		foreach ( $options as $key => $val ) :
			// Var
			$label = isset( $val['label'] ) ? $val['label'] : '';
			$desc  = isset( $val['desc'] ) ? $val['desc'] : '';
			$icon  = isset( $val['icon'] ) ? $val['icon'] : '';
			$panel = isset( $val['panel'] ) ? $val['panel'] : false;
			$id    = $key;
			$customizer_autofocus = isset( $val['customizer_autofocus'] ) ? $val['customizer_autofocus'] : $id;

			if ( true == $panel ) {
				$focus = 'panel';
			} else {
				$focus = 'control';
			}
			?>

			<div class="oceanwp-tp-quick-settings-block column-wrap">
				<img class="oceanwp-tp-block-image-small" src="<?php echo esc_url( OCEANWP_THEME_PANEL_URI . '/assets/images/icons/quick-settings/' . $icon ); ?>" />
				<h3 class="title"><?php echo esc_attr( $label ); ?></h3>
				<?php if ( $desc ) { ?>
					<p class="desc"><?php echo esc_attr( $desc ); ?></p>
				<?php } ?>
				<div class="bottom-column">
					<a class="option-link button blue" href="<?php echo esc_url( admin_url( 'customize.php?autofocus[' . $focus . ']=' . $customizer_autofocus . '' ) ); ?>" target="_blank"><?php esc_html_e( 'Go Now', 'ocean-wp' ); ?></a>
				</div>

			</div>

		<?php endforeach; ?>

	</div>

</div>
