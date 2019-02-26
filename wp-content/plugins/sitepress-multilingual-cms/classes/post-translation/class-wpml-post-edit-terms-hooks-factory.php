<?php

class WPML_Post_Edit_Terms_Hooks_Factory implements IWPML_Backend_Action_Loader {

	public function create() {
		global $sitepress, $wpdb;

		if ( $this->is_saving_post_data_with_terms() ) {
			return new WPML_Post_Edit_Terms_Hooks( $sitepress, $wpdb );
		}

		return null;
	}

	private function is_saving_post_data_with_terms() {
		return isset( $_POST['action'] )
			&& in_array( $_POST['action'], array( 'editpost', 'inline-save' ) )
			&& ! empty( $_POST['tax_input'] );
	}
}