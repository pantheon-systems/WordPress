<?php
/**
 *
 */
class BackWPup_Destination_Ftp extends BackWPup_Destinations {

	/**
	 * @return array
	 */
	public function option_defaults() {

		return array( 'ftphost' => '', 'ftphostport' => 21, 'ftptimeout' => 90, 'ftpuser' => '', 'ftppass' => '', 'ftpdir' => trailingslashit( sanitize_title_with_dashes( get_bloginfo( 'name' ) ) ), 'ftpmaxbackups' => 15, 'ftppasv' => TRUE, 'ftpssl' => FALSE );
	}



	/**
	 * @param $jobid
	 */
	public function edit_tab( $jobid ) {
		?>

		<h3 class="title"><?php esc_html_e( 'FTP server and login', 'backwpup' ); ?></h3>
		<p></p>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="idftphost"><?php esc_html_e( 'FTP server', 'backwpup' ); ?></label></th>
				<td>
                    <input id="idftphost" name="ftphost" type="text" value="<?php echo esc_attr( BackWPup_Option::get( $jobid, 'ftphost' ) );?>" class="regular-text" autocomplete="off" />
					&nbsp;&nbsp;
                    <label for="idftphostport"><?php esc_html_e( 'Port:', 'backwpup' ); ?>
                    <input name="ftphostport" id="idftphostport" type="number" step="1" min="1" max="66000" value="<?php echo esc_attr( BackWPup_Option::get( $jobid, 'ftphostport' ) ); ?>" class="small-text" /></label>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="idftpuser"><?php esc_html_e( 'Username', 'backwpup' ); ?></label></th>
				<td>
                    <input id="idftpuser" name="ftpuser" type="text" value="<?php echo esc_attr( BackWPup_Option::get( $jobid, 'ftpuser' ) ); ?>" class="user regular-text" autocomplete="off" />
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="idftppass"><?php esc_html_e( 'Password', 'backwpup' ); ?></label></th>
				<td>
                    <input id="idftppass" name="ftppass" type="password" value="<?php echo esc_attr( BackWPup_Encryption::decrypt(BackWPup_Option::get( $jobid, 'ftppass' ) ) ); ?>" class="password regular-text" autocomplete="off" />
				</td>
			</tr>
		</table>

		<h3 class="title"><?php esc_html_e( 'Backup settings', 'backwpup' ); ?></h3>
		<p></p>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="idftpdir"><?php esc_html_e( 'Folder to store files in', 'backwpup' ); ?></label></th>
				<td>
					<input id="idftpdir" name="ftpdir" type="text" value="<?php echo esc_attr( BackWPup_Option::get( $jobid, 'ftpdir' ) ); ?>" class="regular-text" />
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'File Deletion', 'backwpup' ); ?></th>
				<td>
					<?php
					if ( BackWPup_Option::get( $jobid, 'backuptype' ) === 'archive' ) {
						?>
						<label for="idftpmaxbackups">
							<input id="idftpmaxbackups" name="ftpmaxbackups" type="number" min="0" step="1" value="<?php echo esc_attr( BackWPup_Option::get( $jobid, 'ftpmaxbackups' ) ); ?>" class="small-text" />
							&nbsp;<?php esc_html_e( 'Number of files to keep in folder.', 'backwpup' ); ?>
						</label>
					<?php } else { ?>
						<label for="idftpsyncnodelete">
							<input class="checkbox" value="1" type="checkbox" <?php checked( BackWPup_Option::get( $jobid, 'ftpsyncnodelete' ), true ); ?> name="ftpsyncnodelete" id="idftpsyncnodelete" />
							&nbsp;<?php esc_html_e( 'Do not delete files while syncing to destination!', 'backwpup' ); ?>
						</label>
					<?php } ?>
				</td>
			</tr>
		</table>

