<?php

class OTGS_Installer_PHP_Functions {

	/**
	 * @param string $constant_name
	 *
	 * @return bool
	 */
	public function defined( $constant_name ) {
		return defined( $constant_name );
	}

	/**
	 * @param string $constant_name
	 *
	 * @return string|int|null
	 */
	public function constant( $constant_name ) {
		return $this->defined( $constant_name ) ? constant( $constant_name ) : null;
	}

	/**
	 * @return int
	 */
	public function time() {
		return time();
	}

	public function phpversion() {
		return phpversion();
	}
}