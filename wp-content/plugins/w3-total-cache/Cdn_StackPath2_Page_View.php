<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();

?>
<?php if ( !$authorized ): ?>
	<tr>
		<th style="width: 300px;"><label><?php _e( 'Create account:', 'w3-total-cache' )?></label></th>
		<td>
			<a href="<?php echo esc_url( W3TC_STACKPATH_SIGNUP_URL )?>" target="_blank" id="netdna-stackpath-create-account" class="button-primary"><?php w3tc_e( 'cdn.stackpath2.signUpAndSave', 'Sign Up Now and save!' ) ?></a>
			<br />
			<span class="description"><?php w3tc_e( 'cdn.stackpath2.signUpAndSave.description', 'StackPath is a service that lets you speed up your site even more with W3 Total Cache. Sign up now to recieve a special offer!' ) ?></span>
		</td>
	</tr>
<?php endif ?>



<tr>
	<th style="width: 300px;">
		<label>
			<?php _e( 'Specify account credentials:', 'w3-total-cache' ); ?>
		</label>
	</th>
	<td>
		<?php if ( $authorized ): ?>
			<input class="w3tc_cdn_stackpath2_authorize button-primary"
				type="button"
				value="<?php _e( 'Reauthorize', 'w3-total-cache' ); ?>"
				/>
		<?php else: ?>
			<input class="w3tc_cdn_stackpath2_authorize button-primary"
				type="button"
				value="<?php _e( 'Authorize', 'w3-total-cache' ); ?>"
				/>
		<?php endif ?>
	</td>
</tr>

<?php if ( $authorized ): ?>
<?php if ( !is_null( $http_domain ) ): ?>
<tr>
	<th>
		<label><?php _e( '<acronym title="Content Delivery Network">CDN</acronym> <acronym title="HyperText Transfer Protocol">HTTP</acronym> <acronym title="Canonical Name">CNAME</acronym>:', 'w3-total-cache' ); ?></label>
	</th>
	<td class="w3tc_config_value_text">
		<?php echo htmlspecialchars( $http_domain ) ?><br />
		<span class="description">
			This website domain has to be <acronym title="Canonical Name">CNAME</acronym> pointing to this
			<acronym title="Content Delivery Network">CDN</acronym> domain for <acronym title="HyperText Transfer Protocol">HTTP</acronym> requests
		</span>
	</td>
</tr>
<?php endif ?>
<?php if ( !is_null( $https_domain ) ): ?>
<tr>
	<th>
		<label><?php _e( '<acronym title="Content Delivery Network">CDN</acronym> <acronym title="HyperText Transfer Protocol over SSL">HTTPS</acronym> <acronym title="Canonical Name">CNAME</acronym>:', 'w3-total-cache' ); ?></label>
	</th>
	<td class="w3tc_config_value_text">
		<?php echo htmlspecialchars( $https_domain ) ?><br />
		<span class="description">
			This website domain has to be <acronym title="Canonical Name">CNAME</acronym> pointing to this
			<acronym title="Content Delivery Network">CDN</acronym> domain for <acronym title="HyperText Transfer Protocol over SSL">HTTPS</acronym> requests
		</span>
	</td>
</tr>
<?php endif ?>

<tr>
	<th><label for="cdn_stackpath_ssl"><?php _e( '<acronym title="Secure Sockets Layer">SSL</acronym> support', 'w3-total-cache' )?>:</label></th>
	<td>
		<select id="cdn_stackpath_ssl" name="cdn__stackpath__ssl" <?php Util_Ui::sealing_disabled( 'cdn.' ) ?>>
			<option value="auto"<?php selected( $config->get_string( 'cdn.stackpath.ssl' ), 'auto' ); ?>><?php _e( 'Auto (determine connection type automatically)', 'w3-total-cache' )?></option>
			<option value="enabled"<?php selected( $config->get_string( 'cdn.stackpath.ssl' ), 'enabled' ); ?>><?php _e( 'Enabled (always use <acronym title="Secure Sockets Layer">SSL</acronym>)', 'w3-total-cache' )?></option>
			<option value="disabled"<?php selected( $config->get_string( 'cdn.stackpath.ssl' ), 'disabled' ); ?>><?php _e( 'Disabled (always use <acronym title="HyperText Transfer Protocol">HTTP</acronym>)', 'w3-total-cache' )?></option>
		</select>
		<br /><span class="description"><?php _e( 'Some <acronym title="Content Delivery Network">CDN</acronym> providers may or may not support <acronym title="Secure Sockets Layer">SSL</acronym>, contact your vendor for more information.', 'w3-total-cache' )?></span>
	</td>
</tr>
<tr>
	<th><?php _e( 'Replace site\'s hostname with:', 'w3-total-cache' )?></th>
	<td>
		<?php $cnames = $config->get_array( 'cdn.stackpath2.domain' ); include W3TC_INC_DIR . '/options/cdn/common/cnames.php'; ?>
		<br /><span class="description"><?php _e( 'Enter the hostname provided by your <acronym title="Content Delivery Network">CDN</acronym> provider, this value will replace your site\'s hostname in the <acronym title="Hypertext Markup Language">HTML</acronym>.', 'w3-total-cache' )?></span>
	</td>
</tr>
<tr>
	<th colspan="2">
		<input id="cdn_test" class="button {type: 'stackpath', nonce: '<?php echo wp_create_nonce( 'w3tc' ); ?>'}" type="button" value="<?php _e( 'Test StackPath', 'w3-total-cache' )?>" /> <span id="cdn_test_status" class="w3tc-status w3tc-process"></span>
	</th>
</tr>

<?php endif ?>
