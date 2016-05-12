<?php
// Windows Azure SDK v0.4.1
// http://www.windowsazure.com/en-us/develop/php/
// https://github.com/WindowsAzure/azure-sdk-for-php

/**
 * Documentation: http://www.windowsazure.com/en-us/develop/php/how-to-guides/blob-service/
 */
class BackWPup_Destination_MSAzure extends BackWPup_Destinations {

	/**
	 * @return array
	 */
	public function option_defaults() {

		return array( 'msazureaccname' => '', 'msazurekey' => '', 'msazurecontainer' => '', 'msazuredir' => trailingslashit( sanitize_file_name( get_bloginfo( 'name' ) ) ), 'msazuremaxbackups' => 15, 'msazuresyncnodelete' => TRUE );
	}


	/**
	 * @param $jobid
	 */
	public function edit_tab( $jobid ) {
		?>
		<h3 class="title"><?php esc_html_e( 'MS Azure access keys', 'backwpup' ); ?></h3>
		<p></p>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="msazureaccname"><?php esc_html_e( 'Account name', 'backwpup' ); ?></label></th>
				<td>
					<input id="msazureaccname" name="msazureaccname" type="text"
						   value="<?php echo esc_attr( BackWPup_Option::get( $jobid, 'msazureaccname' ) );?>" class="regular-text" autocomplete="off" />
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="msazurekey"><?php esc_html_e( 'Access key', 'backwpup' ); ?></label></th>
				<td>
					<input id="msazurekey" name="msazurekey" type="password"
						   value="<?php echo esc_attr( BackWPup_Encryption::decrypt( BackWPup_Option::get( $jobid, 'msazurekey' ) ) );?>" class="regular-text" autocomplete="off" />
				</td>
			</tr>
		</table>

		<h3 class="title"><?php esc_html_e( 'Blob container', 'backwpup' ); ?></h3>
		<p></p>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="msazurecontainerselected"><?php esc_html_e( 'Container selection', 'backwpup' ); ?></label></th>
				<td>
					<input id="msazurecontainerselected" name="msazurecontainerselected" type="hidden" value="<?php echo esc_attr( BackWPup_Option::get( $jobid, 'msazurecontainer' ) );?>" />
					<?php if ( BackWPup_Option::get( $jobid, 'msazureaccname' ) && BackWPup_Option::get( $jobid, 'msazurekey' ) ) $this->edit_ajax( array(
																																						 'msazureaccname'  => BackWPup_Option::get( $jobid, 'msazureaccname' ),
																																						 'msazurekey'      => BackWPup_Encryption::decrypt( BackWPup_Option::get( $jobid, 'msazurekey' ) ),
																																						 'msazureselected' => BackWPup_Option::get( $jobid, 'msazurecontainer' )
																																					) ); ?>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="newmsazurecontainer"><?php esc_html_e( 'Create a new container', 'backwpup' ); ?></label></th>
				<td>
					<input id="newmsazurecontainer" name="newmsazurecontainer" type="text" value="" class="small-text" autocomplete="off" />
				</td>
			</tr>
		</table>

		<h3 class="title"><?php esc_html_e( 'Backup settings', 'backwpup' ); ?></h3>
		<p></p>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="idmsazuredir"><?php esc_html_e( 'Folder in container', 'backwpup' ); ?></label></th>
				<td>
					<input id="idmsazuredir" name="msazuredir" type="text" value="<?php echo esc_attr( BackWPup_Option::get( $jobid, 'msazuredir' ) ); ?>" class="regular-text" />
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'File deletion', 'backwpup' ); ?></th>
				<td>
					<?php
					if ( BackWPup_Option::get( $jobid, 'backuptype' ) === 'archive' ) {
						?>
						<label for="idmsazuremaxbackups">
							<input id="idmsazuremaxbackups" name="msazuremaxbackups" type="number" min="0" step="1" value="<?php echo esc_attr( BackWPup_Option::get( $jobid, 'msazuremaxbackups' ) ); ?>" class="small-text" />
							&nbsp;<?php esc_html_e( 'Number of files to keep in folder.', 'backwpup' ); ?>
						</label>
					<?php } else { ?>
						<label for="idmsazuresyncnodelete">
							<input class="checkbox" value="1" type="checkbox" <?php checked( BackWPup_Option::get( $jobid, 'msazuresyncnodelete' ), true ); ?> name="msazuresyncnodelete" id="idmsazuresyncnodelete" />
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
	 * @return string
	 */
	public function edit_form_post_save( $jobid ) {

		BackWPup_Option::update( $jobid, 'msazureaccname', sanitize_text_field( $_POST[ 'msazureaccname' ] ) );
		BackWPup_Option::update( $jobid, 'msazurekey', sanitize_text_field( $_POST[ 'msazurekey' ] ) );
		BackWPup_Option::update( $jobid, 'msazurecontainer', sanitize_text_field( $_POST[ 'msazurecontainer' ] ) );

		$_POST[ 'msazuredir' ] = trailingslashit( str_replace( '//', '/', str_replace( '\\', '/', trim( sanitize_text_field( $_POST[ 'msazuredir' ] ) ) ) ) );
		if ( substr( $_POST[ 'msazuredir' ], 0, 1 ) == '/' )
			$_POST[ 'msazuredir' ] = substr( $_POST[ 'msazuredir' ], 1 );
		if ( $_POST[ 'msazuredir' ] == '/' )
			$_POST[ 'msazuredir' ] = '';
		BackWPup_Option::update( $jobid, 'msazuredir', $_POST[ 'msazuredir' ] );

		BackWPup_Option::update( $jobid, 'msazuremaxbackups', ! empty( $_POST[ 'msazuremaxbackups' ] ) ? absint( $_POST[ 'msazuremaxbackups' ] ) : 0 );
		BackWPup_Option::update( $jobid, 'msazuresyncnodelete', ! empty( $_POST[ 'msazuresyncnodelete' ] ) );

		//create a new container
		if ( ! empty( $_POST[ 'newmsazurecontainer' ] ) && ! empty( $_POST[ 'msazureaccname' ] ) && ! empty( $_POST[ 'msazurekey' ] ) ) {
			try {
				set_include_path( get_include_path() . PATH_SEPARATOR . BackWPup::get_plugin_data( 'plugindir' ) .'/vendor/PEAR/');
				$blobRestProxy = WindowsAzure\Common\ServicesBuilder::getInstance()->createBlobService( 'DefaultEndpointsProtocol=https;AccountName=' . sanitize_text_field( $_POST[ 'msazureaccname' ] ) . ';AccountKey=' . sanitize_text_field( $_POST[ 'msazurekey' ] ) );
				$container_options = new WindowsAzure\Blob\Models\CreateContainerOptions();
				$container_options->setPublicAccess( WindowsAzure\Blob\Models\PublicAccessType::NONE );
				$blobRestProxy->createContainer( $_POST[ 'newmsazurecontainer' ], $container_options );
				BackWPup_Option::update( $jobid, 'msazurecontainer', sanitize_text_field( $_POST[ 'newmsazurecontainer' ] ) );
				BackWPup_Admin::message( sprintf( __( 'MS Azure container "%s" created.', 'backwpup' ), esc_html( sanitize_text_field( $_POST[ 'newmsazurecontainer' ] ) ) ) );
			}
			catch ( Exception $e ) {
				BackWPup_Admin::message( sprintf( __( 'MS Azure container create: %s', 'backwpup' ), $e->getMessage() ), TRUE );
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

		if ( BackWPup_Option::get( $jobid, 'msazureaccname' ) && BackWPup_Option::get( $jobid, 'msazurekey' ) && BackWPup_Option::get( $jobid, 'msazurecontainer' ) ) {
			try {
				set_include_path( get_include_path() . PATH_SEPARATOR . BackWPup::get_plugin_data( 'plugindir' ) .'/vendor/PEAR/');
				$blobRestProxy = WindowsAzure\Common\ServicesBuilder::getInstance()->createBlobService( 'DefaultEndpointsProtocol=https;AccountName=' . BackWPup_Option::get( $jobid, 'msazureaccname' ) . ';AccountKey=' . BackWPup_Encryption::decrypt( BackWPup_Option::get( $jobid, 'msazurekey' ) ) );
				$blobRestProxy->deleteBlob( BackWPup_Option::get( $jobid, 'msazurecontainer' ), $backupfile );
				//update file list
				foreach ( $files as $key => $file ) {
					if ( is_array( $file ) && $file[ 'file' ] == $backupfile )
						unset( $files[ $key ] );
				}
			}
			catch ( Exception $e ) {
				BackWPup_Admin::message( 'MS AZURE: ' . $e->getMessage(), TRUE );
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
			set_include_path( get_include_path() . PATH_SEPARATOR . BackWPup::get_plugin_data( 'plugindir' ) .'/vendor/PEAR/');
			$blobRestProxy = WindowsAzure\Common\ServicesBuilder::getInstance()->createBlobService( 'DefaultEndpointsProtocol=https;AccountName=' . BackWPup_Option::get( $jobid, 'msazureaccname' ) . ';AccountKey=' . BackWPup_Encryption::decrypt( BackWPup_Option::get( $jobid, 'msazurekey' ) ) );
			$blob = $blobRestProxy->getBlob( BackWPup_Option::get( $jobid, 'msazurecontainer' ), $get_file );
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
			header( 'Content-Length: ' . $blob->getProperties()->getContentLength() );
			fpassthru( $blob->getContentStream() );
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
		return get_site_transient( 'backwpup_' . $jobdest );
	}

	/**
	 * @param $job_object
	 * @return bool
	 */
	public function job_run_archive( BackWPup_Job $job_object ) {

		$job_object->substeps_todo = $job_object->backup_filesize + 2;

		if ( $job_object->steps_data[ $job_object->step_working ]['SAVE_STEP_TRY'] != $job_object->steps_data[ $job_object->step_working ][ 'STEP_TRY' ] )
			$job_object->log( sprintf( __( '%d. Try sending backup to a Microsoft Azure (Blob)&#160;&hellip;', 'backwpup' ), $job_object->steps_data[ $job_object->step_working ][ 'STEP_TRY' ] ), E_USER_NOTICE );

		try {
			set_include_path( get_include_path() . PATH_SEPARATOR . BackWPup::get_plugin_data( 'plugindir' ) .'/vendor/PEAR/');
			/* @var $blobRestProxy   WindowsAzure\Blob\BlobRestProxy */ //https causes an error SSL: Connection reset by peer that is why http
			$blobRestProxy = WindowsAzure\Common\ServicesBuilder::getInstance()->createBlobService('DefaultEndpointsProtocol=http;AccountName=' . $job_object->job[ 'msazureaccname' ] . ';AccountKey=' . BackWPup_Encryption::decrypt( $job_object->job[ 'msazurekey' ] ) );


			if ( $job_object->steps_data[ $job_object->step_working ]['SAVE_STEP_TRY'] != $job_object->steps_data[ $job_object->step_working ][ 'STEP_TRY' ] ) {

				//test vor existing container
				$containers    = $blobRestProxy->listContainers()->getContainers();

				$job_object->steps_data[ $job_object->step_working ][ 'container_url' ] = '';
				foreach( $containers as $container ) {
					if ( $container->getName() == $job_object->job[ 'msazurecontainer' ] ) {
						$job_object->steps_data[ $job_object->step_working ][ 'container_url' ] = $container->getUrl();
						break;
					}
				}

				if ( ! $job_object->steps_data[ $job_object->step_working ][ 'container_url' ] ) {
					$job_object->log( sprintf( __( 'MS Azure container "%s" does not exist!', 'backwpup'), $job_object->job[ 'msazurecontainer' ] ), E_USER_ERROR );

					return TRUE;
				} else {
					$job_object->log( sprintf( __( 'Connected to MS Azure container "%s".', 'backwpup'), $job_object->job[ 'msazurecontainer' ] ), E_USER_NOTICE );
				}

				$job_object->log( __( 'Starting upload to MS Azure&#160;&hellip;', 'backwpup' ), E_USER_NOTICE );
			}

			//Prepare Upload
			if ( $file_handel = fopen( $job_object->backup_folder . $job_object->backup_file, 'rb' ) ) {
				fseek( $file_handel, $job_object->substeps_done );

				if ( empty( $job_object->steps_data[ $job_object->step_working ][ 'BlockList' ] ) ) {
					$job_object->steps_data[ $job_object->step_working ][ 'BlockList' ] = array();
				}

				while ( ! feof( $file_handel ) ) {
					$data = fread( $file_handel, 1048576 * 4 ); //4MB
					if ( strlen( $data ) == 0 ) {
						continue;
					}
					$chunk_upload_start = microtime( TRUE );
					$block_count = count( $job_object->steps_data[ $job_object->step_working ][ 'BlockList' ] ) + 1;
					$block_id = md5( $data ) . str_pad( $block_count, 6, "0", STR_PAD_LEFT );
					$blobRestProxy->createBlobBlock( $job_object->job[ 'msazurecontainer' ], $job_object->job[ 'msazuredir'  ] . $job_object->backup_file, $block_id, $data );
					$job_object->steps_data[ $job_object->step_working ][ 'BlockList' ][] = $block_id;
					$chunk_upload_time = microtime( TRUE ) - $chunk_upload_start;
					$job_object->substeps_done = $job_object->substeps_done + strlen( $data );
					$time_remaining = $job_object->do_restart_time();
					if ( $time_remaining < $chunk_upload_time ) {
						$job_object->do_restart_time( TRUE );
					}
					$job_object->update_working_data();
				}
				fclose( $file_handel );
			} else {
				$job_object->log( __( 'Can not open source file for transfer.', 'backwpup' ), E_USER_ERROR );
				return FALSE;
			}

			//crate blog list
			$blocklist = new WindowsAzure\Blob\Models\BlockList();
			foreach( $job_object->steps_data[ $job_object->step_working ][ 'BlockList' ] as $block_id ) {
				$blocklist->addUncommittedEntry( $block_id );
			}
			unset( $job_object->steps_data[ $job_object->step_working ][ 'BlockList' ] );

			//Commit Blocks
			$blobRestProxy->commitBlobBlocks( $job_object->job[ 'msazurecontainer' ], $job_object->job[ 'msazuredir'  ] . $job_object->backup_file, $blocklist->getEntries() );

			$job_object->substeps_done ++;
			$job_object->log( sprintf( __( 'Backup transferred to %s', 'backwpup' ), $job_object->steps_data[ $job_object->step_working ][ 'container_url' ] . '/' . $job_object->job[ 'msazuredir'  ] . $job_object->backup_file ), E_USER_NOTICE );
			if ( !empty( $job_object->job[ 'jobid' ] ) ) {
				BackWPup_Option::update( $job_object->job[ 'jobid' ] , 'lastbackupdownloadurl', network_admin_url( 'admin.php' ) . '?page=backwpupbackups&action=downloadmsazure&file=' . $job_object->job[ 'msazuredir'  ] . $job_object->backup_file . '&jobid=' . $job_object->job[ 'jobid' ] );
			}
		}
		catch ( Exception $e ) {
			$job_object->log( E_USER_ERROR, sprintf( __( 'Microsoft Azure API: %s', 'backwpup' ), $e->getMessage() ), $e->getFile(), $e->getLine() );
			$job_object->substeps_done = 0;
			unset( $job_object->steps_data[ $job_object->step_working ][ 'BlockList' ] );
			if ( isset( $file_handel ) && is_resource( $file_handel ) )
				fclose( $file_handel );

			return FALSE;
		}


		try {

			$backupfilelist = array();
			$filecounter    = 0;
			$files          = array();
			$blob_options = new WindowsAzure\Blob\Models\ListBlobsOptions();
			$blob_options->setPrefix( $job_object->job[ 'msazuredir'  ] );
			$blobs          = $blobRestProxy->listBlobs( $job_object->job[ 'msazurecontainer' ], $blob_options )->getBlobs();

			if ( is_array( $blobs ) ) {
				foreach ( $blobs as $blob ) {
					$file = basename( $blob->getName() );
					if ( $job_object->is_backup_archive( $file ) )
						$backupfilelist[ $blob->getProperties()->getLastModified()->getTimestamp() ] = $file;
					$files[ $filecounter ][ 'folder' ]      = $job_object->steps_data[ $job_object->step_working ][ 'container_url' ] . "/" . dirname( $blob->getName() ) . "/";
					$files[ $filecounter ][ 'file' ]        = $blob->getName();
					$files[ $filecounter ][ 'filename' ]    = basename( $blob->getName() );
					$files[ $filecounter ][ 'downloadurl' ] = network_admin_url( 'admin.php' ) . '?page=backwpupbackups&action=downloadmsazure&file=' . $blob->getName() . '&jobid=' . $job_object->job[ 'jobid' ];
					$files[ $filecounter ][ 'filesize' ]    = $blob->getProperties()->getContentLength();
					$files[ $filecounter ][ 'time' ]        = $blob->getProperties()->getLastModified()->getTimestamp()  + ( get_option( 'gmt_offset' ) * 3600 );
					$filecounter ++;
				}
			}
			// Delete old backups
			if ( ! empty ($job_object->job[ 'msazuremaxbackups' ] ) && $job_object->job[ 'msazuremaxbackups' ] > 0 ) {
				if ( count( $backupfilelist ) > $job_object->job[ 'msazuremaxbackups' ] ) {
					ksort( $backupfilelist );
					$numdeltefiles = 0;
					while ( $file = array_shift( $backupfilelist ) ) {
						if ( count( $backupfilelist ) < $job_object->job[ 'msazuremaxbackups' ] )
							break;
						$blobRestProxy->deleteBlob( $job_object->job[ 'msazurecontainer' ], $job_object->job[ 'msazuredir'  ] . $file );
						foreach ( $files as $key => $filedata ) {
							if ( $filedata[ 'file' ] == $job_object->job[ 'msazuredir' ] . $file )
								unset( $files[ $key ] );
						}
						$numdeltefiles ++;
					}
					if ( $numdeltefiles > 0 )
						$job_object->log( sprintf( _n( 'One file deleted on Microsoft Azure container.', '%d files deleted on Microsoft Azure container.', $numdeltefiles, 'backwpup' ), $numdeltefiles ), E_USER_NOTICE );

				}
			}
			set_site_transient( 'backwpup_' . $job_object->job[ 'jobid' ] . '_msazure', $files, YEAR_IN_SECONDS );
		}
		catch ( Exception $e ) {
			$job_object->log( E_USER_ERROR, sprintf( __( 'Microsoft Azure API: %s', 'backwpup' ), $e->getMessage() ), $e->getFile(), $e->getLine() );

			return FALSE;
		}

		$job_object->substeps_done = $job_object->backup_filesize + 2;

		return TRUE;
	}

	/**
	 * @param $job_settings array
	 * @return bool
	 */
	public function can_run( array $job_settings ) {

		if ( empty( $job_settings[ 'msazureaccname' ] ) )
			return FALSE;

		if ( empty( $job_settings[ 'msazurekey' ]) )
			return FALSE;

		if ( empty( $job_settings[ 'msazurecontainer' ] ) )
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
				function msazuregetcontainer() {
					var data = {
						action: 'backwpup_dest_msazure',
						msazureaccname: $('#msazureaccname').val(),
						msazurekey: $('#msazurekey').val(),
						msazureselected: $('#msazurecontainerselected').val(),
						_ajax_nonce: $('#backwpupajaxnonce').val()
					};
					$.post(ajaxurl, data, function (response) {
						$('#msazurecontainererror').remove();
						$('#msazurecontainer').remove();
						$('#msazurecontainerselected').after(response);
					});
				}

				$('#msazureaccname').backwpupDelayKeyup(function () {
					msazuregetcontainer();
				});
				$('#msazurekey').backwpupDelayKeyup(function () {
					msazuregetcontainer();
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
		}
		else {
			if ( ! current_user_can( 'backwpup_jobs_edit' ) )
				wp_die( -1 );
			check_ajax_referer( 'backwpup_ajax_nonce' );
			$args[ 'msazureaccname' ]  = sanitize_text_field( $_POST[ 'msazureaccname' ] );
			$args[ 'msazurekey' ]      = sanitize_text_field( $_POST[ 'msazurekey' ] );
			$args[ 'msazureselected' ] = sanitize_text_field( $_POST[ 'msazureselected' ] );
			$ajax            = TRUE;
		}
		echo '<span id="msazurecontainererror" style="color:red;">';

		if ( ! empty( $args[ 'msazureaccname' ] ) && ! empty( $args[ 'msazurekey' ] ) ) {
			try {
				set_include_path( get_include_path() . PATH_SEPARATOR . BackWPup::get_plugin_data( 'plugindir' ) .'/vendor/PEAR/');
				$blobRestProxy = WindowsAzure\Common\ServicesBuilder::getInstance()->createBlobService( 'DefaultEndpointsProtocol=https;AccountName=' . $args[ 'msazureaccname' ] . ';AccountKey=' . BackWPup_Encryption::decrypt( $args[ 'msazurekey' ] ) );
				$containers    = $blobRestProxy->listContainers()->getContainers();
			}
			catch ( Exception $e ) {
				$error = $e->getMessage();
			}
		}

		if ( empty( $args[ 'msazureaccname' ] ) )
			_e( 'Missing account name!', 'backwpup' );
		elseif ( empty( $args[ 'msazurekey' ] ) )
			_e( 'Missing access key!', 'backwpup' );
		elseif ( ! empty( $error ) )
			echo esc_html( $error );
		elseif ( empty( $containers ) )
			_e( 'No container found!', 'backwpup' );
		echo '</span>';

		if ( !empty( $containers ) ) {
			echo '<select name="msazurecontainer" id="msazurecontainer">';
			foreach ( $containers as $container ) {
				echo "<option " . selected( strtolower( $args[ 'msazureselected' ] ), strtolower( $container->getName() ), FALSE ) . ">" . esc_html( $container->getName() ) . "</option>";
			}
			echo '</select>';
		}
		if ( $ajax )
			die();
		else
			return;
	}
}
