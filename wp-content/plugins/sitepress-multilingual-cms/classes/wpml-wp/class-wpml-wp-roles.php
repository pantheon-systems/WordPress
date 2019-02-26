<?php

class WPML_WP_Roles {

	const EDITOR_LEVEL = 'level_7';
	const CONTRIBUTOR_LEVEL = 'level_1';
	const SUBSCRIBER_LEVEL = 'level_0';

	public static function get_editor_roles() {
		return self::get_roles_for_level( self::EDITOR_LEVEL );
	}

	public static function get_contributor_roles() {
		return self::get_roles_for_level( self::CONTRIBUTOR_LEVEL );
	}

	public static function get_subscriber_roles() {
		return self::get_roles_for_level( self::SUBSCRIBER_LEVEL );
	}

	private static function get_roles_for_level( $level ) {
		$roles = array();

		$editable_roles = get_editable_roles();
		foreach ( $editable_roles as $id => $role ) {
			if ( isset( $role['capabilities'][ $level ] ) && $role['capabilities'][ $level ] ) {
				$roles[] = array( 'id' => $id, 'name' => $role['name'] );
			}
		}

		return $roles;
	}

}