<?php
// Rackspace OpenCloud SDK v1.12.2
// http://www.rackspace.com/cloud/files/
// https://github.com/rackspace/php-opencloud

/**
 *
 */
class BackWPup_Destination_RSC extends BackWPup_Destinations {


	/**
	 * @return array
	 */
	public function option_defaults() {

		return array( 'rscusername' => '', 'rscapikey' => '', 'rsccontainer' => '', 'rscregion' => 'DFW', 'rscdir' => trailingslashit( sanitize_file_name( get_bloginfo( 'name' ) ) ), 'rscmaxbackups' => 15, 'rscsyncnodelete' => TRUE );
	}

	/**
	 * Get Auht url by region code
	 *
	 * @param $region string region code
	 * @return string
	 */
	public static function get_auth_url_by_region( $region ) {

		$region = strtoupper( $region );

		if ( $region === 'LON' ) {
			return RACKSPACE_UK;
		}

		return RACKSPACE_US;
	}

	/**
	 * @param $jobid
	 */
	public function edit_tab( $jobid ) {
		?>
		<h3 class="title"><?php esc_html_e( 'Rack Space Cloud Keys', 'backwpup' ); ?></h3>
		<p></p>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="rscusername"><?php esc_html_e( 'Username', 'backwpup' ); ?></label></th>
				<td>
					<input id="rscusername" name="rscusername" type="text" value="<?php echo esc_attr( BackWPup_Option::get( $jobid, 'rscusername' ) );?>" class="regular-text" autocomplete="off" />
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="rscapikey"><?php esc_html_e( 'API Key', 'backwpup' ); ?></label></th>
				<td>
					<input id="rscapikey" name="rscapikey" type="password" value="<?php echo esc_attr( BackWPup_Encryption::decrypt( BackWPup_Option::get( $jobid, 'rscapikey' ) ) ); ?>" class="regular-text" autocomplete="off" />
				</td>
			</tr>
		</table>

		<h3 class="title"><?php esc_html_e( 'Select region', 'backwpup' ); ?></h3>
		<p></p>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="rscregion"><?php esc_html_e( 'Rackspace Cloud Files Region', 'backwpup' ); ?></label></th>
				<td>
					<select name="rscregion" id="rscregion" title="<?php esc_html_e( 'Rackspace Cloud Files Region', 'backwpup' ); ?>">
						<option value="DFW" <?php selected( 'DFW', BackWPup_Option::get( $jobid, 'rscregion' ), TRUE ) ?>><?php esc_html_e( 'Dallas (DFW)', 'backwpup' ); ?></option>
						<option value="ORD" <?php selected( 'ORD', BackWPup_Option::get( $jobid, 'rscregion' ), TRUE ) ?>><?php esc_html_e( 'Chicago (ORD)', 'backwpup' ); ?></option>
						<option value="SYD" <?php selected( 'SYD', BackWPup_Option::get( $jobid, 'rscregion' ), TRUE ) ?>><?php esc_html_e( 'Sydney (SYD)', 'backwpup' ); ?></option>
						<option value="LON" <?php selected( 'LON', BackWPup_Option::get( $jobid, 'rscregion' ), TRUE ) ?>><?php esc_html_e( 'London (LON)', 'backwpup' ); ?></option>
						<option value="IAD" <?php selected( 'IAD', BackWPup_Option::get( $jobid, 'rscregion' ), TRUE ) ?>><?php esc_html_e( 'Northern Virginia (IAD)', 'backwpup' ); ?></option>
						<option value="HKG" <?php selected( 'HKG', BackWPup_Option::get( $jobid, 'rscregion' ), TRUE ) ?>><?php esc_html_e( 'Hong Kong (HKG)', 'backwpup' ); ?></option>
					</select><br/>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="rsccontainerselected"><?php esc_html_e( 'Container selection', 'backwpup' ); ?></label></th>
				<td>
					<input id="rsccontainerselected" name="rsccontainerselected" type="hidden" value="<?php echo esc_attr( BackWPup_Option::get( $jobid, 'rsccontainer' ) ); ?>" />
					<?php if ( BackWPup_Option::get( $jobid, 'rscusername' ) && BackWPup_Option::get( $jobid, 'rscapikey' ) ) $this->edit_ajax( array(
																																					 'rscusername' => BackWPup_Option::get( $jobid, 'rscusername' ),
																																					 'rscregion' => BackWPup_Option::get( $jobid, 'rscregion' ),
																																					 'rscapikey'   => BackWPup_Encryption::decrypt( BackWPup_Option::get( $jobid, 'rscapikey' ) ),
																																					 'rscselected' => BackWPup_Option::get( $jobid, 'rsccontainer' )
																																				) ); ?>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="idnewrsccontainer"><?php esc_html_e( 'Create a new container', 'backwpup' ); ?></label></th>
				<td>
					<input id="idnewrsccontainer" name="newrsccontainer" type="text" value="" class="text" />
				</td>
			</tr>
		</table>

		<h3 class="title"><?php esc_html_e( 'Backup settings', 'backwpup' ); ?></h3>
		<p></p>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="idrscdir"><?php esc_html_e( 'Folder in bucket', 'backwpup' ); ?></label></th>
				<td>
					<input id="idrscdir" name="rscdir" type="text" value="<?php echo esc_attr( BackWPup_Option::get( $jobid, 'rscdir' ) ); ?>" class="regular-text" />
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'File deletion', 'backwpup' ); ?></th>
				<td>
					<?php
					if ( BackWPup_Option::get( $jobid, 'backuptype' ) === 'archive' ) {
						?>
						<label for="idrscmaxbackups">
							<input id="idrscmaxbackups" name="rscmaxbackups" type="number" min="0" step="1" value="<?php echo esc_attr( BackWPup_Option::get( $jobid, 'rscmaxbackups' ) ); ?>" class="small-text" />
							&nbsp;<?php esc_html_e( 'Number of files to keep in folder.', 'backwpup' ); ?>
						</label>
					<?php } else { ?>
						<label for="idrscsyncnodelete">
							<input class="checkbox" value="1" type="checkbox" <?php checked( BackWPup_Option::get( $jobid, 'rscsyncnodelete' ), true ); ?> name="rscsyncnodelete" id="idrscsyncnodelete" />
							&nbsp;<?php esc_html_e( 'Do not delete files while syncing to destination!', 'backwpup' ); ?>
						</label>
					<?php } ?>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * @param $id
	 */
	public function edit_form_post_save( $id ) {

		BackWPup_Option::update( $id, 'rscusername', sanitize_text_field( $_POST[ 'rscusername' ] ) );
		BackWPup_Option::update( $id, 'rscapikey', sanitize_text_field( $_POST[ 'rscapikey' ] ) );
		BackWPup_Option::update( $id, 'rsccontainer', isset( $_POST[ 'rsccontainer' ] ) ? sanitize_text_field( $_POST[ 'rsccontainer' ] ) : '' );
		BackWPup_Option::update( $id, 'rscregion', ! empty( $_POST[ 'rscregion' ] ) ? sanitize_text_field( $_POST[ 'rscregion' ] ) : 'DFW' );

		$_POST[ 'rscdir' ] = trailingslashit( str_replace( '//', '/', str_replace( '\\', '/', trim( sanitize_text_field( $_POST[ 'rscdir' ] ) ) ) ) );
		if ( substr( $_POST[ 'rscdir' ], 0, 1 ) === '/' )
			$_POST[ 'rscdir' ] = substr( $_POST[ 'rscdir' ], 1 );
		if ( $_POST[ 'rscdir' ] === '/' )
			$_POST[ 'rscdir' ] = '';
		BackWPup_Option::update( $id, 'rscdir', $_POST[ 'rscdir' ] );

		BackWPup_Option::update( $id, 'rscmaxbackups', ! empty( $_POST[ 'rscmaxbackups' ] ) ? absint( $_POST[ 'rscmaxbackups' ] ) : 0 );
		BackWPup_Option::update( $id, 'rscsyncnodelete', ! empty( $_POST[ 'rscsyncnodelete' ] ) );

		if ( ! empty( $_POST[ 'rscusername' ] ) && ! empty( $_POST[ 'rscapikey' ] ) && ! empty( $_POST[ 'newrsccontainer' ] ) ) {
			try {
				$conn = new OpenCloud\Rackspace(
					self::get_auth_url_by_region( $_POST[ 'rscregion' ] ),
					array(
						 'username' => $_POST[ 'rscusername' ],
						 'apiKey' => $_POST[ 'rscapikey' ]
					));
				$ostore = $conn->objectStoreService( 'cloudFiles', sanitize_text_field( $_POST[ 'rscregion' ] ), 'publicURL' );
				$ostore->createContainer( $_POST[ 'newrsccontainer' ] );
				BackWPup_Option::update( $id, 'rsccontainer', sanitize_text_field( $_POST[ 'newrsccontainer' ] ) );
				BackWPup_Admin::message( sprintf( __( 'Rackspace Cloud container "%s" created.', 'backwpup' ), esc_html( sanitize_text_field( $_POST[ 'newrsccontainer' ] ) ) ) );

			}
			catch ( Exception $e ) {
				BackWPup_Admin::message( sprintf( __( 'Rackspace Cloud API: %s', 'backwpup' ), $e->getMessage() ), TRUE );
			}
		}
	}

