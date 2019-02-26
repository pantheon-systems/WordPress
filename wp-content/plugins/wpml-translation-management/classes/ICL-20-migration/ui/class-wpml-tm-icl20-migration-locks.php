<?php

/**
 * @author OnTheGo Systems
 */
class WPML_TM_ICL20_Migration_Locks {
	private $progress;

	public function __construct( WPML_TM_ICL20_Migration_Progress $progress ) {
		$this->progress = $progress;
	}

	public function add_hooks() {
		if ( ! $this->progress->is_migration_done() ) {
			add_filter( 'wpml_tm_lock_ui', '__return_true' );
		}
	}
}