<?php

class WPML_TM_Jobs_Summary_Report_Process_Factory {

	/** @var WPML_TM_Jobs_Summary_Report_View $template */
	private $template;

	/** @var WPML_TM_Jobs_Summary_Report_Process $weekly_report */
	private $weekly_report;

	/** @var WPML_TM_Jobs_Summary_Report_Process $daily_report */
	private $daily_report;

	/**
	 * @return WPML_TM_Jobs_Summary_Report_Process
	 */
	public function create_weekly_report() {
		if ( ! $this->weekly_report ) {
			$summary_report = $this->get_summary_report( WPML_TM_Jobs_Summary::WEEKLY_REPORT );

			$this->weekly_report = new WPML_TM_Jobs_Summary_Report_Process(
				$this->get_template(),
				new WPML_TM_Jobs_Weekly_Summary_Report_Model(),
				$summary_report->get_jobs()
			);
		}

		return $this->weekly_report;
	}

	/**
	 * @return WPML_TM_Jobs_Summary_Report_Process
	 */
	public function create_daily_report() {
		if ( ! $this->daily_report ) {
			$summary_report = $this->get_summary_report( WPML_TM_Jobs_Summary::DAILY_REPORT );

			$this->daily_report = new WPML_TM_Jobs_Summary_Report_Process(
				$this->get_template(),
				new WPML_TM_Jobs_Daily_Summary_Report_Model(),
				$summary_report->get_jobs()
			);
		}

		return $this->daily_report;
	}

	/**
	 * @param string $frequency
	 *
	 * @return WPML_TM_Jobs_Summary_Report
	 */
	private function get_summary_report( $frequency ) {
		global $sitepress, $wpdb;

		$word_count_records_factory = new WPML_TM_Word_Count_Records_Factory();
		$word_count_records         = $word_count_records_factory->create();
		$single_process_factory     = new WPML_TM_Word_Count_Single_Process_Factory();
		$single_process             = $single_process_factory->create();

		return new WPML_TM_Jobs_Summary_Report(
			new WPML_Translation_Jobs_Collection( $wpdb, array() ),
			new WPML_TM_String( false, $word_count_records, $single_process ),
			new WPML_TM_Post( false, $word_count_records, $single_process ),
			$frequency,
			new WPML_Translation_Element_Factory( $sitepress )
		);
	}

	/**
	 * @return WPML_TM_Jobs_Summary_Report_View
	 */
	private function get_template() {
		if ( ! $this->template ) {
			$template_service_factory = new WPML_TM_Email_Twig_Template_Factory();
			$this->template = new WPML_TM_Jobs_Summary_Report_View( $template_service_factory->create() );
		}

		return $this->template;
	}
}