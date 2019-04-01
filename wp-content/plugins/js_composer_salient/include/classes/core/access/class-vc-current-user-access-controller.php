<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once vc_path_dir( 'CORE_DIR', 'access/class-vc-role-access-controller.php' );

class Vc_Current_User_Access_Controller extends Vc_Role_Access_Controller {
	/**
	 * Get capability for current user
	 *
	 * @param $rule
	 *
	 * @return bool
	 */
	public function getCapRule( $rule ) {
		$role_rule = $this->getStateKey() . '/' . $rule;

		return current_user_can( $role_rule );
	}

	/**
	 * Add capability to role.
	 *
	 * @param $rule
	 * @param bool $value
	 */
	public function setCapRule( $rule, $value = true ) {
		$role_rule = $this->getStateKey() . '/' . $rule;

		wp_get_current_user()->add_cap( $role_rule, $value );
	}

	public function getRole() {
		if ( ! $this->roleName && function_exists( 'wp_get_current_user' ) ) {
			$user = wp_get_current_user();
			$user_roles = array_intersect( array_values( (array) $user->roles ), array_keys( (array) get_editable_roles() ) );
			$this->roleName = reset( $user_roles );
			$this->role = get_role( $this->roleName );
		}

		return $this->role;
	}
}
