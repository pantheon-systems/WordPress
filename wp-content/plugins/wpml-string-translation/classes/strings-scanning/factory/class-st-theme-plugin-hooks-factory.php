<?php

class WPML_ST_Theme_Plugin_Hooks_Factory implements IWPML_Backend_Action_Loader {

	/**
	 * @return WPML_ST_Theme_Plugin_Hooks
	 */
	public function create() {
		return new WPML_ST_Theme_Plugin_Hooks( new WPML_ST_File_Hashing() );
	}
}