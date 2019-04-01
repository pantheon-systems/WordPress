<?php

class WPML_TM_Emails_Settings {

	const TEMPLATE                = 'emails-settings.twig';
	const COMPLETED_JOB_FREQUENCY = 'completed_frequency';
	const NOTIFY_IMMEDIATELY      = 1;
	const NOTIFY_DAILY            = 2;
	const NOTIFY_WEEKLY           = 3;

	/**
	 * @var IWPML_Template_Service
	 */
	private $template_service;

	/**
	 * @var array
	 */
	private $tm;

	public function __construct( IWPML_Template_Service $template_service, TranslationManagement $tm ) {
		$this->template_service = $template_service;
		$this->tm               = $tm;
	}

	public function add_hooks() {
		add_action( 'wpml_tm_translation_notification_setting_after', array( $this, 'render' ) );
		add_action( 'wpml_tm_notification_settings_saved', array( $this, 'remove_scheduled_summary_email' ) );
	}

	public function render() {
		echo $this->template_service->show( $this->get_model(), self::TEMPLATE );
	}

	private function get_model() {
		return array(
			'strings' => array(
				'section_title_translator' => __( 'Notification emails to translators', 'wpml-translation-management' ),
				'label_new_job' => __( 'Notify translators when new jobs are waiting for them', 'wpml-translation-management' ),
				'label_include_xliff' => __( 'Include XLIFF files in the notification emails', 'wpml-translation-management' ),
				'label_resigned_job' => __( 'Notify translators when they are removed from jobs', 'wpml-translation-management' ),
				'section_title_manager' => __( 'Notification emails to the translation manager', 'wpml-translation-management' ),
				'label_completed_job' => esc_html__( 'Notify the translation manager when jobs are completed %s', 'wpml-translation-management' ),
				'label_overdue_job' => esc_html__( 'Notify the translation manager when jobs are late by %s days', 'wpml-translation-management' ),
			),
			'settings' => array(
				'new_job' => array(
					'value'   => self::NOTIFY_IMMEDIATELY,
					'checked' => checked( self::NOTIFY_IMMEDIATELY, $this->tm->settings['notification']['new-job'], false ),
				),
				'include_xliff' => array(
					'value'    => 1,
					'checked'  => checked( 1, $this->tm->settings['notification']['include_xliff'], false ),
					'disabled' => disabled( 0, $this->tm->settings['notification']['new-job'], false ),
				),
				'resigned' => array(
					'value'   => self::NOTIFY_IMMEDIATELY,
					'checked' => checked( self::NOTIFY_IMMEDIATELY, $this->tm->settings['notification']['resigned'], false ),
				),
				'completed' => array(
					'value'   => 1,
					'checked' => checked( self::NOTIFY_IMMEDIATELY, $this->tm->settings['notification']['completed'], false ),
				),
				'completed_frequency' => array(
					'options' => array(
						array(
							'label'   => __( 'immediately', 'wpml-translation-management' ),
							'value'   => self::NOTIFY_IMMEDIATELY,
							'checked' => selected( self::NOTIFY_IMMEDIATELY, $this->tm->settings['notification'][ self::COMPLETED_JOB_FREQUENCY ], false )
						),
						array(
							'label'   => __( 'once a day', 'wpml-translation-management' ),
							'value'   => self::NOTIFY_DAILY,
							'checked' => selected( self::NOTIFY_DAILY, $this->tm->settings['notification'][ self::COMPLETED_JOB_FREQUENCY ], false )
						),
						array(
							'label'   => __( 'once a week', 'wpml-translation-management' ),
							'value'   => self::NOTIFY_WEEKLY,
							'checked' => selected( self::NOTIFY_WEEKLY, $this->tm->settings['notification'][ self::COMPLETED_JOB_FREQUENCY ], false )
						),
					),
					'disabled' => disabled( 0, $this->tm->settings['notification']['completed'], false ),
				),
				'overdue' => array(
					'value'   => 1,
					'checked' => checked( self::NOTIFY_IMMEDIATELY, $this->tm->settings['notification']['overdue'], false ),
				),
				'overdue_offset' => array(
					'value'    => $this->tm->settings['notification']['overdue_offset'],
					'disabled' => disabled( 0, $this->tm->settings['notification']['overdue'], false ),
				),
			),
		);
	}

	public function remove_scheduled_summary_email() {
		wp_clear_scheduled_hook( WPML_TM_Jobs_Summary_Report_Hooks::EVENT_HOOK );
	}
}
