<?php
/**
 *
 */
class BackWPup_JobType_DBDump extends BackWPup_JobTypes {

	/**
	 *
	 */
	public function __construct() {

		$this->info[ 'ID' ]          = 'DBDUMP';
		$this->info[ 'name' ]        = __( 'DB Backup', 'backwpup' );
		$this->info[ 'description' ] = __( 'Database backup', 'backwpup' );
		$this->info[ 'URI' ]         = __( 'http://backwpup.com', 'backwpup' );
		$this->info[ 'author' ]      = 'Inpsyde GmbH';
		$this->info[ 'authorURI' ]   = __( 'http://inpsyde.com', 'backwpup' );
		$this->info[ 'version' ]     = BackWPup::get_plugin_data( 'Version' );

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
		global $wpdb;
		/* @var wpdb $wpdb */

		$defaults = array(
			'dbdumpexclude' => array(), 'dbdumpfile' => sanitize_file_name( DB_NAME ), 'dbdumptype' => 'sql', 'dbdumpfilecompression' => ''
		);
		//set only wordpress tables as default
		$dbtables = $wpdb->get_results( 'SHOW TABLES FROM `' . DB_NAME . '`', ARRAY_N );
		foreach ( $dbtables as $dbtable) {
			if ( $wpdb->prefix != substr( $dbtable[ 0 ], 0, strlen( $wpdb->prefix ) ) )
				$defaults[ 'dbdumpexclude' ][] = $dbtable[ 0 ];
		}

		return $defaults;
	}


	/**
	 * @param $jobid
	 */
	public function edit_tab( $jobid ) {
		global $wpdb;
		/* @var wpdb $wpdb */

		?>
        <input name="dbdumpwpony" type="hidden" value="1" />
        <h3 class="title"><?php _e( 'Settings for database backup', 'backwpup' ) ?></h3>
        <p></p>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e( 'Tables to backup', 'backwpup' ); ?></th>
                <td>
                    <input type="button" class="button-secondary" id="dball" value="<?php esc_attr_e( 'all', 'backwpup' ); ?>">&nbsp;
					<input type="button" class="button-secondary" id="dbnone" value="<?php esc_attr_e( 'none', 'backwpup' ); ?>">&nbsp;
                    <input type="button" class="button-secondary" id="dbwp" value="<?php echo esc_attr($wpdb->prefix); ?>">
					<?php
					$tables = $wpdb->get_results( 'SHOW FULL TABLES FROM `' . DB_NAME . '`', ARRAY_N );
					echo '<fieldset id="dbtables"><div style="width: 30%; float:left; min-width: 250px; margin-right: 10px;">';
					$next_row = ceil( count( $tables ) / 3 );
					$counter = 0;
					foreach ( $tables as $table ) {
						$tabletype = '';
						if ( $table[ 1 ] !== 'BASE TABLE' ) {
							$tabletype = ' <i>(' . strtolower( esc_html( $table[ 1 ] ) ) . ')</i>';
						}
						echo '<label for="idtabledb-' . esc_html( $table[ 0 ] ) . '""><input class="checkbox" type="checkbox"' . checked( ! in_array( $table[ 0 ], BackWPup_Option::get( $jobid, 'dbdumpexclude' ), true ), TRUE, FALSE ) . ' name="tabledb[]" id="idtabledb-' . esc_html( $table[ 0 ] ) . '" value="' . esc_html( $table[ 0 ] ) . '"/> ' . esc_html( $table[ 0 ] ) . $tabletype . '</label><br />';
						$counter++;
						if ($next_row <= $counter) {
							echo '</div><div style="width: 30%; float:left; min-width: 250px; margin-right: 10px;">';
							$counter = 0;
						}
					}
					echo '</div></fieldset>';
					?>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="iddbdumpfile"><?php _e( 'Backup file name', 'backwpup' ) ?></label></th>
                <td>
                    <input id="iddbdumpfile" name="dbdumpfile" type="text"
                           value="<?php echo esc_attr( BackWPup_Option::get( $jobid, 'dbdumpfile' ) );?>"
                           class="medium-text code"/>.sql
                </td>
            </tr>
			<tr>
				<th scope="row"><?php _e( 'Backup file compression', 'backwpup' ) ?></th>
				<td>
					<fieldset>
						<?php
						echo '<label for="iddbdumpfilecompression"><input class="radio" type="radio"' . checked( '', BackWPup_Option::get( $jobid, 'dbdumpfilecompression' ), FALSE ) . ' name="dbdumpfilecompression"  id="iddbdumpfilecompression" value="" /> ' . __( 'none', 'backwpup' ). '</label><br />';
						if ( function_exists( 'gzopen' ) ) {
							echo '<label for="iddbdumpfilecompression-gz"><input class="radio" type="radio"' . checked( '.gz', BackWPup_Option::get( $jobid, 'dbdumpfilecompression' ), FALSE ) . ' name="dbdumpfilecompression" id="iddbdumpfilecompression-gz" value=".gz" /> ' . __( 'GZip', 'backwpup' ). '</label><br />';
						} else {
							echo '<label for="iddbdumpfilecompression-gz"><input class="radio" type="radio"' . checked( '.gz', BackWPup_Option::get( $jobid, 'dbdumpfilecompression' ), FALSE ) . ' name="dbdumpfilecompression" id="iddbdumpfilecompression-gz" value=".gz" disabled="disabled" /> ' . __( 'GZip', 'backwpup' ). '</label><br />';
						}
						?>
					</fieldset>
				</td>
			</tr>
        </table>
		<?php
	}


