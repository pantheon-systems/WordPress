<?php

/**
 * Documentation: https://www.dropbox.com/developers/reference/api
 */
class BackWPup_Destination_Dropbox extends BackWPup_Destinations {

	/**
	 * @var $backwpup_job_object BackWPup_Job
	 */
	public static $backwpup_job_object = null;

	/**
	 * @return array
	 */
	public function option_defaults() {

		return array(
			'dropboxtoken'        => array(),
			'dropboxroot'         => 'sandbox',
			'dropboxmaxbackups'   => 15,
			'dropboxsyncnodelete' => true,
			'dropboxdir'          => trailingslashit( sanitize_file_name( get_bloginfo( 'name' ) ) )
		);
	}


	/**
	 * @param $jobid
	 */
	public function edit_tab( $jobid ) {

		if ( ! empty( $_GET['deleteauth'] ) ) {
			//disable token on dropbox
			try {
				$dropbox = new BackWPup_Destination_Dropbox_API( BackWPup_Option::get( $jobid, 'dropboxroot' ) );
				if ( BackWPup_Option::get( $jobid, 'dropboxsecret' ) ) {
					$dropbox->setOAuthTokens( array(
						'access_token'       => BackWPup_Option::get( $jobid, 'dropboxtoken' ),
						'oauth_token_secret' => BackWPup_Encryption::decrypt( BackWPup_Option::get( $jobid, 'dropboxsecret' ) )
					) );
				} else {
					$dropbox->setOAuthTokens( BackWPup_Option::get( $jobid, 'dropboxtoken' ) );
				}
				$dropbox->disable_access_token();
			} catch ( Exception $e ) {
				echo '<div id="message" class="error"><p>' . sprintf( __( 'Dropbox API: %s', 'backwpup' ), $e->getMessage() ) . '</p></div>';
			}
			BackWPup_Option::update( $jobid, 'dropboxtoken', array() );
			BackWPup_Option::update( $jobid, 'dropboxroot', 'sandbox' );
			BackWPup_Option::delete( $jobid, 'dropboxsecret' );
		}

		$dropbox          = new BackWPup_Destination_Dropbox_API( 'dropbox' );
		$dropbox_auth_url = $dropbox->oAuthAuthorize();
		$dropbox          = new BackWPup_Destination_Dropbox_API( 'sandbox' );
		$sandbox_auth_url = $dropbox->oAuthAuthorize();

		$dropboxtoken = BackWPup_Option::get( $jobid, 'dropboxtoken' );
		?>

		<h3 class="title"><?php esc_html_e( 'Login', 'backwpup' ); ?></h3>
		<p></p>
		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'Authentication', 'backwpup' ); ?></th>
				<td><?php if ( empty( $dropboxtoken['access_token'] ) ) { ?>
						<span style="color:red;"><?php esc_html_e( 'Not authenticated!', 'backwpup' ); ?></span><br/>&nbsp;<br/>
						<a class="button secondary"
						   href="http://db.tt/8irM1vQ0"><?php esc_html_e( 'Create Account', 'backwpup' ); ?></a>
					<?php } else { ?>
						<span style="color:green;"><?php esc_html_e( 'Authenticated!', 'backwpup' ); ?></span><br/>&nbsp;<br/>
						<a class="button secondary"
						   href="<?php echo wp_nonce_url(network_admin_url( 'admin.php?page=backwpupeditjob&deleteauth=1&jobid=' . $jobid . '&tab=dest-dropbox'), 'edit-job'  ); ?>"
						   title="<?php esc_html_e( 'Delete Dropbox Authentication', 'backwpup' ); ?>"><?php esc_html_e( 'Delete Dropbox Authentication', 'backwpup' ); ?></a>
					<?php } ?>
				</td>
			</tr>

			<?php if ( empty( $dropboxtoken['access_token'] ) ) { ?>
				<tr>
					<th scope="row"><label for="id_sandbox_code"><?php esc_html_e( 'App Access to Dropbox', 'backwpup' ); ?></label></th>
					<td>
						<input id="id_sandbox_code" name="sandbox_code" type="text" value="" class="regular-text code" />&nbsp;
						<a class="button secondary" href="<?php echo esc_attr( $sandbox_auth_url ); ?>" target="_blank"><?php esc_html_e( 'Get Dropbox App auth code', 'backwpup' ); ?></a>
						<p class="description"><?php esc_html_e( 'A dedicated folder named BackWPup will be created inside of the Apps folder in your Dropbox. BackWPup will get read and write access to that folder only. You can specify a subfolder as your backup destination for this job in the destination field below.', 'backwpup' ); ?></p>
					</td>
				</tr>
				<tr>
					<th></th>
					<td><?php esc_html_e( '— OR —', 'backwpup' ); ?></td>
				</tr>
				<tr>
					<th scope="row"><label for="id_dropbbox_code"><?php esc_html_e( 'Full Access to Dropbox', 'backwpup' ); ?></label></th>
					<td>
						<input id="id_dropbbox_code" name="dropbbox_code" type="text" value="" class="regular-text code" />&nbsp;
						<a class="button secondary" href="<?php echo esc_attr( $dropbox_auth_url ); ?>" target="_blank"><?php esc_html_e( 'Get full Dropbox auth code ', 'backwpup' ); ?></a>
						<p class="description"><?php esc_html_e( 'BackWPup will have full read and write access to your entire Dropbox. You can specify your backup destination wherever you want, just be aware that ANY files or folders inside of your Dropbox can be overridden or deleted by BackWPup.', 'backwpup' ); ?></p>
					</td>
				</tr>
			<?php } ?>
		</table>


