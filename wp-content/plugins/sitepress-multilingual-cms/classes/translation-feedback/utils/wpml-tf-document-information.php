<?php

/**
 * Class WPML_TF_Document_Information
 *
 * @author OnTheGoSystems
 */
class WPML_TF_Document_Information {

	/** @var  SitePress $sitepress */
	protected $sitepress;

	/** @var  int $id */
	protected $id;

	/** @var  string $type */
	protected $type;

	/** @var  stdClass $language_details */
	protected $language_details;

	/** @var null|int|stdClass */
	private $translation_job;

	/**
	 * WPML_TF_Document_Information constructor.
	 *
	 * @param SitePress             $sitepress
	 */
	public function __construct( SitePress $sitepress ) {
		$this->sitepress = $sitepress;
	}

	/**
	 * @param int    $id
	 * @param string $type
	 */
	public function init( $id, $type ) {
		$this->id               = $id;
		$this->type             = $type;
		$this->language_details = $this->sitepress->get_element_language_details( $this->id, $this->type );
	}

	/**
	 * @return string|null
	 */
	public function get_source_language() {
		return isset( $this->language_details->source_language_code )
			? $this->language_details->source_language_code
			: null;
	}

	/**
	 * @return string
	 */
	public function get_language() {
		return isset( $this->language_details->language_code )
			? $this->language_details->language_code
			: null;
	}

	/**
	 * @return null|int
	 */
	public function get_job_id() {
		$args = array(
			'trid'          => $this->get_trid(),
			'language_code' => $this->get_language(),
		);

		$job_id = apply_filters( 'wpml_translation_job_id', false, $args );

		return $job_id ? $job_id : null;
	}

	/**
	 * @return null|int
	 */
	protected function get_trid() {
		$trid = null;

		if ( isset( $this->language_details->trid ) ) {
			$trid = $this->language_details->trid;
		}

		return $trid;
	}

	/**
	 * @param int $job_id
	 *
	 * @return bool
	 */
	public function is_local_translation( $job_id ) {
		$is_local        = true;
		$translation_job = $this->get_translation_job( $job_id );

		if ( isset( $translation_job->translation_service ) && 'local' !== $translation_job->translation_service ) {
			$is_local = false;
		}

		return $is_local;
	}

	/**
	 * @param int $job_id
	 *
	 * @return int|stdClass
	 */
	protected function get_translation_job( $job_id ) {
		if ( ! $this->translation_job ) {
			$this->translation_job = apply_filters( 'wpml_get_translation_job', $job_id );
		}

		return $this->translation_job;
	}
}
