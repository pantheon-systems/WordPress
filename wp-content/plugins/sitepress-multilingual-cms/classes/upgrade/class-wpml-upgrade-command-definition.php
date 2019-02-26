<?php

/**
 * @author OnTheGo Systems
 */
class WPML_Upgrade_Command_Definition {
	private $class_name;
	private $dependencies = array();
	/** @var array Can be 'admin', 'ajax' or 'front-end' */
	private $scopes = array();
	private $method;

	/**
	 * WPML_Upgrade_Command_Definition constructor.
	 *
	 * @param string      $class_name   A class implementing \IWPML_Upgrade_Command.
	 * @param array       $dependencies An array of dependencies passed to the `$class_name`'s constructor.
	 * @param array       $scopes       An array of scope values. Accepted values are: `\WPML_Upgrade::SCOPE_ADMIN`, `\WPML_Upgrade::SCOPE_AJAX`, and `\WPML_Upgrade::SCOPE_FRONT_END`.
	 * @param string|null $method       The method to call to run the upgrade (otherwise, it calls the "run" method),
	 */
	public function __construct( $class_name, array $dependencies, array $scopes, $method = null ) {
		$this->class_name   = $class_name;
		$this->dependencies = $dependencies;
		$this->scopes       = $scopes;
		$this->method       = $method;
	}

	/**
	 * @return array
	 */
	public function get_dependencies() {
		return $this->dependencies;
	}

	/**
	 * @return string
	 */
	public function get_class_name() {
		return $this->class_name;
	}

	/**
	 * @return string
	 */
	public function get_method() {
		return $this->method;
	}

	/**
	 * @return array
	 */
	public function get_scopes() {
		return $this->scopes;
	}
}
