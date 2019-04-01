<?php

class WPML_TM_Word_Count_Single_Process_Factory {

	public function create() {
		/** @var wpdb $wpdb */
		global $wpdb;

		$setters_factory      = new WPML_TM_Word_Count_Setters_Factory();
		$dependencies_builder = null;

		if ( class_exists( 'WPML_ST_String_Dependencies_Builder' ) ) {
			$dependencies_builder = new WPML_ST_String_Dependencies_Builder(
				new WPML_ST_String_Dependencies_Records( $wpdb )
			);
		}

		return new WPML_TM_Word_Count_Single_Process( $setters_factory->create(), $dependencies_builder );
	}
}