		<h3 class="title"><?php esc_html_e( 'Backup settings', 'backwpup' ); ?></h3>
		<p></p>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="iddropboxdir"><?php esc_html_e( 'Destination Folder', 'backwpup' ); ?></label></th>
				<td>
					<input id="iddropboxdir" name="dropboxdir" type="text" value="<?php echo esc_attr( BackWPup_Option::get( $jobid, 'dropboxdir' ) ); ?>" class="regular-text" />
					<p class="description">
						<?php esc_attr_e( 'Specify a subfolder where your backup archives will be stored. If you use the App option from above, this folder will be created inside of Apps/BackWPup. Otherwise it will be created at the root of your Dropbox. Already exisiting folders with the same name will not be overriden.', 'backwpup' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'File Deletion', 'backwpup' ); ?></th>
				<td>
					<?php
					if ( BackWPup_Option::get( $jobid, 'backuptype' ) === 'archive' ) {
						?>
						<label for="iddropboxmaxbackups">
							<input id="iddropboxmaxbackups" name="dropboxmaxbackups" type="number" min="0" step="1" value="<?php echo esc_attr( BackWPup_Option::get( $jobid, 'dropboxmaxbackups' ) ); ?>" class="small-text" />
							&nbsp;<?php esc_html_e( 'Number of files to keep in folder.', 'backwpup' ); ?>
						</label>
					<?php } else { ?>
						<label for="iddropboxsyncnodelete">
							<input class="checkbox" value="1" type="checkbox" <?php checked( BackWPup_Option::get( $jobid, 'dropboxsyncnodelete' ), true ); ?> name="dropboxsyncnodelete" id="iddropboxsyncnodelete" />
							&nbsp;<?php esc_html_e( 'Do not delete files while syncing to destination!', 'backwpup' ); ?>
						</label>
					<?php } ?>
				</td>
			</tr>
		</table>

