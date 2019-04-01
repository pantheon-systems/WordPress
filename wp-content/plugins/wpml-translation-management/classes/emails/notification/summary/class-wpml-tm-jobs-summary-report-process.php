<?php

class WPML_TM_Jobs_Summary_Report_Process {

	/**
	 * @var WPML_TM_Jobs_Summary_Report_View
	 */
	private $view;

	/**
	 * @var WPML_TM_Jobs_Summary_Report_Model
	 */
	private $report_model;

	/**
	 * @var array
	 */
	private $jobs;

	public function __construct(
		WPML_TM_Jobs_Summary_Report_View $view,
		WPML_TM_Jobs_Summary_Report_Model $report_model,
		array $jobs
	) {
		$this->view         = $view;
		$this->report_model = $report_model;
		$this->jobs         = $jobs;
	}

	public function send() {
		foreach ( $this->jobs as $manager_id => $jobs ) {
			if ( array_key_exists( WPML_TM_Jobs_Summary::JOBS_COMPLETED_KEY, $jobs ) ) {
				$this->view
					->set_jobs( $jobs )
					->set_manager_id( $manager_id )
					->set_summary_text( $this->report_model->get_summary_text() );

				$this->send_email( $manager_id );
			}
		}
	}

	/**
	 * @param int $manager_id
	 */
	private function send_email( $manager_id ) {
		wp_mail(
			get_userdata( $manager_id )->user_email,
			sprintf( $this->report_model->get_subject(), get_bloginfo( 'name' ), date( 'd/F/Y', time() ) ),
			$this->view->get_report_content(),
			array(
				'MIME-Version: 1.0',
				'Content-type: text/html; charset=UTF-8',
			)
		);
	}
}