	/**
	 * @param $jobdest
	 * @param $backupfile
	 */
	public function file_delete( $jobdest, $backupfile ) {

		$files = get_site_transient( 'backwpup_'. strtolower( $jobdest ) );
		list( $jobid, $dest ) = explode( '_', $jobdest );

		if ( BackWPup_Option::get( $jobid, 'rscusername' ) && BackWPup_Option::get( $jobid, 'rscapikey' ) && BackWPup_Option::get( $jobid, 'rsccontainer' ) ) {
			try {
				$conn = new OpenCloud\Rackspace(
					self::get_auth_url_by_region( BackWPup_Option::get( $jobid, 'rscregion' ) ),
					array(
						 'username' =>  BackWPup_Option::get( $jobid, 'rscusername' ),
						 'apiKey' => BackWPup_Encryption::decrypt( BackWPup_Option::get( $jobid, 'rscapikey' ) )
					));
				$conn->
				$ostore = $conn->objectStoreService( 'cloudFiles' , BackWPup_Option::get( $jobid, 'rscregion' ), 'publicURL');
				$container = $ostore->getContainer( BackWPup_Option::get( $jobid, 'rsccontainer' ) );
				$fileobject = $container->getObject( $backupfile );
				$fileobject->delete();
				//update file list
				foreach ( $files as $key => $file ) {
					if ( is_array( $file ) && $file[ 'file' ] == $backupfile )
						unset( $files[ $key ] );
				}

			}
			catch ( Exception $e ) {
				BackWPup_Admin::message( 'RSC: ' . $e->getMessage(), TRUE );
			}
		}

		set_site_transient( 'backwpup_'. strtolower( $jobdest ), $files, YEAR_IN_SECONDS );
	}

