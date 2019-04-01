<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once vc_path_dir( 'CORE_DIR', 'access/abstract-class-vc-access.php' );

/**
 * Class Vc_Role_Access
 */
class Vc_Role_Access extends Vc_Access {
	/**
	 * @var bool
	 */
	protected $roleName = false;
	/**
	 * @var array
	 */
	protected $parts = array();

	/**
	 *
	 */
	public function __construct() {
		require_once( ABSPATH . 'wp-admin/includes/user.php' );
	}

	/**
	 * @param $part
	 * @return \Vc_Role_Access_Controller
	 * @throws \Exception
	 */
	public function part( $part ) {
		$role_name = $this->getRoleName();
		if ( ! $role_name ) {
			throw new Exception( 'roleName for vc_role_access is not set, please use ->who(roleName) method to set!' );
		}
		$key = $part . '_' . $role_name;
		if ( ! isset( $this->parts[ $key ] ) ) {
			require_once vc_path_dir( 'CORE_DIR', 'access/class-vc-role-access-controller.php' );
			/** @var $role_access_controller Vc_Role_Access_Controller */
			$role_access_controller = $this->parts[ $key ] = new Vc_Role_Access_Controller( $part );
			$role_access_controller->setRoleName( $this->getRoleName() );
		}
		/** @var $role_access_controller Vc_Role_Access_Controller */
		$role_access_controller = $this->parts[ $key ];
		$role_access_controller->setValidAccess( $this->getValidAccess() ); // send current status to upper level
		$this->setValidAccess( true ); // reset

		return $role_access_controller;
	}

	/**
	 * Set role to get access to data.
	 *
	 * @param $roleName
	 * @return $this
	 * @internal param $role
	 *
	 */
	public function who( $roleName ) {
		$this->roleName = $roleName;

		return $this;
	}

	/**
	 * @return null|string
	 */
	public function getRoleName() {
		return $this->roleName;
	}
}
