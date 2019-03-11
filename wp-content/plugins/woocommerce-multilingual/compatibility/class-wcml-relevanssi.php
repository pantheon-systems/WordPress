<?php

class WCML_Relevanssi {

	public function add_hooks() {
		// Re-index translated product to add missing terms (wcml-2282)
		add_action( 'wcml_update_extra_fields', array( $this, 'index_product' ), 10, 4 );
	}

	public function index_product( $product_id, $tr_product_id, $translations, $target_language ) {
		$current_language = apply_filters( 'wpml_current_language', null );
		do_action( 'wpml_switch_language', $target_language );
		relevanssi_insert_edit( $tr_product_id );
		do_action( 'wpml_switch_language', $current_language );
	}

}