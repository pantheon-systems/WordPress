<?php

class WPML_TM_Jobs_Summary_Report_View extends WPML_TM_Email_View {

	const WEEKLY_SUMMARY_TEMPLATE = 'notification/summary/summary.twig';

	/**
	 * @var array
	 */
	private $jobs;

	/**
	 * @var int
	 */
	private $manager_id;

	/**
	 * @var string
	 */
	private $summary_text;

	/**
	 * @return string
	 */
	public function get_report_content() {
		$model   = $this->get_model();
		$content = $this->render_header( $model['username'] );
		$content .= $this->template_service->show( $model, self::WEEKLY_SUMMARY_TEMPLATE );
		$content .= $this->render_email_footer();

		return $content;
	}

	/**
	 * @return array
	 */
	private function get_model() {
		return array(
			'username'          => get_userdata( $this->manager_id )->display_name,
			'jobs'              => $this->jobs,
			'text'              => $this->summary_text,
			'site_name'         => get_bloginfo( 'name' ),
			'number_of_updates' => isset( $this->jobs['completed'] ) ? count( $this->jobs['completed'] ) : 0,
			'strings'           => array(
				'jobs_waiting'          => __( 'Jobs that are waiting for translation', 'wpml-translation-management' ),
				'original_page'         => __( 'Original Page', 'wpml-translation-management' ),
				'translation'           => __( 'Translation', 'wpml-translation-management' ),
				'translator'            => __( 'Translator', 'wpml-translation-management' ),
				'updated'               => __( 'Updated / Translated', 'wpml-translation-management' ),
				'date'                  => __( 'Date', 'wpml-translation-management' ),
				'your_deadline'         => __( 'Your deadline', 'wpml-translation-management' ),
				'translation_languages' => __( 'Translation languages', 'wpml-translation-management' ),
				'number_of_pages'       => __( 'Number of pages', 'wpml-translation-management' ),
				'number_of_strings'     => __( 'Number of strings', 'wpml-translation-management' ),
				'number_of_words'       => __( 'Number of words', 'wpml-translation-management' ),
				'undefined'             => __( 'Undefined', 'wpml-translation-management' ),
			),
			'improve_quality'   => array(
				'title'   => __( 'Want to improve the quality of your site’s translation?', 'wpml-translation-management' ),
				'options' => array(
					array(
						'link_url'  => admin_url( 'admin.php?page=' . WPML_PLUGIN_FOLDER . '/menu/languages.php#wpml-translation-feedback-options' ),
						'link_text' => __( 'Translation Feedback', 'wpml-translation-management' ),
						'text'      => __( 'Allow visitors to tell you about translation issues by enabling %s', 'wpml-translation-management' ),
					),
					array(
						'link_url'  => admin_url( 'admin.php?page=' . WPML_TM_FOLDER . '/menu/main.php&sm=translation-services' ),
						'link_text' => __( 'translation services that are integrated with WPML', 'wpml-translation-management' ),
						'text'      => __( 'Try one of the %s', 'wpml-translation-management' ),
					),
				)
			),
		);
	}

	/**
	 * @param array $jobs
	 *
	 * @return $this
	 */
	public function set_jobs( $jobs ) {
		$this->jobs = $jobs;

		return $this;
	}

	/**
	 * @param int $manager_id
	 *
	 * @return $this
	 */
	public function set_manager_id( $manager_id ) {
		$this->manager_id = $manager_id;

		return $this;
	}

	/**
	 * @param string $summary_text
	 *
	 * @return $this
	 */
	public function set_summary_text( $summary_text ) {
		$this->summary_text = $summary_text;

		return $this;
	}
}