		<?php
	}

	/**
	 * @param $jobid
	 *
	 * @return string|void
	 */
	public function edit_form_post_save( $jobid ) {

		// get auth
		if ( ! empty( $_POST['sandbox_code'] ) ) {
			try {
				$dropbox      = new BackWPup_Destination_Dropbox_API( 'sandbox' );
				$dropboxtoken = $dropbox->oAuthToken( $_POST['sandbox_code'] );
				BackWPup_Option::update( $jobid, 'dropboxtoken', $dropboxtoken );
				BackWPup_Option::update( $jobid, 'dropboxroot', 'sandbox' );
			} catch ( Exception $e ) {
				BackWPup_Admin::message( 'DROPBOX: ' . $e->getMessage(), true );
			}
		}

		if ( ! empty( $_POST['dropbbox_code'] ) ) {
			try {
				$dropbox      = new BackWPup_Destination_Dropbox_API( 'dropbox' );
				$dropboxtoken = $dropbox->oAuthToken( $_POST['dropbbox_code'] );
				BackWPup_Option::update( $jobid, 'dropboxtoken', $dropboxtoken );
				BackWPup_Option::update( $jobid, 'dropboxroot', 'dropbox' );
			} catch ( Exception $e ) {
				BackWPup_Admin::message( 'DROPBOX: ' . $e->getMessage(), true );
			}
		}

		BackWPup_Option::update( $jobid, 'dropboxsyncnodelete', ! empty( $_POST['dropboxsyncnodelete'] ) );
		BackWPup_Option::update( $jobid, 'dropboxmaxbackups', ! empty( $_POST['dropboxmaxbackups'] ) ? absint( $_POST['dropboxmaxbackups'] ) : 0 );

		$_POST['dropboxdir'] = trailingslashit( str_replace( '//', '/', str_replace( '\\', '/', trim( sanitize_text_field( $_POST['dropboxdir'] ) ) ) ) );
		if ( substr( $_POST['dropboxdir'], 0, 1 ) === '/' ) {
			$_POST['dropboxdir'] = substr( $_POST['dropboxdir'], 1 );
		}
		if ( $_POST['dropboxdir'] === '/' ) {
			$_POST['dropboxdir'] = '';
		}
		BackWPup_Option::update( $jobid, 'dropboxdir', $_POST['dropboxdir'] );

	}

	/**
	 * @param $jobdest
	 * @param $backupfile
	 */
	public function file_delete( $jobdest, $backupfile ) {

		$files = get_site_transient( 'backwpup_' . strtolower( $jobdest ) );
		list( $jobid, $dest ) = explode( '_', $jobdest );

		try {
			$dropbox = new BackWPup_Destination_Dropbox_API( BackWPup_Option::get( $jobid, 'dropboxroot' ) );
			$dropbox->setOAuthTokens( BackWPup_Option::get( $jobid, 'dropboxtoken' ) );
			$dropbox->fileopsDelete( $backupfile );
			//update file list
			foreach ( $files as $key => $file ) {
				if ( is_array( $file ) && $file['file'] == $backupfile ) {
					unset( $files[ $key ] );
				}
			}
			unset( $dropbox );
		} catch ( Exception $e ) {
			BackWPup_Admin::message( 'DROPBOX: ' . $e->getMessage(), true );
		}

		set_site_transient( 'backwpup_' . strtolower( $jobdest ), $files, YEAR_IN_SECONDS );
	}

	/**
	 * @param $jobid
	 * @param $get_file
	 */
	public function file_download( $jobid, $get_file ) {

		try {
			$dropbox = new BackWPup_Destination_Dropbox_API( BackWPup_Option::get( $jobid, 'dropboxroot' ) );
			$dropbox->setOAuthTokens( BackWPup_Option::get( $jobid, 'dropboxtoken' ) );
			$media = $dropbox->media( $get_file );
			if ( ! empty( $media['url'] ) ) {
				header( "Location: " . $media['url'] );
			}
			die();
		} catch ( Exception $e ) {
			die( $e->getMessage() );
		}
	}

	/**
	 * @param $jobdest
	 *
	 * @return mixed
	 */
	public function file_get_list( $jobdest ) {
		return get_site_transient( 'backwpup_' . strtolower( $jobdest ) );
	}

	/**
	 * @param $job_object
	 *
	 * @return bool
	 */
	public function job_run_archive( BackWPup_Job $job_object ) {

		$job_object->substeps_todo = 2 + $job_object->backup_filesize;
		if ( $job_object->steps_data[ $job_object->step_working ]['SAVE_STEP_TRY'] != $job_object->steps_data[ $job_object->step_working ]['STEP_TRY'] ) {
			$job_object->log( sprintf( __( '%d. Try to send backup file to Dropbox&#160;&hellip;', 'backwpup' ), $job_object->steps_data[ $job_object->step_working ]['STEP_TRY'] ) );
		}

		try {
			$dropbox = new BackWPup_Destination_Dropbox_API( $job_object->job['dropboxroot'] );
			// cahnge oauth1 to oauth2 token
			if ( ! empty( $job_object->job['dropboxsecret'] ) && empty( $job_object->job['dropboxtoken']['access_token'] ) ) {
				$dropbox->setOAuthTokens( array(
					'access_token'       => $job_object->job['dropboxtoken'],
					'oauth_token_secret' => BackWPup_Encryption::decrypt( $job_object->job['dropboxsecret'] )
				) );
				$job_object->job['dropboxtoken'] = $dropbox->token_from_oauth1();
				BackWPup_Option::update( $job_object->job['jobid'], 'dropboxtoken', $job_object->job['dropboxtoken'] );
				BackWPup_Option::delete( $job_object->job['jobid'], 'dropboxsecret' );
			}
			// set the tokens
			$dropbox->setOAuthTokens( $job_object->job['dropboxtoken'] );

			//get account info
			if ( $job_object->steps_data[ $job_object->step_working ]['SAVE_STEP_TRY'] != $job_object->steps_data[ $job_object->step_working ]['STEP_TRY'] ) {
				$info = $dropbox->accountInfo();
				if ( ! empty( $info['uid'] ) ) {
					if ( $job_object->is_debug() ) {
						$user = $info['display_name'] . ' (' . $info['email'] . ')';
					} else {
						$user = $info['display_name'];
					}
					$job_object->log( sprintf( __( 'Authenticated with Dropbox of user: %s', 'backwpup' ), $user ) );
					//Quota
					if ( $job_object->is_debug() ) {
						$dropboxfreespase = $info['quota_info']['quota'] - $info['quota_info']['shared'] - $info['quota_info']['normal'];
						$job_object->log( sprintf( __( '%s available on your Dropbox', 'backwpup' ), size_format( $dropboxfreespase, 2 ) ) );
					}
				} else {
					$job_object->log( __( 'Not Authenticated with Dropbox!', 'backwpup' ), E_USER_ERROR );

					return false;
				}
				$job_object->log( __( 'Uploading to Dropbox&#160;&hellip;', 'backwpup' ) );
			}

			// put the file
			self::$backwpup_job_object = &$job_object;

			if ( $job_object->substeps_done < $job_object->backup_filesize ) { //only if upload not complete
				$response = $dropbox->upload( $job_object->backup_folder . $job_object->backup_file, $job_object->job['dropboxdir'] . $job_object->backup_file );
				if ( $response['bytes'] == $job_object->backup_filesize ) {
					if ( ! empty( $job_object->job['jobid'] ) ) {
						BackWPup_Option::update( $job_object->job['jobid'], 'lastbackupdownloadurl', network_admin_url( 'admin.php' ) . '?page=backwpupbackups&action=downloaddropbox&file=' . ltrim( $response['path'], '/' ) . '&jobid=' . $job_object->job['jobid'] );
					}
					$job_object->substeps_done = 1 + $job_object->backup_filesize;
					$job_object->log( sprintf( __( 'Backup transferred to %s', 'backwpup' ), 'https://content.dropboxapi.com/1/files/' . $job_object->job['dropboxroot'] . $response['path'] ), E_USER_NOTICE );
				} else {
					if ( $response['bytes'] != $job_object->backup_filesize ) {
						$job_object->log( __( 'Uploaded file size and local file size don\'t match.', 'backwpup' ), E_USER_ERROR );
					} else {
						$job_object->log(
							sprintf(
								__( 'Error transfering backup to %s.', 'backwpup' ) . ' ' . $response['error'],
								__( 'Dropbox', 'backwpup' )
							), E_USER_ERROR );
					}

					return false;
				}
			}


			$backupfilelist = array();
			$filecounter    = 0;
			$files          = array();
			$metadata       = $dropbox->metadata( $job_object->job['dropboxdir'] );
			if ( is_array( $metadata ) ) {
				foreach ( $metadata['contents'] as $data ) {
					if ( $data['is_dir'] != true ) {
						$file = basename( $data['path'] );
						if ( $job_object->is_backup_archive( $file ) ) {
							$backupfilelist[ strtotime( $data['modified'] ) ] = $file;
						}
						$files[ $filecounter ]['folder']      = "https://content.dropboxapi.com/1/files/" . $job_object->job['dropboxroot'] . dirname( $data['path'] ) . "/";
						$files[ $filecounter ]['file']        = $data['path'];
						$files[ $filecounter ]['filename']    = basename( $data['path'] );
						$files[ $filecounter ]['downloadurl'] = network_admin_url( 'admin.php?page=backwpupbackups&action=downloaddropbox&file=' . $data['path'] . '&jobid=' . $job_object->job['jobid'] );
						$files[ $filecounter ]['filesize']    = $data['bytes'];
						$files[ $filecounter ]['time']        = strtotime( $data['modified'] ) + ( get_option( 'gmt_offset' ) * 3600 );
						$filecounter ++;
					}
				}
			}
			if ( $job_object->job['dropboxmaxbackups'] > 0 && is_object( $dropbox ) ) { //Delete old backups
				if ( count( $backupfilelist ) > $job_object->job['dropboxmaxbackups'] ) {
					ksort( $backupfilelist );
					$numdeltefiles = 0;
					while ( $file = array_shift( $backupfilelist ) ) {
						if ( count( $backupfilelist ) < $job_object->job['dropboxmaxbackups'] ) {
							break;
						}
						$response = $dropbox->fileopsDelete( $job_object->job['dropboxdir'] . $file ); //delete files on Cloud
						if ( $response['is_deleted'] == 'true' ) {
							foreach ( $files as $key => $filedata ) {
								if ( $filedata['file'] == '/' . $job_object->job['dropboxdir'] . $file ) {
									unset( $files[ $key ] );
								}
							}
							$numdeltefiles ++;
						} else {
							$job_object->log( sprintf( __( 'Error while deleting file from Dropbox: %s', 'backwpup' ), $file ), E_USER_ERROR );
						}
					}
					if ( $numdeltefiles > 0 ) {
						$job_object->log( sprintf( _n( 'One file deleted from Dropbox', '%d files deleted on Dropbox', $numdeltefiles, 'backwpup' ), $numdeltefiles ), E_USER_NOTICE );
					}
				}
			}
			set_site_transient( 'backwpup_' . $job_object->job['jobid'] . '_dropbox', $files, YEAR_IN_SECONDS );
		} catch ( Exception $e ) {
			$job_object->log( E_USER_ERROR, sprintf( __( 'Dropbox API: %s', 'backwpup' ), $e->getMessage() ), $e->getFile(), $e->getLine() );

			return false;
		}
		$job_object->substeps_done ++;

		return true;
	}

	/**
	 * @param $job_settings
	 *
	 * @return bool
	 */
	public function can_run( array $job_settings ) {

		if ( empty( $job_settings['dropboxtoken'] ) ) {
			return false;
		}

		return true;
	}

}


