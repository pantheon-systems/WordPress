<?php

/**
 * Class WPML_TM_Unsent_Jobs_Notifications_Hooks
 */
class WPML_TM_Unsent_Jobs_Notice_Hooks {
	/** @var string */
	protected $dismissed_option_key;

	/**
	 * @var WPML_TM_Unsent_Jobs_Notice
	 */
	private $wpml_tm_notice_email_notice;

	/**
	 * @var WPML_Notices
	 */
	private $wpml_admin_notices;

	/**
	 * @var WPML_WP_API
	 */
	private $wp_api;

	/**
	 * WPML_TM_Unsent_Jobs_Notice_Hooks constructor.
	 *
	 * @param WPML_TM_Unsent_Jobs_Notice $wpml_tm_notice_email_notice
	 * @param WPML_WP_API                $wp_api
	 * @param string                     $dismissed_option_key
	 */
	public function __construct( WPML_TM_Unsent_Jobs_Notice $wpml_tm_notice_email_notice, WPML_WP_API $wp_api, $dismissed_option_key ) {
		$this->wpml_tm_notice_email_notice = $wpml_tm_notice_email_notice;
		$this->wpml_admin_notices = wpml_get_admin_notices();
		$this->wp_api = $wp_api;
		$this->dismissed_option_key = $dismissed_option_key;
	}

	public function add_hooks() {
		add_action( 'wpml_tm_jobs_translator_notification', array( $this, 'email_for_job' ) );
		add_action( 'wpml_tm_basket_committed', array( $this, 'add_notice' ) );
		add_action( 'shutdown', array( $this, 'remove_notice' ) );
	}

	/**
	 * @param array $args
	 */
	public function email_for_job( $args ){
		$job_set = array_key_exists( 'job', $args ) && $args['job'];
		$event_set = array_key_exists( 'event', $args ) && $args['event'];
		if ( $job_set && $event_set ) {
			if ( 'unsent' === $args['event'] ) {
				$this->wpml_tm_notice_email_notice->add_job( $args );
			} else {
				$this->wpml_tm_notice_email_notice->remove_job( $args );
			}
		}
	}

	public function add_notice(){
		$this->wpml_tm_notice_email_notice->add_notice( $this->wpml_admin_notices, $this->get_dismissed_option_key() );
	}

	public function remove_notice() {
		if ( $this->wp_api->is_jobs_tab() ) {
			$this->wpml_admin_notices->remove_notice( WPML_TM_Unsent_Jobs_Notice::NOTICE_GROUP_ID, WPML_TM_Unsent_Jobs_Notice::NOTICE_ID );
		}
	}

	/**
	 * @return string
	 */
	private function get_dismissed_option_key() {
		return $this->dismissed_option_key;
	}
}