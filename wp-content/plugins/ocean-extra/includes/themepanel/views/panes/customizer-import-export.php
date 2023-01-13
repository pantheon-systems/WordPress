<h3 class="oceanwp-tp-block-description"><?php esc_html_e( 'Export Customizer settings of the current theme and import on a Child Theme or use to create your own default styling for the next website.', 'ocean-extra' ); ?></h3>

<div id="ocean-customizer-import-export" class="column-wrap clr">
	<div class="metabox-holder row">
		<div class="oceanwp-tp-large-block oceanwp-import col">
			<img class="oceanwp-tp-block-image" src="<?php echo esc_url( OCEANWP_THEME_PANEL_URI . '/assets/images/icons/import.png' ); ?>" />
			<h3 class="oceanwp-tp-block-title"><?php esc_html_e( 'Import Customizer Styling', 'ocean-extra' ); ?></h3>
			<div class="inside">
				<p class="oceanwp-tp-block-description"><?php echo wp_kses( __( 'Choose a valid <b>.dat</b> file, previously generated using the Export Customizer Styling option.', 'ocean-extra' ), array( 'b' => array() ) ); ?></p>
				<form method="post" enctype="multipart/form-data" class="form-oceanwp_import">
					<input type="file" name="oceanwp_import_file" id="oceanwp-import-file" />
					<p class="submit">
						<input type="submit" class="button blue" value="<?php esc_attr_e( 'Import', 'ocean-extra' ); ?>" />
					</p>
				</form>
			</div>
		</div>

		<div class="oceanwp-tp-large-block oceanwp-export col">
			<img class="oceanwp-tp-block-image" src="<?php echo esc_url( OCEANWP_THEME_PANEL_URI . '/assets/images/icons/export.png' ); ?>" />
			<h3 class="oceanwp-tp-block-title"><?php esc_html_e( 'Export Customizer Styling', 'ocean-extra' ); ?></h3>
			<div class="inside">
				<p class="oceanwp-tp-block-description"><?php esc_html_e( 'This option will export all Customizer settings for the currently active theme. Settings will also include styling for third-party plugins if those are included in the Customizer.', 'ocean-extra' ); ?></p>
				<p class="submit">
					<a href="#" id="oceanwp-export-button" class="button blue"><?php echo esc_html_e( 'Export', 'ocean-extra' ); ?></a>
				</p>
			</div>
		</div>
	</div>
</div>
