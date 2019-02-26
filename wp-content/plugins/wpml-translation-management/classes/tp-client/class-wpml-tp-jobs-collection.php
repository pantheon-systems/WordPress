<?php

class WPML_TP_Jobs_Collection {

	private $project;

	private $job_factory;

	private $batch_factory;

	private $jobs;

	public function __construct(
		TranslationProxy_Project $project,
		WPML_TP_Job_Factory $job_factory,
		WPML_Translation_Batch_Factory $batch_factory
	) {
		$this->project       = $project;
		$this->job_factory   = $job_factory;
		$this->batch_factory = $batch_factory;
	}

	/**
	 * @return WPML_TP_Job[]
	 */
	public function get_all() {
		$jobs_obj = array();
		if ( ! $this->jobs ) {
			$jobs = $this->project->jobs();
			foreach ( $jobs as $job ) {
				$jobs_obj[] = $this->job_factory->create( $job );
			}
			$this->jobs = $jobs_obj;
		}
		return $this->jobs;
	}

	/**
	 * @param WPML_Translation_Job $job
	 *
	 * @return bool
	 */
	public function is_job_canceled( WPML_Translation_Job $job ) {
		$canceled = false;
		$batch    = $this->batch_factory->create( $job->get_batch_id() );

		foreach ( $this->get_all() as $tp_job ) {
			if ( (int) $batch->get_batch_tp_id() === $tp_job->get_batch()->id
			     && (int) $job->get_original_element_id() === $tp_job->get_original_element_id()
			     && WPML_TP_Job::CANCELLED === $tp_job->get_job_state()
			) {
				$canceled = true;
			}
		}

		return $canceled;
	}
}