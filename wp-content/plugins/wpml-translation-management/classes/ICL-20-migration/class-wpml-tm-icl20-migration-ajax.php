<?php

/**
 * @author OnTheGo Systems
 */
class WPML_TM_ICL20_Migration_AJAX extends WPML_TM_AJAX {
	/** @var WPML_TM_ICL20_Migration_Progress */
	private $progress;

	/**
	 * WPML_TM_ICL20_Migration_AJAX constructor.
	 *
	 * @param WPML_TM_ICL20_Migration_Progress $progress
	 */
	public function __construct( WPML_TM_ICL20_Migration_Progress $progress ) {
		$this->progress = $progress;
	}

	/**
	 * AJAX callback used to set the user confirmation for starting the migration
	 */
	public function user_confirmation() {
		if ( $this->is_valid_request() ) {
			$this->progress->set_user_confirmed();
			wp_send_json_success();
		}
	}
}