<?php

class WPML_TM_Overdue_Jobs_Report {

	const OVERDUE_JOBS_REPORT_TEMPLATE = 'notification/overdue-jobs-report.twig';

	/** @var  WPML_Translation_Jobs_Collection $jobs_collection */
	private $jobs_collection;

	/** @var WPML_TM_Email_Notification_View $email_view */
	private $email_view;

	/** @var bool $has_active_remote_service */
	private $has_active_remote_service;

	/** @var array $notification_settings */
	private $notification_settings;

	private $sitepress;

	private $tp_jobs;

	/**
	 * @param WPML_Translation_Jobs_Collection $jobs_collection
	 * @param WPML_TM_Email_Notification_View $email_view
	 * @param bool $has_active_remote_service
	 * @param array $notification_settings
	 * @param SitePress $sitepress
	 * @param WPML_TP_Jobs_Collection|null $tp_jobs
	 */
	public function __construct(
		WPML_Translation_Jobs_Collection $jobs_collection,
		WPML_TM_Email_Notification_View $email_view,
		$has_active_remote_service,
		array $notification_settings,
		SitePress $sitepress,
		WPML_TP_Jobs_Collection $tp_jobs = null
	) {
		$this->jobs_collection           = $jobs_collection;
		$this->email_view                = $email_view;
		$this->has_active_remote_service = $has_active_remote_service;
		$this->notification_settings     = $notification_settings;
		$this->sitepress                 = $sitepress;
		$this->tp_jobs                   = $tp_jobs;
	}

	public function send() {
		$jobs_by_manager_id = $this->get_overdue_jobs_by_manager_id();

		if ( $jobs_by_manager_id ) {
			$current_language = $this->sitepress->get_current_language();
			$this->sitepress->switch_lang( $this->sitepress->get_default_language() );
			foreach ( $jobs_by_manager_id as $manager_id => $jobs ) {
				$this->send_email( $manager_id, $jobs );
			}
			$this->sitepress->switch_lang( $current_language );
		}
	}

	/** @return array */
	private function get_overdue_jobs_by_manager_id() {
		$args = array(
			'overdue'       => true,
			'translator_id' => '',
		);

		$jobs               = $this->jobs_collection->get_jobs( $args );
		$jobs_by_manager_id = array();

		foreach ( $jobs as $key => $job ) {

			if ( $this->tp_jobs && $this->tp_jobs->is_job_canceled( $job ) ) {
				continue;
			}

			if ( ! $job instanceof WPML_Element_Translation_Job ) {
				continue;
			}

			if ( $job->get_number_of_days_overdue() < $this->notification_settings['overdue_offset'] ) {
				continue;
			}

			$job->get_basic_data();
			$jobs_by_manager_id[ $job->get_manager_id() ][] = $job;
		}

		return $jobs_by_manager_id;
	}

	/**
	 * @param string $manager_id
	 * @param array  $jobs
	 */
	private function send_email( $manager_id, array $jobs ) {
		$manager = get_user_by( 'id', $manager_id );

		$translation_jobs_url = admin_url( 'admin.php?page=' . WPML_TM_FOLDER . '/menu/main.php&sm=jobs' );

		/* translators: List of translation jobs: %s replaced by "list of translation jobs" */
		$message_to_translation_jobsA = esc_html_x( 'You can see all the jobs that you sent and their deadlines in the %s.', 'List of translation jobs: %s replaced by "list of translation jobs"', 'wpml-translation-management' );
		/* translators: List of translation jobs: used to build a link to the translation jobs page */
		$message_to_translation_jobsB = esc_html_x( 'list of translation jobs', 'List of translation jobs: used to build a link to the translation jobs page', 'wpml-translation-management' );

		$message_to_translation_jobs = sprintf(
			$message_to_translation_jobsA,
			'<a href="' . esc_url( $translation_jobs_url ) . '">' . $message_to_translation_jobsB . '</a>'
		);

		$model = array(
			'username'                     => $manager->display_name,
		    'intro_message_1'              => __( 'This is a quick reminder about translation jobs that you sent and are behind schedule.', 'wpml-translation-management' ),
		    'intro_message_2'              => __( 'The deadline that you set for the following jobs has passed:', 'wpml-translation-management' ),
		    'jobs'                         => $jobs,
		    'job_deadline_details'         => __( 'deadline: %1$s, late by %2$d days', 'wpml-translation-management' ),
		    'message_to_translation_jobs'  => $message_to_translation_jobs,
		    'promote_translation_services' => ! $this->has_active_remote_service,
		);

		$to      = $manager->display_name . ' <' . $manager->user_email . '>';
		$subject = esc_html__( 'Overdue translation jobs report', 'wpml-translation-management' );
		$message = $this->email_view->render_model( $model, self::OVERDUE_JOBS_REPORT_TEMPLATE );

		$headers = array(
			'MIME-Version: 1.0',
			'Content-type: text/html; charset=UTF-8',
		);

		wp_mail( $to, $subject, $message, $headers );
	}
}
