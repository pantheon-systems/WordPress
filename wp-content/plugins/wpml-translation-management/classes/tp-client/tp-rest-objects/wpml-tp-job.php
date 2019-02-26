<?php

/**
 * @link https://git.onthegosystems.com/tp/translation-proxy/wikis/add_files_batch_job
 */
class WPML_TP_Job extends WPML_TP_REST_Object {

	const CANCELLED = 'cancelled';

	/** @var int */
	private $id;

	private $cms_id;

	private $batch;

	private $job_state;

	/** @param int $id */
	public function set_id( $id ) {
		$this->id = (int) $id;
	}

	/** @return int */
	public function get_id() {
		return $this->id;
	}

	/** @return string */
	public function get_cms_id() {
		return $this->cms_id;
	}

	/** @return string */
	public function get_job_state() {
		return $this->job_state;
	}

	/**
	 * @return int|null
	 */
	public function get_original_element_id() {
		preg_match_all( '/\d+/', $this->get_cms_id(), $matches );
		return isset( $matches[0][0] ) ? (int) $matches[0][0] : null;
	}

	/** @return stdClass */
	public function get_batch() {
		return $this->batch;
	}

	/**
	 * @param int $id
	 */
	public function set_cms_id( $id ) {
		$this->cms_id = $id;
	}

	/**
	 * @param string $state
	 */
	public function set_job_state( $state ) {
		$this->job_state = $state;
	}

	public function set_batch( stdClass $batch ) {
		$this->batch = $batch;
	}

	/** @return array */
	protected function get_properties() {
		return array(
			'id'        => 'id',
			'batch'     => 'batch',
			'cms_id'    => 'cms_id',
			'job_state' => 'job_state',
		);
	}
}
