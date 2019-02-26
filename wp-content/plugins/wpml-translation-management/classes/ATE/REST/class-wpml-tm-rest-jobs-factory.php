<?php

class WPML_TM_REST_Jobs_Factory extends WPML_REST_Factory_Loader {

	public function create() {
		return new WPML_TM_REST_Jobs();
	}
}