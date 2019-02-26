<?php

/**
 * Class WPML_TM_Unsent_Jobs_Notice
 */
class WPML_TM_Unsent_Jobs_Notice {

	const OPT_JOBS_NOT_NOTIFIED = '_wpml_jobs_not_notified';
	const NOTICE_ID = 'job-not-notified';
	const NOTICE_GROUP_ID = 'tm-jobs-notification';

	/**
	 * @var string
	 */
	private $body;

	/**
	 * @var WPML_WP_API
	 */
	private $wp_api;

	/**
	 * @var WPML_TM_Unsent_Jobs_Notice_Template
	 */
	private $notice_template;

	/**
	 * WPML_TM_Unsent_Jobs_Notice constructor.
	 *
	 * @param WPML_WP_API $wp_api
	 * @param WPML_TM_Unsent_Jobs_Notice_Template|null $notice_template
	 */
	public function __construct( WPML_WP_API $wp_api, WPML_TM_Unsent_Jobs_Notice_Template $notice_template = null ) {
		$this->wp_api          = $wp_api;
		$this->notice_template = $notice_template;
	}

	private function prepare_notice_body() {
		$this->body = $this->get_notice_template()->get_notice_body( $this->get_jobs() );
	}

	/**
	 * @return null|WPML_TM_Unsent_Jobs_Notice_Template
	 */
	private function get_notice_template() {
		if ( ! $this->notice_template ) {
			$template_paths   = array(
				WPML_TM_PATH . '/templates/notices/',
			);
			$twig_loader      = new Twig_Loader_Filesystem( $template_paths );
			$environment_args = array();
			if ( WP_DEBUG ) {
				$environment_args['debug'] = true;
			}
			$twig         = new Twig_Environment( $twig_loader, $environment_args );
			$twig_service = new WPML_Twig_Template( $twig );

			$this->notice_template = new WPML_TM_Unsent_Jobs_Notice_Template( $twig_service, $this->get_jobs() );
		}

		return $this->notice_template;
	}

	/**
	 * @param WPML_Notices $wpml_admin_notices
	 */
	public function add_notice( WPML_Notices $wpml_admin_notices, $dismissed_option_key ) {
		if ( $this->get_jobs() ) {
			$this->prepare_notice_body();

			$notice = new WPML_Notice( self::NOTICE_ID, $this->body, 'tm-jobs-notification' );
			$notice->set_css_class_types( 'info' );
			$notice->add_display_callback( array( $this->wp_api, 'is_jobs_tab' ) );
			$this->add_actions( $notice );
			$this->remove_notice_from_dismissed_list( self::NOTICE_GROUP_ID, $dismissed_option_key );

			$wpml_admin_notices->add_notice( $notice );

			$this->update_jobs_option( array() );
		}

	}

	private function remove_notice_from_dismissed_list( $notice_group_id, $dismissed_option_key ) {
		$dismissed_notices = get_option( $dismissed_option_key );

		if ( is_array( $dismissed_notices ) ) {
			foreach ( (array) $dismissed_notices as $key => $notices ) {
				if ( $key === $notice_group_id ) {
					unset( $dismissed_notices[ $key ] );
				}
			}

			update_option( $dismissed_option_key, $dismissed_notices );
		}
	}

	/**
	 * @param WPML_Notice $notice
	 */
	private function add_actions( WPML_Notice $notice ) {
		$dismiss_action = new WPML_Notice_Action( __( 'Dismiss', 'wpml-translation-management' ), '#', true, false, false, true );
		$notice->add_action( $dismiss_action );
	}

	/**
	 * @param array $args
	 */
	public function add_job( $args ) {
		$job_id = $args['job']->get_id();
		$lang_from = $args['job']->get_source_language_code( true );
		$lang_to = $args['job']->get_language_code( true );

		$jobs = $this->get_jobs();

		if ( ! wp_filter_object_list( $jobs, array( 'job_id' => $job_id ) ) ) {
			$jobs[] = array(
				'job_id'       => $job_id,
				'lang_from' => $lang_from,
				'lang_to'   => $lang_to,
			);

			$this->update_jobs_option( $jobs );
		}
	}

	/**
	 * @param array $args
	 */
	public function remove_job( $args ) {
		$job_id = $args['job']->get_id();
		$unsent_jobs = $this->get_jobs();

		if ( $unsent_jobs ) {
			foreach ( $unsent_jobs as $key => $unsent_job ) {
				if ( $unsent_job['job_id'] === $job_id ) {
					unset( $unsent_jobs[$key] );
				}
			}
		}
		$this->update_jobs_option( $unsent_jobs );
	}

	/**
	 * @param array $jobs
	 */
	private function update_jobs_option( $jobs ) {
		update_option( self::OPT_JOBS_NOT_NOTIFIED, $jobs );
	}

	/**
	 * @return array
	 */
	private function get_jobs() {
		return get_option( self::OPT_JOBS_NOT_NOTIFIED );
	}
}