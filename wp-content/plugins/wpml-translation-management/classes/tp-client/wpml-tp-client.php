<?php

/**
 * Class WPML_TP_Client
 *
 * @author OnTheGoSystems
 */
class WPML_TP_Client {

	/** @var WPML_TP_Project $project */
	private $project;

	/** @var WPML_TP_TM_Jobs $tm_jobs */
	private $tm_jobs;

	/** @var WPML_TP_API_Services $services */
	private $services;

	/** @var WPML_TP_API_Batches $batches */
	private $batches;

	/** @var WPML_TP_API_TF_Ratings $ratings */
	private $ratings;

	/** @var WPML_TP_API_TF_Feedback $feedback */
	private $feedback;

	public function __construct(
		WPML_TP_Project $project,
		WPML_TP_TM_Jobs $tm_jobs
	) {
		$this->project = $project;
		$this->tm_jobs = $tm_jobs;
	}

	public function services() {
		if ( ! $this->services ) {
			$this->services = new WPML_TP_API_Services( $this );
		}

		return $this->services;
	}

	public function batches() {
		if ( ! $this->batches ) {
			$this->batches = new WPML_TP_API_Batches( $this );
		}

		return $this->batches;
	}

	/** @return WPML_TP_API_TF_Ratings */
	public function ratings() {
		if ( ! $this->ratings ) {
			$this->ratings = new WPML_TP_API_TF_Ratings( $this );
		}

		return $this->ratings;
	}

	/** @return WPML_TP_API_TF_Feedback */
	public function feedback() {
		if ( ! $this->feedback ) {
			$this->feedback = new WPML_TP_API_TF_Feedback( $this );
		}

		return $this->feedback;
	}

	/** @return WPML_TP_Project */
	public function get_project() {
		return $this->project;
	}

	public function get_tm_jobs() {
		return $this->tm_jobs;
	}
}