	/**
	 * @param $id
	 */
	public function edit_form_post_save( $id ) {
		global $wpdb;
		/* @var wpdb $wpdb */

		if ( $_POST[ 'dbdumpfilecompression' ] === '' || $_POST[ 'dbdumpfilecompression' ] === '.gz' ) {
			BackWPup_Option::update( $id, 'dbdumpfilecompression', $_POST[ 'dbdumpfilecompression' ] );
		}
		BackWPup_Option::update( $id, 'dbdumpfile', BackWPup_Job::sanitize_file_name( $_POST[ 'dbdumpfile' ] ) );
		//selected tables
		$dbdumpexclude = array();
		$checked_db_tables = array();
		if ( isset( $_POST[ 'tabledb' ] ) ) {
			foreach ( $_POST[ 'tabledb' ] as $dbtable ) {
				$checked_db_tables[ ] = sanitize_text_field( $dbtable );
			}
		}
		$dbtables = $wpdb->get_results( 'SHOW TABLES FROM `' . DB_NAME . '`', ARRAY_N );
		foreach ( $dbtables as $dbtable ) {
			if ( ! in_array( $dbtable[ 0 ], $checked_db_tables, true ) ) {
				$dbdumpexclude[ ] = $dbtable[ 0 ];
			}
		}
		BackWPup_Option::update( $id, 'dbdumpexclude', $dbdumpexclude );

	}

