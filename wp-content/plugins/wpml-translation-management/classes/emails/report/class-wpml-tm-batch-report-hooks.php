<?php

/**
 * Class WPML_TM_Notification_Batch_Hooks
 */
class WPML_TM_Batch_Report_Hooks {

	/**
	 * @var WPML_TM_Batch_Report
	 */
	private $batch_report;

	/**
	 * @var WPML_TM_Batch_Report_Email_Process
	 */
	private $email_process;

	/**
	 * WPML_TM_Batch_Report_Hooks constructor.
	 *
	 * @param WPML_TM_Batch_Report $batch_report
	 * @param WPML_TM_Batch_Report_Email_Process $email_process
	 */
	public function __construct(
		WPML_TM_Batch_Report $batch_report,
		WPML_TM_Batch_Report_Email_Process $email_process
	) {
		$this->batch_report  = $batch_report;
		$this->email_process = $email_process;
	}

	public function add_hooks() {
		add_action( 'wpml_tm_assign_job_notification', array( $this, 'set_job' ) );
		add_action( 'wpml_tm_new_job_notification', array( $this, 'set_job' ), 10, 2 );
		add_action( 'wpml_tm_local_string_sent', array( $this, 'set_job' ) );
		add_action( 'wpml_tm_basket_committed', array( $this->email_process, 'process_emails' ) );
	}

	public function set_job( $job ) {
		if ( $job instanceof WPML_Translation_Job ) {
			$this->batch_report->set_job( $job );
		}
	}
}