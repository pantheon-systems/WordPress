<?php

/**
 * @author OnTheGo Systems
 */
class WPML_TM_ATE_Jobs {
	const UPDATED_JOB_STATUS = 'status_updated';
	const UPDATED_XLIFF = 'xliff_updated';
	private $records;

	/**
	 * WPML_TM_ATE_Jobs constructor.
	 *
	 * @param WPML_TM_ATE_Job_Records $records
	 */
	public function __construct( WPML_TM_ATE_Job_Records $records ) {
		$this->records = $records;
	}

	/**
	 * @param int $wpml_job_id
	 *
	 * @return int
	 */
	public function get_ate_job_id( $wpml_job_id ) {
		$wpml_job_id = (int) $wpml_job_id;

		return $this->records->get_ate_job_id( $wpml_job_id );
	}

	/**
	 * @param int $wpml_job_id
	 *
	 * @return int
	 */
	public function get_ate_job_progress( $wpml_job_id ) {
		$wpml_job_id = (int) $wpml_job_id;

		return $this->records->get_ate_job_progress( $wpml_job_id );
	}

	/**
	 * @param int $ate_job_id
	 *
	 * @return int
	 */
	public function get_wpml_job_id( $ate_job_id ) {
		$ate_job_id = (int) $ate_job_id;

		$ate_job = $this->records->get_data_from_ate_job_id( $ate_job_id );

		$wpml_job_id = null;

		if ( array_key_exists( 'wpml_job_id', $ate_job ) ) {
			$wpml_job_id = (int) $ate_job['wpml_job_id'];
		}

		return $wpml_job_id;
	}

	/**
	 * @param int $wpml_job_id
	 * @param array $ate_job_data
	 *
	 * @return array
	 */
	public function store( $wpml_job_id, $ate_job_data ) {
		$wpml_job_id = (int) $wpml_job_id;

		try {
			$this->records->store( $wpml_job_id, $ate_job_data );

			return $this->update_wpml_job( $wpml_job_id );
		} catch ( Exception $ex ) {
			return array(
				'error' => array(
					'code'    => $ex->getCode(),
					'message' => $ex->getMessage(),
					'trace'   => $ex->getTraceAsString(),
				)
			);
		}
	}

	/**
	 * @param int $wpml_job_id
	 *
	 * @return array
	 * @throws \Requests_Exception
	 */
	private function update_wpml_job( $wpml_job_id ) {
		$result = array();

		$xliff = $this->records->get_translated_xliff( $wpml_job_id );
		if ( $xliff ) {
			$this->update_translated_content( $xliff );
			$result[] = self::UPDATED_XLIFF;
		}

		$status             = $this->records->get_ate_job_status( $wpml_job_id );
		$factory            = wpml_tm_load_job_factory();
		$job                = $factory->get_translation_job( $wpml_job_id, false, 0, true );
		$translation_id     = $job->get_translation_id();

		if ( $translation_id ) {
			$translation_status = wpml_tm_get_records()->icl_translation_status_by_translation_id( $translation_id );

			if ( $status && (int) $translation_status->status() !== (int) $status ) {
				$translation_status->update( array( 'status' => $status ) );
				$result[] = self::UPDATED_JOB_STATUS;
			}
		}

		return $result;
	}

	/**
	 * @param $xliff
	 *
	 * @return bool
	 * @throws \Requests_Exception
	 */
	private function update_translated_content( $xliff ) {
		$factory       = wpml_tm_load_job_factory();
		$xliff_factory = new WPML_TM_Xliff_Reader_Factory( $factory );
		$xliff_reader  = $xliff_factory->general_xliff_reader();
		$job_data      = $xliff_reader->get_data( $xliff );
		if ( is_wp_error( $job_data ) ) {
			throw new Requests_Exception( $job_data->get_error_message(), $job_data->get_error_code() );
		}

		kses_remove_filters();
		wpml_tm_save_data( $job_data, false );
		kses_init();

		return true;
	}
}