/**
 *
 */
final class BackWPup_Destination_Dropbox_API {

	/**
	 *
	 */
	const API_URL = 'https://api.dropboxapi.com/';

	/**
	 *
	 */
	const API_CONTENT_URL = 'https://content.dropboxapi.com/';

	/**
	 *
	 */
	const API_WWW_URL = 'https://www.dropbox.com/';

	/**
	 *
	 */
	const API_VERSION_URL = '1/';

	/**
	 * dropbox vars
	 *
	 * @var string
	 */
	private $root = 'sandbox';

	/**
	 * oAuth vars
	 *
	 * @var string
	 */
	private $oauth_app_key = '';

	/**
	 * @var string
	 */
	private $oauth_app_secret = '';
	/**
	 * @var string
	 */
	private $oauth_token = '';


	/**
	 * @param string $boxtype
	 *
	 * @throws BackWPup_Destination_Dropbox_API_Exception
	 */
	public function __construct( $boxtype = 'dropbox' ) {

		if ( $boxtype == 'dropbox' ) {
			$this->oauth_app_key    = get_site_option( 'backwpup_cfg_dropboxappkey', base64_decode( "dHZkcjk1MnRhZnM1NmZ2" ) );
			$this->oauth_app_secret = BackWPup_Encryption::decrypt( get_site_option( 'backwpup_cfg_dropboxappsecret', base64_decode( "OWV2bDR5MHJvZ2RlYmx1" ) ) );
			$this->root             = 'dropbox';
		} else {
			$this->oauth_app_key    = get_site_option( 'backwpup_cfg_dropboxsandboxappkey', base64_decode( "cHVrZmp1a3JoZHR5OTFk" ) );
			$this->oauth_app_secret = BackWPup_Encryption::decrypt( get_site_option( 'backwpup_cfg_dropboxsandboxappsecret', base64_decode( "eGNoYzhxdTk5eHE0eWdq" ) ) );
			$this->root             = 'sandbox';
		}

		if ( empty( $this->oauth_app_key ) || empty( $this->oauth_app_secret ) ) {
			throw new BackWPup_Destination_Dropbox_API_Exception( "No App key or App Secret specified." );
		}
	}

