<?php
$mailchimp_settings = Ocean_Extra_New_Theme_Panel::get_mailchimp_settings();
?>

<p class="oceanwp-tp-block-description">
	<?php
	echo sprintf(
		esc_html__( 'Used for the MailChimp widget and the Newsletter widget of the Ocean Elementor Widgets extension. %1$sFollow this article%2$s to get your API Key and Audience ID.', 'ocean-extra' ),
		'<a href="https://docs.oceanwp.org/article/520-get-your-mailchimp-api-key-and-list-id" target="_blank">',
		'</a>'
	);
	?>
</p>

<div id="ocean-mailchimp-control" class="column-wrap clr">
	<form class="integration-settings" data-settings-for="mailchimp">
		<table class="form-table">
			<tbody>
				<tr id="owp_mailchimp_api_key_tr">
					<th scope="row">
						<label for="owp_mailchimp_api_key"><?php esc_html_e( 'API Key', 'ocean-extra' ); ?></label>
					</th>
					<td>
						<input name="owp_integrations[mailchimp_api_key]" type="text" id="owp_mailchimp_api_key" value="<?php echo esc_attr( $mailchimp_settings['mailchimp_api_key'] ); ?>" class="regular-text">
					</td>
				</tr>
				<tr id="owp_mailchimp_list_id_tr">
					<th scope="row">
						<label for="owp_mailchimp_list_id"><?php esc_html_e( 'Audience ID', 'ocean-extra' ); ?></label>
					</th>
					<td>
						<input name="owp_integrations[mailchimp_list_id]" type="text" id="owp_mailchimp_list_id" value="<?php echo esc_attr( $mailchimp_settings['mailchimp_list_id'] ); ?>" class="regular-text">
					</td>
				</tr>
			</tbody>
		</table>
		<?php submit_button(); ?>
	</form>
</div>
