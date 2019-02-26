<?php

/**
 * Class WPML_TP_Polling_Status
 */
class WPML_TP_Polling_Status {
	protected $project;
	private $sitepress;
	private $cms_id_helper;

	/**
	 * WPML_TP_Polling_Status constructor.
	 *
	 * @param TranslationProxy_Project $project
	 * @param SitePress                $sitepress
	 * @param WPML_TM_CMS_ID           $cms_id_helper
	 */
	public function __construct(
		TranslationProxy_Project $project,
		SitePress $sitepress,
		WPML_TM_CMS_ID $cms_id_helper
	) {
		$this->project       = $project;
		$this->sitepress     = $sitepress;
		$this->cms_id_helper = $cms_id_helper;
	}

	/**
	 * @return array containing strings displayed in the translation service polling status box
	 *
	 * @throws Exception in case communication with Translation Proxy fails
	 */
	public function get_status_array() {
		try {
			$data = $this->filter_obsolete( $this->project->jobs() );
		} catch ( Exception $e ) {
			throw new WPMLTranslationProxyApiException( 'Got the following error when trying to load status data from Translation Proxy via polling: '
			                                            . $e->getMessage(), 0 );
		}
		$button_text = esc_html__( 'Check status and get translations', 'wpml-translation-management' );
		if ( ( $job_in_progress = $this->in_progress_count( $data ) ) == 1 ) {
			$jobs_in_progress_text = sprintf( esc_html__( '%s1 translation job%s has been sent to remote translators',
				'wpml-translation-management' ), '<strong>', '</strong>' );
		} else {
			$jobs_in_progress_text = sprintf( esc_html__( '%s%d translation jobs%s have been sent to remote translators',
				'wpml-translation-management' ), '<strong>', $job_in_progress, '</strong>' );
		}
		$last_picked_up      = $this->sitepress->get_setting( 'last_picked_up' );
		$last_time_picked_up = ! empty( $last_picked_up ) ? date_i18n( 'Y, F jS @g:i a',
			$last_picked_up ) : esc_html__( 'never', 'wpml-translation-management' );
		$last_pickup_text    = sprintf( esc_html__( 'Last check: %s', 'wpml-translation-management' ), $last_time_picked_up );

		return array(
			'jobs_in_progress_text' => $jobs_in_progress_text,
			'button_text'           => $button_text,
			'last_pickup_text'      => $last_pickup_text,
			'polling_data'          => $this->filter_known_pending( $data ),
		);
	}

	/**
	 * @param array $data
	 *
	 * @return int number of jobs that are in progress with the translation service
	 */
	private function in_progress_count( array $data ) {
		$count = 0;
		foreach ( $data as $job ) {
			$count += in_array( $job->job_state, array(
				'waiting_translation',
				'translation_ready',
				'received'
			), true )
			          || ( $job->job_state === 'cancelled'
			               && $job->cms_id
			               && $this->cms_id_helper->get_translation_id( $job->cms_id ) )
			          || apply_filters( 'wpml_st_job_state_pending', false, $job )
				? 1 : 0;
		}

		return $count;
	}

	/**
	 * Removes pending translation jobs from the list of jobs to be synchronized
	 * with Translation Proxy
	 *
	 * @param array $jobs
	 *
	 * @return array jobs array, from which all pending jobs correctly tracked in the wpml database were removed
	 */
	private function filter_known_pending( array $jobs ) {
		foreach ( $jobs as &$job ) {
			if ( $job->cms_id
			     && in_array( $job->job_state, array(
					'waiting_translation',
					'delivered'
				) )
			     && $this->cms_id_helper->get_translation_id( $job->cms_id )
			) {
				$job = null;
			} elseif ( $job->job_state === 'delivered'
			           && ! $job->cms_id
			           && apply_filters( 'wpml_st_job_state_pending', false, $job )
			) {
				$job->job_state = 'translation_ready';
			}
		}

		return array_values( array_filter( $jobs ) );
	}

	/**
	 * Filters those jobs who are obsolete on a account of a newer job
	 * existing for the given content from the TP returned data.
	 *
	 * @param array $jobs
	 *
	 * @return array
	 */
	private function filter_obsolete( array $jobs ) {
		foreach ( $jobs as &$job ) {
			if ( $job->cms_id ) {
				$compared_jobs = array_filter( $jobs );
				foreach ( $compared_jobs as $compared_job ) {
					if ( $compared_job->cms_id === $job->cms_id && $compared_job->id > $job->id ) {
						$job = null;
						break;
					}
				}
			}
		}

		return array_filter( $jobs );
	}
}