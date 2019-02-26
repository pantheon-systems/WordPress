<?php

class WPML_ST_MO_Component_Stats_Update_Hooks {
	/** @var WPML_ST_Strings_Stats */
	private $string_stats;

	/**
	 * @param WPML_ST_Strings_Stats $string_stats
	 */
	public function __construct( WPML_ST_Strings_Stats $string_stats ) {
		$this->string_stats = $string_stats;
	}

	public function add_hooks() {
		add_action( 'wpml-st-mo-post-import', array( $this, 'update_stats' ), 10, 1 );
	}

	/**
	 * @param WPML_ST_MO_File $file
	 */
	public function update_stats( WPML_ST_MO_File $file ) {
		if ( $file->get_component_id() ) {
			$this->string_stats->update( $file->get_component_id(), $file->get_component_type(), $file->get_domain() );
		}
	}
}