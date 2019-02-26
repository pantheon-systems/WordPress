<?php

/**
 * @author OnTheGo Systems
 */
class WPML_TM_ATE_Models_Job_Create {
	/** @var int */
	public $deadline;
	/** @var WPML_TM_ATE_Models_Job_File */
	public $file;
	/** @var bool */
	public $notify_enabled;
	/** @var string */
	public $notify_url;
	/** @var int */
	public $source_id;
	/** @var string */
	public $permalink;
	/** @var string */
	public $site_identifier;
	/** @var WPML_TM_ATE_Models_Language */
	public $source_language;
	/** @var WPML_TM_ATE_Models_Language */
	public $target_language;

	/**
	 * WPML_TM_ATE_Models_Job_Create constructor.
	 *
	 * @param array $args
	 */
	public function __construct( array $args = array() ) {
		foreach ( $args as $key => $value ) {
			$this->$key = $value;
		}
		if ( ! $this->file ) {
			$this->file = new WPML_TM_ATE_Models_Job_File();
		}
		if ( ! $this->source_language ) {
			$this->source_language = new WPML_TM_ATE_Models_Language();
		}
		if ( ! $this->target_language ) {
			$this->target_language = new WPML_TM_ATE_Models_Language();
		}
	}
}