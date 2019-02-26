<?php

/**
 * Class WPML_TM_Unsent_Jobs_Notice_Template
 */
class WPML_TM_Unsent_Jobs_Notice_Template {

	const TEMPLATE_FILE = 'jobs-not-notified.twig';

	/**
	 * @var WPML_Twig_Template
	 */
	private $template_service;

	/**
	 * WPML_TM_Unsent_Jobs_Notice_Template constructor.
	 *
	 * @param IWPML_Template_Service $template_service
	 */
	public function __construct( IWPML_Template_Service $template_service ) {
		$this->template_service = $template_service;
	}

	/**
	 * @param array $jobs
	 *
	 * @return string
	 */
	public function get_notice_body( $jobs ) {
		$model = $this->get_notice_model( $jobs );

		return $this->template_service->show( $model, self::TEMPLATE_FILE );
	}

	/**
	 * @param array $jobs
	 *
	 * @return array
	 */
	private function get_notice_model( $jobs ) {
		$translators_tab = 'admin.php?page=' . WPML_TM_FOLDER . '/menu/main.php&sm=translators';
		$jobs_formatted = $this->get_formatted_jobs( $jobs );

		$model = array(
			'strings' => array(
				'title'  => esc_html__( 'Translations may delay because translators did not receive notifications', 'wpml-translation-management' ),
				'body'   => esc_html__( 'You have sent documents to translation. WPML can send notification emails to assigned translators, but translators for some languages have selected not to receive this notification.', 'wpml-translation-management' ),
				'jobs'   => $jobs_formatted,
				'bottom' => sprintf(
					esc_html__( 'You should contact your %1$s and ask them to enable the notification emails which will allow them to see when there is new work waiting for them. To enable notifications, translators need to log-in to this site, go to their user profile page and change the related option in the WPML language settings section.', 'wpml-translation-management' ),
					'<a href="' . admin_url( $translators_tab ) . '">' . esc_html__( 'translators' ) . '</a> '
				),
			),
		);

		return $model;
	}

	/**
	 * @param array $jobs
	 *
	 * @return array
	 */
	private function get_formatted_jobs( $jobs ) {
		$jobs_formatted = array();
		foreach ( $jobs as $job ) {
			$jobs_formatted[] = sprintf( __( 'Job %1$s: %2$s - %3$s', 'wpml-translation-management' ), $job['job_id'], $job['lang_from'], $job['lang_to'] );
		}

		return $jobs_formatted;
	}
}