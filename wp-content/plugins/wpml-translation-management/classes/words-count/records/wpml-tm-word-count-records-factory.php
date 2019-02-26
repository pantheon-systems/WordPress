<?php

class WPML_TM_Word_Count_Records_Factory {

	public function create() {
		global $wpdb;

		return new WPML_TM_Word_Count_Records(
			new WPML_TM_Word_Count_Post_Records( $wpdb ),
			class_exists( 'WPML_ST_Word_Count_Package_Records' ) ? new WPML_ST_Word_Count_Package_Records( $wpdb ) : null,
			class_exists( 'WPML_ST_Word_Count_String_Records' ) ? new WPML_ST_Word_Count_String_Records( $wpdb ) : null
		);
	}
}