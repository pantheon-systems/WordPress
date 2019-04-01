<?php

class WPML_Media_Add_To_Basket_Factory implements IWPML_Backend_Action_Loader {

	public function create() {
		global $sitepress;
		if ( isset( $_POST['icl_tm_action'] ) && 'add_jobs' === $_POST['icl_tm_action'] ) {
			return new WPML_Media_Add_To_Basket( $sitepress );
		}

		return null;
	}

}