<?php

class WPML_Media_Usage_Factory {

	public function create( $attachment_id ) {
		return new WPML_Media_Usage( $attachment_id );
	}

}