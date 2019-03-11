<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();

?>
<tr>
	<th colspan="2">
		<?php $this->checkbox( 'cdn.ftp.pasv' ) ?> <?php _e( 'Use passive <acronym title="File Transfer Protocol">FTP</acronym> mode', 'w3-total-cache' ); ?></label><br />
		<span class="description"><?php _e( 'Enable this option only if there are connectivity issues, otherwise it\'s not recommended.', 'w3-total-cache' ); ?></span>
	</th>
</tr>
<tr>
	<th style="width: 300px;"><label for="cdn_ftp_host"><?php _e( '<acronym title="File Transfer Protocol">FTP</acronym> hostname:', 'w3-total-cache' ); ?></label></th>
	<td>
		<input id="cdn_ftp_host" type="text" name="cdn__ftp__host"
                   <?php Util_Ui::sealing_disabled( 'cdn.' ) ?> value="<?php echo esc_attr( $this->_config->get_string( 'cdn.ftp.host' ) ); ?>" size="30" /><br />
		<span class="description"><?php _e( 'Specify the server\'s address, e.g.: "ftp.domain.com". Try "127.0.0.1" if using a sub-domain on the same server as your site.', 'w3-total-cache' ); ?></span>
	</td>
</tr>
<tr>
	<th><label for="cdn_ftp_type"><?php _e( '<acronym title="File Transfer Protocol">FTP</acronym> connection:', 'w3-total-cache' ); ?></label></th>
	<td>
		<select id="cdn_ftp_type" name="cdn__ftp__type" <?php Util_Ui::sealing_disabled( 'cdn.' ) ?>>
			<option value=""<?php selected( $this->_config->get_string( 'cdn.ftp.type' ), '' ); ?>><?php _e( 'Plain FTP', 'w3-total-cache' ); ?></option>
			<option value="ftps"<?php selected( $this->_config->get_string( 'cdn.ftp.type' ), 'ftps' ); ?>><?php _e( 'SSL-FTP connection (FTPS)', 'w3-total-cache' ); ?></option>
			<option value="sftp"<?php selected( $this->_config->get_string( 'cdn.ftp.type' ), 'sftp' ); echo function_exists( 'ssh2_connect' ) ? '' : ' disabled'; ?>><?php _e( 'FTP over SSH (SFTP)', 'w3-total-cache' ); ?></option>
		</select>
	</td>
</tr>
<tr>
	<th><label for="cdn_ftp_user"><?php _e( '<acronym title="File Transfer Protocol">FTP</acronym> username:', 'w3-total-cache' ); ?></label></th>
	<td>
		<input id="cdn_ftp_user" class="w3tc-ignore-change" type="text"
                   <?php Util_Ui::sealing_disabled( 'cdn.' ) ?> name="cdn__ftp__user" value="<?php echo esc_attr( $this->_config->get_string( 'cdn.ftp.user' ) ); ?>" size="30" />
	</td>
</tr>
<tr>
	<th><label for="cdn_ftp_pass"><?php _e( '<acronym title="File Transfer Protocol">FTP</acronym> password:', 'w3-total-cache' ); ?></label></th>
	<td>
		<input id="cdn_ftp_pass" class="w3tc-ignore-change"
                   <?php Util_Ui::sealing_disabled( 'cdn.' ) ?> type="password" name="cdn__ftp__pass" value="<?php echo esc_attr( $this->_config->get_string( 'cdn.ftp.pass' ) ); ?>" size="30" />
	</td>
</tr>
<tr>
	<th><label for="cdn_ftp_path"><?php _e( '<acronym title="File Transfer Protocol">FTP</acronym> path:', 'w3-total-cache' ); ?></label></th>
	<td>
		<input id="cdn_ftp_path" type="text" name="cdn__ftp__path"
                   <?php Util_Ui::sealing_disabled( 'cdn.' ) ?> value="<?php echo esc_attr( $this->_config->get_string( 'cdn.ftp.path' ) ); ?>" size="30" /><br />
		<span class="description"><?php _e( 'Specify the directory where files must be uploaded to be accessible in a web browser (the document root).', 'w3-total-cache' ); ?></span>
	</td>
