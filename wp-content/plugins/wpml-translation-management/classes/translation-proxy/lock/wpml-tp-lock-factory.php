<?php

class WPML_TP_Lock_Factory {

	public function create() {
		return new WPML_TP_Lock( new WPML_WP_API() );
	}
}