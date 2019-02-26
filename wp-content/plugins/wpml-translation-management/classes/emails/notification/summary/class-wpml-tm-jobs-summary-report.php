<?php

class WPML_TM_Jobs_Summary_Report {

	/**
	 * @var WPML_Translation_Jobs_Collection
	 */
	private $jobs_collection;

	/**
	 * @var array
	 */
	private $jobs = array();

	/**
	 * @var WPML_TM_String
	 */
	private $string_counter;

	/**
	 * @var WPML_TM_Post
	 */
	private $post_counter;

	/**
	 * @var string
	 */
	private $type;

	/**
	 * @var WPML_Translation_Element_Factory
	 */
	private $element_factory;

	public function __construct(
		WPML_Translation_Jobs_Collection $jobs_collection,
		WPML_TM_String $string_counter,
		WPML_TM_Post $post_counter,
		$type = WPML_TM_Jobs_Summary::WEEKLY_REPORT,
		WPML_Translation_Element_Factory $element_factory
	) {
		$this->jobs_collection = $jobs_collection;
		$this->string_counter  = $string_counter;
		$this->post_counter    = $post_counter;
		$this->type            = $type;
		$this->element_factory = $element_factory;
		$this->build_completed_jobs();
		$this->build_waiting_jobs();
	}

	private function build_completed_jobs() {
		$jobs = $this->jobs_collection->get_jobs( array(
				'any_translation_service' => true,
				'status'                  => ICL_TM_COMPLETE
			)
		);

		foreach ( $jobs as $job ) {
			$completed_date = $job instanceof WPML_Post_Translation_Job || $job instanceof WPML_Element_Translation_Job ? $job->get_completed_date() : '';
			$out_of_period  = strtotime( $completed_date ) < strtotime( '-' . WPML_TM_Jobs_Summary::WEEKLY_SCHEDULE );

			if ( WPML_TM_Jobs_Summary::DAILY_REPORT === $this->type ) {
				$out_of_period = strtotime( $completed_date ) < strtotime( '-' . WPML_TM_Jobs_Summary::DAILY_SCHEDULE );
			}

			if ( ! $completed_date || $out_of_period ) {
				continue;
			}

			$original_element = $this->element_factory->create( $job->get_original_element_id(), $job->get_type() );
			$translation_element = $original_element->get_translation( $job->get_language_code() );


			$this->jobs[ $job->get_basic_data()->manager_id ][ WPML_TM_Jobs_Summary::JOBS_COMPLETED_KEY ][] = array(
				'completed_date'  => date_i18n( get_option( 'date_format', 'F d, Y' ), strtotime( $job->get_completed_date() ) ),
				'original_page'   => array(
					'title' => $job->get_title(),
					'url'   => $job->get_url( true ),
				),
				'translated_page' => array(
					'title' => get_the_title( $translation_element->get_element_id() ) . ' (' . $job->get_language_code() . ')',
					'url'   => get_the_permalink( $translation_element->get_element_id() ),
				),
				'translator'      => $this->get_translator_name( $job ),
				'deadline'        => $job->get_deadline_date() ?
					date_i18n( get_option( 'date_format', 'F d, Y' ), strtotime( $job->get_deadline_date() ) ) :
					'',
				'status'          => $job->get_status(),
				'overdue'         => $job->get_deadline_date() &&
				                     strtotime( $job->get_deadline_date() ) < strtotime( $job->get_completed_date() )
			);
		}
	}

	private function build_waiting_jobs() {
		$jobs                       = $this->jobs_collection->get_jobs( array(
				'any_translation_service' => true,
				'status'                  => ICL_TM_WAITING_FOR_TRANSLATOR
			)
		);
		$counters                   = array();
		$number_of_strings          = 0;
		$number_of_words_in_strings = 0;

		foreach ( $jobs as $job ) {
			$manager_id = isset( $job->get_basic_data()->manager_id ) ? $job->get_basic_data()->manager_id : 0;
			$lang_pair  = $job->get_source_language_code() . '|' . $job->get_language_code();

			if ( ! isset( $counters[ $manager_id ][ $lang_pair ]['number_of_strings'], $counters[ $manager_id ][ $lang_pair ]['number_of_words'], $counters[ $manager_id ][ $lang_pair ]['number_of_pages'] ) ) {
				$counters[ $manager_id ][ $lang_pair ]['number_of_strings'] = 0;
				$counters[ $manager_id ][ $lang_pair ]['number_of_words']   = 0;
				$counters[ $manager_id ][ $lang_pair ]['number_of_pages']   = 0;
			}

			if ( 'String' === $job->get_type() ) {
				$this->string_counter->set_id( $job->get_original_element_id() );
				$number_of_strings ++;
				$number_of_words_in_strings += $this->string_counter->get_words_count();
			} else {
				$this->post_counter->set_id( $job->get_original_element_id() );
				$counters[ $manager_id ][ $lang_pair ]['number_of_pages'] += 1;
				$counters[ $manager_id ][ $lang_pair ]['number_of_words'] += $this->post_counter->get_words_count();

				$this->jobs[ $manager_id ][ WPML_TM_Jobs_Summary::JOBS_WAITING_KEY ][ $lang_pair ] = array(
					'lang_pair'         => $job->get_source_language_code( true ) . ' ' . __( 'to', 'wpml-translation-management' ) . ' ' . $job->get_language_code( true ),
					'number_of_strings' => $number_of_strings,
					'number_of_words'   => $counters[ $manager_id ][ $lang_pair ]['number_of_words'] + $number_of_words_in_strings,
					'number_of_pages'   => $counters[ $manager_id ][ $lang_pair ]['number_of_pages'],
				);
			}
		}
	}

	/**
	 * @param WPML_Element_Translation_Job $job
	 *
	 * @return string
	 */
	private function get_translator_name( WPML_Element_Translation_Job $job ) {
		$translator_name = $job->get_translation_service() ?
			TranslationProxy::get_service_name( $job->get_translation_service() ) :
			$job->get_translator_name();

		if ( 'local' === $job->get_translation_service() ) {
			$user            = get_userdata( $job->get_translator_id() );
			$translator_name = $user->display_name . ' (' . $user->user_login . ')';
		}

		return $translator_name;
	}

	/**
	 * @return array
	 */
	public function get_jobs() {
		return $this->jobs;
	}
}