		<h3 class="title"><?php esc_html_e( 'FTP specific settings', 'backwpup' ); ?></h3>
		<p></p>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="idftptimeout"><?php esc_html_e( 'Timeout for FTP connection', 'backwpup' ); ?></label></th>
				<td>
                    <input id="idftptimeout" name="ftptimeout" type="number" step="1" min="1" max="300"
                           value="<?php echo esc_attr( BackWPup_Option::get( $jobid, 'ftptimeout' ) ); ?>"
                           class="small-text" /> <?php esc_html_e( 'seconds', 'backwpup' ); ?>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'SSL-FTP connection', 'backwpup' ); ?></th>
				<td>
                    <label for="idftpssl"><input class="checkbox" value="1" type="checkbox" <?php checked( BackWPup_Option::get( $jobid, 'ftpssl' ), TRUE ); ?>
                           id="idftpssl" name="ftpssl"<?php if ( ! function_exists( 'ftp_ssl_connect' ) ) echo " disabled=\"disabled\""; ?> /> <?php esc_html_e( 'Use explicit SSL-FTP connection.', 'backwpup' ); ?></label>

				</td>
			</tr>
            <tr>
                <th scope="row"><?php esc_html_e( 'FTP Passive Mode', 'backwpup' ); ?></th>
                <td>
                    <label for="idftppasv"><input class="checkbox" value="1" type="checkbox" <?php checked( BackWPup_Option::get( $jobid, 'ftppasv' ), TRUE ); ?> name="ftppasv" id="idftppasv" /> <?php esc_html_e( 'Use FTP Passive Mode.', 'backwpup' ); ?></label>
                </td>
            </tr>
		</table>

		<?php
	}


	/**
	 * @param $id
	 */
	public function edit_form_post_save( $id ) {

		$_POST[ 'ftphost' ] = str_replace( array( 'http://', 'ftp://' ), '', sanitize_text_field( $_POST[ 'ftphost' ] ) );
		BackWPup_Option::update( $id, 'ftphost', isset( $_POST[ 'ftphost' ] ) ? $_POST[ 'ftphost' ] : '' );

		BackWPup_Option::update( $id, 'ftphostport', ! empty( $_POST[ 'ftphostport' ] ) ? absint( $_POST[ 'ftphostport' ] ) : 21 );
		BackWPup_Option::update( $id, 'ftptimeout', ! empty( $_POST[ 'ftptimeout' ] ) ? absint( $_POST[ 'ftptimeout' ] ) : 90 );
		BackWPup_Option::update( $id, 'ftpuser', sanitize_text_field( $_POST[ 'ftpuser' ] ) );
		BackWPup_Option::update( $id, 'ftppass', BackWPup_Encryption::encrypt( $_POST[ 'ftppass' ] ) );

		if ( ! empty( $_POST[ 'ftpdir' ] ) ) {
			$_POST['ftpdir'] = trailingslashit( str_replace( '//', '/', str_replace( '\\', '/', trim( sanitize_text_field( $_POST['ftpdir'] ) ) ) ) );
		}
		BackWPup_Option::update( $id, 'ftpdir', $_POST[ 'ftpdir' ] );

		BackWPup_Option::update( $id, 'ftpmaxbackups', ! empty( $_POST[ 'ftpmaxbackups' ] ) ? absint( $_POST[ 'ftpmaxbackups' ] ) : 0 );

		if ( function_exists( 'ftp_ssl_connect' ) ) {
			BackWPup_Option::update( $id, 'ftpssl', ! empty( $_POST[ 'ftpssl' ] ) );
		} else {
			BackWPup_Option::update( $id, 'ftpssl', false );
		}

		BackWPup_Option::update( $id, 'ftppasv', ! empty( $_POST[ 'ftppasv' ] ) );
	}

	/**
	 * @param $jobdest
	 * @param $backupfile
	 */
	public function file_delete( $jobdest, $backupfile ) {

		$files = get_site_transient( 'backwpup_'. strtolower( $jobdest ) );
		list( $jobid, $dest ) = explode( '_', $jobdest );

		if ( BackWPup_Option::get( $jobid, 'ftphost' ) && BackWPup_Option::get( $jobid, 'ftpuser' ) && BackWPup_Option::get( $jobid, 'ftppass' ) && function_exists( 'ftp_connect' ) ) {
			$ftp_conn_id = FALSE;
			if ( function_exists( 'ftp_ssl_connect' ) && BackWPup_Option::get( $jobid, 'ftpssl' ) ) { //make SSL FTP connection
				$ftp_conn_id = ftp_ssl_connect( BackWPup_Option::get( $jobid, 'ftphost' ), BackWPup_Option::get( $jobid, 'ftphostport' ), BackWPup_Option::get( $jobid, 'ftptimeout' ) );
			}
			elseif ( ! BackWPup_Option::get( $jobid, 'ftpssl' ) ) { //make normal FTP conection if SSL not work
				$ftp_conn_id = ftp_connect( BackWPup_Option::get( $jobid, 'ftphost' ), BackWPup_Option::get( $jobid, 'ftphostport' ), BackWPup_Option::get( $jobid, 'ftptimeout' ) );
			}
			$loginok = FALSE;
			if ( $ftp_conn_id ) {
				//FTP Login
				if ( @ftp_login( $ftp_conn_id, BackWPup_Option::get( $jobid, 'ftpuser' ), BackWPup_Encryption::decrypt( BackWPup_Option::get( $jobid, 'ftppass' ) ) ) ) {
					$loginok = TRUE;
				}
				else { //if PHP ftp login don't work use raw login
					ftp_raw( $ftp_conn_id, 'USER ' . BackWPup_Option::get( $jobid, 'ftpuser' ) );
					$return = ftp_raw( $ftp_conn_id, 'PASS ' . BackWPup_Encryption::decrypt( BackWPup_Option::get( $jobid, 'ftppass' ) ) );
					if ( substr( trim( $return[ 0 ] ), 0, 3 ) <= 400 )
						$loginok = TRUE;
				}
			}
			if ( $loginok ) {
				ftp_pasv( $ftp_conn_id, BackWPup_Option::get( $jobid, 'ftppasv' ) );
				ftp_delete( $ftp_conn_id, $backupfile );
				//update file list
				foreach ( $files as $key => $file ) {
					if ( is_array( $file ) && $file[ 'file' ] == $backupfile )
						unset( $files[ $key ] );
				}
			}
			else {
				BackWPup_Admin::message( __( 'FTP: Login failure!', 'backwpup' ), TRUE );
			}
		}

		set_site_transient( 'backwpup_'. strtolower( $jobdest ), $files, YEAR_IN_SECONDS );
	}

	/**
	 * @param $jobdest
	 * @return mixed
	 */
	public function file_get_list( $jobdest ) {

		return get_site_transient( 'backwpup_' . strtolower( $jobdest ) );
	}

	/**
	 * @param $job_object
	 * @return bool
	 */
	public function job_run_archive( BackWPup_Job $job_object ) {

		$job_object->substeps_todo = 2 + $job_object->backup_filesize;
		if ( $job_object->steps_data[ $job_object->step_working ]['SAVE_STEP_TRY'] != $job_object->steps_data[ $job_object->step_working ][ 'STEP_TRY' ] )
			$job_object->log( sprintf( __( '%d. Try to send backup file to an FTP server&#160;&hellip;', 'backwpup' ), $job_object->steps_data[ $job_object->step_working ][ 'STEP_TRY' ] ), E_USER_NOTICE );

		if ( ! empty( $job_object->job[ 'ftpssl' ] ) ) { //make SSL FTP connection
			if ( function_exists( 'ftp_ssl_connect' ) ) {
				$ftp_conn_id = ftp_ssl_connect( $job_object->job[ 'ftphost' ], $job_object->job[ 'ftphostport' ], $job_object->job[ 'ftptimeout' ] );
				if ( $ftp_conn_id )
					$job_object->log( sprintf( __( 'Connected via explicit SSL-FTP to server: %s', 'backwpup' ), $job_object->job[ 'ftphost' ] . ':' . $job_object->job[ 'ftphostport' ] ), E_USER_NOTICE );
				else {
					$job_object->log( sprintf( __( 'Cannot connect via explicit SSL-FTP to server: %s', 'backwpup' ), $job_object->job[ 'ftphost' ] . ':' . $job_object->job[ 'ftphostport' ] ), E_USER_ERROR );

					return FALSE;
				}
			}
			else {
				$job_object->log( __( 'PHP function to connect with explicit SSL-FTP to server does not exist!', 'backwpup' ), E_USER_ERROR );

				return TRUE;
			}
		}
		else { //make normal FTP connection if SSL not work
			$ftp_conn_id = ftp_connect( $job_object->job[ 'ftphost' ], $job_object->job[ 'ftphostport' ], $job_object->job[ 'ftptimeout' ] );
			if ( $ftp_conn_id )
				$job_object->log( sprintf( __( 'Connected to FTP server: %s', 'backwpup' ), $job_object->job[ 'ftphost' ] . ':' . $job_object->job[ 'ftphostport' ] ), E_USER_NOTICE );
			else {
				$job_object->log( sprintf( __( 'Cannot connect to FTP server: %s', 'backwpup' ), $job_object->job[ 'ftphost' ] . ':' . $job_object->job[ 'ftphostport' ] ), E_USER_ERROR );

				return FALSE;
			}
		}

		//FTP Login
		$job_object->log( sprintf( __( 'FTP client command: %s', 'backwpup' ), 'USER ' . $job_object->job[ 'ftpuser' ] ), E_USER_NOTICE );
		if ( $loginok = @ftp_login( $ftp_conn_id, $job_object->job[ 'ftpuser' ], BackWPup_Encryption::decrypt( $job_object->job[ 'ftppass' ] ) ) ) {
			$job_object->log( sprintf( __( 'FTP server response: %s', 'backwpup' ), 'User ' . $job_object->job[ 'ftpuser' ] . ' logged in.' ), E_USER_NOTICE );
		}
		else { //if PHP ftp login don't work use raw login
			$return = ftp_raw( $ftp_conn_id, 'USER ' . $job_object->job[ 'ftpuser' ] );
			$job_object->log( sprintf( __( 'FTP server reply: %s', 'backwpup' ), $return[ 0 ] ), E_USER_NOTICE );
			if ( substr( trim( $return[ 0 ] ), 0, 3 ) <= 400 ) {
				$job_object->log( sprintf( __( 'FTP client command: %s', 'backwpup' ), 'PASS *******' ), E_USER_NOTICE );
				$return = ftp_raw( $ftp_conn_id, 'PASS ' . BackWPup_Encryption::decrypt( $job_object->job[ 'ftppass' ] ) );
				if ( substr( trim( $return[ 0 ] ), 0, 3 ) <= 400 ) {
					$job_object->log( sprintf( __( 'FTP server reply: %s', 'backwpup' ), $return[ 0 ] ), E_USER_NOTICE );
					$loginok = TRUE;
				} else {
					$job_object->log( sprintf( __( 'FTP server reply: %s', 'backwpup' ), $return[ 0 ] ), E_USER_ERROR );
				}
			}
		}

		if ( ! $loginok ) {
			return FALSE;
		}

		//SYSTYPE
		$job_object->log( sprintf( __( 'FTP client command: %s', 'backwpup' ), 'SYST' ), E_USER_NOTICE );
		$systype = ftp_systype( $ftp_conn_id );
		if ( $systype )
			$job_object->log( sprintf( __( 'FTP server reply: %s', 'backwpup' ), $systype ), E_USER_NOTICE );
		else
			$job_object->log( sprintf( __( 'FTP server reply: %s', 'backwpup' ), __( 'Error getting SYSTYPE', 'backwpup' ) ), E_USER_ERROR );

		//set actual ftp dir to ftp dir
		if ( empty( $job_object->job[ 'ftpdir' ] ) )
			$job_object->job[ 'ftpdir' ] = trailingslashit( ftp_pwd( $ftp_conn_id ) );
		// prepend actual ftp dir if relative dir
		if ( substr( $job_object->job[ 'ftpdir' ], 0, 1 ) != '/' )
			$job_object->job[ 'ftpdir' ] = trailingslashit( ftp_pwd( $ftp_conn_id ) ) . $job_object->job[ 'ftpdir' ];

		//test ftp dir and create it if not exists
		if ( $job_object->job[ 'ftpdir' ] != '/' ) {
			@ftp_chdir( $ftp_conn_id, '/' ); //go to root
			$ftpdirs = explode( '/', trim( $job_object->job[ 'ftpdir' ], '/' ) );
			foreach ( $ftpdirs as $ftpdir ) {
				if ( empty( $ftpdir ) )
					continue;
				if ( ! @ftp_chdir( $ftp_conn_id, $ftpdir ) ) {
					if ( @ftp_mkdir( $ftp_conn_id, $ftpdir ) ) {
						$job_object->log( sprintf( __( 'FTP Folder "%s" created!', 'backwpup' ), $ftpdir ), E_USER_NOTICE );
						ftp_chdir( $ftp_conn_id, $ftpdir );
					}
					else {
						$job_object->log( sprintf( __( 'FTP Folder "%s" cannot be created!', 'backwpup' ), $ftpdir ), E_USER_ERROR );

						return FALSE;
					}
				}
			}
		}

		// Get the current working directory
		$current_ftp_dir = trailingslashit( ftp_pwd( $ftp_conn_id ) );
		if ( $job_object->substeps_done == 0 )
			$job_object->log( sprintf( __( 'FTP current folder is: %s', 'backwpup' ), $current_ftp_dir ), E_USER_NOTICE );

		//get file size to resume upload
		@clearstatcache();
		$job_object->substeps_done = @ftp_size( $ftp_conn_id, $job_object->job[ 'ftpdir' ] . $job_object->backup_file );
		if ( $job_object->substeps_done == -1 )
			$job_object->substeps_done = 0;

		//PASV
		$job_object->log( sprintf( __( 'FTP client command: %s', 'backwpup' ), 'PASV' ), E_USER_NOTICE );
		if ( $job_object->job[ 'ftppasv' ] ) {
			if ( ftp_pasv( $ftp_conn_id, TRUE ) )
				$job_object->log( sprintf( __( 'FTP server reply: %s', 'backwpup' ), __( 'Entering passive mode', 'backwpup' ) ), E_USER_NOTICE );
			else
				$job_object->log( sprintf( __( 'FTP server reply: %s', 'backwpup' ), __( 'Cannot enter passive mode', 'backwpup' ) ), E_USER_WARNING );
		}
		else {
			if ( ftp_pasv( $ftp_conn_id, FALSE ) )
				$job_object->log( sprintf( __( 'FTP server reply: %s', 'backwpup' ), __( 'Entering normal mode', 'backwpup' ) ), E_USER_NOTICE );
			else
				$job_object->log( sprintf( __( 'FTP server reply: %s', 'backwpup' ), __( 'Cannot enter normal mode', 'backwpup' ) ), E_USER_WARNING );
		}

		if ( $job_object->substeps_done < $job_object->backup_filesize ) {
			$job_object->log( __( 'Starting upload to FTP &#160;&hellip;', 'backwpup' ), E_USER_NOTICE );
			if ( $fp = fopen( $job_object->backup_folder . $job_object->backup_file, 'rb' ) ) {
				//go to actual file pos
				fseek( $fp, $job_object->substeps_done );
				$ret = ftp_nb_fput( $ftp_conn_id, $current_ftp_dir . $job_object->backup_file, $fp, FTP_BINARY, $job_object->substeps_done );
				while ( $ret == FTP_MOREDATA ) {
					$job_object->substeps_done = ftell( $fp );
					$job_object->update_working_data();
					$job_object->do_restart_time();
					$ret = ftp_nb_continue( $ftp_conn_id );
				}
				if ( $ret != FTP_FINISHED ) {
					$job_object->log( __( 'Cannot transfer backup to FTP server!', 'backwpup' ), E_USER_ERROR );
					return FALSE;
				}
				else {
					$job_object->substeps_done = $job_object->backup_filesize + 1;
					$job_object->log( sprintf( __( 'Backup transferred to FTP server: %s', 'backwpup' ), $current_ftp_dir . $job_object->backup_file ), E_USER_NOTICE );
					if ( ! empty( $job_object->job[ 'jobid' ] ) ) {
						BackWPup_Option::update( $job_object->job[ 'jobid' ], 'lastbackupdownloadurl', "ftp://" . $job_object->job[ 'ftpuser' ] . ":" . BackWPup_Encryption::decrypt( $job_object->job[ 'ftppass' ] ) . "@" . $job_object->job[ 'ftphost' ] . ':' . $job_object->job[ 'ftphostport' ] . $current_ftp_dir . $job_object->backup_file );
					}
				}
				fclose( $fp );
			} else {
				$job_object->log( __( 'Can not open source file for transfer.', 'backwpup' ), E_USER_ERROR );
				return FALSE;
			}
		}

		$backupfilelist = array();
		$filecounter    = 0;
		$files          = array();
		if ( $filelist = ftp_nlist( $ftp_conn_id, '.' ) ) {
			foreach ( $filelist as $file ) {
				if ( basename( $file ) != '.' && basename( $file ) != '..' ) {
					if ( $job_object->is_backup_archive( $file ) ) {
						$time = ftp_mdtm( $ftp_conn_id, $file );
						if ( $time != - 1 )
							$backupfilelist[ $time ] = basename( $file );
						else
							$backupfilelist[ ] = basename( $file );
					}
					$files[ $filecounter ][ 'folder' ]      = 'ftp://' . $job_object->job[ 'ftphost' ] . ':' . $job_object->job[ 'ftphostport' ] . $job_object->job[ 'ftpdir' ];
					$files[ $filecounter ][ 'file' ]        = $job_object->job[ 'ftpdir' ] . basename( $file );
					$files[ $filecounter ][ 'filename' ]    = basename( $file );
					$files[ $filecounter ][ 'downloadurl' ] = 'ftp://' . rawurlencode( $job_object->job[ 'ftpuser' ] ) . ':' . rawurlencode( BackWPup_Encryption::decrypt( $job_object->job[ 'ftppass' ] ) ) . '@' . $job_object->job[ 'ftphost' ] . ':' . $job_object->job[ 'ftphostport' ] . $job_object->job[ 'ftpdir' ] . basename( $file );
					$files[ $filecounter ][ 'filesize' ]    = ftp_size( $ftp_conn_id, $file );
					$files[ $filecounter ][ 'time' ]        = ftp_mdtm( $ftp_conn_id, $file );
					$filecounter ++;
				}
			}
		}

		if ( ! empty( $job_object->job[ 'ftpmaxbackups' ] ) && $job_object->job[ 'ftpmaxbackups' ] > 0 ) { //Delete old backups
			if ( count( $backupfilelist ) > $job_object->job[ 'ftpmaxbackups' ] ) {
				ksort( $backupfilelist );
				$numdeltefiles = 0;
				while ( $file = array_shift( $backupfilelist ) ) {
					if ( count( $backupfilelist ) < $job_object->job[ 'ftpmaxbackups' ] )
						break;
					if ( ftp_delete( $ftp_conn_id, $file ) ) { //delete files on ftp
						foreach ( $files as $key => $filedata ) {
							if ( $filedata[ 'file' ] == $job_object->job[ 'ftpdir' ] . $file )
								unset( $files[ $key ] );
						}
						$numdeltefiles ++;
					}
					else
						$job_object->log( sprintf( __( 'Cannot delete "%s" on FTP server!', 'backwpup' ), $job_object->job[ 'ftpdir' ] . $file ), E_USER_ERROR );
				}
				if ( $numdeltefiles > 0 )
					$job_object->log( sprintf( _n( 'One file deleted on FTP server', '%d files deleted on FTP server', $numdeltefiles, 'backwpup' ), $numdeltefiles ), E_USER_NOTICE );
			}
		}
		set_site_transient( 'backwpup_' . $job_object->job[ 'jobid' ] . '_ftp', $files, YEAR_IN_SECONDS );
		$job_object->substeps_done++;

		ftp_close( $ftp_conn_id );

		return TRUE;
	}

	/**
	 * @param $job_settings
	 * @return bool
	 */
	public function can_run( array $job_settings ) {

		if ( empty( $job_settings[ 'ftphost' ] ) )
			return FALSE;

		if ( empty( $job_settings[ 'ftpuser' ] ) )
			return FALSE;

		if ( empty( $job_settings[ 'ftppass' ] ) )
			return FALSE;

		return TRUE;
	}

}