	/**
	 * Dumps the Database
	 *
	 * @param $job_object BackWPup_Job
	 *
	 * @return bool
	 */
	public function job_run( BackWPup_Job $job_object ) {

		$job_object->substeps_todo = 1;

		if ( $job_object->steps_data[ $job_object->step_working ]['SAVE_STEP_TRY'] != $job_object->steps_data[ $job_object->step_working ][ 'STEP_TRY' ] )
			$job_object->log( sprintf( __( '%d. Try to backup database&#160;&hellip;', 'backwpup' ), $job_object->steps_data[ $job_object->step_working ][ 'STEP_TRY' ] ) );

		//build filename
		if ( empty( $job_object->steps_data[ $job_object->step_working ][ 'dbdumpfile' ] ) )
			$job_object->steps_data[ $job_object->step_working ][ 'dbdumpfile' ] = $job_object->generate_filename( $job_object->job[ 'dbdumpfile' ], 'sql' ) . $job_object->job[ 'dbdumpfilecompression' ];

		try {

			//Connect to Database
			$sql_dump = new BackWPup_MySQLDump( array(
													 'dumpfile'	  => BackWPup::get_plugin_data( 'TEMP' ) . $job_object->steps_data[ $job_object->step_working ][ 'dbdumpfile' ],
												) );

			if ( $job_object->steps_data[ $job_object->step_working ]['SAVE_STEP_TRY'] != $job_object->steps_data[ $job_object->step_working ][ 'STEP_TRY' ] ) {
				$job_object->log( sprintf( __( 'Connected to database %1$s on %2$s', 'backwpup' ), DB_NAME, DB_HOST ) );
			}


			//Exclude Tables
			foreach ( $sql_dump->tables_to_dump as $key => $table ) {
				if ( in_array( $table, $job_object->job[ 'dbdumpexclude' ], true ) )
					unset( $sql_dump->tables_to_dump[ $key ] );
			}

			//set steps must done
			$job_object->substeps_todo = count( $sql_dump->tables_to_dump );

			if ( $job_object->substeps_todo == 0 ) {
				$job_object->log( __( 'No tables to backup.', 'backwpup' ), E_USER_WARNING );
				unset( $sql_dump );

				return TRUE;
			}

			//dump head
			if ( ! isset( $job_object->steps_data[ $job_object->step_working ][ 'is_head' ] ) ) {
				$sql_dump->dump_head( TRUE );
				$job_object->steps_data[ $job_object->step_working ][ 'is_head' ] = TRUE;
			}
			//dump tables
			$i = 0;
			foreach(  $sql_dump->tables_to_dump as $table ) {
				if ( $i < $job_object->substeps_done ) {
					$i++;
					continue;
				}
				if ( empty( $job_object->steps_data[ $job_object->step_working ][ 'tables' ][ $table ] ) ) {
					$num_records = $sql_dump->dump_table_head( $table );
					$job_object->steps_data[ $job_object->step_working ][ 'tables' ][ $table ] = array( 'start'   => 0,
																										'length'   => 1000 );
					if ( $job_object->is_debug() ) {
						$job_object->log( sprintf( __( 'Backup database table "%s" with "%s" records', 'backwpup' ), $table, $num_records ) );
					}
				}
				$while = true;
				while ( $while ) {
					$dump_start_time = microtime( TRUE );
					$done_records = $sql_dump->dump_table( $table ,$job_object->steps_data[ $job_object->step_working ][ 'tables' ][ $table ][ 'start' ], $job_object->steps_data[ $job_object->step_working ][ 'tables' ][ $table ][ 'length' ] );
					$dump_time = microtime( TRUE ) - $dump_start_time;
					if ( empty( $dump_time ) )
						$dump_time = 0.01;
					if ( $done_records < $job_object->steps_data[ $job_object->step_working ][ 'tables' ][ $table ][ 'length' ] ) //that is the last chunk
						$while = FALSE;
					$job_object->steps_data[ $job_object->step_working ][ 'tables' ][ $table ][ 'start' ] = $job_object->steps_data[ $job_object->step_working ][ 'tables' ][ $table ][ 'start' ] + $done_records;
					// dump time per record and set next length
					$length = ceil( ( $done_records / $dump_time ) * $job_object->get_restart_time() );
					if ( $length > 25000 || 0 >= $job_object->get_restart_time() )
						$length = 25000;
					if ( $length < 1000 )
						$length = 1000;
					$job_object->steps_data[ $job_object->step_working ][ 'tables' ][ $table ][ 'length' ] =  $length;
					$job_object->do_restart_time();
				}
				$sql_dump->dump_table_footer( $table );
				$job_object->substeps_done++;
				$i++;
				$job_object->update_working_data();
			}
			//dump footer
			$sql_dump->dump_footer();
			unset( $sql_dump );

		} catch ( Exception $e ) {
			$job_object->log( $e->getMessage(), E_USER_ERROR, $e->getFile(), $e->getLine() );
			unset( $sql_dump );
			return FALSE;
		}

		$filesize = filesize( BackWPup::get_plugin_data( 'TEMP' ) . $job_object->steps_data[ $job_object->step_working ][ 'dbdumpfile' ] );

		if ( ! is_file( BackWPup::get_plugin_data( 'TEMP' ) . $job_object->steps_data[ $job_object->step_working ][ 'dbdumpfile' ] ) || $filesize < 1 ) {
			$job_object->log( __( 'MySQL backup file not created', 'backwpup' ), E_USER_ERROR );
			return FALSE;
		} else {
			$job_object->additional_files_to_backup[ ] = BackWPup::get_plugin_data( 'TEMP' ) . $job_object->steps_data[ $job_object->step_working ][ 'dbdumpfile' ];
			$job_object->log( sprintf( __( 'Added database dump "%1$s" with %2$s to backup file list', 'backwpup' ), $job_object->steps_data[ $job_object->step_working ][ 'dbdumpfile' ], size_format( $filesize, 2 ) ) );
		}

		//cleanups
		unset( $job_object->steps_data[ $job_object->step_working ][ 'tables' ] );

		$job_object->log( __( 'Database backup done!', 'backwpup' ) );

		return TRUE;
	}

	/**
	 *
	 */
	public function admin_print_scripts() {

		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			wp_enqueue_script( 'backwpupjobtypedbdump', BackWPup::get_plugin_data( 'URL' ) . '/assets/js/page_edit_jobtype_dbdump.js', array('jquery'), time(), TRUE );
		} else {
			wp_enqueue_script( 'backwpupjobtypedbdump', BackWPup::get_plugin_data( 'URL' ) . '/assets/js/page_edit_jobtype_dbdump.min.js', array('jquery'), BackWPup::get_plugin_data( 'Version' ), TRUE );
		}
	}


}
