<?php
/**
 *
 */
class BackWPup_Destination_SugarSync extends BackWPup_Destinations {

	public static $backwpup_job_object = NULL;

	/**
	 * @return array
	 */
	public function option_defaults() {
		return array( 'sugarrefreshtoken' => '', 'sugarroot' => '', 'sugardir' => trailingslashit( sanitize_file_name( get_bloginfo( 'name' ) ) ), 'sugarmaxbackups' => 15 );
	}


	/**
	 * @param $jobid
	 */
	public function edit_tab( $jobid ) {
		?>
		<h3 class="title"><?php esc_html_e( 'Sugarsync Login', 'backwpup' ); ?></h3>
        <p></p>
        <table class="form-table">

		<?php if ( ! BackWPup_Option::get( $jobid, 'sugarrefreshtoken' ) ) { ?>
			<tr>
				<th scope="row"><?php esc_html_e( 'Authentication', 'backwpup' ); ?></th>
                <td>
                    <label for="sugaremail"><?php esc_html_e( 'Email address:', 'backwpup' ); ?><br/>
                    <input id="sugaremail" name="sugaremail" type="text" value="" class="large-text" autocomplete="off" /></label>
					<br/>
                    <label for="sugarpass"><?php esc_html_e( 'Password:', 'backwpup' ); ?><br/>
					<input id="sugarpass" name="sugarpass" type="password" value="" class="large-text" autocomplete="off" /></label>
					<br/>
					<br/>
					<input type="submit" id="idauthbutton" name="authbutton" class="button-primary" value="<?php esc_html_e( 'Authenticate with Sugarsync!', 'backwpup' ); ?>"/>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="authbutton" class="button" value="<?php esc_html_e( 'Create Sugarsync account', 'backwpup' ); ?>"/>
                </td>
            </tr>
		<?php } else { ?>
            <tr>
                <th scope="row"><label for="idauthbutton"><?php esc_html_e( 'Authentication', 'backwpup' ); ?></label></th>
                <td>
					<span style="color:green;"><?php esc_html_e( 'Authenticated!', 'backwpup' ); ?></span>
					<input type="submit" id="idauthbutton" name="authbutton" class="button-primary" value="<?php esc_html_e( 'Delete Sugarsync authentication!', 'backwpup' ); ?>" />
                </td>
            </tr>
		<?php } ?>
        </table>

        <h3 class="title"><?php esc_html_e( 'SugarSync Root', 'backwpup' ); ?></h3>
        <p></p>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="sugarroot"><?php esc_html_e( 'Sync folder selection', 'backwpup' ); ?></label></th>
                <td>
				<?php
				try {
					$sugarsync   = new BackWPup_Destination_SugarSync_API( BackWPup_Option::get( $jobid, 'sugarrefreshtoken' ) );
					$user        = $sugarsync->user();
					$syncfolders = $sugarsync->get( $user->syncfolders );
					if ( ! is_object( $syncfolders ) )
						echo '<span style="color:red;">' . __( 'No Syncfolders found!', 'backwpup' ) . '</span>';
				}
				catch ( Exception $e ) {
					echo '<span style="color:red;">' . $e->getMessage() . '</span>';
				}
				if ( isset( $syncfolders ) && is_object( $syncfolders ) ) {
					echo '<select name="sugarroot" id="sugarroot">';
					foreach ( $syncfolders->collection as $roots ) {
						echo "<option " . selected( strtolower( BackWPup_Option::get( $jobid, 'sugarroot' ) ), strtolower( $roots->ref ), FALSE ) . " value=\"" . $roots->ref . "\">" . $roots->displayName . "</option>";
					}
					echo '</select>';
				}
				?>
                </td>
            </tr>
        </table>

    <h3 class="title"><?php esc_html_e( 'Backup settings', 'backwpup' ); ?></h3>
    <p></p>
    <table class="form-table">
        <tr>
            <th scope="row"><label for="idsugardir"><?php esc_html_e( 'Folder in root', 'backwpup' ); ?></label></th>
            <td>
                <input id="idsugardir" name="sugardir" type="text" value="<?php echo esc_attr( BackWPup_Option::get( $jobid, 'sugardir' ) ) ; ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th scope="row"><?php esc_html_e( 'File Deletion', 'backwpup' ); ?></th>
            <td>
	            <?php
	            if ( BackWPup_Option::get( $jobid, 'backuptype' ) === 'archive' ) {
		            ?>
		            <label for="idsugarmaxbackups">
			            <input id="idsugarmaxbackups" name="sugarmaxbackups" type="number" min="0" step="1" value="<?php echo esc_attr( BackWPup_Option::get( $jobid, 'sugarmaxbackups' ) ); ?>" class="small-text" />
			            &nbsp;<?php esc_html_e( 'Number of files to keep in folder.', 'backwpup' ); ?>
		            </label>
	            <?php } else { ?>
		            <label for="idsugarsyncnodelete">
			            <input class="checkbox" value="1" type="checkbox" <?php checked( BackWPup_Option::get( $jobid, 'sugarsyncnodelete' ), true ); ?> name="sugarsyncnodelete" id="idsugarsyncnodelete" />
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
	 */
	public function edit_form_post_save( $jobid ) {


		if ( ! empty( $_POST[ 'sugaremail' ] ) && ! empty( $_POST[ 'sugarpass' ] ) && $_POST[ 'authbutton' ] === __( 'Authenticate with Sugarsync!', 'backwpup' ) ) {
			try {
				$sugarsync     = new BackWPup_Destination_SugarSync_API();
				$refresh_token = $sugarsync->get_Refresh_Token( sanitize_email( $_POST[ 'sugaremail' ] ), $_POST[ 'sugarpass' ] );
				if ( ! empty( $refresh_token ) )
					BackWPup_Option::update( $jobid, 'sugarrefreshtoken', $refresh_token );
			}
			catch ( Exception $e ) {
				BackWPup_Admin::message( 'SUGARSYNC: ' . $e->getMessage(), TRUE );
			}
		}

		if ( isset( $_POST[ 'authbutton' ] ) && $_POST[ 'authbutton' ] === __( 'Delete Sugarsync authentication!', 'backwpup' ) ) {
			BackWPup_Option::delete( $jobid, 'sugarrefreshtoken' );
		}

		if ( isset( $_POST[ 'authbutton' ] ) && $_POST[ 'authbutton' ] === __( 'Create Sugarsync account', 'backwpup' ) ) {
			try {
				$sugarsync = new BackWPup_Destination_SugarSync_API();
				$sugarsync->create_account( sanitize_email( $_POST[ 'sugaremail' ] ), $_POST[ 'sugarpass' ] );
			}
			catch ( Exception $e ) {
				BackWPup_Admin::message( 'SUGARSYNC: ' . $e->getMessage(), TRUE );
			}
		}

		$_POST[ 'sugardir' ] = trailingslashit( str_replace( '//', '/', str_replace( '\\', '/', trim( sanitize_text_field( $_POST[ 'sugardir' ] ) ) ) ) );
		if ( substr( $_POST[ 'sugardir' ], 0, 1 ) == '/' )
			$_POST[ 'sugardir' ] = substr( $_POST[ 'sugardir' ], 1 );
		if ( $_POST[ 'sugardir' ] == '/' )
			$_POST[ 'sugardir' ] = '';
		BackWPup_Option::update( $jobid, 'sugardir', $_POST[ 'sugardir' ] );

		BackWPup_Option::update( $jobid, 'sugarroot', isset( $_POST[ 'sugarroot' ] ) ? sanitize_text_field( $_POST[ 'sugarroot' ] ) : '' );
		BackWPup_Option::update( $jobid, 'sugarmaxbackups', isset( $_POST[ 'sugarmaxbackups' ] ) ? absint( $_POST[ 'sugarmaxbackups' ] ) : 0 );
	}

	/**
	 * @param $jobdest
	 * @param $backupfile
	 */
	public function file_delete( $jobdest, $backupfile ) {

		$files = get_site_transient( 'backwpup_' . strtolower( $jobdest ) );
		list( $jobid, $dest ) = explode( '_', $jobdest );

		if ( BackWPup_Option::get( $jobid, 'sugarrefreshtoken' ) ) {
			try {
				$sugarsync = new BackWPup_Destination_SugarSync_API( BackWPup_Option::get( $jobid, 'sugarrefreshtoken' ) );
				$sugarsync->delete( urldecode( $backupfile ) );
				//update file list
				foreach ( $files as $key => $file ) {
					if ( is_array( $file ) && $file[ 'file' ] == $backupfile )
						unset( $files[ $key ] );
				}
				unset( $sugarsync );
			}
			catch ( Exception $e ) {
				BackWPup_Admin::message( 'SUGARSYNC: ' . $e->getMessage(), TRUE );
			}
		}

		set_site_transient( 'backwpup_' . strtolower( $jobdest ), $files, YEAR_IN_SECONDS );
	}

	/**
	 * @param $jobid
	 * @param $get_file
	 */
	public function file_download( $jobid, $get_file ) {

		try {
			$sugarsync = new BackWPup_Destination_SugarSync_API( BackWPup_Option::get( $jobid, 'sugarrefreshtoken' ) );
			$response  = $sugarsync->get( urldecode( $get_file ) );
			if ( $level = ob_get_level() ) {
				for ( $i = 0; $i < $level; $i ++ ) {
					ob_end_clean();
				}
			}
			@set_time_limit( 300 );
			nocache_headers();
			header( 'Content-Description: File Transfer' );
			header( 'Content-Type: ' . BackWPup_Job::get_mime_type( (string) $response->displayName ) );
			header( 'Content-Disposition: attachment; filename="' . (string) $response->displayName . '"' );
			header( 'Content-Transfer-Encoding: binary' );
			header( 'Content-Length: ' . (int) $response->size );
			echo $sugarsync->download( urldecode( $get_file ) );
			die();
		}
		catch ( Exception $e ) {
			die( $e->getMessage() );
		}
	}

	/**
	 * @param $jobdest
	 * @return mixed
	 */
	public function file_get_list( $jobdest ) {

		return get_site_transient( 'backwpup_' . strtolower( $jobdest ) );
	}

	/**
	 * @param $job_object BackWPup_Job
	 * @return bool
	 */
	public function job_run_archive( BackWPup_Job $job_object ) {

		$job_object->substeps_todo = 2 + $job_object->backup_filesize;
		$job_object->log( sprintf( __( '%d. Try to send backup to SugarSync&#160;&hellip;', 'backwpup' ), $job_object->steps_data[ $job_object->step_working ][ 'STEP_TRY' ] ), E_USER_NOTICE );

		try {
			$sugarsync = new BackWPup_Destination_SugarSync_API( $job_object->job[ 'sugarrefreshtoken' ] );
			//Check Quota
			$user = $sugarsync->user();
			if ( ! empty( $user->nickname ) )
				$job_object->log( sprintf( __( 'Authenticated to SugarSync with nickname %s', 'backwpup' ), $user->nickname ), E_USER_NOTICE );
			$sugarsyncfreespase = (float)$user->quota->limit - (float)$user->quota->usage; //float fixes bug for display of no free space
			if ( $job_object->backup_filesize > $sugarsyncfreespase ) {
				$job_object->log( sprintf( _x( 'Not enough disk space available on SugarSync. Available: %s.','Available space on SugarSync', 'backwpup' ), size_format( $sugarsyncfreespase, 2 ) ), E_USER_ERROR );
				$job_object->substeps_todo = 1 + $job_object->backup_filesize;

				return TRUE;
			}
			else {
				$job_object->log( sprintf( __( '%s available at SugarSync', 'backwpup' ), size_format( $sugarsyncfreespase, 2 ) ), E_USER_NOTICE );
			}
			//Create and change folder
			$sugarsync->mkdir( $job_object->job[ 'sugardir' ], $job_object->job[ 'sugarroot' ] );
			$dirid = $sugarsync->chdir( $job_object->job[ 'sugardir' ], $job_object->job[ 'sugarroot' ] );
			//Upload to SugarSync
			$job_object->substeps_done = 0;
			$job_object->log( __( 'Starting upload to SugarSync&#160;&hellip;', 'backwpup' ), E_USER_NOTICE );
			self::$backwpup_job_object = &$job_object;
			$reponse = $sugarsync->upload( $job_object->backup_folder . $job_object->backup_file );
			if ( is_object( $reponse ) ) {
				if ( ! empty( $job_object->job[ 'jobid' ] ) )
					BackWPup_Option::update( $job_object->job[ 'jobid' ], 'lastbackupdownloadurl', network_admin_url( 'admin.php' ) . '?page=backwpupbackups&action=downloadsugarsync&file=' . (string)$reponse . '&jobid=' . $job_object->job[ 'jobid' ] );
				$job_object->substeps_done ++;
				$job_object->log( sprintf( __( 'Backup transferred to %s', 'backwpup' ), 'https://' . $user->nickname . '.sugarsync.com/' . $sugarsync->showdir( $dirid ) . $job_object->backup_file ), E_USER_NOTICE );
			}
			else {
				$job_object->log( __( 'Cannot transfer backup to SugarSync!', 'backwpup' ), E_USER_ERROR );

				return FALSE;
			}

			$backupfilelist = array();
			$files          = array();
			$filecounter    = 0;
			$dir            = $sugarsync->showdir( $dirid );
			$getfiles       = $sugarsync->getcontents( 'file' );
			if ( is_object( $getfiles ) ) {
				foreach ( $getfiles->file as $getfile ) {
					$getfile->displayName = utf8_decode( (string)$getfile->displayName );
					if ( $job_object->is_backup_archive( $getfile->displayName ) )
						$backupfilelist[ strtotime( (string)$getfile->lastModified ) ] = (string)$getfile->ref;
					$files[ $filecounter ][ 'folder' ]      = 'https://' . (string)$user->nickname . '.sugarsync.com/' . $dir;
					$files[ $filecounter ][ 'file' ]        = (string)$getfile->ref;
					$files[ $filecounter ][ 'filename' ]    = (string)$getfile->displayName;
					$files[ $filecounter ][ 'downloadurl' ] = network_admin_url( 'admin.php' ) . '?page=backwpupbackups&action=downloadsugarsync&file=' . (string)$getfile->ref . '&jobid=' . $job_object->job[ 'jobid' ];
					$files[ $filecounter ][ 'filesize' ]    = (int)$getfile->size;
					$files[ $filecounter ][ 'time' ]        = strtotime( (string)$getfile->lastModified ) + ( get_option( 'gmt_offset' ) * 3600 );
					$filecounter ++;
				}
			}
			if ( ! empty( $job_object->job[ 'sugarmaxbackups' ] ) && $job_object->job[ 'sugarmaxbackups' ] > 0 ) { //Delete old backups
				if ( count( $backupfilelist ) >  $job_object->job[ 'sugarmaxbackups' ] ) {
					ksort( $backupfilelist );
					$numdeltefiles = 0;
					while ( $file = array_shift( $backupfilelist ) ) {
						if ( count( $backupfilelist ) <  $job_object->job[ 'sugarmaxbackups' ] )
							break;
						$sugarsync->delete( $file ); //delete files on Cloud
						foreach ( $files as $key => $filedata ) {
							if ( $filedata[ 'file' ] == $file )
								unset( $files[ $key ] );
						}
						$numdeltefiles ++;
					}
					if ( $numdeltefiles > 0 )
						$job_object->log( sprintf( _n( 'One file deleted on SugarSync folder', '%d files deleted on SugarSync folder', $numdeltefiles, 'backwpup' ), $numdeltefiles ), E_USER_NOTICE );
				}
			}
			set_site_transient( 'BackWPup_' .  $job_object->job[ 'jobid' ] . '_SUGARSYNC', $files, YEAR_IN_SECONDS );
		}
		catch ( Exception $e ) {
			$job_object->log( E_USER_ERROR, sprintf( __( 'SugarSync API: %s', 'backwpup' ), $e->getMessage() ), $e->getFile(), $e->getLine() );

			return FALSE;
		}
		$job_object->substeps_done ++;

		return TRUE;
	}

	/**
	 * @param $job_settings array
	 * @return bool
	 */
	public function can_run( array $job_settings ) {

		if ( empty( $job_settings[ 'sugarrefreshtoken' ] ) )
			return FALSE;

		if ( empty( $job_settings[ 'sugarroot' ] ) )
			return FALSE;

		return TRUE;
	}
}

/**
 *
 */
class BackWPup_Destination_SugarSync_API {

	/**
	 * url for the sugarsync-api
	 */
	const API_URL  = 'https://api.sugarsync.com';

	/**
	 *
	 * @var    string
	 */
	protected $folder = '';

	/**
	 * @var mixed|string
	 */
	protected $encoding = 'UTF-8';

	/**
	 * @var null|string
	 */
	protected $refresh_token = '';

	/**
	 * The Auth-token
	 *
	 * @var string
	 */
	protected $access_token = '';

	// class methods
	/**
	 * Default constructor/Auth
	 */
	public function __construct( $refresh_token = NULL ) {

		//auth xml
		$this->encoding = mb_internal_encoding();

		//get access token
		if ( isset( $refresh_token ) and ! empty( $refresh_token ) ) {
			$this->refresh_token = $refresh_token;
			$this->get_Access_Token();
		}
	}

	/**
	 * Make the call
	 *
	 * @return    string
	 *
	 * @param    string $url                        The url to call.
	 * @param string    $data
	 * @param string    $method
	 * @throws BackWPup_Destination_SugarSync_API_Exception
	 * @internal param $string [optiona] $data            File on put, xml on post.
	 * @internal param $string [optional] $method        The method to use. Possible values are GET, POST, PUT, DELETE.
	 */
	private function doCall( $url, $data = '', $method = 'GET' ) {

		// allowed methods
		$allowedMethods = array( 'GET', 'POST', 'PUT', 'DELETE' );

		// redefine
		$url    = (string)$url;
		$method = (string)$method;

		// validate method
		if ( ! in_array( $method, $allowedMethods, true ) )
			throw new BackWPup_Destination_SugarSync_API_Exception( 'Unknown method (' . $method . '). Allowed methods are: ' . implode( ', ', $allowedMethods ) );

		// check auth token
		if ( empty( $this->access_token ) )
			throw new BackWPup_Destination_SugarSync_API_Exception( 'Auth Token not set correctly!' );
		else
			$headers[ ] = 'Authorization: ' . $this->access_token;
		$headers[ ] = 'Expect:';

		// init
		$curl = curl_init();
		//set options
		curl_setopt( $curl, CURLOPT_URL, $url );
		curl_setopt( $curl, CURLOPT_USERAGENT, BackWPup::get_plugin_data( 'User-Agent' )  );
		if ( ini_get( 'open_basedir' ) == '' ) curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, TRUE );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, TRUE );
		if ( BackWPup::get_plugin_data( 'cacert' ) ) {
			curl_setopt( $curl, CURLOPT_SSLVERSION, 1 );
			curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, TRUE );
			curl_setopt( $curl, CURLOPT_CAINFO, BackWPup::get_plugin_data( 'cacert' ) );
			curl_setopt( $curl, CURLOPT_CAPATH, dirname( BackWPup::get_plugin_data( 'cacert' ) ) );
		} else {
			curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, FALSE );
		}

		if ( $method == 'POST' ) {
			$headers[ ] = 'Content-Type: application/xml; charset=UTF-8';
			curl_setopt( $curl, CURLOPT_POSTFIELDS, $data );
			curl_setopt( $curl, CURLOPT_POST, TRUE );
			$headers[ ] = 'Content-Length: ' . strlen( $data );
		}
		elseif ( $method == 'PUT' ) {
			if ( is_readable( $data ) ) {
				$headers[ ] = 'Content-Length: ' . filesize( $data );
				$datafilefd = fopen( $data, 'rb' );
				curl_setopt( $curl, CURLOPT_PUT, TRUE );
				curl_setopt( $curl, CURLOPT_INFILE, $datafilefd );
				curl_setopt( $curl, CURLOPT_INFILESIZE, filesize( $data ) );
				curl_setopt( $curl, CURLOPT_READFUNCTION,  array( BackWPup_Destination_SugarSync::$backwpup_job_object, 'curl_read_callback' ) );
			}
			else {
				throw new BackWPup_Destination_SugarSync_API_Exception( 'Is not a readable file:' . $data );
			}
		}
		elseif ( $method == 'DELETE' ) {
			curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, 'DELETE' );
		}
		else {
			curl_setopt( $curl, CURLOPT_POST, FALSE );
		}

		// set headers
		curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $curl, CURLINFO_HEADER_OUT, TRUE );
		// execute
		$response    = curl_exec( $curl );
		$curlgetinfo = curl_getinfo( $curl );

		// fetch curl errors
		if ( curl_errno( $curl ) != 0 )
			throw new BackWPup_Destination_SugarSync_API_Exception( 'cUrl Error: ' . curl_error( $curl ) );
		curl_close( $curl );
		if ( ! empty( $datafilefd ) && is_resource( $datafilefd ) )
			fclose( $datafilefd );

		if ( $curlgetinfo[ 'http_code' ] >= 200 && $curlgetinfo[ 'http_code' ] < 300 ) {
			if ( FALSE !== stripos( $curlgetinfo[ 'content_type' ], 'xml' ) && ! empty( $response ) )
				return simplexml_load_string( $response );
			else
				return $response;
		}
		else {
			if ( $curlgetinfo[ 'http_code' ] == 401 )
				throw new BackWPup_Destination_SugarSync_API_Exception( 'Http Error: ' . $curlgetinfo[ 'http_code' ] . ' Authorization required.' );
			elseif ( $curlgetinfo[ 'http_code' ] == 403 )
				throw new BackWPup_Destination_SugarSync_API_Exception( 'Http Error: ' . $curlgetinfo[ 'http_code' ] . ' (Forbidden)  Authentication failed.' );
			elseif ( $curlgetinfo[ 'http_code' ] == 404 )
				throw new BackWPup_Destination_SugarSync_API_Exception( 'Http Error: ' . $curlgetinfo[ 'http_code' ] . ' Not found' );
			else
				throw new BackWPup_Destination_SugarSync_API_Exception( 'Http Error: ' . $curlgetinfo[ 'http_code' ] );
		}
	}


	/**
	 * @return string
	 * @throws BackWPup_Destination_SugarSync_API_Exception
	 */
	private function get_Access_Token() {

		$auth = '<?xml version="1.0" encoding="UTF-8" ?>';
		$auth .= '<tokenAuthRequest>';
		$auth .= '<accessKeyId>' . get_site_option( 'backwpup_cfg_sugarsynckey', base64_decode( "TlRBek1EY3lOakV6TkRrMk1URXhNemM0TWpJ" ) ) . '</accessKeyId>';
		$auth .= '<privateAccessKey>' . BackWPup_Encryption::decrypt( get_site_option( 'backwpup_cfg_sugarsyncsecret', base64_decode( "TkRFd01UazRNVEpqTW1Ga05EaG1NR0k1TVRFNFpqa3lPR1V6WlRVMk1tTQ==" ) ) ) . '</privateAccessKey>';
		$auth .= '<refreshToken>' . trim( $this->refresh_token ) . '</refreshToken>';
		$auth .= '</tokenAuthRequest>';
		// init
		$curl = curl_init();
		//set options
		curl_setopt( $curl, CURLOPT_URL, self::API_URL . '/authorization' );
		curl_setopt( $curl, CURLOPT_USERAGENT, BackWPup::get_plugin_data( 'User-Agent' )  );
		if ( ini_get( 'open_basedir' ) == ''  ) curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, TRUE );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, TRUE );
		if ( BackWPup::get_plugin_data( 'cacert' ) ) {
			curl_setopt( $curl, CURLOPT_SSLVERSION, 1 );
			curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, TRUE );
			curl_setopt( $curl, CURLOPT_CAINFO, BackWPup::get_plugin_data( 'cacert' ) );
			curl_setopt( $curl, CURLOPT_CAPATH, dirname( BackWPup::get_plugin_data( 'cacert' ) ) );
		} else {
			curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, FALSE );
		}
		curl_setopt( $curl, CURLOPT_HEADER, TRUE );
		curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 'Content-Type: application/xml; charset=UTF-8', 'Content-Length: ' . strlen( $auth ) ) );
		curl_setopt( $curl, CURLOPT_POSTFIELDS, $auth );
		curl_setopt( $curl, CURLOPT_POST, TRUE );
		// execute
		$response    = curl_exec( $curl );
		$curlgetinfo = curl_getinfo( $curl );
		// fetch curl errors
		if ( curl_errno( $curl ) != 0 )
			throw new BackWPup_Destination_SugarSync_API_Exception( 'cUrl Error: ' . curl_error( $curl ) );

		curl_close( $curl );

		if ( $curlgetinfo[ 'http_code' ] >= 200 && $curlgetinfo[ 'http_code' ] < 300 ) {
			if ( preg_match( '/Location:(.*?)\r/i', $response, $matches ) )
				$this->access_token = trim( $matches[ 1 ] );

			return $this->access_token;
		}
		else {
			if ( $curlgetinfo[ 'http_code' ] == 401 )
				throw new BackWPup_Destination_SugarSync_API_Exception( 'Http Error: ' . $curlgetinfo[ 'http_code' ] . ' Authorization required.' );
			elseif ( $curlgetinfo[ 'http_code' ] == 403 )
				throw new BackWPup_Destination_SugarSync_API_Exception( 'Http Error: ' . $curlgetinfo[ 'http_code' ] . ' (Forbidden)  Authentication failed.' );
			elseif ( $curlgetinfo[ 'http_code' ] == 404 )
				throw new BackWPup_Destination_SugarSync_API_Exception( 'Http Error: ' . $curlgetinfo[ 'http_code' ] . ' Not found' );
			else
				throw new BackWPup_Destination_SugarSync_API_Exception( 'Http Error: ' . $curlgetinfo[ 'http_code' ] );
		}
	}

	/**
	 * @param $email
	 * @param $password
	 * @return null|string
	 * @throws BackWPup_Destination_SugarSync_API_Exception
	 */
	public function get_Refresh_Token( $email, $password ) {

		$auth = '<?xml version="1.0" encoding="UTF-8" ?>';
		$auth .= '<appAuthorization>';
		$auth .= '<username>' . mb_convert_encoding( $email, 'UTF-8', $this->encoding ) . '</username>';
		$auth .= '<password>' . mb_convert_encoding( $password, 'UTF-8', $this->encoding ) . '</password>';
		$auth .= '<application>' . get_site_option( 'backwpup_cfg_sugarsyncappid', "/sc/5030726/449_18207099" ) . '</application>';
		$auth .= '<accessKeyId>' . get_site_option( 'backwpup_cfg_sugarsynckey',base64_decode( "TlRBek1EY3lOakV6TkRrMk1URXhNemM0TWpJ" ) ) . '</accessKeyId>';
		$auth .= '<privateAccessKey>' . BackWPup_Encryption::decrypt( get_site_option( 'backwpup_cfg_sugarsyncsecret', base64_decode( "TkRFd01UazRNVEpqTW1Ga05EaG1NR0k1TVRFNFpqa3lPR1V6WlRVMk1tTQ==" ) ) ) . '</privateAccessKey>';
		$auth .= '</appAuthorization>';
		// init
		$curl = curl_init();
		//set options
		curl_setopt( $curl, CURLOPT_URL, self::API_URL . '/app-authorization' );
		curl_setopt( $curl, CURLOPT_USERAGENT, BackWPup::get_plugin_data( 'User-Agent' )  );
		if ( ini_get( 'open_basedir' ) == '' ) curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, TRUE );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, TRUE );
		if ( BackWPup::get_plugin_data( 'cacert' ) ) {
			curl_setopt( $curl, CURLOPT_SSLVERSION, 1 );
			curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, TRUE );
			curl_setopt( $curl, CURLOPT_CAINFO, BackWPup::get_plugin_data( 'cacert' ) );
			curl_setopt( $curl, CURLOPT_CAPATH, dirname( BackWPup::get_plugin_data( 'cacert' ) ) );
		} else {
			curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, FALSE );
		}
		curl_setopt( $curl, CURLOPT_HEADER, TRUE );
		curl_setopt( $curl, CURLOPT_POSTFIELDS, $auth );
		curl_setopt( $curl, CURLOPT_POST, TRUE );
		curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 'Content-Type: application/xml; charset=UTF-8', 'Content-Length: ' . strlen( $auth ) ) );
		// execute
		$response    = curl_exec( $curl );
		$curlgetinfo = curl_getinfo( $curl );
		// fetch curl errors
		if ( curl_errno( $curl ) != 0 )
			throw new BackWPup_Destination_SugarSync_API_Exception( 'cUrl Error: ' . curl_error( $curl ) );

		curl_close( $curl );

		if ( $curlgetinfo[ 'http_code' ] >= 200 && $curlgetinfo[ 'http_code' ] < 300 ) {
			if ( preg_match( '/Location:(.*?)\r/i', $response, $matches ) )
				$this->refresh_token = trim( $matches[ 1 ] );

			return $this->refresh_token;
		}
		else {
			if ( $curlgetinfo[ 'http_code' ] == 401 )
				throw new BackWPup_Destination_SugarSync_API_Exception( 'Http Error: ' . $curlgetinfo[ 'http_code' ] . ' Authorization required.' );
			elseif ( $curlgetinfo[ 'http_code' ] == 403 )
				throw new BackWPup_Destination_SugarSync_API_Exception( 'Http Error: ' . $curlgetinfo[ 'http_code' ] . ' (Forbidden)  Authentication failed.' );
			elseif ( $curlgetinfo[ 'http_code' ] == 404 )
				throw new BackWPup_Destination_SugarSync_API_Exception( 'Http Error: ' . $curlgetinfo[ 'http_code' ] . ' Not found' );
			else
				throw new BackWPup_Destination_SugarSync_API_Exception( 'Http Error: ' . $curlgetinfo[ 'http_code' ] );
		}
	}

	/**
	 * @param $email
	 * @param $password
	 * @throws BackWPup_Destination_SugarSync_API_Exception
	 */
	public function create_account( $email, $password ) {

		$auth = '<?xml version="1.0" encoding="UTF-8" ?>';
		$auth .= '<user>';
		$auth .= '<email>' . mb_convert_encoding( $email, 'UTF-8', $this->encoding ) . '</email>';
		$auth .= '<password>' . mb_convert_encoding( $password, 'UTF-8', $this->encoding ) . '</password>';
		$auth .= '<accessKeyId>' . get_site_option( 'backwpup_cfg_sugarsynckey', base64_decode( "TlRBek1EY3lOakV6TkRrMk1URXhNemM0TWpJ" ) ) . '</accessKeyId>';
		$auth .= '<privateAccessKey>' . BackWPup_Encryption::decrypt( get_site_option( 'backwpup_cfg_sugarsyncsecret', base64_decode( "TkRFd01UazRNVEpqTW1Ga05EaG1NR0k1TVRFNFpqa3lPR1V6WlRVMk1tTQ==" ) ) ) . '</privateAccessKey>';
		$auth .= '</user>';
		// init
		$curl = curl_init();
		//set options
		curl_setopt( $curl, CURLOPT_URL, 'https://provisioning-api.sugarsync.com/users' );
		curl_setopt( $curl, CURLOPT_USERAGENT, BackWPup::get_plugin_data( 'User-Agent' )  );
		if ( ini_get( 'open_basedir' ) == '' ) curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, TRUE );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, TRUE );
		if ( BackWPup::get_plugin_data( 'cacert' ) ) {
			curl_setopt( $curl, CURLOPT_SSLVERSION, 1 );
			curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, TRUE );
			curl_setopt( $curl, CURLOPT_CAINFO, BackWPup::get_plugin_data( 'cacert' ) );
			curl_setopt( $curl, CURLOPT_CAPATH, dirname( BackWPup::get_plugin_data( 'cacert' ) ) );
		} else {
			curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, FALSE );
		}
		curl_setopt( $curl, CURLOPT_HEADER, TRUE );
		curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 'Content-Type: application/xml; charset=UTF-8', 'Content-Length: ' . strlen( $auth ) ) );
		curl_setopt( $curl, CURLOPT_POSTFIELDS, $auth );
		curl_setopt( $curl, CURLOPT_POST, TRUE );
		// execute
		$response    = curl_exec( $curl );
		$curlgetinfo = curl_getinfo( $curl );
		// fetch curl errors
		if ( curl_errno( $curl ) != 0 )
			throw new BackWPup_Destination_SugarSync_API_Exception( 'cUrl Error: ' . curl_error( $curl ) );

		curl_close( $curl );

		if ( $curlgetinfo[ 'http_code' ] == 201 ) {
			throw new BackWPup_Destination_SugarSync_API_Exception( 'Account created.' );
		}
		else {
			if ( $curlgetinfo[ 'http_code' ] == 400 )
				throw new BackWPup_Destination_SugarSync_API_Exception( 'Http Error: ' . $curlgetinfo[ 'http_code' ] . ' ' . substr( $response, $curlgetinfo[ 'header_size' ] ) );
			elseif ( $curlgetinfo[ 'http_code' ] == 401 )
				throw new BackWPup_Destination_SugarSync_API_Exception( 'Http Error: ' . $curlgetinfo[ 'http_code' ] . ' Developer credentials cannot be verified. Either a developer with the specified accessKeyId does not exist or the privateKeyID does not match an assigned accessKeyId.' );
			elseif ( $curlgetinfo[ 'http_code' ] == 403 )
				throw new BackWPup_Destination_SugarSync_API_Exception( 'Http Error: ' . $curlgetinfo[ 'http_code' ] . ' ' . substr( $response, $curlgetinfo[ 'header_size' ] ) );
			elseif ( $curlgetinfo[ 'http_code' ] == 503 )
				throw new BackWPup_Destination_SugarSync_API_Exception( 'Http Error: ' . $curlgetinfo[ 'http_code' ] . ' ' . substr( $response, $curlgetinfo[ 'header_size' ] ) );
			else
				throw new BackWPup_Destination_SugarSync_API_Exception( 'Http Error: ' . $curlgetinfo[ 'http_code' ] );
		}
	}

	/**
	 * @param        $folder
	 * @param string $root
	 * @return string
	 * @throws BackWPup_Destination_SugarSync_API_Exception
	 */
	public function chdir( $folder, $root = '' ) {

		$folder = rtrim( $folder, '/' );
		if ( substr( $folder, 0, 1 ) == '/' || empty( $this->folder ) ) {
			if ( ! empty( $root ) )
				$this->folder = $root;
			else
				throw new BackWPup_Destination_SugarSync_API_Exception( 'chdir: root folder must set!' );
		}
		$folders = explode( '/', $folder );
		foreach ( $folders as $dir ) {
			if ( $dir == '..' ) {
				$contents = $this->doCall( $this->folder );
				if ( ! empty( $contents->parent ) )
					$this->folder = $contents->parent;
			}
			elseif ( ! empty( $dir ) && $dir != '.' ) {
				$isdir    = FALSE;
				$contents = $this->getcontents( 'folder' );
				foreach ( $contents->collection as $collection ) {
					if ( strtolower( $collection->displayName ) == strtolower( $dir ) ) {
						$isdir        = TRUE;
						$this->folder = $collection->ref;
						break;
					}
				}
				if ( ! $isdir )
					throw new BackWPup_Destination_SugarSync_API_Exception( 'chdir: Folder ' . $folder . ' not exitst' );
			}
		}

		return $this->folder;
	}

	/**
	 * @param $folderid
	 * @return string
	 */
	public function showdir( $folderid ) {

		$showfolder = '';
		while ( $folderid ) {
			$contents   = $this->doCall( $folderid );
			$showfolder = $contents->displayName . '/' . $showfolder;
			if ( isset( $contents->parent ) )
				$folderid = $contents->parent;
			else
				break;
		}

		return $showfolder;
	}

	/**
	 * @param        $folder
	 * @param string $root
	 * @return bool
	 * @throws BackWPup_Destination_SugarSync_API_Exception
	 */
	public function mkdir( $folder, $root = '' ) {

		$savefolder = $this->folder;
		$folder     = rtrim( $folder, '/' );
		if ( substr( $folder, 0, 1 ) == '/' || empty( $this->folder ) ) {
			if ( ! empty( $root ) )
				$this->folder = $root;
			else
				throw new BackWPup_Destination_SugarSync_API_Exception( 'mkdir: root folder must set!' );
		}
		$folders = explode( '/', $folder );
		foreach ( $folders as $dir ) {
			if ( $dir == '..' ) {
				$contents = $this->doCall( $this->folder );
				if ( ! empty( $contents->parent ) )
					$this->folder = $contents->parent;
			}
			elseif ( ! empty( $dir ) && $dir != '.' ) {
				$isdir    = FALSE;
				$contents = $this->getcontents( 'folder' );
				foreach ( $contents->collection as $collection ) {
					if ( strtolower( $collection->displayName ) == strtolower( $dir ) ) {
						$isdir        = TRUE;
						$this->folder = $collection->ref;
						break;
					}
				}
				if ( ! $isdir ) {
					$this->doCall( $this->folder, '<?xml version="1.0" encoding="UTF-8"?><folder><displayName>' . mb_convert_encoding( $dir, 'UTF-8', $this->encoding ) . '</displayName></folder>', 'POST' );
					$contents = $this->getcontents( 'folder' );
					foreach ( $contents->collection as $collection ) {
						if ( strtolower( $collection->displayName ) == strtolower( $dir ) ) {
							$isdir        = TRUE;
							$this->folder = $collection->ref;
							break;
						}
					}
				}
			}
		}
		$this->folder = $savefolder;

		return TRUE;
	}


	/**
	 * @return string
	 */
	public function user() {
		return $this->doCall( self::API_URL . '/user' );
	}


	/**
	 * @param $url
	 * @return string
	 */
	public function get( $url ) {
		return $this->doCall( $url, '', 'GET' );
	}

	/**
	 * @param $url
	 * @return string
	 */
	public function download( $url ) {
		return $this->doCall( $url . '/data' );
	}

	/**
	 * @param $url
	 * @return string
	 */
	public function delete( $url ) {
		return $this->doCall( $url, '', 'DELETE' );
	}


	/**
	 * @param string $type
	 * @param int    $start
	 * @param int    $max
	 * @return string
	 */
	public function getcontents( $type = '', $start = 0, $max = 500 ) {

		$parameters = '';

		if ( strtolower( $type ) == 'folder' || strtolower( $type ) == 'file' )
			$parameters .= 'type=' . strtolower( $type );
		if ( ! empty( $start ) && is_integer( $start ) ) {
			if ( ! empty( $parameters ) )
				$parameters .= '&';
			$parameters .= 'start=' . $start;

		}
		if ( ! empty( $max ) && is_integer( $max ) ) {
			if ( ! empty( $parameters ) )
				$parameters .= '&';
			$parameters .= 'max=' . $max;
		}

		$request = $this->doCall( $this->folder . '/contents?' . $parameters );

		return $request;
	}

	/**
	 * @param        $file
	 * @param string $name
	 * @return mixed
	 */
	public function upload( $file, $name = '' ) {

		if ( empty( $name ) ) {
			$name = basename( $file );
		}

		$content_type = BackWPup_Job::get_mime_type( $file );

		$xmlrequest = '<?xml version="1.0" encoding="UTF-8"?>';
		$xmlrequest .= '<file>';
		$xmlrequest .= '<displayName>' . mb_convert_encoding( $name, 'UTF-8', $this->encoding ) . '</displayName>';
		$xmlrequest .= '<mediaType>' . $content_type . '</mediaType>';
		$xmlrequest .= '</file>';

		$this->doCall( $this->folder, $xmlrequest, 'POST' );
		$getfiles = $this->getcontents( 'file' );
		foreach ( $getfiles->file as $getfile ) {
			if ( $getfile->displayName == $name ) {
				$this->doCall( $getfile->ref . '/data', $file, 'PUT' );

				return $getfile->ref;
			}
		}
	}
}

/**
 * SugarSync Exception class
 *
 * @author    Daniel Hüsken <daniel@huesken-net.de>
 */
class BackWPup_Destination_SugarSync_API_Exception extends Exception {

}
