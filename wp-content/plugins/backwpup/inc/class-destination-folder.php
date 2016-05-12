<?php
/**
 *
 */
class BackWPup_Destination_Folder extends BackWPup_Destinations {


	/**
	 * @return array
	 */
	public function option_defaults() {

		$upload_dir = wp_upload_dir( null, false, true );
		$backups_dir = trailingslashit( str_replace( '\\', '/',$upload_dir[ 'basedir' ] ) ) . 'backwpup-' . BackWPup::get_plugin_data( 'hash' ) . '-backups/';
		$content_path = trailingslashit( str_replace( '\\', '/', WP_CONTENT_DIR ) );
		$backups_dir = str_replace( $content_path, '', $backups_dir );

		return array( 'maxbackups' => 15, 'backupdir' => $backups_dir, 'backupsyncnodelete' => TRUE );
	}


	/**
	 * @param $jobid
	 * @return void
	 * @internal param $main
	 */
	public function edit_tab( $jobid ) {
		?>
    <h3 class="title"><?php esc_html_e( 'Backup settings', 'backwpup' ); ?></h3>
    <p></p>
    <table class="form-table">
        <tr>
            <th scope="row"><label for="idbackupdir"><?php esc_html_e( 'Folder to store backups in', 'backwpup' ); ?></label></th>
            <td>
                <input name="backupdir" id="idbackupdir" type="text" value="<?php echo esc_attr( BackWPup_Option::get( $jobid, 'backupdir' ) ); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th scope="row"><?php esc_html_e( 'File Deletion', 'backwpup' ); ?></th>
            <td>
	            <?php
	            if ( BackWPup_Option::get( $jobid, 'backuptype' ) === 'archive' ) {
		            ?>
		            <label for="idmaxbackups">
			            <input id="idmaxbackups" name="maxbackups" type="number" min="0" step="1" value="<?php echo esc_attr( BackWPup_Option::get( $jobid, 'maxbackups' ) ); ?>" class="small-text"/>
			            &nbsp;<?php esc_html_e( 'Number of files to keep in folder.', 'backwpup' ); ?>
		            </label>
	            <?php } else { ?>
		            <label for="idbackupsyncnodelete">
			            <input class="checkbox" value="1" type="checkbox" <?php checked( BackWPup_Option::get( $jobid, 'backupsyncnodelete' ), true ); ?> name="backupsyncnodelete" id="idbackupsyncnodelete"/>
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

		$_POST[ 'backupdir' ] = trailingslashit( str_replace( array( '//', '\\' ), '/', trim( sanitize_text_field( $_POST[ 'backupdir' ] ) ) ) );
		BackWPup_Option::update( $jobid, 'backupdir', $_POST[ 'backupdir' ] );

		BackWPup_Option::update( $jobid, 'maxbackups', ! empty( $_POST[ 'maxbackups' ] ) ? absint( $_POST[ 'maxbackups' ] ) : 0 );
		BackWPup_Option::update( $jobid, 'backupsyncnodelete', ! empty( $_POST[ 'backupsyncnodelete' ] ) );
	}

	/**
	 * @param $jobdest
	 * @param $backupfile
	 */
	public function file_delete( $jobdest, $backupfile ) {

		list( $jobid, $dest ) = explode( '_', $jobdest, 2 );

		if ( empty( $jobid ) ) {
			return;
		}

		$backup_dir = esc_attr( BackWPup_Option::get( (int)$jobid, 'backupdir' ) );
		$backup_dir = BackWPup_File::get_absolute_path( $backup_dir );

		$backupfile =  realpath( trailingslashit( $backup_dir ) . basename( $backupfile ) );

		if ( $backupfile && is_writeable( $backupfile ) && !is_dir( $backupfile ) && !is_link( $backupfile ) ) {
			 unlink( $backupfile );
		}
	}

	/**
	 * @param $jobid
	 * @param $get_file
	 */
	public function file_download( $jobid, $get_file ) {

		$backup_dir = esc_attr( BackWPup_Option::get( (int) $jobid, 'backupdir' ) );
		$backup_dir = BackWPup_File::get_absolute_path( $backup_dir );

		$get_file = realpath( trailingslashit( $backup_dir ) . basename( $get_file ) );

		if ( $get_file && is_readable( $get_file ) ) {
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
			header( 'Content-Length: ' . filesize( $get_file ) );
			$handle = fopen( $get_file, 'rb' );
			if ( $handle ) {
				while ( ! feof( $handle ) ) {
					echo fread( $handle, 10241024 ); //chunks
					flush();
				}
				fclose( $handle );
			}
			die();
		} else {
			header( $_SERVER["SERVER_PROTOCOL"] . " 404 Not Found" );
			header( "Status: 404 Not Found" );
			die();
		}
	}

	/**
	 * @param $jobdest
	 * @return mixed
	 */
	public function file_get_list( $jobdest ) {

		list( $jobid, $dest ) = explode( '_', $jobdest, 2 );
		$filecounter    = 0;
		$files          = array();
		$backup_folder  = BackWPup_Option::get( $jobid, 'backupdir' );
		$backup_folder  = BackWPup_File::get_absolute_path( $backup_folder );
		if ( is_dir( $backup_folder ) && $dir = opendir( $backup_folder ) ) { //make file list
			while ( false !== ( $file = readdir( $dir ) ) ) {
				if ( in_array( $file, array( '.', '..', 'index.php', '.htaccess', '.donotbackup' ), true ) || is_dir( $backup_folder . $file ) || is_link( $backup_folder . $file ) ) {
					continue;
				}
				if ( is_readable( $backup_folder . $file ) ) {
					//file list for backups
					$files[ $filecounter ][ 'folder' ]      = $backup_folder;
					$files[ $filecounter ][ 'file' ]        = $backup_folder . $file;
					$files[ $filecounter ][ 'filename' ]    = $file;
					$files[ $filecounter ][ 'downloadurl' ] = add_query_arg( array(
																				  'page'   => 'backwpupbackups',
																				  'action' => 'downloadfolder',
																				  'file'   => $file,
																				  'jobid'  => $jobid
																			 ), network_admin_url( 'admin.php' ) );
					$files[ $filecounter ][ 'filesize' ]    = filesize( $backup_folder . $file );
					$files[ $filecounter ][ 'time' ]        = filemtime( $backup_folder . $file );
					$filecounter ++;
				}
			}
			closedir( $dir );
		}

		return $files;
	}

	/**
	 * @param $job_object
	 * @return bool
	 */
	public function job_run_archive( BackWPup_Job $job_object ) {

		$job_object->substeps_todo = 1;
		if ( ! empty( $job_object->job[ 'jobid' ] ) )
			BackWPup_Option::update( $job_object->job[ 'jobid' ], 'lastbackupdownloadurl', add_query_arg( array(
																								  'page'   => 'backwpupbackups',
																								  'action' => 'downloadfolder',
																								  'file'   => basename( $job_object->backup_file ),
																								  'jobid'  => $job_object->job[ 'jobid' ]
																							 ), network_admin_url( 'admin.php' ) ) );
		//Delete old Backupfiles
		$backupfilelist = array();
		$files          = array();
		if ( is_writable( $job_object->backup_folder ) && $dir = opendir( $job_object->backup_folder ) ) { //make file list
			while ( FALSE !== ( $file = readdir( $dir ) ) ) {
				if ( is_writeable( $job_object->backup_folder . $file ) && ! is_dir( $job_object->backup_folder . $file ) && ! is_link( $job_object->backup_folder . $file ) ) {
					//list for deletion
					if ( $job_object->is_backup_archive( $file ) ) {
						$backupfilelist[ filemtime( $job_object->backup_folder . $file ) ] = $file;
					}
				}
			}
			closedir( $dir );
		}
		if ( $job_object->job[ 'maxbackups' ] > 0 ) {
			if ( count( $backupfilelist ) > $job_object->job[ 'maxbackups' ] ) {
				ksort( $backupfilelist );
				$numdeltefiles = 0;
				while ( $file = array_shift( $backupfilelist ) ) {
					if ( count( $backupfilelist ) < $job_object->job[ 'maxbackups' ] )
						break;
					unlink( $job_object->backup_folder . $file );
					foreach ( $files as $key => $filedata ) {
						if ( $filedata[ 'file' ] == $job_object->backup_folder . $file ) {
							unset( $files[ $key ] );
						}
					}
					$numdeltefiles ++;
				}
				if ( $numdeltefiles > 0 )
					$job_object->log( sprintf( _n( 'One backup file deleted', '%d backup files deleted', $numdeltefiles, 'backwpup' ), $numdeltefiles ), E_USER_NOTICE );
			}
		}

		$job_object->substeps_done ++;

		return TRUE;
	}

	/**
	 * @param $job_settings array
	 * @return bool
	 */
	public function can_run( array $job_settings ) {

		if ( empty( $job_settings[ 'backupdir' ] ) || $job_settings[ 'backupdir' ] == '/' )
			return FALSE;

		return TRUE;
	}

}
