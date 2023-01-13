<h3 class="oceanwp-tp-block-description"><?php esc_html_e( 'Find all Customizer settings with ease.', 'ocean-extra' ); ?></h3>
<div id="ocean-customizer-search" class="column-wrap clr">
	<label for="oceanwp-switch-customizer-search" class="oceanwp-tp-switcher column-name clr">
		<input type="checkbox" role="checkbox" name="oe_panels_settings[customizer-search]" value="true" id="oceanwp-switch-customizer-search" <?php checked( (bool) Ocean_Extra_New_Theme_Panel::get_setting( 'customizer-search' ) ); ?>>
		<span class="slider round"></span>
	</label>
</div>
