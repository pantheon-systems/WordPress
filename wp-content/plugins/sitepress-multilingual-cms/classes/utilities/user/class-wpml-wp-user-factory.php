<?php

class WPML_WP_User_Factory {

	public function create( $user_id ) {
		return new WPML_User( $user_id );
	}

	public function create_by_email( $user_email ) {
		$user = get_user_by( 'email', $user_email );
		return new WPML_User( $user->ID );
	}

	public function create_current() {
		return new WPML_User( get_current_user_id() );
	}
}