	/**
	 * @param $token
	 *
	 * @throws BackWPup_Destination_Dropbox_API_Exception
	 */
	public function setOAuthTokens( $token ) {

		if ( empty( $token['access_token'] ) ) {
			throw new BackWPup_Destination_Dropbox_API_Exception( "No oAuth token specified." );
		}

		$this->oauth_token = $token;
	}

	public function token_from_oauth1() {

		$url = self::API_URL . self::API_VERSION_URL . 'oauth2/token_from_oauth1';

		return $this->request( $url, array(), 'POST' );
	}

	/**
	 * @return array|mixed|string
	 */
	public function accountInfo() {

		$url = self::API_URL . self::API_VERSION_URL . 'account/info';

		return $this->request( $url );
	}

	public function disable_access_token() {

		$url = self::API_URL . self::API_VERSION_URL . 'disable_access_token';

		return $this->request( $url, array(), 'POST' );
	}

	/**
	 * @param        $file
	 * @param string $path
	 * @param bool $overwrite
	 *
	 * @return array|mixed|string
	 * @throws BackWPup_Destination_Dropbox_API_Exception
	 */
	public function upload( $file, $path = '', $overwrite = true ) {

		$file = str_replace( "\\", "/", $file );

		if ( ! is_readable( $file ) ) {
			throw new BackWPup_Destination_Dropbox_API_Exception( "Error: File \"$file\" is not readable or doesn't exist." );
		}

		if ( filesize( $file ) < 5242880 ) { //chunk transfer on bigger uploads
			$url    = self::API_CONTENT_URL . self::API_VERSION_URL . 'files_put/' . $this->root . '/' . $this->encode_path( $path );
			$output = $this->request( $url, array( 'overwrite' => ( $overwrite ) ? 'true' : 'false' ), 'PUT', file_get_contents( $file ) );
		} else {
			$output = $this->chunked_upload( $file, $path, $overwrite );
		}

		return $output;
	}

