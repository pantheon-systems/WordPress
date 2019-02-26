<?php
class ameRoleUtils {
	/**
	  * Retrieve a list of all known, non-meta capabilities of all roles.
	  *
	  * @return array Associative array with capability names as keys
	  */
	public static function get_all_capabilities(){
		//Cache the results.
		static $capabilities = null;
		if ( isset($capabilities) ) {
			return $capabilities;
		}

		$wp_roles = self::get_roles();
		$capabilities = array();

		//Iterate over all known roles and collect their capabilities
		foreach($wp_roles->roles as $role){
			if ( !empty($role['capabilities']) && is_array($role['capabilities']) ){ //Being defensive here
				$capabilities = array_merge($capabilities, $role['capabilities']);
			}
		}

		//Add multisite-specific capabilities (not listed in any roles in WP 3.0)
		$multisite_caps = array(
			'manage_sites' => 1,
			'manage_network' => 1,
			'manage_network_users' => 1,
			'manage_network_themes' => 1,
			'manage_network_options' => 1,
			'manage_network_plugins' => 1,
		);
		$capabilities = array_merge($capabilities, $multisite_caps);

		return $capabilities;
	}

	 /**
	  * Retrieve a list of all known roles and their names.
	  *
	  * @return array Associative array with role IDs as keys and role display names as values
	  */
	public static function get_role_names(){
		$wp_roles = self::get_roles();
		$roles = array();

		foreach($wp_roles->roles as $role_id => $role){
			$roles[$role_id] = $role['name'];
		}

		return $roles;
	}

	/**
	 * Get the global WP_Roles instance.
	 *
	 * @global WP_Roles $wp_roles
	 * @return WP_Roles
	 */
	public static function get_roles() {
		global $wp_roles;
		if ( !isset($wp_roles) ) {
			$wp_roles = new WP_Roles();
		}
		return $wp_roles;
	}
}