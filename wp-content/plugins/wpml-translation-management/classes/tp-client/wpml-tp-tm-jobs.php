<?php

/**
 * Class WPML_TP_TM_Jobs
 *
 * @author OnTheGoSystems
 */
class WPML_TP_TM_Jobs {

	const CACHE_BATCH_ID = 'wpml_tp_tm_jobs_batch_id';

	/** @var wpdb $wpdb */
	private $wpdb;

	/**
	 * WPML_TF_Rating_TP_API constructor.
	 *
	 * @param wpdb $wpdb
	 */
	public function __construct( wpdb $wpdb ) {
		$this->wpdb = $wpdb;
	}

	/**
	 * @param int $job_id
	 *
	 * @return null|string
	 */
	public function get_batch_id( $job_id ) {
		$cached_batch_id = wp_cache_get( $job_id, self::CACHE_BATCH_ID );

		if ( $cached_batch_id ) {
			return $cached_batch_id;
		}

		$query = "SELECT tb.tp_id FROM {$this->wpdb->prefix}icl_translation_batches AS tb
					LEFT JOIN {$this->wpdb->prefix}icl_translation_status AS ts ON tb.id = ts.batch_id
					LEFT JOIN {$this->wpdb->prefix}icl_translate_job AS tj ON ts.rid = tj.rid
					WHERE tj.job_id = %d";

		$batch_id = $this->wpdb->get_var( $this->wpdb->prepare( $query, $job_id ) );

		wp_cache_set( $job_id, $batch_id, self::CACHE_BATCH_ID );

		return $batch_id;
	}
}
