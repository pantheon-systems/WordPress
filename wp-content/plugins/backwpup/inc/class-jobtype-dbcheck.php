<?php
/**
 *
 */
class BackWPup_JobType_DBCheck extends BackWPup_JobTypes {

	/**
	 *
	 */
	public function __construct() {

		$this->info[ 'ID' ]          = 'DBCHECK';
		$this->info[ 'name' ]        = __( 'DB Check', 'backwpup' );
		$this->info[ 'description' ] = __( 'Check database tables', 'backwpup' );
		$this->info[ 'URI' ]         = __( 'http://backwpup.com', 'backwpup' );
		$this->info[ 'author' ]      = 'Inpsyde GmbH';
		$this->info[ 'authorURI' ]   = __( 'http://inpsyde.com', 'backwpup' );
		$this->info[ 'version' ]     = BackWPup::get_plugin_data( 'Version' );

	}

	/**
	 * @return array
	 */
	public function option_defaults() {
		return array( 'dbcheckwponly' => TRUE, 'dbcheckrepair' => FALSE );
	}


	/**
	 * @param $jobid
	 */
	public function edit_tab( $jobid ) {
		?>
		<h3 class="title"><?php esc_html_e( 'Settings for database check', 'backwpup' ) ?></h3>
		<p></p>
		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'WordPress tables only', 'backwpup' ); ?></th>
				<td>
					<label for="iddbcheckwponly">
					<input class="checkbox" value="1" id="iddbcheckwponly"
						   type="checkbox" <?php checked( BackWPup_Option::get( $jobid, 'dbcheckwponly' ), TRUE ); ?>
						   name="dbcheckwponly"/> <?php esc_html_e( 'Check WordPress database tables only', 'backwpup' ); ?>
                    </label>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Repair', 'backwpup' ); ?></th>
				<td>
                    <label for="iddbcheckrepair">
					<input class="checkbox" value="1" id="iddbcheckrepair"
						   type="checkbox" <?php checked( BackWPup_Option::get( $jobid, 'dbcheckrepair' ), TRUE ); ?>
						   name="dbcheckrepair" /> <?php esc_html_e( 'Try to repair defect table', 'backwpup' ); ?>
					</label>
				</td>
			</tr>
		</table>
		<?php
	}


	/**
	 * @param $jobid
	 */
	public function edit_form_post_save( $jobid ) {
		BackWPup_Option::update( $jobid, 'dbcheckwponly', ! empty( $_POST[ 'dbcheckwponly' ] ) );
		BackWPup_Option::update( $jobid, 'dbcheckrepair', ! empty( $_POST[ 'dbcheckrepair' ] ) );
	}

	/**
	 * @param $job_object
	 * @return bool
	 */
	public function job_run( BackWPup_Job $job_object ) {
		global $wpdb;
		/* @var wpdb $wpdb */

		$job_object->log( sprintf( __( '%d. Trying to check database&#160;&hellip;', 'backwpup' ), $job_object->steps_data[ $job_object->step_working ][ 'STEP_TRY' ] ) );
		if ( ! isset( $job_object->steps_data[ $job_object->step_working ][ 'DONETABLE' ] ) || ! is_array( $job_object->steps_data[ $job_object->step_working ][ 'DONETABLE' ] ) )
			$job_object->steps_data[ $job_object->step_working ][ 'DONETABLE' ] = array();

		//to check
		$tables = array();
		$tablestype = array();
		$restables = $wpdb->get_results( 'SHOW FULL TABLES FROM `' . DB_NAME . '`', ARRAY_N );
		foreach ( $restables as $table ) {
			if ( $job_object->job[ 'dbcheckwponly' ] && substr( $table[ 0 ], 0, strlen( $wpdb->prefix ) ) != $wpdb->prefix )
				continue;
			$tables[ ]                 = $table[ 0 ];
			$tablestype[ $table[ 0 ] ] = $table[ 1 ];
		}

		//Set num
		$job_object->substeps_todo = sizeof( $tables );

		//Get table status
		$status = array();
		$resstatus = $wpdb->get_results( "SHOW TABLE STATUS FROM `" . DB_NAME . "`", ARRAY_A );
		foreach ( $resstatus as $tablestatus ) {
			$status[ $tablestatus[ 'Name' ] ] = $tablestatus;
		}

		//check tables
		if ( $job_object->substeps_todo > 0 ) {
			foreach ( $tables as $table ) {
				if ( in_array( $table, $job_object->steps_data[ $job_object->step_working ][ 'DONETABLE' ], true ) )
					continue;

				if ( $tablestype[ $table ] == 'VIEW' ) {
					$job_object->log( sprintf( __( 'Table %1$s is a view. Not checked.', 'backwpup' ), $table ) );
					continue;
				}

				if ( $status[ $table ][ 'Engine' ] != 'MyISAM' && $status[ $table ][ 'Engine' ] != 'InnoDB' ) {
					$job_object->log( sprintf( __( 'Table %1$s is not a MyISAM/InnoDB table. Not checked.', 'backwpup' ), $table ) );
					continue;
				}

				//CHECK TABLE funktioniert bei MyISAM- und InnoDB-Tabellen (http://dev.mysql.com/doc/refman/5.1/de/check-table.html)
				$check = $wpdb->get_row( "CHECK TABLE `" . $table . "` MEDIUM", OBJECT );
				if ( strtolower( $check->Msg_text ) == 'ok' ) {
					if ( $job_object->is_debug() ) {
						$job_object->log( sprintf( __( 'Result of table check for %1$s is: %2$s', 'backwpup' ), $table, $check->Msg_text ) );
					}
				} elseif ( strtolower( $check->Msg_type ) == 'warning' ) {
					$job_object->log( sprintf( __( 'Result of table check for %1$s is: %2$s', 'backwpup' ), $table, $check->Msg_text ), E_USER_WARNING );
				} else {
					$job_object->log( sprintf( __( 'Result of table check for %1$s is: %2$s', 'backwpup' ), $table, $check->Msg_text ), E_USER_ERROR );
				}
				//Try to Repair table
				if ( ! empty( $job_object->job[ 'dbcheckrepair' ] ) && strtolower( $check->Msg_text ) != 'ok' && $status[ $table ][ 'Engine' ] == 'MyISAM' ) {
					$repair = $wpdb->get_row( 'REPAIR TABLE `' . $table . '` EXTENDED', OBJECT );
					if ( strtolower( $repair->Msg_text ) == 'ok' ) {
						$job_object->log( sprintf( __( 'Result of table repair for %1$s is: %2$s', 'backwpup' ), $table, $repair->Msg_text ) );
					} elseif ( strtolower( $repair->Msg_type ) == 'warning' ) {
						$job_object->log( sprintf( __( 'Result of table repair for %1$s is: %2$s', 'backwpup' ), $table, $repair->Msg_text ), E_USER_WARNING );
					} else {
						$job_object->log( sprintf( __( 'Result of table repair for %1$s is: %2$s', 'backwpup' ), $table, $repair->Msg_text ), E_USER_ERROR );
					}
				}
				$job_object->steps_data[ $job_object->step_working ][ 'DONETABLE' ][ ] = $table;
				$job_object->substeps_done ++;
			}
			$job_object->log( __( 'Database check done!', 'backwpup' ) );
		}
		else {
			$job_object->log( __( 'No tables to check.', 'backwpup' ) );
		}

		unset( $job_object->steps_data[ $job_object->step_working ][ 'DONETABLE' ] );
		return TRUE;
	}
}
