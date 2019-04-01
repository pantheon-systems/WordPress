<?php

class WPML_WP_User_Query_Factory {

	public function create( $args ) {
		return new WP_User_Query( $args );
	}
}