	/**
	 * @param $jobid
	 * @param $get_file
	 */
	public function file_download( $jobid, $get_file ) {

		try {
			$conn = new OpenCloud\Rackspace(
				self::get_auth_url_by_region( BackWPup_Option::get( $jobid, 'rscregion' ) ),
				array(
					 'username' =>  BackWPup_Option::get( $jobid, 'rscusername' ),
					 'apiKey' => BackWPup_Encryption::decrypt( BackWPup_Option::get( $jobid, 'rscapikey' ) )
				));
			$ostore = $conn->objectStoreService( 'cloudFiles' , BackWPup_Option::get( $jobid, 'rscregion' ), 'publicURL');
			$container = $ostore->getContainer( BackWPup_Option::get( $jobid, 'rsccontainer' ) );
			$backupfile = $container->getObject( $get_file );
			if ( $level = ob_get_level() ) {
				for ( $i = 0; $i < $level; $i ++ ) {
					ob_end_clean();
				}
			}
			@set_time_limit( 300 );
			nocache_headers();
			header( 'Content-Description: File Transfer' );
			header( 'Content-Type: ' . BackWPup_Job::get_mime_type( $get_file ) );
			header( 'Content-Disposition: attachment; filename="' . basename( $get_file ) . '"' );
			header( 'Content-Transfer-Encoding: binary' );
			header( 'Content-Length: ' . $backupfile->getContentLength() );
			echo $backupfile->getContent();
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
	 * @param $job_object BAckWPup_Job
	 * @return bool
	 */
	public function job_run_archive( BackWPup_Job $job_object ) {

		$job_object->substeps_todo = 2 + $job_object->backup_filesize;
		$job_object->substeps_done = 0;
		$job_object->log( sprintf( __( '%d. Trying to send backup file to Rackspace cloud &hellip;', 'backwpup' ), $job_object->steps_data[ $job_object->step_working ][ 'STEP_TRY' ] ), E_USER_NOTICE );

		try {

			$conn = new OpenCloud\Rackspace(
				self::get_auth_url_by_region( $job_object->job[ 'rscregion' ] ),
				array(
					 'username' => $job_object->job[ 'rscusername' ],
					 'apiKey' => BackWPup_Encryption::decrypt( $job_object->job[ 'rscapikey' ] )
				));
			//connect to cloud files
			$ostore = $conn->objectStoreService( 'cloudFiles' , $job_object->job[ 'rscregion' ], 'publicURL' );

			$container = $ostore->getContainer( $job_object->job[ 'rsccontainer' ] );
			$job_object->log( sprintf(__( 'Connected to Rackspace cloud files container %s', 'backwpup' ), $job_object->job[ 'rsccontainer' ] ) );
		}
		catch ( Exception $e ) {
			$job_object->log( E_USER_ERROR, sprintf( __( 'Rackspace Cloud API: %s', 'backwpup' ), $e->getMessage() ), $e->getFile(), $e->getLine() );

			return FALSE;
		}


		try {
			//Transfer Backup to Rackspace Cloud
			$job_object->substeps_done    = 0;
			$job_object->log( __( 'Upload to Rackspace cloud started &hellip;', 'backwpup' ), E_USER_NOTICE );

			if ( $handle = fopen( $job_object->backup_folder . $job_object->backup_file, 'rb' ) ) {
				$uploded = $container->uploadObject( $job_object->job[ 'rscdir' ] . $job_object->backup_file, $handle );
				fclose( $handle );
			} else {
				$job_object->log( __( 'Can not open source file for transfer.', 'backwpup' ), E_USER_ERROR );
				return FALSE;
			}

//			$transfer = $container->setupObjectTransfer( array(
//															 'name' => $job_object->job[ 'rscdir' ] . $job_object->backup_file,
//															 'path' => $job_object->backup_folder . $job_object->backup_file,
//															 'concurrency' => 1,
//															 'partSize'    => 4 * 1024 * 1024
//														) );
//			$uploded = $transfer->upload();

			if ( $uploded ) {
				$job_object->log( __( 'Backup File transferred to RSC://', 'backwpup' ) . $job_object->job[ 'rsccontainer' ] . '/' . $job_object->job[ 'rscdir' ] . $job_object->backup_file, E_USER_NOTICE );
				$job_object->substeps_done = 1 + $job_object->backup_filesize;
				if ( ! empty( $job_object->job[ 'jobid' ] ) ) {
					BackWPup_Option::update( $job_object->job[ 'jobid' ], 'lastbackupdownloadurl', network_admin_url( 'admin.php' ) . '?page=backwpupbackups&action=downloadrsc&file=' . $job_object->job[ 'rscdir' ] . $job_object->backup_file . '&jobid=' . $job_object->job[ 'jobid' ] );
				}
			} else {
				$job_object->log( __( 'Cannot transfer backup to Rackspace cloud.', 'backwpup' ), E_USER_ERROR );

				return FALSE;
			}
		}
		catch ( Exception $e ) {
			$job_object->log( E_USER_ERROR, sprintf( __( 'Rackspace Cloud API: %s', 'backwpup' ), $e->getMessage() ), $e->getFile(), $e->getLine() );

			return FALSE;
		}

		try {
			$backupfilelist = array();
			$filecounter    = 0;
			$files          = array();
			$objlist        = $container->objectList( array( 'prefix' => $job_object->job[ 'rscdir' ] ) );
			while ( $object = $objlist->next() ) {
				$file = basename( $object->getName() );
				if ( $job_object->job[ 'rscdir' ] . $file == $object->getName() ) { //only in the folder and not in complete bucket
					if ( $job_object->is_backup_archive( $file ) )
						$backupfilelist[ strtotime( $object->getLastModified() ) ] = $object;
				}
				$files[ $filecounter ][ 'folder' ]      = "RSC://" . $job_object->job[ 'rsccontainer' ] . "/" . dirname( $object->getName() ) . "/";
				$files[ $filecounter ][ 'file' ]        = $object->getName();
				$files[ $filecounter ][ 'filename' ]    = basename( $object->getName() );
				$files[ $filecounter ][ 'downloadurl' ] = network_admin_url( 'admin.php' ) . '?page=backwpupbackups&action=downloadrsc&file=' . $object->getName() . '&jobid=' . $job_object->job[ 'jobid' ];
				$files[ $filecounter ][ 'filesize' ]    = $object->getContentLength();
				$files[ $filecounter ][ 'time' ]        = strtotime( $object->getLastModified() );
				$filecounter ++;
			}
			if ( ! empty( $job_object->job[ 'rscmaxbackups' ] ) && $job_object->job[ 'rscmaxbackups' ] > 0 ) { //Delete old backups
				if ( count( $backupfilelist ) > $job_object->job[ 'rscmaxbackups' ] ) {
					ksort( $backupfilelist );
					$numdeltefiles = 0;
					while ( $file = array_shift( $backupfilelist ) ) {
						if ( count( $backupfilelist ) < $job_object->job[ 'rscmaxbackups' ] )
							break;
						foreach ( $files as $key => $filedata ) {
							if ( $filedata[ 'file' ] == $file->getName() )
								unset( $files[ $key ] );
						}
						$file->delete();
						$numdeltefiles ++;
					}
					if ( $numdeltefiles > 0 )
						$job_object->log( sprintf( _n( 'One file deleted on Rackspace cloud container.', '%d files deleted on Rackspace cloud container.', $numdeltefiles, 'backwpup' ), $numdeltefiles ), E_USER_NOTICE );
				}
			}
			set_site_transient( 'backwpup_' . $job_object->job[ 'jobid' ] . '_rsc', $files, YEAR_IN_SECONDS );
		}
		catch ( Exception $e ) {
			$job_object->log( E_USER_ERROR, sprintf( __( 'Rackspace Cloud API: %s', 'backwpup' ), $e->getMessage() ), $e->getFile(), $e->getLine() );

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

		if ( empty( $job_settings[ 'rscusername'] ) )
			return FALSE;

		if ( empty( $job_settings[ 'rscapikey'] ) )
			return FALSE;

		if ( empty( $job_settings[ 'rsccontainer'] ) )
			return FALSE;

		return TRUE;
	}

	/**
	 *
	 */
	public function edit_inline_js() {
		?>
		<script type="text/javascript">
			jQuery(document).ready(function ($) {
				function rscgetcontainer() {
					var data = {
						action: 'backwpup_dest_rsc',
						rscusername: $('#rscusername').val(),
						rscapikey: $('#rscapikey').val(),
						rscregion: $('#rscregion').val(),
						rscselected: $('#rsccontainerselected').val(),
						_ajax_nonce: $('#backwpupajaxnonce').val()
					};
					$.post(ajaxurl, data, function (response) {
						$('#rsccontainererror').remove();
						$('#rsccontainer').remove();
						$('#rsccontainerselected').after(response);
					});
				}

				$('#rscregion').change(function () {
					rscgetcontainer();
				});
				$('#rscusername').backwpupDelayKeyup(function () {
					rscgetcontainer();
				});
				$('#rscapikey').backwpupDelayKeyup(function () {
					rscgetcontainer();
				});
			});
		</script>
	<?php
	}

	/**
	 * @param string $args
	 */
	public function edit_ajax( $args = '' ) {

		$error = '';

		if ( is_array( $args ) ) {
			$ajax = FALSE;
		} else {
			if ( ! current_user_can( 'backwpup_jobs_edit' ) )
				wp_die( -1 );
			check_ajax_referer( 'backwpup_ajax_nonce' );
			$args[ 'rscusername' ] = sanitize_text_field( $_POST[ 'rscusername' ] );
			$args[ 'rscapikey' ]   = sanitize_text_field( $_POST[ 'rscapikey' ] );
			$args[ 'rscselected' ] = sanitize_text_field( $_POST[ 'rscselected' ] );
			$args[ 'rscregion' ] = sanitize_text_field( $_POST[ 'rscregion' ] );
			$ajax        = TRUE;
		}
		echo '<span id="rsccontainererror" style="color:red;">';

		$container_list = array();
		if ( ! empty( $args[ 'rscusername' ] ) && ! empty( $args[ 'rscapikey' ]  )  && ! empty( $args[ 'rscregion' ]  ) ) {
			try {
				$conn = new OpenCloud\Rackspace(
					self::get_auth_url_by_region( $args[ 'rscregion' ] ),
					array(
						 'username' => $args[ 'rscusername' ],
						 'apiKey' => BackWPup_Encryption::decrypt( $args[ 'rscapikey' ] )
					));

				$ostore = $conn->objectStoreService( 'cloudFiles' , $args[ 'rscregion' ], 'publicURL' );
				$containerlist = $ostore->listContainers();
				while( $container = $containerlist->next() ) {
					$container_list[] = $container->name;
				}
			}
			catch ( Exception $e ) {
				$error = $e->getMessage();
			}
		}

		if ( empty( $args[ 'rscusername' ] ) )
			_e( 'Missing username!', 'backwpup' );
		elseif ( empty( $args[ 'rscapikey' ]  ) )
			_e( 'Missing API Key!', 'backwpup' );
		elseif ( ! empty( $error ) )
			echo esc_html( $error );
		elseif ( empty( $container_list ) )
			_e( "A container could not be found!", 'backwpup' );
		echo '</span>';

		if ( ! empty( $container_list ) ) {
			echo '<select name="rsccontainer" id="rsccontainer">';
			foreach( $container_list as $container_name )
				echo "<option " . selected( strtolower( $args[ 'rscselected' ] ), strtolower( $container_name ), FALSE ) . ">" . $container_name . "</option>";
			echo '</select>';
		}

		if ( $ajax )
			die();
		else
			return;
	}
}
