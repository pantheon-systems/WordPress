<?php

class WCML_Etheme_Blanco {

	public function add_hooks() {
		add_filter( 'wcml_calculate_totals_exception', array(
			$this,
			'calculate_totals_on_et_refreshed_fragments'
		), 9 );
	}

	public function calculate_totals_on_et_refreshed_fragments( $calculate ) {
		if ( isset( $_POST['action'] ) && 'et_refreshed_fragments' === $_POST['action'] ) {
			return false;
		}

		return $calculate;
	}

}
