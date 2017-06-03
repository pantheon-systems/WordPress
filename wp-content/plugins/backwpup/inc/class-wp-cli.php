<?php
/**
 * Class for WP-CLI commands
 */
class BackWPup_WP_CLI extends WP_CLI_Command {

	/**
	 * Start a BackWPup job
	 *
	 * # EXAMPLES
	 *
	 *   backwpup start 13
	 *   backwpup start --jobid=13 (deprecated)
	 *
	 * @param $args
	 * @param $assoc_args
	 */
	public function start( $args, $assoc_args ) {

		$jobid = 0;

		if ( file_exists( BackWPup::get_plugin_data( 'running_file' ) ) ) {
			WP_CLI::error( __( 'A job is already running.', 'backwpup' ) );
		}

		if ( isset( $jobid ) ) {
			$jobid = (int) $assoc_args[ 'jobid' ];
		}

		if ( ! empty( $args[ 0 ] ) ) {
			$jobid = (int) $args[ 0 ];
		}

		if ( empty( $jobid ) ) {
			WP_CLI::error( __( 'No job ID specified!', 'backwpup' ) );
		}


		$jobids = BackWPup_Option::get_job_ids();
		if ( ! in_array( $jobid, $jobids, true ) ) {
			WP_CLI::error( __( 'Job ID does not exist!', 'backwpup' ) );
		}

		BackWPup_Job::start_cli( $jobid );
	}

	/**
	 *  Abort a working BackWPup Job
	 *
	 */
	public function abort( $args, $assoc_args ) {

		if ( ! file_exists( BackWPup::get_plugin_data( 'running_file' ) ) ) {
			WP_CLI::error( __( 'Nothing to abort!', 'backwpup' ) );
		}

		//abort
		BackWPup_Job::user_abort();
		WP_CLI::success( __( 'Job will be terminated.', 'backwpup' ) ) ;
	}


	/**
	 * Display a List of Jobs
	 *
	 */
	public function jobs( $args, $assoc_args ) {

		$formatter_args = array(
			'format' => 'table',
			'fields' => array(
				'Job ID',
				'Name'
			),
			'field' => NULL
		);

		$items = array();

		$formatter = new WP_CLI\Formatter( $formatter_args );

		$jobids = BackWPup_Option::get_job_ids();

		foreach ($jobids as $jobid ) {
			$items[] = array(
				'Job ID' => $jobid,
				'Name'  => BackWPup_Option::get( $jobid, 'name' )
			);
		}

		$formatter->display_items( $items );
	}

	/**
	 * See Status of a working job
	 *
	 * @param $args
	 * @param $assoc_args
	 */
	public function working( $args, $assoc_args ) {

		$job_object = BackWPup_Job::get_working_data();

		if ( ! is_object( $job_object ) ) {
			WP_CLI::error( __( 'No job running', 'backwpup' ) );
		}

		$formatter_args = array(
			'format' => 'table',
			'fields' => array(
				'JobID',
				'Name',
				'Warnings',
				'Errors',
				'On Step',
				'Done',
			),
			'field' => NULL
		);

		$formatter = new WP_CLI\Formatter( $formatter_args );

		$items = array();
		$items[] = array(
			'JobID' => $job_object->job[ 'jobid' ],
			'Name' => $job_object->job[ 'name' ],
			'Warnings' => $job_object->warnings,
			'Errors' => $job_object->errors,
			'On Step' => $job_object->steps_data[ $job_object->step_working ][ 'NAME' ],
			'Done' => $job_object->step_percent . ' / ' . $job_object->substep_percent,
			'Last message' => str_replace( '&hellip;', '...', strip_tags( $job_object->lastmsg ) )
		);

		$formatter->display_items( $items );

		WP_CLI::log( 'Last Message: ' . str_replace( '&hellip;', '...', strip_tags( $job_object->lastmsg ) ) );
	}

}
