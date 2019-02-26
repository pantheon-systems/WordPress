<?php

class WPML_TM_Jobs_Summary_Report_Hooks {

	const EVENT_HOOK = 'wpml_tm_send_summary_report';
	const EVENT_CALLBACK = 'send_summary_report';

	/**
	 * @var WPML_TM_Jobs_Summary_Report_Process_Factory
	 */
	private $process_factory;

	/**
	 * @var TranslationManagement
	 */
	private $tm;

	public function __construct( WPML_TM_Jobs_Summary_Report_Process_Factory $process_factory, TranslationManagement $tm ) {
		$this->process_factory = $process_factory;
		$this->tm              = $tm;
	}

	public function add_hooks() {
		if ( $this->notification_setting_allow_scheduling() ) {
			add_action( self::EVENT_HOOK, array( $this, self::EVENT_CALLBACK ) );
			add_action( 'init', array( $this, 'schedule_email' ) );
		}
	}

	/**
	 * @return bool
	 */
	private function notification_setting_allow_scheduling() {
		$schedulable_settings = array(
			WPML_TM_Emails_Settings::NOTIFY_DAILY,
			WPML_TM_Emails_Settings::NOTIFY_WEEKLY
		);

		return isset( $this->tm->settings['notification'][ WPML_TM_Emails_Settings::COMPLETED_JOB_FREQUENCY ] ) &&
		       in_array(
			       (int) $this->tm->settings['notification'][ WPML_TM_Emails_Settings::COMPLETED_JOB_FREQUENCY ],
			       $schedulable_settings,
			       true
		       );

	}

	public function send_summary_report() {
		if ( WPML_TM_Emails_Settings::NOTIFY_DAILY === (int) $this->tm->settings['notification'][ WPML_TM_Emails_Settings::COMPLETED_JOB_FREQUENCY ] ) {
			$summary_report_process = $this->process_factory->create_daily_report();
		} else {
			$summary_report_process = $this->process_factory->create_weekly_report();
		}

		if ( $summary_report_process ) {
			$summary_report_process->send();
		}
	}

	public function schedule_email() {
		if ( ! wp_next_scheduled( self::EVENT_HOOK ) ) {
			wp_schedule_single_event( $this->get_schedule_time(), self::EVENT_HOOK );
		}
	}

	/**
	 * @return int
	 */
	private function get_schedule_time() {
		$schedule_time = strtotime( '+ ' . WPML_TM_Jobs_Summary::DAILY_SCHEDULE );

		if ( WPML_TM_Emails_Settings::NOTIFY_WEEKLY === (int) $this->tm->settings['notification'][ WPML_TM_Emails_Settings::COMPLETED_JOB_FREQUENCY ] ) {
			$schedule_time = strtotime( '+ ' . WPML_TM_Jobs_Summary::WEEKLY_SCHEDULE );
		}

		return $schedule_time;
	}
}