	/**
	 * @param        $file
	 * @param string $path
	 * @param bool $overwrite
	 *
	 * @return array|mixed|string
	 * @throws BackWPup_Destination_Dropbox_API_Exception
	 */
	public function chunked_upload( $file, $path = '', $overwrite = true ) {

		$backwpup_job_object = BackWPup_Destination_Dropbox::$backwpup_job_object;

		$file = str_replace( "\\", "/", $file );

		if ( ! is_readable( $file ) ) {
			throw new BackWPup_Destination_Dropbox_API_Exception( "Error: File \"$file\" is not readable or doesn't exist." );
		}

		$chunk_size = 4194304; //4194304 = 4MB

		$file_handel = fopen( $file, 'rb' );
		if ( ! $file_handel ) {
			throw new BackWPup_Destination_Dropbox_API_Exception( "Can not open source file for transfer." );
		}

		if ( ! isset( $backwpup_job_object->steps_data[ $backwpup_job_object->step_working ]['uploadid'] ) ) {
			$backwpup_job_object->steps_data[ $backwpup_job_object->step_working ]['uploadid'] = null;
		}
		if ( ! isset( $backwpup_job_object->steps_data[ $backwpup_job_object->step_working ]['offset'] ) ) {
			$backwpup_job_object->steps_data[ $backwpup_job_object->step_working ]['offset'] = 0;
		}

		//seek to current position
		if ( $backwpup_job_object->steps_data[ $backwpup_job_object->step_working ]['offset'] > 0 ) {
			fseek( $file_handel, $backwpup_job_object->steps_data[ $backwpup_job_object->step_working ]['offset'] );
		}

		while ( $data = fread( $file_handel, $chunk_size ) ) {
			$chunk_upload_start = microtime( true );
			$url                = self::API_CONTENT_URL . self::API_VERSION_URL . 'chunked_upload';
			$output             = $this->request( $url, array(
				'upload_id' => $backwpup_job_object->steps_data[ $backwpup_job_object->step_working ]['uploadid'],
				'offset'    => $backwpup_job_object->steps_data[ $backwpup_job_object->step_working ]['offset']
			), 'PUT', $data );
			$chunk_upload_time  = microtime( true ) - $chunk_upload_start;
			//args for next chunk
			$backwpup_job_object->steps_data[ $backwpup_job_object->step_working ]['offset']   = $output['offset'];
			$backwpup_job_object->steps_data[ $backwpup_job_object->step_working ]['uploadid'] = $output['upload_id'];
			if ( $backwpup_job_object->job['backuptype'] === 'archive' ) {
				$backwpup_job_object->substeps_done = $backwpup_job_object->steps_data[ $backwpup_job_object->step_working ]['offset'];
				if ( strlen( $data ) == $chunk_size ) {
					$time_remaining = $backwpup_job_object->do_restart_time();
					//calc next chunk
					if ( $time_remaining < $chunk_upload_time ) {
						$chunk_size = floor( $chunk_size / $chunk_upload_time * ( $time_remaining - 3 ) );
						if ( $chunk_size < 0 ) {
							$chunk_size = 1024;
						}
						if ( $chunk_size > 4194304 ) {
							$chunk_size = 4194304;
						}
					}
				}
			}
			$backwpup_job_object->update_working_data();
			//correct position
			fseek( $file_handel, $backwpup_job_object->steps_data[ $backwpup_job_object->step_working ]['offset'] );
		}

		fclose( $file_handel );

		$url = self::API_CONTENT_URL . self::API_VERSION_URL . 'commit_chunked_upload/' . $this->root . '/' . $this->encode_path( $path );

		$request = $this->request( $url, array(
			'overwrite' => ( $overwrite ) ? 'true' : 'false',
			'upload_id' => $backwpup_job_object->steps_data[ $backwpup_job_object->step_working ]['uploadid']
		), 'POST' );

		unset( $backwpup_job_object->steps_data[ $backwpup_job_object->step_working ]['uploadid'] );
		unset( $backwpup_job_object->steps_data[ $backwpup_job_object->step_working ]['offset'] );

		return $request;
	}

	/**
	 * @param      $path
	 * @param bool $echo
	 *
	 * @return string
	 */
	public function download( $path, $echo = false ) {

		$url = self::API_CONTENT_URL . self::API_VERSION_URL . 'files/' . $this->root . '/' . $path;
		if ( ! $echo ) {
			return $this->request( $url );
		} else {
			$this->request( $url, null, 'GET', '', true );

			return '';
		}
	}

	/**
	 * @param string $path
	 * @param bool $listContents
	 * @param int $fileLimit
	 * @param string $hash
	 *
	 * @return array|mixed|string
	 */
	public function metadata( $path = '', $listContents = true, $fileLimit = 10000, $hash = '' ) {

		$url = self::API_URL . self::API_VERSION_URL . 'metadata/' . $this->root . '/' . $this->encode_path( $path );

		return $this->request( $url, array(
			'list'       => ( $listContents ) ? 'true' : 'false',
			'hash'       => ( $hash ) ? $hash : '',
			'file_limit' => $fileLimit
		) );
	}

	/**
	 * @param string $path
	 *
	 * @return array|mixed|string
	 */
	public function media( $path = '' ) {

		$url = self::API_URL . self::API_VERSION_URL . 'media/' . $this->root . '/' . $path;

		return $this->request( $url );
	}

	/**
	 * @param $path
	 *
	 * @return array|mixed|string
	 */
	public function fileopsDelete( $path ) {

		$url = self::API_URL . self::API_VERSION_URL . 'fileops/delete';

		return $this->request( $url, array(
			'path' => '/' . $path,
			'root' => $this->root
		) );
	}

