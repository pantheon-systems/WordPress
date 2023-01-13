<?php
$google_maps_settings = Ocean_Extra_New_Theme_Panel::get_google_maps_settings();
?>

<div id="ocean-google-maps-control" class="column-wrap clr">
	<form class="integration-settings" data-settings-for="google_maps">
		<table class="form-table">
			<tbody>
				<tr id="owp_google_map_api_tr">
					<th scope="row">
						<label for="owp_google_map_api"><?php esc_html_e( 'API Key', 'ocean-extra' ); ?></label>
					</th>
					<td>
						<input name="owp_integrations[google_map_api]" type="text" id="owp_google_map_api" value="<?php echo esc_attr( $google_maps_settings['google_map_api'] ); ?>" class="regular-text">
						<p class="description"><?php echo sprintf(
							esc_html__( 'To integrate with our google map widget you need an %1$sAPI Key%2$s', 'ocean-extra' ),
							'<a href="https://docs.oceanwp.org/article/537-get-your-google-map-api-key" target="_blank">', '</a>'
							); ?></p>
					</td>
				</tr>
			</tbody>
		</table>
		<?php submit_button(); ?>
	</form>
</div>
