<?php

class WPML_TM_Jobs_Deadline_Cron_Hooks implements IWPML_Action {

	const CHECK_OVERDUE_JOBS_EVENT = 'wpml-tm-check-overdue-jobs-event';

	/** @var WPML_TM_Overdue_Jobs_Report_Factory $overdue_jobs_report_factory */
	private $overdue_jobs_report_factory;

	/** @var TranslationManagement $notification_settings */
	private $translation_management;

	public function __construct(
		WPML_TM_Overdue_Jobs_Report_Factory $overdue_jobs_report_factory,
		TranslationManagement $translation_management
	) {
		$this->overdue_jobs_report_factory = $overdue_jobs_report_factory;
		$this->translation_management      = $translation_management;
	}

	public function add_hooks() {
		if ( is_admin() ) {
			add_action( 'init', array( $this, 'schedule_event' ), $this->get_init_priority() );
		}

		add_action( self::CHECK_OVERDUE_JOBS_EVENT, array( $this, 'send_overdue_email_report' ) );
	}

	public function schedule_event() {
		if ( $this->is_notification_enabled() ) {
			if ( ! wp_next_scheduled( self::CHECK_OVERDUE_JOBS_EVENT ) ) {
				wp_schedule_event( time(), 'daily', self::CHECK_OVERDUE_JOBS_EVENT );
			}
		} else {
			$timestamp = wp_next_scheduled( self::CHECK_OVERDUE_JOBS_EVENT );
			wp_unschedule_event( $timestamp, self::CHECK_OVERDUE_JOBS_EVENT );
		}
	}

	/** @return int */
	private function get_init_priority() {
		return $this->translation_management->get_init_priority() + 1;
	}

	/** @return bool */
	private function is_notification_enabled() {
		$settings = $this->translation_management->get_settings();
		return ICL_TM_NOTIFICATION_NONE !== (int) $settings['notification']['overdue'];
	}

	public function send_overdue_email_report() {
		$overdue_jobs_report = $this->overdue_jobs_report_factory->create();
		$overdue_jobs_report->send();
	}
}