</tr>
<tr>
	<th><label for="cdn_ftp_ssl"><?php _e( '<acronym title="Secure Sockets Layer">SSL</acronym> support:', 'w3-total-cache' ); ?></label></th>
	<td>
		<select id="cdn_ftp_ssl" name="cdn__ftp__ssl" <?php Util_Ui::sealing_disabled( 'cdn.' ) ?>>
			<option value="auto"<?php selected( $this->_config->get_string( 'cdn.ftp.ssl' ), 'auto' ); ?>><?php _e( 'Auto (determine connection type automatically)', 'w3-total-cache' ); ?></option>
			<option value="enabled"<?php selected( $this->_config->get_string( 'cdn.ftp.ssl' ), 'enabled' ); ?>><?php _e( 'Enabled (always use SSL)', 'w3-total-cache' ); ?></option>
			<option value="disabled"<?php selected( $this->_config->get_string( 'cdn.ftp.ssl' ), 'disabled' ); ?>><?php _e( 'Disabled (always use HTTP)', 'w3-total-cache' ); ?></option>
		</select>
        <br /><span class="description"><?php _e( 'Some <acronym title="Content Delivery Network">CDN</acronym> providers may or may not support <acronym title="Secure Sockets Layer">SSL</acronym>, contact your vendor for more information.', 'w3-total-cache' ); ?></span>
	</td>
</tr>
<tr>
	<th colspan="2">
		<?php $this->checkbox( 'cdn.ftp.default_keys', !function_exists( 'ssh2_connect' ) ) ?> <?php _e( 'Use default <acronym title="Secure Shell">SSH</acronym> public/private key files', 'w3-total-cache' ); ?></label><br />
		<span class="description"><?php _e( 'Enable this option if you don\'t have special public/private key files.', 'w3-total-cache' ); ?></span>
	</th>
</tr>
<tr>
	<th><label for="cdn_ftp_pubkey"><?php _e( '<acronym title="Secure File Transfer Protocol">SFTP</acronym> public key:', 'w3-total-cache' ); ?></label></th>
	<td>
		<input id="cdn_ftp_pubkey" class="w3tc-ignore-change" type="text"
			<?php Util_Ui::sealing_disabled( 'cdn.' ) ?> name="cdn__ftp__pubkey" value="<?php echo esc_attr( $this->_config->get_string( 'cdn.ftp.pubkey' ) ); ?>" size="30" <?php echo function_exists( 'ssh2_connect' ) ? '' : 'disabled'; ?> />
	</td>
</tr>
<tr>
	<th><label for="cdn_ftp_privkey"><?php _e( '<acronym title="Secure File Transfer Protocol">SFTP</acronym> private key:', 'w3-total-cache' ); ?></label></th>
	<td>
		<input id="cdn_ftp_privkey" class="w3tc-ignore-change" type="text"
			<?php Util_Ui::sealing_disabled( 'cdn.' ) ?> name="cdn__ftp__privkey" value="<?php echo esc_attr( $this->_config->get_string( 'cdn.ftp.privkey' ) ); ?>" size="30" <?php echo function_exists( 'ssh2_connect' ) ? '' : 'disabled'; ?> />
	</td>
</tr>
<tr>
	<th><?php _e( 'Replace site\'s hostname with:', 'w3-total-cache' ); ?></th>
	<td>
		<?php $cnames = $this->_config->get_array( 'cdn.ftp.domain' ); include W3TC_INC_DIR . '/options/cdn/common/cnames.php'; ?>
		<br /><span class="description"><?php _e( 'Enter the hostname or <acronym title="Canonical Name">CNAME</acronym>(s) of your <acronym title="File Transfer Protocol">FTP</acronym> server configured above, these values will replace your site\'s hostname in the <acronym title="Hypertext Markup Language">HTML</acronym>.', 'w3-total-cache' ); ?></span>
	</td>
</tr>
<tr>
	<th colspan="2">
        <input id="cdn_test" class="button {type: 'ftp', nonce: '<?php echo wp_create_nonce( 'w3tc' ); ?>'}" type="button" value="<?php _e( 'Test FTP server', 'w3-total-cache' ); ?>" /> <span id="cdn_test_status" class="w3tc-status w3tc-process"></span>
    </th>
</tr>
