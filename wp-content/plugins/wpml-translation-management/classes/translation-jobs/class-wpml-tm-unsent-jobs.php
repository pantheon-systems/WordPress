<?php

/**
 * Class WPML_TM_Unsent_Jobs_Notice
 *
 * @group unsent-jobs-notification
 */
class WPML_TM_Unsent_Jobs {

	/**
	 * @var WPML_TM_Blog_Translators
	 */
	private $blog_translators;

	/**
	 * @var SitePress
	 */
	private $sitepress;

	/**
	 * WPML_TM_Unsent_Jobs constructor.
	 *
	 * @param WPML_TM_Blog_Translators $blog_translators
	 * @param SitePress $sitepress
	 */
	public function __construct( WPML_TM_Blog_Translators $blog_translators, SitePress $sitepress ) {
		$this->blog_translators = $blog_translators;
		$this->sitepress = $sitepress;
	}

	public function add_hooks(){
		add_action( 'wpml_tm_assign_job_notification', array( $this, 'prepare_unsent_job_for_notice' ) );
		add_action( 'wpml_tm_new_job_notification', array( $this, 'prepare_unsent_job_for_notice' ), 10, 2 );
		add_action( 'wpml_tm_local_string_sent', array( $this, 'prepare_unsent_job_for_notice' ) );
	}

	/**
	 * @param WPML_Translation_Job $job
	 * @param null $translator_id
	 */
	public function prepare_unsent_job_for_notice( WPML_Translation_Job $job, $translator_id = null ) {

		if ( $translator_id ) {
			$translators = array( get_userdata( $translator_id ) );
		} else {
			$translators = $this->blog_translators->get_blog_translators( array(
				'from' => $job->get_source_language_code(),
				'to'   => $job->get_language_code()
			) );
		}

		foreach ( $translators as $translator ) {

			$args = array(
				'job' => $job,
				'event' => WPML_User_Jobs_Notification_Settings::is_new_job_notification_enabled( $translator->ID ) ? 'sent' : 'unsent',
			);

			do_action( 'wpml_tm_jobs_translator_notification', $args );
		}
	}
}