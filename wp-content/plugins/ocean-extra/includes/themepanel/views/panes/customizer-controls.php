<?php
$theme_panels = Ocean_Extra_New_Theme_Panel::get_panels();
?>
<h3 class="oceanwp-tp-block-description"><?php esc_html_e( 'Disable Customizer OceanWP Control panels for faster Customizer load or faster management by displaying fewer options.', 'ocean-extra' ); ?></h3>
<div id="ocean-customizer-control" class="column-wrap clr">

	<form>

		<?php wp_nonce_field( 'customizer_control', 'customizer_control_nonce' ); ?>
		<input type="hidden" name="option_name" value="oe_panels_settings" />

		<div id="ocean-customizer-reset-bulk" class="oceanwp-tp-switcher column-wrap clr">
			<label for="customizer-reset-bulk" class="column-name clr">
				<input type="checkbox" role="checkbox" name="customizer-reset-bulk" value="true" id="customizer-reset-bulk" class="oe-switcher-bulk" />
				<span class="slider round"></span>
			</label>
		</div>
		<div id="ocean-customizer-items" class="column-wrap clr">

			<?php
			foreach ( $theme_panels as $key => $val ) :

				// Var
				$label = isset( $val['label'] ) ? $val['label'] : '';
				$desc  = isset( $val['desc'] ) ? $val['desc'] : '';

				// Get settings
				$settings = Ocean_Extra_New_Theme_Panel::get_setting( $key );
				?>

				<div id="<?php echo esc_attr( $key ); ?>" class="oceanwp-tp-small-block column-wrap clr">

					<h3 class="title"><?php echo esc_attr( $label ); ?></h3>
					<label for="oceanwp-switch-[<?php echo esc_attr( $key ); ?>]" class="oceanwp-tp-switcher column-name clr">
						<input type="checkbox" role="checkbox" name="oe_panels_settings[<?php echo esc_attr( $key ); ?>]" value="true" id="oceanwp-switch-[<?php echo esc_attr( $key ); ?>]" <?php checked( $settings ); ?>>
						<span class="slider round"></span>
					</label>
                    <?php if ( $desc ) { ?>
                        <div class="desc"><?php echo esc_attr( $desc ); ?></div>
                    <?php } ?>

				</div>

			<?php endforeach; ?>

		</div>
		<?php submit_button(); ?>
	</form>

</div>