	public function oAuthAuthorize() {

		return self::API_WWW_URL . self::API_VERSION_URL . 'oauth2/authorize?response_type=code&client_id=' . $this->oauth_app_key;
	}


	public function oAuthToken( $code ) {

		$url = self::API_URL . self::API_VERSION_URL . 'oauth2/token';

		return $this->request( $url, array(
			'code'          => trim( $code ),
			'grant_type'    => 'authorization_code',
			'client_id'     => $this->oauth_app_key,
			'client_secret' => $this->oauth_app_secret
		), 'POST' );

	}


	/**
	 * @param        $url
	 * @param array $args
	 * @param string $method
	 * @param string $data
	 * @param bool $echo
	 *
	 * @throws BackWPup_Destination_Dropbox_API_Exception
	 * @internal param null $file
	 * @return array|mixed|string
	 */
	private function request( $url, $args = array(), $method = 'GET', $data = '', $echo = false ) {

		/* Header*/
		// oAuth 2
		if ( ! empty( $this->oauth_token['access_token'] ) && ! empty( $this->oauth_token['token_type'] ) && strtolower( $this->oauth_token['token_type'] ) == 'bearer' ) {
			$headers[] = 'Authorization: Bearer ' . $this->oauth_token['access_token'];
		} // oAuth 1
		elseif ( ! empty( $this->oauth_token['access_token'] ) && ! empty( $this->oauth_token['oauth_token_secret'] ) ) {
			$headers[] = 'Authorization: OAuth oauth_version="1.0", oauth_signature_method="PLAINTEXT", oauth_consumer_key="' . $this->oauth_app_key . '", oauth_token="' . $this->oauth_token['access_token'] . '", oauth_signature="' . $this->oauth_app_secret . '&' . $this->oauth_token['oauth_token_secret'] . '"';
		}

		$headers[] = 'Expect:';

		/* Build cURL Request */
		$ch = curl_init();
		if ( $method == 'POST' ) {
			curl_setopt( $ch, CURLOPT_POST, true );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $args );
			curl_setopt( $ch, CURLOPT_URL, $url );
		} elseif ( $method == 'PUT' ) {
			curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'PUT' );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
			$headers[] = 'Content-Type: application/octet-stream';
			$args      = ( is_array( $args ) ) ? '?' . http_build_query( $args, '', '&' ) : $args;
			curl_setopt( $ch, CURLOPT_URL, $url . $args );
		} else {
			curl_setopt( $ch, CURLOPT_BINARYTRANSFER, true );
			$args = ( is_array( $args ) ) ? '?' . http_build_query( $args, '', '&' ) : $args;
			curl_setopt( $ch, CURLOPT_URL, $url . $args );
		}
		curl_setopt( $ch, CURLOPT_USERAGENT, BackWPup::get_plugin_data( 'User-Agent' ) );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		if ( BackWPup::get_plugin_data( 'cacert' ) ) {
			curl_setopt( $ch, CURLOPT_SSLVERSION, 1 );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, true );
			$curl_version = curl_version();
			if ( strstr( $curl_version['ssl_version'], 'NSS/' ) === false ) {
				curl_setopt( $ch, CURLOPT_SSL_CIPHER_LIST,
					'ECDHE-RSA-AES256-GCM-SHA384:' .
					'ECDHE-RSA-AES128-GCM-SHA256:' .
					'ECDHE-RSA-AES256-SHA384:' .
					'ECDHE-RSA-AES128-SHA256:' .
					'ECDHE-RSA-AES256-SHA:' .
					'ECDHE-RSA-AES128-SHA:' .
					'ECDHE-RSA-RC4-SHA:' .
					'DHE-RSA-AES256-GCM-SHA384:' .
					'DHE-RSA-AES128-GCM-SHA256:' .
					'DHE-RSA-AES256-SHA256:' .
					'DHE-RSA-AES128-SHA256:' .
					'DHE-RSA-AES256-SHA:' .
					'DHE-RSA-AES128-SHA:' .
					'AES256-GCM-SHA384:' .
					'AES128-GCM-SHA256:' .
					'AES256-SHA256:' .
					'AES128-SHA256:' .
					'AES256-SHA:' .
					'AES128-SHA'
				);
			}
			if ( defined( 'CURLOPT_PROTOCOLS' ) ) {
				curl_setopt( $ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS );
			}
			if ( defined( 'CURLOPT_REDIR_PROTOCOLS' ) ) {
				curl_setopt( $ch, CURLOPT_REDIR_PROTOCOLS, CURLPROTO_HTTPS );
			}
			curl_setopt( $ch, CURLOPT_CAINFO, BackWPup::get_plugin_data( 'cacert' ) );
			curl_setopt( $ch, CURLOPT_CAPATH, dirname( BackWPup::get_plugin_data( 'cacert' ) ) );
		} else {
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		}
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
		$output = '';
		if ( $echo ) {
			echo curl_exec( $ch );
		} else {
			curl_setopt( $ch, CURLOPT_HEADER, true );
			if ( 0 == curl_errno( $ch ) ) {
				$responce = explode( "\r\n\r\n", curl_exec( $ch ), 2 );
				if ( ! empty( $responce[1] ) ) {
					$output = json_decode( $responce[1], true );
				}
			}
		}
		$status = curl_getinfo( $ch );
		if ( $status['http_code'] == 503 ) {
			$wait = 0;
			if ( preg_match( "/retry-after:(.*?)\r/i", $responce[0], $matches ) ) {
				$wait = trim( $matches[1] );
			}
			//only wait if we get a retry-after header.
			if ( ! empty( $wait ) ) {
				trigger_error( sprintf( '(503) Your app is making too many requests and is being rate limited. Error 503 can be triggered on a per-app or per-user basis. Wait for %d seconds.', $wait ), E_USER_WARNING );
				sleep( $wait );
			} else {
				throw new BackWPup_Destination_Dropbox_API_Exception( '(503) This indicates a transient server error.' );
			}

			//redo request
			return $this->request( $url, $args, $method, $data, $echo );
		} elseif ( $status['http_code'] === 400 && $method === 'PUT' && strstr( $url, '/chunked_upload' ) ) {    //correct offset on chunk uploads
			trigger_error( '(' . $status['http_code'] . ') False offset will corrected', E_USER_NOTICE );

			return $output;
		} elseif ( $status['http_code'] === 404 && ! empty( $output['error'] ) ) {
			trigger_error( '(' . $status['http_code'] . ') ' . $output['error'], E_USER_WARNING );

			return false;
		} elseif ( isset( $output['error'] ) || $status['http_code'] >= 300 || $status['http_code'] < 200 || curl_errno( $ch ) > 0 ) {
			if ( isset( $output['error'] ) && is_string( $output['error'] ) ) {
				$args    = ( is_array( $args ) ) ? '?' . http_build_query( $args, '', '&' ) : $args;
				$message = '(' . $status['http_code'] . ') ' . $output['error'] . ' ' . $url . $args;
			} elseif ( isset( $output['error']['hash'] ) && $output['error']['hash'] != '' ) {
				$message = (string) '(' . $status['http_code'] . ') ' . $output['error']['hash'] . ' ' . $url . $args;
			} elseif ( 0 != curl_errno( $ch ) ) {
				$message = '(' . curl_errno( $ch ) . ') ' . curl_error( $ch );
			} elseif ( $status['http_code'] == 304 ) {
				$message = '(304) Folder contents have not changed (relies on hash parameter).';
			} elseif ( $status['http_code'] == 400 ) {
				$message = '(400) Bad input parameter: ' . strip_tags( $responce[1] );
			} elseif ( $status['http_code'] == 401 ) {
				$message = '(401) Bad or expired token. This can happen if the user or Dropbox revoked or expired an access token. To fix, you should re-authenticate the user.';
			} elseif ( $status['http_code'] == 403 ) {
				$message = '(403) Bad OAuth request (wrong consumer key, bad nonce, expired timestamp...). Unfortunately, re-authenticating the user won\'t help here.';
			} elseif ( $status['http_code'] == 404 ) {
				$message = '(404) File or folder not found at the specified path.';
			} elseif ( $status['http_code'] == 405 ) {
				$message = '(405) Request method not expected (generally should be GET or POST).';
			} elseif ( $status['http_code'] == 406 ) {
				$message = '(406) There are too many file entries to return.';
			} elseif ( $status['http_code'] == 411 ) {
				$message = '(411) Missing Content-Length header (this endpoint doesn\'t support HTTP chunked transfer encoding).';
			} elseif ( $status['http_code'] == 415 ) {
				$message = '(415) The image is invalid and cannot be converted to a thumbnail.';
			} elseif ( $status['http_code'] == 429 ) {
				$message = '(429) Your app is making too many requests and is being rate limited. 429s can trigger on a per-app or per-user basis.';
			} elseif ( $status['http_code'] == 507 ) {
				$message = '(507) User is over Dropbox storage quota.';
			} else {
				$message = '(' . $status['http_code'] . ') Invalid response.';
			}
			throw new BackWPup_Destination_Dropbox_API_Exception( $message );
		} else {
			curl_close( $ch );
			if ( ! is_array( $output ) ) {
				return $responce[1];
			} else {
				return $output;
			}
		}
	}

	/**
	 * @param $path
	 *
	 * @return mixed
	 */
	private function encode_path( $path ) {

		$path = preg_replace( '#/+#', '/', trim( $path, '/' ) );
		$path = str_replace( '%2F', '/', rawurlencode( $path ) );

		return $path;
	}
}

/**
 *
 */
class BackWPup_Destination_Dropbox_API_Exception extends Exception {

}
