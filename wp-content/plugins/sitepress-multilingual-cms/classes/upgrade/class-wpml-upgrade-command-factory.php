<?php

class WPML_Upgrade_Command_Factory {

	/**
	 * @param string $class_name
	 * @param array $args
	 *
	 * @return IWPML_Upgrade_Command
	 */
	public function create( $class_name, $args ) {
		return new $class_name( $args );
	}

	/**
	 * @param string $class_name
	 * @param array $dependencies
	 * @param array $scopes
	 * @param string|null $method
	 *
	 * @return WPML_Upgrade_Command_Definition
	 */
	public function create_command_definition( $class_name, array $dependencies, array $scopes, $method = null ) {
		return new WPML_Upgrade_Command_Definition( $class_name, $dependencies, $scopes, $method );
	}
}
