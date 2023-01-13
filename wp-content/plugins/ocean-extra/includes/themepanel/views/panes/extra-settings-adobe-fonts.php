<?php
$ocean_adobe_fonts_settings = Ocean_Extra_New_Theme_Panel::get_adobe_fonts_settings();
?>

<form class="integration-settings" data-settings-for="adobe_fonts">
	<table class="form-table">
		<tbody>
			<tr id="owp_adobe_fonts_integration_tr">
				<th scope="row">
					<label for="owp_adobe_fonts_integration"><?php esc_html_e( 'Enable Adobe Fonts Module', 'oceanwp' ); ?></label>
				</th>
				<td>
					<select name="owp_integrations[adobe_fonts_integration]" id="owp_adobe_fonts_integration">
						<option <?php selected( $ocean_adobe_fonts_settings['adobe_fonts_integration'], '0', true ); ?> value="0">
							<?php esc_html_e( 'Disable', 'oceanwp' ); ?>
						</option>
						<option <?php selected( $ocean_adobe_fonts_settings['adobe_fonts_integration'], '1', true ); ?> value="1">
							<?php esc_html_e( 'Enable', 'oceanwp' ); ?>
						</option>
					</select>
				</td>
			</tr>
			<tr id="owp_adobe_fonts_integration_project_id_tr" data-for="owp_adobe_fonts">
				<th scope="row">
					<label for="owp_adobe_fonts_integration_project_id"><?php esc_html_e( 'Project ID', 'oceanwp' ); ?></label>
				</th>
				<td>
					<input type="text" name="owp_integrations[adobe_fonts_integration_project_id]" id="owp_adobe_fonts_integration_project_id" value="<?php echo $ocean_adobe_fonts_settings['adobe_fonts_integration_project_id']; ?>" />
					<?php
					$project_id_validate_result = OceanWP_Adobe_Font()->check_project_id();
					?>
					<span class="adobe adobe-ok <?php echo $project_id_validate_result['status'] === 'success' ? '' : 'hidden'; ?>"><?php echo esc_html__( 'Validated!', 'oceanwp' ); ?></span>
					<span class="adobe adobe-error <?php echo $project_id_validate_result['status'] !== 'success' ? '' : 'hidden'; ?>"><?php echo esc_html__( 'Not Validated! Check Project ID and resave.', 'oceanwp' ); ?></span>
				</td>
			</tr>
			<tr id="owp_adobe_fonts_integration_enable_customizer_tr" data-for="owp_adobe_fonts">
				<th scope="row">
					<label for="owp_adobe_fonts_integration_enable_customizer_tr"><?php esc_html_e( 'Enable for Customizer', 'oceanwp' ); ?></label>
				</th>
				<td>
					<select name="owp_integrations[adobe_fonts_integration_enable_customizer]">
						<option <?php selected( $ocean_adobe_fonts_settings['adobe_fonts_integration_enable_customizer'], '0', true ); ?> value="0">
							<?php esc_html_e( 'Disable', 'oceanwp' ); ?>
						</option>
						<option <?php selected( $ocean_adobe_fonts_settings['adobe_fonts_integration_enable_customizer'], '1', true ); ?> value="1">
							<?php esc_html_e( 'Enable', 'oceanwp' ); ?>
						</option>
					</select>
				</td>
			</tr>
			<tr id="owp_adobe_fonts_integration_enable_elementor_tr" data-for="owp_adobe_fonts">
				<th scope="row">
					<label for="owp_adobe_fonts_integration_enable_elementor_tr"><?php esc_html_e( 'Enable for Elementor', 'oceanwp' ); ?></label>
				</th>
				<td>
					<select name="owp_integrations[adobe_fonts_integration_enable_elementor]">
						<option <?php selected( $ocean_adobe_fonts_settings['adobe_fonts_integration_enable_elementor'], '0', true ); ?> value="0">
							<?php esc_html_e( 'Disable', 'oceanwp' ); ?>
						</option>
						<option <?php selected( $ocean_adobe_fonts_settings['adobe_fonts_integration_enable_elementor'], '1', true ); ?> value="1">
							<?php esc_html_e( 'Enable', 'oceanwp' ); ?>
						</option>
					</select>
				</td>
			</tr>
		</tbody>
	</table>
	<?php submit_button(); ?>
</form>
