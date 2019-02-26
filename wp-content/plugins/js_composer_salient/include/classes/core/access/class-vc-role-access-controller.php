<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once vc_path_dir( 'CORE_DIR', 'access/abstract-class-vc-access.php' );

/**
 * Class Vc_Role_Access_Controller
 *
 * @since 4.8
 */
class Vc_Role_Access_Controller extends Vc_Access {
	protected static $part_name_prefix = 'vc_access_rules_';
	protected $part = false;
	protected $roleName = false;
	protected $role = false;
	protected $validAccess = true;
	protected $mergedCaps = array(
		'vc_row_inner_all' => 'vc_row_all',
		'vc_column_all' => 'vc_row_all',
		'vc_column_inner_all' => 'vc_row_all',
		'vc_row_inner_edit' => 'vc_row_edit',
		'vc_column_edit' => 'vc_row_edit',
		'vc_column_inner_edit' => 'vc_row_edit',
	);

	function __construct( $part ) {
		$this->part = $part;
	}

	/**
	 * Set role name.
	 *
	 * @param $role_name
	 */
	public function setRoleName( $role_name ) {
		$this->roleName = $role_name;
	}

	/**
	 * Get part for role.
	 * @return bool
	 */
	public function getPart() {
		return $this->part;
	}

	/**
	 * Get state of the Vc access rules part.
	 *
	 * @return mixed;
	 */
	public function getState() {
		$role = $this->getRole();
		$state = null;
		if ( $role && isset( $role->capabilities, $role->capabilities[ $this->getStateKey() ] ) ) {
			$state = $role->capabilities[ $this->getStateKey() ];
		}

		return apply_filters( 'vc_role_access_with_' . $this->getPart() . '_get_state', $state, $this->getRole() );
	}

	/**
	 * Set state for full part.
	 *
	 * State can have 3 values:
	 * true - all allowed under this part;
	 * false - all disabled under this part;
	 * string|'custom' - custom settings. It means that need to check exact capability.
	 *
	 * @param bool $value
	 *
	 * @return $this
	 */
	public function setState( $value = true ) {
		$this->getRole() && $this->getRole()->add_cap( $this->getStateKey(), $value );

		return $this;
	}

	/**
	 * Can user do what he doo.
	 * Any rule has three types of state: true, false, string.
	 *
	 * @param string $rule
	 * @param bool|true $check_state
	 *
	 * @return $this
	 */
	public function can( $rule = '', $check_state = true ) {
		if ( null === $this->getRole() ) {
			$this->setValidAccess( is_super_admin() );
		} elseif ( $this->getValidAccess() ) {
			// YES it is hard coded :)
			if ( 'administrator' === $this->getRole()->name && 'settings' === $this->getPart() && ( 'vc-roles-tab' === $rule || 'vc-updater-tab' === $rule ) ) {
				$this->setValidAccess( true );

				return $this;
			}
			$rule = $this->updateMergedCaps( $rule );

			if ( true === $check_state ) {
				$state = $this->getState();
				$return = false !== $state;
				if ( null === $state ) {
					$return = true;
				} elseif ( is_bool( $state ) ) {
					$return = $state;
				} elseif ( '' !== $rule ) {
					$return = $this->getCapRule( $rule );
				}
			} else {
				$return = $this->getCapRule( $rule );
			}
			$return = apply_filters( 'vc_role_access_with_' . $this->getPart() . '_can', $return, $this->getRole(), $rule );
			$return = apply_filters( 'vc_role_access_with_' . $this->getPart() . '_can_' . $rule, $return, $this->getRole() );
			$this->setValidAccess( $return );
		}

		return $this;
	}

	/**
	 * Can user do what he doo.
	 * Any rule has three types of state: true,false, string.
	 */
	public function canAny() {
		if ( $this->getValidAccess() ) {
			$args = func_get_args();
			$this->checkMulti( 'can', true, $args );
		}

		return $this;
	}

	/**
	 * Can user do what he doo.
	 * Any rule has three types of state: true,false, string.
	 */
	public function canAll() {
		if ( $this->getValidAccess() ) {
			$args = func_get_args();
			$this->checkMulti( 'can', false, $args );
		}

		return $this;
	}

	/**
	 * Get capability for role
	 *
	 * @param $rule
	 *
	 * @return bool
	 */
	public function getCapRule( $rule ) {
		$rule = $this->getStateKey() . '/' . $rule;

		return $this->getRole() ? $this->getRole()->has_cap( $rule ) : false;
	}

	/**
	 * Add capability to role.
	 *
	 * @param $rule
	 * @param bool $value
	 */
	public function setCapRule( $rule, $value = true ) {
		$role_rule = $this->getStateKey() . '/' . $rule;
		$this->getRole() && $this->getRole()->add_cap( $role_rule, $value );
	}

	/**
	 * Get all capability for this part.
	 */
	public function getAllCaps() {
		$role = $this->getRole();
		$caps = array();
		if ( $role ) {
			$role = apply_filters( 'vc_role_access_all_caps_role', $role );
			if ( isset( $role->capabilities ) && is_array( $role->capabilities ) ) {
				foreach ( $role->capabilities as $key => $value ) {
					if ( preg_match( '/^' . $this->getStateKey() . '\//', $key ) ) {
						$rule = preg_replace( '/^' . $this->getStateKey() . '\//', '', $key );
						$caps[ $rule ] = $value;
					}
				}
			}
		}

		return $caps;
	}

	/**
	 * @return null|\WP_Role
	 * @throws Exception
	 */
	public function getRole() {
		if ( ! $this->role ) {
			if ( ! $this->getRoleName() ) {
				throw new Exception( 'roleName for role_manager is not set, please use ->who(roleName) method to set!' );
			}
			$this->role = get_role( $this->getRoleName() );
		}

		return $this->role;
	}

	/**
	 * @return null|string
	 */
	public function getRoleName() {
		return $this->roleName;
	}

	public function getStateKey() {
		return self::$part_name_prefix . $this->getPart();
	}

	public function checkState( $data ) {
		if ( $this->getValidAccess() ) {
			$this->setValidAccess( $this->getState() === $data );
		}

		return $this;
	}

	public function checkStateAny() {
		if ( $this->getValidAccess() ) {
			$args = func_get_args();
			$this->checkMulti( 'checkState', true, $args );
		}

		return $this;
	}

	/**
	 * Return access value.
	 * @return string
	 */
	public function __toString() {
		return (string) $this->get();
	}

	public function updateMergedCaps( $rule ) {
		if ( isset( $this->mergedCaps[ $rule ] ) ) {
			return $this->mergedCaps[ $rule ];
		}

		return $rule;
	}

	/**
	 * @return array
	 */
	public function getMergedCaps() {
		return $this->mergedCaps;
	}
}
