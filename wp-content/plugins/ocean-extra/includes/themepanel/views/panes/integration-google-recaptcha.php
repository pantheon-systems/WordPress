<?php
$google_recaptcha_settings = Ocean_Extra_New_Theme_Panel::get_google_recaptcha_settings();
?>

<p class="oceanwp-tp-block-description">
	<?php
	echo sprintf(
		esc_html__( '%1$sreCAPTCHA%2$s is a free service by Google that protects your website from spam and abuse. It does this while letting your valid users pass through with ease.', 'ocean-extra' ),
		'<a href="https://docs.oceanwp.org/article/536-get-your-google-recaptcha-site-key-and-secret-key" target="_blank">',
		'</a>'
	);
	?>
</p>


<div id="ocean-google-recaptcha-control" class="column-wrap clr">
	<form class="integration-settings" data-settings-for="google_recaptcha">

		<table class="form-table">
			<tbody>
				<tr id="owp_google_recaptcha_site_key_tr">
					<th scope="row">
						<label for="owp_recaptcha_version"><?php esc_html_e( 'Use reCAPTCHA version', 'ocean-extra' ); ?></label>
					</th>
					<td>
						<select name="owp_integrations[recaptcha_version]" id="owp_recaptcha_version">
							<option <?php selected( $google_recaptcha_settings['recaptcha_version'], 'default', true ); ?> value="default">
								<?php esc_html_e( 'Use default', 'ocean-extra' ); ?>
							</option>
							<option <?php selected( $google_recaptcha_settings['recaptcha_version'], 'v3', true ); ?> value="v3">
								<?php esc_html_e( 'Use reCAPTCHA v3', 'ocean-extra' ); ?>
							</option>
						</select>				
					</td>
				</tr>
			</tbody>
		</table>

		<div id="owp_google_recaptcha-default" style="display: none;">
			<h3 id="recaptcha"><?php esc_html_e( 'v2', 'ocean-extra' ); ?></h3>
			<table class="form-table">
				<tbody>
					<tr id="owp_google_recaptcha_site_key_tr">
						<th scope="row">
							<label for="owp_recaptcha_site_key"><?php esc_html_e( 'Site Key', 'ocean-extra' ); ?></label>
						</th>
						<td>
							<input name="owp_integrations[recaptcha_site_key]" type="text" id="owp_recaptcha_site_key" value="<?php echo esc_attr( $google_recaptcha_settings['recaptcha_site_key'] ); ?>" class="regular-text">
						</td>
					</tr>
					<tr id="owp_google_recaptcha_secret_key_tr">
						<th scope="row">
							<label for="owp_recaptcha_secret_key"><?php esc_html_e( 'Secret Key', 'ocean-extra' ); ?></label>
						</th>
						<td>
							<input name="owp_integrations[recaptcha_secret_key]" type="text" id="owp_recaptcha_secret_key" value="<?php echo esc_attr( $google_recaptcha_settings['recaptcha_secret_key'] ); ?>" class="regular-text">
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div id="owp_google_recaptcha-v3" style="display: none;">
			<h3 id="recaptcha"><?php esc_html_e( 'v3', 'ocean-extra' ); ?></h3>
			<table class="form-table">
				<tbody>
					<tr id="owp_google_recaptcha_site_key_tr">
						<th scope="row">
							<label for="owp_recaptcha3_site_key"><?php esc_html_e( 'Site Key', 'ocean-extra' ); ?></label>
						</th>
						<td>
							<input
								name="owp_integrations[recaptcha3_site_key]"
								type="text"
								id="owp_recaptcha3_site_key"
								value="<?php echo esc_attr( $google_recaptcha_settings['recaptcha3_site_key'] ); ?>"
								class="regular-text"
							/>
						</td>
					</tr>
					<tr id="owp_google_recaptcha3_secret_key_tr">
						<th scope="row">
							<label for="owp_recaptcha3_secret_key"><?php esc_html_e( 'Secret Key', 'ocean-extra' ); ?></label>
						</th>
						<td>
							<input
								name="owp_integrations[recaptcha3_secret_key]"
								type="text"
								id="owp_recaptcha3_secret_key"
								value="<?php echo esc_attr( $google_recaptcha_settings['recaptcha3_secret_key'] ); ?>"
								class="regular-text"
							/>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<?php submit_button(); ?>
	</form>
</div>
