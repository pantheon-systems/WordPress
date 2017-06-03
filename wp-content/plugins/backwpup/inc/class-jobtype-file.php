<?php
/**
 *
 */
class BackWPup_JobType_File extends BackWPup_JobTypes {

	/**
	 *
	 */
	public function __construct() {

		$this->info[ 'ID' ]          = 'FILE';
		$this->info[ 'name' ]        = __( 'Files', 'backwpup' );
		$this->info[ 'description' ] = __( 'File backup', 'backwpup' );
		$this->info[ 'URI' ]         = __( 'http://backwpup.com', 'backwpup' );
		$this->info[ 'author' ]      = 'Inpsyde GmbH';
		$this->info[ 'authorURI' ]   = __( 'http://inpsyde.com', 'backwpup' );
		$this->info[ 'version' ]     = BackWPup::get_plugin_data( 'Version' );

	}

	/**
	 *
	 */
	public function admin_print_scripts() {

		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			wp_enqueue_script( 'backwpupjobtypefile', BackWPup::get_plugin_data( 'URL' ) . '/assets/js/page_edit_jobtype_file.js', array( 'jquery' ), time(), TRUE );
		} else {
			wp_enqueue_script( 'backwpupjobtypefile', BackWPup::get_plugin_data( 'URL' ) . '/assets/js/page_edit_jobtype_file.min.js', array( 'jquery' ), BackWPup::get_plugin_data( 'Version' ), TRUE );
		}
	}

	/**
	 * @return bool
	 */
	public function creates_file() {

		return TRUE;
	}

	/**
	 * @return array
	 */
	public function option_defaults() {

		$log_folder = get_site_option( 'backwpup_cfg_logfolder' );
		$log_folder = BackWPup_File::get_absolute_path( $log_folder );

		return array(
			'backupexcludethumbs'   => FALSE, 'backupspecialfiles' => TRUE,
			'backuproot'            => TRUE, 'backupcontent' => TRUE, 'backupplugins' => TRUE, 'backupthemes' => TRUE, 'backupuploads' => TRUE,
			'backuprootexcludedirs' => array( 'logs', 'usage' ), 'backupcontentexcludedirs' => array( 'cache', 'upgrade', 'w3tc' ), 'backuppluginsexcludedirs' => array( 'backwpup', 'backwpup-pro' ), 'backupthemesexcludedirs' => array(), 'backupuploadsexcludedirs' => array( basename( $log_folder ) ),
			'fileexclude'           => '.tmp,.svn,.git,desktop.ini,.DS_Store,/node_modules/', 'dirinclude' => '',
			'backupabsfolderup'    => FALSE
		);
	}

	/**
	 * @param $main
	 */
	public function edit_tab( $main ) {

		@set_time_limit( 300 );
		$abs_folder_up  = BackWPup_Option::get( $main, 'backupabsfolderup' );
		$abs_path = realpath( ABSPATH );
		if ( $abs_folder_up ) {
			$abs_path = dirname( $abs_path );
		}
		?>
		<h3 class="title"><?php esc_html_e( 'Folders to backup', 'backwpup' ) ?></h3>
		<p></p>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="idbackuproot"><?php esc_html_e( 'Backup WordPress install folder', 'backwpup' ); ?></label></th>
				<td>
					<?php
					$folder = $abs_path;
					if ( $folder ) {
						$folder = untrailingslashit( str_replace( '\\', '/', $folder ) );
						$folder_size = ( get_site_option( 'backwpup_cfg_showfoldersize') ) ? ' (' . size_format( BackWPup_File::get_folder_size( $folder, FALSE ), 2 ) . ')' : '';
					}
					?>
					<input class="checkbox"
						   type="checkbox"<?php checked( BackWPup_Option::get( $main, 'backuproot' ), TRUE, TRUE );?>
						   name="backuproot" id="idbackuproot" value="1" /> <code title="<?php echo esc_attr(sprintf( __( 'Path as set by user (symlink?): %s', 'backwpup' ), $abs_path )); ?>"><?php echo esc_attr( $folder ); ?></code><?php echo esc_html( $folder_size ); ?>

					<fieldset id="backuprootexcludedirs" style="padding-left:15px; margin:2px;">
                        <legend><strong><?php  esc_html_e( 'Exclude:', 'backwpup' ); ?></strong></legend>
						<?php
						if ( $folder &&  $dir = opendir( $folder ) ) {
							while ( ( $file = readdir( $dir ) ) !== FALSE ) {
								$excludes = BackWPup_Option::get( $main, 'backuprootexcludedirs' );
								if ( ! in_array( $file, array( '.', '..' ), true ) && is_dir( $folder . '/' . $file ) && ! in_array( trailingslashit( $folder . '/' . $file ), $this->get_exclude_dirs( $folder ), true ) ) {
									$donotbackup = file_exists( $folder . '/' . $file . '/.donotbackup' );
									$folder_size = ( get_site_option( 'backwpup_cfg_showfoldersize') ) ? ' (' . size_format( BackWPup_File::get_folder_size( $folder . '/' . $file ), 2 ) . ')' : '';
									$title = '';
									if ( $donotbackup ) {
										$excludes[] = $file;
										$title = ' title="' . esc_attr__( 'Excluded by .donotbackup file!', 'backwpup' ) . '"';
									}
									echo '<nobr><label for="idrootexcludedirs-'.sanitize_file_name( $file ).'"><input class="checkbox" type="checkbox"' . checked( in_array( $file, $excludes, true ), TRUE, FALSE ) . ' name="backuprootexcludedirs[]" id="idrootexcludedirs-' . sanitize_file_name( $file ) . '" value="' . esc_attr( $file ) . '"' . disabled( $donotbackup, TRUE, FALSE ) . $title . ' /> ' . esc_html( $file ) . esc_html( $folder_size ) . '</label><br /></nobr>';
								}
							}
							closedir( $dir );
						}
						?>
                    </fieldset>
				</td>
			</tr>
            <tr>
                <th scope="row"><label for="idbackupcontent"><?php esc_html_e( 'Backup content folder', 'backwpup' ); ?></label></th>
                <td>
					<?php
					$folder = realpath( WP_CONTENT_DIR );
					if ( $folder ) {
						$folder = untrailingslashit( str_replace( '\\', '/', $folder ) );
						$folder_size = ( get_site_option( 'backwpup_cfg_showfoldersize') ) ? ' (' . size_format( BackWPup_File::get_folder_size( $folder, FALSE ), 2 ) . ')' : '';
					}
					?>
                    <input class="checkbox"
                           type="checkbox"<?php checked( BackWPup_Option::get( $main, 'backupcontent' ), TRUE, TRUE );?>
                           name="backupcontent" id="idbackupcontent" value="1" /> <code title="<?php echo esc_attr(sprintf( __( 'Path as set by user (symlink?): %s', 'backwpup' ),  WP_CONTENT_DIR )); ?>"><?php echo esc_html( $folder ); ?></code><?php echo esc_html($folder_size); ?>

                    <fieldset id="backupcontentexcludedirs" style="padding-left:15px; margin:2px;">
						<legend><strong><?php  esc_html_e( 'Exclude:', 'backwpup' ); ?></strong></legend>
						<?php
						if ( $folder &&  $dir = opendir( $folder ) ) {
							$excludes = BackWPup_Option::get( $main, 'backupcontentexcludedirs' );
							while ( ( $file = readdir( $dir ) ) !== FALSE ) {
								if ( ! in_array( $file, array( '.', '..' ), true ) && is_dir( $folder . '/' . $file ) && ! in_array( trailingslashit( $folder . '/' . $file ), $this->get_exclude_dirs( $folder ), true ) ) {
									$folder_size = ( get_site_option( 'backwpup_cfg_showfoldersize') ) ? ' (' . size_format( BackWPup_File::get_folder_size( $folder . '/' . $file ), 2 ) . ')' : '';
									$donotbackup = file_exists( $folder . '/' . $file . '/.donotbackup' );
									$title = '';
									if ( $donotbackup ) {
										$excludes[] = $file;
										$title = ' title="' . esc_attr__( 'Excluded by .donotbackup file!', 'backwpup' ) . '"';
									}
									echo '<nobr><label for="idcontentexcludedirs-'.sanitize_file_name( $file ).'"><input class="checkbox" type="checkbox"' . checked( in_array( $file, $excludes, true ), TRUE, FALSE ) . ' name="backupcontentexcludedirs[]" id="idcontentexcludedirs-'.sanitize_file_name( $file ).'" value="' . esc_attr($file) . '"' . disabled( $donotbackup, TRUE, FALSE ) . $title . ' /> ' . esc_html( $file ) . esc_html($folder_size) . '</label><br /></nobr>';
								}
							}
							closedir( $dir );
						}
						?>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="idbackupplugins"><?php _e( 'Backup plugins', 'backwpup' ); ?></label></th>
                <td>
					<?php
					$folder = realpath( WP_PLUGIN_DIR );
					if ( $folder ) {
						$folder = untrailingslashit( str_replace( '\\', '/', $folder ) );
						$folder_size = ( get_site_option( 'backwpup_cfg_showfoldersize') ) ? ' (' . size_format( BackWPup_File::get_folder_size( $folder, FALSE ), 2 ) . ')' : '';
					}
					?>
                    <input class="checkbox"
                           type="checkbox"<?php checked( BackWPup_Option::get( $main, 'backupplugins' ), TRUE, TRUE );?>
                           name="backupplugins" id="idbackupplugins" value="1" /> <code title="<?php echo sprintf( __( 'Path as set by user (symlink?): %s', 'backwpup' ), esc_attr( WP_PLUGIN_DIR ) ); ?>"><?php echo esc_attr( $folder ); ?></code><?php echo $folder_size; ?>

                    <fieldset id="backuppluginsexcludedirs" style="padding-left:15px; margin:2px;">
						<legend><strong><?php  _e( 'Exclude:', 'backwpup' ); ?></strong></legend>
						<?php
						if ( $folder &&  $dir = opendir( $folder ) ) {
							$excludes = BackWPup_Option::get( $main, 'backuppluginsexcludedirs' );
							while ( ( $file = readdir( $dir ) ) !== FALSE ) {
								if ( ! in_array( $file, array( '.', '..' ), true ) && is_dir( $folder . '/' . $file ) && ! in_array( trailingslashit( $folder . '/' . $file ), $this->get_exclude_dirs( $folder ), true ) ) {
									$folder_size = ( get_site_option( 'backwpup_cfg_showfoldersize') ) ? ' (' . size_format( BackWPup_File::get_folder_size( $folder . '/' . $file ), 2 ) . ')' : '';
									$donotbackup = file_exists( $folder . '/' . $file . '/.donotbackup' );
									$title = '';
									if ( $donotbackup ) {
										$excludes[] = $file;
										$title = ' title="' . esc_attr__( 'Excluded by .donotbackup file!', 'backwpup' ) . '"';
									}
									echo '<nobr><label for="idpluginexcludedirs-'.sanitize_file_name( $file ).'"><input class="checkbox" type="checkbox"' . checked( in_array( $file, $excludes, true ), TRUE, FALSE ) . ' name="backuppluginsexcludedirs[]" id="idpluginexcludedirs-'.sanitize_file_name( $file ).'" value="' . esc_attr($file) . '"' . disabled( $donotbackup, TRUE, FALSE ) . $title .  ' /> ' . esc_html( $file ) . esc_html($folder_size) . '</label><br /></nobr>';
								}
							}
							closedir( $dir );
						}
						?>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="idbackupthemes"><?php esc_html_e( 'Backup themes', 'backwpup' ); ?></label></th>
                <td>
					<?php
					$folder = realpath( get_theme_root() );
					if ( $folder ) {
						$folder = untrailingslashit( str_replace( '\\', '/', $folder ) );
						$folder_size = ( get_site_option( 'backwpup_cfg_showfoldersize') ) ? ' (' . size_format( BackWPup_File::get_folder_size( $folder, FALSE ), 2 ) . ')' : '';
					}
					?>
                    <input class="checkbox"
                           type="checkbox"<?php checked( BackWPup_Option::get( $main, 'backupthemes' ), TRUE, TRUE );?>
                           name="backupthemes" id="idbackupthemes" value="1" /> <code title="<?php echo sprintf( __( 'Path as set by user (symlink?): %s', 'backwpup' ), esc_attr( get_theme_root() ) ); ?>"><?php echo esc_attr( $folder ); ?></code><?php echo $folder_size; ?>

                    <fieldset id="backupthemesexcludedirs" style="padding-left:15px; margin:2px;">
						<legend><strong><?php  _e( 'Exclude:', 'backwpup' ); ?></strong></legend>
						<?php
						if ( $folder &&  $dir = opendir( $folder ) ) {
							$excludes = BackWPup_Option::get( $main, 'backupthemesexcludedirs' );
							while ( ( $file = readdir( $dir ) ) !== FALSE ) {
								if ( ! in_array( $file, array( '.', '..' ), true ) && is_dir( $folder . '/' . $file ) && ! in_array( trailingslashit( $folder . '/' . $file ), $this->get_exclude_dirs( $folder ), true ) ) {
									$folder_size = ( get_site_option( 'backwpup_cfg_showfoldersize') ) ? ' (' . size_format( BackWPup_File::get_folder_size( $folder . '/' . $file ), 2 ) . ')' : '';
									$donotbackup = file_exists( $folder . '/' . $file . '/.donotbackup' );
									$title = '';
									if ( $donotbackup ) {
										$excludes[] = $file;
										$title = ' title="' . esc_attr__( 'Excluded by .donotbackup file!', 'backwpup' ) . '"';
									}
									echo '<nobr><label for="idthemesexcludedirs-'.sanitize_file_name( $file ).'"><input class="checkbox" type="checkbox"' . checked( in_array( $file, $excludes, true ), TRUE, FALSE ) . ' name="backupthemesexcludedirs[]" id="idthemesexcludedirs-'.sanitize_file_name( $file ).'" value="' . $file . '"' . disabled( $donotbackup, TRUE, FALSE ) . $title . ' /> ' . esc_attr( $file ) . $folder_size . '</label><br /></nobr>';
								}
							}
							closedir( $dir );
						}
						?>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="idbackupuploads"><?php esc_html_e( 'Backup uploads folder', 'backwpup' ); ?></label></th>
                <td>
					<?php
					$folder = realpath( BackWPup_File::get_upload_dir() );
					if ( $folder ) {
						$folder = untrailingslashit( str_replace( '\\', '/', $folder ) );
						$folder_size = ( get_site_option( 'backwpup_cfg_showfoldersize') ) ? ' (' . size_format( BackWPup_File::get_folder_size( $folder, FALSE ), 2 ) . ')' : '';
					}
					?>
                    <input class="checkbox"
                           type="checkbox"<?php checked( BackWPup_Option::get( $main, 'backupuploads' ), TRUE, TRUE );?>
                           name="backupuploads" id="idbackupuploads" value="1" /> <code title="<?php echo sprintf( __( 'Path as set by user (symlink?): %s', 'backwpup' ), esc_attr( BackWPup_File::get_upload_dir() ) ); ?>"><?php echo esc_html( $folder ); ?></code><?php echo $folder_size; ?>

                    <fieldset id="backupuploadsexcludedirs" style="padding-left:15px; margin:2px;">
						<legend><strong><?php  esc_html_e( 'Exclude:', 'backwpup' ); ?></strong></legend>
						<?php
						if ( $folder && $dir = opendir( $folder ) ) {
							$excludes = BackWPup_Option::get( $main, 'backupuploadsexcludedirs' );
							while ( ( $file = readdir( $dir ) ) !== FALSE ) {
								if ( ! in_array( $file, array( '.', '..' ), true ) && is_dir( $folder . '/' . $file ) && ! in_array( trailingslashit( $folder . '/' . $file ), $this->get_exclude_dirs( $folder ), true ) ) {
									$folder_size = ( get_site_option( 'backwpup_cfg_showfoldersize') ) ? ' (' . size_format( BackWPup_File::get_folder_size( $folder . '/' . $file ), 2 ) . ')' : '';
									$donotbackup = file_exists( $folder . '/' . $file . '/.donotbackup' );
									$title = '';
									if ( $donotbackup ) {
										$excludes[] = $file;
										$title = ' title="' . esc_attr__( 'Excluded by .donotbackup file!', 'backwpup' ) . '"';
									}
									echo '<nobr><label for="iduploadexcludedirs-'.sanitize_file_name( $file ).'"><input class="checkbox" type="checkbox"' . checked( in_array( $file, $excludes, true ), TRUE, FALSE ) . ' name="backupuploadsexcludedirs[]" id="iduploadexcludedirs-'.sanitize_file_name( $file ).'" value="' . esc_attr($file) . '"' . disabled( $donotbackup, TRUE, FALSE ) . $title . ' /> ' . esc_html( $file ) . esc_html($folder_size) . '</label><br /></nobr>';
								}
							}
							closedir( $dir );
						}
						?>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="dirinclude"><?php esc_html_e( 'Extra folders to backup', 'backwpup' ); ?></label></th>
                <td>
					<textarea name="dirinclude" id="dirinclude" class="text code" rows="7" cols="50"><?php echo esc_attr( BackWPup_Option::get( $main, 'dirinclude' ) ); ?></textarea>
	                <p class="description"><?php esc_attr_e( 'Separate folder names with a line-break or a comma. Folders must be set with their absolute path!', 'backwpup' )?></p>
                </td>
            </tr>
		</table>

		<h3 class="title"><?php esc_html_e( 'Exclude from backup', 'backwpup' ) ?></h3>
		<p></p>
		<table class="form-table">
            <tr>
                <th scope="row"><?php esc_html_e( 'Thumbnails in uploads', 'backwpup' ); ?></th>
                <td>
                    <label for="idbackupexcludethumbs"><input class="checkbox" type="checkbox"<?php checked( BackWPup_Option::get( $main, 'backupexcludethumbs' ), TRUE, TRUE );?> name="backupexcludethumbs" id="idbackupexcludethumbs" value="1" /> <?php esc_html_e( 'Don\'t backup thumbnails from the site\'s uploads folder.', 'backwpup' ); ?></label>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="idfileexclude"><?php esc_html_e( 'Exclude files/folders from backup', 'backwpup' ); ?></label></th>
                <td>
                    <textarea name="fileexclude" id="idfileexclude" class="text code" rows="7" cols="50"><?php echo esc_attr( BackWPup_Option::get( $main, 'fileexclude' ) ); ?></textarea>
	                <p class="description"><?php esc_attr_e( 'Separate file / folder name parts with a line-break or a comma. For example /logs/,.log,.tmp', 'backwpup' ); ?></p>
                </td>
            </tr>
        </table>

		<h3 class="title"><?php esc_html_e( 'Special options', 'backwpup' ) ?></h3>
		<p></p>
		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'Include special files', 'backwpup' ); ?></th>
				<td>
					<label for="idbackupspecialfiles"><input class="checkbox" id="idbackupspecialfiles" type="checkbox"<?php checked( BackWPup_Option::get( $main, 'backupspecialfiles' ), TRUE, TRUE ); ?> name="backupspecialfiles" value="1" /> <?php esc_html_e( 'Backup wp-config.php, robots.txt, nginx.conf, .htaccess, .htpasswd and favicon.ico from root if it is not included in backup.', 'backwpup' ); ?></label>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Use one folder above as WP install folder', 'backwpup' ); ?></th>
				<td>
					<label for="idbackupabsfolderup"><input class="checkbox" id="idbackupabsfolderup" type="checkbox"<?php checked( $abs_folder_up, TRUE, TRUE ); ?>
							name="backupabsfolderup" value="1" /> <?php _e( 'Use one folder above as WordPress install folder! That can be helpful, if you would backup files and folder that are not in the WordPress installation folder. Or if you made a "<a href="https://codex.wordpress.org/Giving_WordPress_Its_Own_Directory">Giving WordPress Its Own Directory</a>" installation. Excludes must be configured again.', 'backwpup' ); ?></label>
				</td>
			</tr>
		</table>
	<?php
	}


	/**
	 * @param $id
	 */
	public function edit_form_post_save( $id ) {

		$fileexclude = explode( ',', sanitize_text_field( stripslashes( str_replace( array( "\r\n", "\r" ), ',', $_POST[ 'fileexclude' ] ) ) ) );

		foreach ( $fileexclude as $key => $value ) {
			$fileexclude[ $key ] = str_replace( '//', '/', str_replace( '\\', '/', trim( $value ) ) );
			if ( empty( $fileexclude[ $key ] ) ) {
				unset( $fileexclude[ $key ] );
			}
		}
		sort( $fileexclude );
		BackWPup_Option::update( $id, 'fileexclude', implode( ',', $fileexclude ) );

		$dirinclude = explode( ',', sanitize_text_field( stripslashes( str_replace( array( "\r\n", "\r" ), ',', $_POST[ 'dirinclude' ] ) ) ) );
		foreach ( $dirinclude as $key => $value ) {
			$dirinclude[ $key ] = trailingslashit( str_replace( '//', '/', str_replace( '\\', '/', trim( $value ) ) ) );
			if ( $dirinclude[ $key ] == '/' || empty( $dirinclude[ $key ] ) || ! is_dir( $dirinclude[ $key ] ) ) {
				unset( $dirinclude[ $key ] );
			}
		}
		sort( $dirinclude );
		BackWPup_Option::update( $id, 'dirinclude', implode( ',', $dirinclude ) );

		BackWPup_Option::update( $id, 'backupexcludethumbs', ! empty( $_POST[ 'backupexcludethumbs' ] ) );
		BackWPup_Option::update( $id, 'backupspecialfiles', ! empty( $_POST[ 'backupspecialfiles' ] ) );
		BackWPup_Option::update( $id, 'backuproot', ! empty( $_POST[ 'backuproot' ] ) );
		BackWPup_Option::update( $id, 'backupabsfolderup', ! empty( $_POST[ 'backupabsfolderup' ] ) );

		if ( ! isset( $_POST[ 'backuprootexcludedirs' ] ) || ! is_array( $_POST[ 'backuprootexcludedirs' ] ) ) {
			$_POST[ 'backuprootexcludedirs' ] = array();
		}
		sort( $_POST[ 'backuprootexcludedirs' ] );
		BackWPup_Option::update( $id, 'backuprootexcludedirs', $_POST[ 'backuprootexcludedirs' ] );

		BackWPup_Option::update( $id, 'backupcontent', ! empty( $_POST[ 'backupcontent' ] ) );

		if ( ! isset( $_POST[ 'backupcontentexcludedirs' ] ) || ! is_array( $_POST[ 'backupcontentexcludedirs' ] ) ) {
			$_POST[ 'backupcontentexcludedirs' ] = array();
		}
		sort( $_POST[ 'backupcontentexcludedirs' ] );
		BackWPup_Option::update( $id, 'backupcontentexcludedirs', $_POST[ 'backupcontentexcludedirs' ] );

		BackWPup_Option::update( $id, 'backupplugins', ! empty( $_POST[ 'backupplugins' ] ) );

		if ( ! isset( $_POST[ 'backuppluginsexcludedirs' ] ) || ! is_array( $_POST[ 'backuppluginsexcludedirs' ] ) ) {
			$_POST[ 'backuppluginsexcludedirs' ] = array();
		}
		sort( $_POST[ 'backuppluginsexcludedirs' ] );
		BackWPup_Option::update( $id, 'backuppluginsexcludedirs', $_POST[ 'backuppluginsexcludedirs' ] );

		BackWPup_Option::update( $id, 'backupthemes', ! empty( $_POST[ 'backupthemes' ] ) );

		if ( ! isset( $_POST[ 'backupthemesexcludedirs' ] ) || ! is_array( $_POST[ 'backupthemesexcludedirs' ] ) ) {
			$_POST[ 'backupthemesexcludedirs' ] = array();
		}
		sort( $_POST[ 'backupthemesexcludedirs' ] );
		BackWPup_Option::update( $id, 'backupthemesexcludedirs', $_POST[ 'backupthemesexcludedirs' ] );

		BackWPup_Option::update( $id, 'backupuploads', ! empty( $_POST[ 'backupuploads' ] ) );

		if ( ! isset( $_POST[ 'backupuploadsexcludedirs' ] ) || ! is_array( $_POST[ 'backupuploadsexcludedirs' ] ) ) {
			$_POST[ 'backupuploadsexcludedirs' ] = array();
		}
		sort( $_POST[ 'backupuploadsexcludedirs' ] );
		BackWPup_Option::update( $id, 'backupuploadsexcludedirs', $_POST[ 'backupuploadsexcludedirs' ] );
	}

	/**
	 * @param $job_object
	 * @return bool
	 */
	public function job_run( BackWPup_Job $job_object ) {

		if ( $job_object->steps_data[ $job_object->step_working ]['SAVE_STEP_TRY'] != $job_object->steps_data[ $job_object->step_working ][ 'STEP_TRY' ] ) {
			$job_object->log( sprintf( __( '%d. Trying to make a list of folders to back up&#160;&hellip;', 'backwpup' ), $job_object->steps_data[ $job_object->step_working ]['STEP_TRY'] ) );
		}
		$job_object->substeps_todo = 8;

		$abs_path = realpath( ABSPATH );
		if ( $job_object->job['backupabsfolderup'] ) {
			$abs_path = dirname( $abs_path );
		}
		$abs_path = trailingslashit( str_replace( '\\', '/', $abs_path ) );

		$job_object->temp['folders_to_backup'] = array();
		$folders_already_in = $job_object->get_folders_to_backup();

		//Folder lists for blog folders
		if ( $job_object->substeps_done === 0 ) {
			if ( $abs_path && ! empty( $job_object->job['backuproot'] ) ) {
				$abs_path = trailingslashit( str_replace( '\\', '/', $abs_path ) );
				$excludes = $this->get_exclude_dirs( $abs_path, $folders_already_in );
				foreach ( $job_object->job['backuprootexcludedirs'] as $folder ) {
					$excludes[] = trailingslashit( $abs_path . $folder );
				}
				$this->get_folder_list( $job_object, $abs_path, $excludes );
			}
			$job_object->substeps_done = 1;
			$job_object->update_working_data();
			$job_object->do_restart_time();
		}

		if ( $job_object->substeps_done === 1 ) {
			$wp_content_dir = realpath( WP_CONTENT_DIR );
			if ( $wp_content_dir && ! empty( $job_object->job['backupcontent'] ) ) {
				$wp_content_dir = trailingslashit( str_replace( '\\', '/', $wp_content_dir ) );
				$excludes       = $this->get_exclude_dirs( $wp_content_dir, $folders_already_in );
				foreach ( $job_object->job['backupcontentexcludedirs'] as $folder ) {
					$excludes[] = trailingslashit( $wp_content_dir . $folder );
				}
				$this->get_folder_list( $job_object, $wp_content_dir, $excludes );
			}
			$job_object->substeps_done = 2;
			$job_object->update_working_data();
			$job_object->do_restart_time();
		}

		if ( $job_object->substeps_done === 2 ) {
			$wp_plugin_dir = realpath( WP_PLUGIN_DIR );
			if ( $wp_plugin_dir && ! empty( $job_object->job['backupplugins'] ) ) {
				$wp_plugin_dir = trailingslashit( str_replace( '\\', '/', $wp_plugin_dir ) );
				$excludes      = $this->get_exclude_dirs( $wp_plugin_dir, $folders_already_in );
				foreach ( $job_object->job['backuppluginsexcludedirs'] as $folder ) {
					$excludes[] = trailingslashit( $wp_plugin_dir . $folder );
				}
				$this->get_folder_list( $job_object, $wp_plugin_dir, $excludes );
			}
			$job_object->substeps_done = 3;
			$job_object->update_working_data();
			$job_object->do_restart_time();
		}

		if ( $job_object->substeps_done === 3 ) {
			$theme_root = realpath( get_theme_root() );
			if ( $theme_root && ! empty( $job_object->job['backupthemes'] ) ) {
				$theme_root = trailingslashit( str_replace( '\\', '/', $theme_root ) );
				$excludes   = $this->get_exclude_dirs( $theme_root, $folders_already_in );
				foreach ( $job_object->job['backupthemesexcludedirs'] as $folder ) {
					$excludes[] = trailingslashit( $theme_root . $folder );
				}
				$this->get_folder_list( $job_object, $theme_root, $excludes );
			}
			$job_object->substeps_done = 4;
			$job_object->update_working_data();
			$job_object->do_restart_time();
		}

		if ( $job_object->substeps_done === 4 ) {
			$upload_dir = realpath( BackWPup_File::get_upload_dir() );
			if ( $upload_dir && ! empty( $job_object->job['backupuploads'] ) ) {
				$upload_dir = trailingslashit( str_replace( '\\', '/', $upload_dir ) );
				$excludes   = $this->get_exclude_dirs( $upload_dir, $folders_already_in );
				foreach ( $job_object->job['backupuploadsexcludedirs'] as $folder ) {
					$excludes[] = trailingslashit( $upload_dir . $folder );
				}
				$this->get_folder_list( $job_object, $upload_dir, $excludes );
			}
			$job_object->substeps_done = 5;
			$job_object->update_working_data();
			$job_object->do_restart_time();
		}

		if ( $job_object->substeps_done === 5 ) {
			//include dirs
			if ( $job_object->job['dirinclude'] ) {
				$dirinclude = explode( ',', $job_object->job['dirinclude'] );
				$dirinclude = array_unique( $dirinclude );
				//Crate file list for includes
				foreach ( $dirinclude as $dirincludevalue ) {
					if ( is_dir( $dirincludevalue ) ) {
						$this->get_folder_list( $job_object, $dirincludevalue );
					}
				}
			}
			$job_object->substeps_done = 6;
			$job_object->update_working_data();
			$job_object->do_restart_time();
		}

		if ( $job_object->substeps_done === 6 ) {
			//clean up folder list
			$folders = $job_object->get_folders_to_backup();
			$job_object->add_folders_to_backup( $folders, true );
			$job_object->substeps_done = 7;
			$job_object->update_working_data();
			$job_object->do_restart_time();
		}

		//add extra files if selected
		if ( ! empty( $job_object->job['backupspecialfiles'] ) ) {
			if ( is_readable( ABSPATH . 'wp-config.php' ) ) {
				$job_object->additional_files_to_backup[] = str_replace( '\\', '/', ABSPATH . 'wp-config.php' );
				$job_object->log( sprintf( __( 'Added "%s" to backup file list', 'backwpup' ), 'wp-config.php' ) );
			} elseif ( BackWPup_File::is_in_open_basedir( dirname( ABSPATH ) . '/wp-config.php' ) ) {
				if ( is_readable( dirname( ABSPATH ) . '/wp-config.php' ) && ! is_readable( dirname( ABSPATH ) . '/wp-settings.php' ) ) {
					$job_object->additional_files_to_backup[] = str_replace( '\\', '/', dirname( ABSPATH ) . '/wp-config.php' );
					$job_object->log( sprintf( __( 'Added "%s" to backup file list', 'backwpup' ), 'wp-config.php' ) );
				}
			}
			if ( is_readable( $abs_path . '.htaccess' ) && empty( $job_object->job['backuproot'] ) ) {
				$job_object->additional_files_to_backup[] = $abs_path . '.htaccess';
				$job_object->log( sprintf( __( 'Added "%s" to backup file list', 'backwpup' ), '.htaccess' ) );
			}
			if ( is_readable( $abs_path . 'nginx.conf' ) && empty( $job_object->job['backuproot'] ) ) {
				$job_object->additional_files_to_backup[] = $abs_path . 'nginx.conf';
				$job_object->log( sprintf( __( 'Added "%s" to backup file list', 'backwpup' ), 'nginx.conf' ) );
			}
			if ( is_readable( $abs_path . '.htpasswd' ) && empty( $job_object->job['backuproot'] ) ) {
				$job_object->additional_files_to_backup[] = $abs_path . '.htpasswd';
				$job_object->log( sprintf( __( 'Added "%s" to backup file list', 'backwpup' ), '.htpasswd' ) );
			}
			if ( is_readable( $abs_path . 'robots.txt' ) && empty( $job_object->job['backuproot'] ) ) {
				$job_object->additional_files_to_backup[] = $abs_path . 'robots.txt';
				$job_object->log( sprintf( __( 'Added "%s" to backup file list', 'backwpup' ), 'robots.txt' ) );
			}
			if ( is_readable( $abs_path . 'favicon.ico' ) && empty( $job_object->job['backuproot'] ) ) {
				$job_object->additional_files_to_backup[] = $abs_path . 'favicon.ico';
				$job_object->log( sprintf( __( 'Added "%s" to backup file list', 'backwpup' ), 'favicon.ico' ) );
			}
		}

		if ( $job_object->count_folder === 0 && count( $job_object->additional_files_to_backup ) === 0 ) {
			$job_object->log( __( 'No files/folder for the backup.', 'backwpup' ), E_USER_WARNING );
		} elseif ( $job_object->count_folder > 1 ) {
			$job_object->log( sprintf( __( '%1$d folders to backup.', 'backwpup' ), $job_object->count_folder ) );
		}

		$job_object->substeps_done = 8;

		return true;
	}

	/**
	 *
	 * Helper function for folder_list()
	 *
	 * @param        $job_object BackWPup_Job
	 * @param string $folder
	 * @param array $excludedirs
	 * @param bool $first
	 *
	 * @return bool
	 *
	 */
	private function get_folder_list( &$job_object, $folder, $excludedirs = array(), $first = true ) {

		$folder = trailingslashit( $folder );

		if ( $dir = opendir( $folder ) ) {
			//add folder to folder list
			$job_object->add_folders_to_backup( $folder );
			//scan folder
			while ( false !== ( $file = readdir( $dir ) ) ) {
				if ( in_array( $file, array( '.', '..' ), true ) ) {
					continue;
				}
				foreach ( $job_object->exclude_from_backup as $exclusion ) { //exclude files
					$exclusion = trim( $exclusion );
					if ( false !== stripos( $folder . $file, trim( $exclusion ) ) && ! empty( $exclusion ) ) {
						continue 2;
					}
				}
				if ( is_dir( $folder . $file ) ) {
					if ( in_array( trailingslashit( $folder . $file ), $excludedirs, true ) ) {
						continue;
					}
					if ( file_exists( trailingslashit( $folder . $file ) . '.donotbackup' ) ) {
						continue;
					}
					if ( ! is_readable( $folder . $file ) ) {
						$job_object->log( sprintf( __( 'Folder "%s" is not readable!', 'backwpup' ), $folder . $file ), E_USER_WARNING );
						continue;
					}
					$this->get_folder_list( $job_object, trailingslashit( $folder . $file ), $excludedirs, false );
				}
				if ( $first ) {
					$job_object->do_restart_time();
				}
			}
			closedir( $dir );
		}

		return true;
	}


	/**
	 *
	 * Get folder to exclude from a given folder for file backups
	 *
	 * @param $folder string folder to check for excludes
	 *
	 * @param array $excludedir
	 *
	 * @return array of folder to exclude
	 */
	private function get_exclude_dirs( $folder, $excludedir = array() ) {

		$folder     = trailingslashit( str_replace( '\\', '/', realpath( $folder ) ) );

		if ( false !== strpos( trailingslashit( str_replace( '\\', '/', realpath( WP_CONTENT_DIR ) ) ), $folder ) && trailingslashit( str_replace( '\\', '/', realpath( WP_CONTENT_DIR ) ) ) != $folder ) {
			$excludedir[] = trailingslashit( str_replace( '\\', '/', realpath( WP_CONTENT_DIR ) ) );
		}
		if ( false !== strpos( trailingslashit( str_replace( '\\', '/', realpath( WP_PLUGIN_DIR ) ) ), $folder ) && trailingslashit( str_replace( '\\', '/', realpath( WP_PLUGIN_DIR ) ) ) != $folder ) {
			$excludedir[] = trailingslashit( str_replace( '\\', '/', realpath( WP_PLUGIN_DIR ) ) );
		}
		if ( false !== strpos( trailingslashit( str_replace( '\\', '/', realpath( get_theme_root() ) ) ), $folder ) && trailingslashit( str_replace( '\\', '/', realpath( get_theme_root() ) ) ) != $folder ) {
			$excludedir[] = trailingslashit( str_replace( '\\', '/', realpath( get_theme_root() ) ) );
		}
		if ( false !== strpos( trailingslashit( str_replace( '\\', '/', realpath( BackWPup_File::get_upload_dir() ) ) ), $folder ) && trailingslashit( str_replace( '\\', '/', realpath( BackWPup_File::get_upload_dir() ) ) ) != $folder ) {
			$excludedir[] = trailingslashit( str_replace( '\\', '/', realpath( BackWPup_File::get_upload_dir() ) ) );
		}

		return array_unique( $excludedir );
	}
}
