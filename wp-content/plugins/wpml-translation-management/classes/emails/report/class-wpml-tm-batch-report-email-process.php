<?php

/**
 * Class WPML_TM_Batch_Report_Email_Process
 */
class WPML_TM_Batch_Report_Email_Process {

	/**
	 * @var WPML_TM_Batch_Report
	 */
	private $batch_report;

	/**
	 * @var WPML_TM_Batch_Report_Email_Builder
	 */
	private $email_builder;

	/**
	 * WPML_TM_Batch_Report_Email_Process constructor.
	 *
	 * @param WPML_TM_Batch_Report $batch_report
	 * @param WPML_TM_Batch_Report_Email_Builder $email_builder
	 */
	public function __construct( WPML_TM_Batch_Report $batch_report, WPML_TM_Batch_Report_Email_Builder $email_builder ) {
		$this->batch_report  = $batch_report;
		$this->email_builder = $email_builder;
	}

	public function process_emails() {
		$batch_jobs = $this->batch_report->get_jobs();

		$this->email_builder->prepare_assigned_jobs_emails( $batch_jobs );
		$this->email_builder->prepare_unassigned_jobs_emails( $batch_jobs );

		$this->send_emails();
	}

	private function send_emails() {
		$headers = array();
		$headers[] = 'Content-type: text/html; charset=UTF-8';

		foreach ( $this->email_builder->get_emails() as $email ) {
			$email['attachment'] = isset( $email['attachment'] ) ? $email['attachment'] : array();
			$email_sent = wp_mail( $email['email'], $email['subject'], $email['body'], $headers, $email['attachment'] );

			if ( $email_sent ) {
				$this->batch_report->reset_batch_report( $email['translator_id'] );
			}
		}

		$this->batch_report->reset_batch_report( 0 );
	}
}