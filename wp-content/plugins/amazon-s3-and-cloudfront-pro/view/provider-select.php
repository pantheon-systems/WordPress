<?php
/* @var \Amazon_S3_And_CloudFront|\Amazon_S3_And_CloudFront_Pro $this */
$current_provider       = $this->get_provider();
$provider_defined       = (bool) defined( 'AS3CF_PROVIDER' ) || $this->get_defined_setting( 'provider', false );
$key_defined            = $this->get_defined_setting( 'access-key-id', false );
$secret_defined         = $this->get_defined_setting( 'secret-access-key', false );
$keys_settings_constant = ( $key_defined || $secret_defined ) ? $this->settings_constant() : false;
$providers              = $this->get_provider_classes();
?>

<div class="as3cf-content as3cf-provider-select">
	<?php
	if ( ! empty( $_GET['action'] ) && 'change-provider' === $_GET['action'] && $this->get_setting( 'bucket' ) && ! empty( $can_write ) ) {
		echo '<a href="' . $this->get_plugin_page_url() . '">' . __( '&laquo;&nbsp;Back', 'amazon-s3-and-cloudfront' ) . '</a>';
	}
	?>
	<h3><?php _e( 'Storage Provider', 'amazon-s3-and-cloudfront' ) ?></h3>

	<table>
		<?php
		/* @var \DeliciousBrains\WP_Offload_Media\Providers\Provider $provider_class */
		foreach ( $providers as $provider_key => $provider_class ) {
			/* @var \DeliciousBrains\WP_Offload_Media\Providers\Provider $provider */
			$provider                = new $provider_class( $this );
			$provider_selected       = $provider_key === $current_provider->get_provider_key_name();
			$provider_selected_class = $provider_selected ? ' as3cf-provider-selected' : '';
			$provider_selected_style = $provider_selected ? '' : ' style="display: none"';

			$key_constant                    = $provider_class::access_key_id_constant();
			$secret_constant                 = $provider_class::secret_access_key_constant();
			$any_access_key_constant_defined = (bool) $key_constant || $secret_constant || $keys_settings_constant;

			$defined_constants = array();
			foreach ( array( $key_constant, $secret_constant, $keys_settings_constant ) as $defined_constant ) {
				if ( $defined_constant ) {
					$defined_constants[] = $defined_constant;
				}
			}

			$use_server_role_constant = $provider_class::use_server_role_constant();

			$selected_authmethod = 'define';

			if ( ! $any_access_key_constant_defined && $provider_class::use_server_roles() ) {
				$selected_authmethod = 'server-role';
			} elseif ( ! $any_access_key_constant_defined && $provider_selected && $current_provider->are_access_keys_set() ) {
				$selected_authmethod = 'db';
			}

			$define_authmethod_attr      = '';
			$server_role_authmethod_attr = '';
			$db_authmethod_attr          = '';

			switch ( $selected_authmethod ) {
				case 'define':
					$define_authmethod_attr      = $provider_selected ? ' checked="checked"' : '';
					$server_role_authmethod_attr = $any_access_key_constant_defined ? ' data-as3cf-disabled="true" disabled="disabled"' : '';
					$db_authmethod_attr          = $any_access_key_constant_defined ? ' data-as3cf-disabled="true" disabled="disabled"' : '';
					break;
				case 'server-role':
					$server_role_authmethod_attr = $provider_selected ? ' checked="checked"' : '';
					$define_authmethod_attr      = ' data-as3cf-disabled="true" disabled="disabled"';
					$db_authmethod_attr          = ' data-as3cf-disabled="true" disabled="disabled"';
					break;
				case 'db':
					$db_authmethod_attr = $provider_selected ? ' checked="checked"' : '';
					break;
			}

			$provider_service_quick_start_slug = $provider_class::get_provider_service_quick_start_slug();
			?>
			<tr class="as3cf-provider-title as3cf-provider-<?php echo $provider_key; ?><?php echo $provider_selected_class; ?>">
				<td>
					<input
						type="radio"
						name="provider"
						id="as3cf-provider-<?php echo $provider_key; ?>"
						value="<?php echo $provider_key; ?>"
						<?php
						echo $provider_selected ? ' checked="checked"' : '';
						echo ( ! $provider_selected && $provider_defined ) ? ' disabled="disabled"' : '';
						?>
					>
				</td>
				<td>
					<?php
					if ( $provider_selected && $provider_defined ) {
						echo '<span class="as3cf-defined-in-config">' . __( 'defined in wp-config.php', 'amazon-s3-and-cloudfront' ) . '</span>';
					}
					?>
					<label for="as3cf-provider-<?php echo $provider_key; ?>">
						<img class="as3cf-provider-logo" src="<?php echo plugins_url( 'assets/img/' . $provider_key . '-logo.svg', $this->get_plugin_file_path() ) ?>" alt="" width="50" height="50">
						<h3 class="as3cf-provider-name"><?php echo $provider_class::get_provider_service_name(); ?></h3>
					</label>
				</td>
			</tr>
			<tr class="as3cf-provider-content as3cf-provider-<?php echo $provider_key; ?><?php echo $provider_selected_class; ?>"<?php echo $provider_selected_style; ?> data-provider="<?php echo $provider_key; ?>">
				<td></td>
				<td>
					<table>
						<!-- Defined Access Keys -->
						<tr class="asc3f-provider-authmethod-title">
							<th>
								<input type="radio" name="authmethod" id="as3cf-provider-<?php echo $provider_key; ?>-define" value="define"<?php echo $provider_selected ? '' : ' disabled="disabled"';
								echo $define_authmethod_attr; ?>>
							</th>
							<td>
								<label for="as3cf-provider-<?php echo $provider_key; ?>-define"><?php _e( 'Define access keys in wp-config.php', 'amazon-s3-and-cloudfront' ) ?></label>
							</td>
						</tr>
						<tr class="asc3f-provider-authmethod-content" data-provider-authmethod="define"<?php echo 'define' !== $selected_authmethod ? ' style="display: none"' : ''; ?>>
							<td></td>
							<td>
								<?php
								if ( $any_access_key_constant_defined ) {
									if ( count( $defined_constants ) > 1 ) {
										$remove_defines_msg = _x( 'You\'ve defined your access keys in your wp-config.php. To select a different option here, simply comment out or remove the \'%1$s\' defines in your wp-config.php.', 'Access Keys defined in multiple defines.', 'amazon-s3-and-cloudfront' );
									} else {
										$remove_defines_msg = _x( 'You\'ve defined your access keys in your wp-config.php. To select a different option here, simply comment out or remove the \'%1$s\' define in your wp-config.php.', 'Access Keys defined in single define.', 'amazon-s3-and-cloudfront' );
									}
									$multiple_defined_keys_glue = _x( ' & ', 'joins multiple define keys in notice', 'amazon-s3-and-cloudfront' );
									$defined_constants_str      = join( $multiple_defined_keys_glue, $defined_constants );
									printf( $remove_defines_msg, $defined_constants_str );
									echo '&nbsp;' . $this->more_info_link( '/wp-offload-media/doc/' . $provider_service_quick_start_slug . '/#save-access-keys' );

									if ( $provider_selected && ! $provider->are_access_keys_set() ) {
										?>
										<div class="notice-error notice">
											<p>
												<?php _e( 'Please check your wp-config.php file as it looks like one of your access key defines is missing or incorrect.', 'amazon-s3-and-cloudfront' ) ?>
											</p>
										</div>
										<?php
									}
								} else {
									_e( 'Copy the following snippet <strong>near the top</strong> of your wp-config.php and replace the stars with the keys.', 'amazon-s3-and-cloudfront' );
									echo '&nbsp;' . $this->more_info_link( '/wp-offload-media/doc/' . $provider_service_quick_start_slug . '/#save-access-keys' );
									?>
									<textarea rows="5" class="as3cf-define-snippet code clear" readonly>
define( 'AS3CF_SETTINGS', serialize( array(
    'provider' => '<?php echo $provider_key; ?>',
    'access-key-id' => '********************',
    'secret-access-key' => '**************************************',
) ) );
				</textarea>
									<?php
								}
								?>
							</td>
						</tr>

						<!-- Use Server Role -->
						<?php
						if ( $provider_class::use_server_roles_allowed() ) {
							?>
							<tr class="asc3f-provider-authmethod-title">
								<th>
									<input type="radio" name="authmethod" id="as3cf-provider-<?php echo $provider_key; ?>-server-role" value="server-role"<?php echo $provider_selected ? '' : ' disabled="disabled"';
									echo $server_role_authmethod_attr; ?>>
								</th>
								<td>
									<label for="as3cf-provider-<?php echo $provider_key; ?>-server-role"><?php _e( 'My server is on Amazon EC2 and I\'d like to use IAM Roles', 'amazon-s3-and-cloudfront' ) ?></label>
								</td>
							</tr>
							<tr class="asc3f-provider-authmethod-content" data-provider-authmethod="server-role"<?php echo 'server-role' !== $selected_authmethod ? ' style="display: none"' : ''; ?>>
								<td></td>
								<td>
									<?php if ( $provider_class::use_server_roles() ) {
										printf( __( 'You\'ve defined the \'%1$s\' constant in your wp-config.php. To select a different option here, simply comment out or remove the define in your wp-config.php.' ), $use_server_role_constant );
										echo '&nbsp;' . $this->more_info_link( '/wp-offload-media/doc/' . $provider_service_quick_start_slug . '/#save-access-keys' );
									} else {
										_e( 'If you host your WordPress site on an Amazon EC2 instance you should make use of IAM Roles. To tell WP Offload Media you\'re using IAM Roles, copy the following snippet <strong>near the top</strong> of your wp-config.php.', 'amazon-s3-and-cloudfront' );
										echo '&nbsp;' . $this->more_info_link( '/wp-offload-media/doc/' . $provider_service_quick_start_slug . '/#save-access-keys' );
										?>
										<textarea rows="1" class="as3cf-define-snippet code clear" readonly>
define( 'AS3CF_AWS_USE_EC2_IAM_ROLE', true );
				</textarea>
										<?php
									}
									?>
								</td>
							</tr>
							<?php
						} // Server Roles Allowed
						?>

						<!-- Stored Access Keys -->
						<tr class="asc3f-provider-authmethod-title">
							<th>
								<input type="radio" name="authmethod" id="as3cf-provider-<?php echo $provider_key; ?>-db" value="db"<?php echo $provider_selected ? '' : ' disabled="disabled"';
								echo $db_authmethod_attr; ?>>
							</th>
							<td>
								<label for="as3cf-provider-<?php echo $provider_key; ?>-db"><?php _e( 'I understand the risks but I\'d like to store access keys in the database anyway (not recommended)', 'amazon-s3-and-cloudfront' ) ?></label>
							</td>
						</tr>
						<tr class="asc3f-provider-authmethod-content" data-provider-authmethod="db"<?php echo 'db' !== $selected_authmethod ? ' style="display: none"' : ''; ?>>
							<td></td>
							<td>
								<?php
								_e( 'Storing your access keys in the database is less secure than the options above, but if you\'re ok with that, go ahead and enter your keys in the form below.', 'amazon-s3-and-cloudfront' );
								echo '&nbsp;' . $this->more_info_link( '/wp-offload-media/doc/' . $provider_service_quick_start_slug . '/#save-access-keys' );
								?>
								<table class="form-table as3cf-access-keys">
									<tr valign="top">
										<th scope="row"><?php _e( 'Access Key ID', 'amazon-s3-and-cloudfront' ) ?></th>
										<td>
											<div class="as3cf-field-wrap">
												<input
													type="text"
													name="access-key-id"
													value="<?php echo $provider_selected ? esc_attr( $provider->get_access_key_id() ) : ''; ?>"
													autocomplete="off"
													<?php echo $provider_selected ? '' : ' disabled="disabled"'; ?>
												>
											</div>
										</td>
									</tr>

									<tr valign="top">
										<th scope="row"><?php _e( 'Secret Access Key', 'amazon-s3-and-cloudfront' ) ?></th>
										<td>
											<div class="as3cf-field-wrap">
												<input
													type="text"
													name="secret-access-key"
													value="<?php echo $provider_selected && $provider->get_secret_access_key() ? _x( '-- not shown --', 'placeholder for hidden secret access key, 39 char max', 'amazon-s3-and-cloudfront' ) : '' ?>"
													autocomplete="off"
													<?php echo $provider_selected ? '' : ' disabled="disabled"'; ?>
												>
											</div>
										</td>
									</tr>
								</table> <!-- DB Access Keys -->
							</td>
						</tr>
					</table> <!-- Auth Methods -->
				</td>
			</tr>
		<?php } // Providers ?>
	</table>
	<p>
		<button type="submit" class="button button-primary"><?php echo empty( $this->get_defined_setting( 'bucket', false ) ) ? __( 'Next', 'amazon-s3-and-cloudfront' ) : __( 'Save Changes', 'amazon-s3-and-cloudfront' ); ?></button>
	</p>
</div>
