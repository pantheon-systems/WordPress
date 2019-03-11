<?php

class OTGS_Installer_Instance {

	/**
	 * @var string
	 */
	private $bootfile;

	/**
	 * @var string
	 */
	private $version;

	/**
	 * @var string
	 */
	private $high_priority;

	/**
	 * @var bool
	 */
	private $delegated;

	/**
	 * @return string
	 */
	public function get_bootfile() {
		return $this->bootfile;
	}

	/**
	 * @return bool
	 */
	public function is_delegated() {
		return $this->delegated;
	}

	/**
	 * @return string
	 */
	public function get_high_priority() {
		return $this->high_priority;
	}

	/**
	 * @return string
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * @param string $bootfile
	 *
	 * @return $this
	 */
	public function set_bootfile( $bootfile ) {
		$this->bootfile = $bootfile;
		return $this;
	}

	/**
	 * @param string $high_priority
	 *
	 * @return $this
	 */
	public function set_high_priority( $high_priority ) {
		$this->high_priority = $high_priority;
		return $this;
	}

	/**
	 * @param string $version
	 *
	 * @return $this
	 */
	public function set_version( $version ) {
		$this->version = $version;
		return $this;
	}

	/**
	 * @param bool $delegated
	 *
	 * @return $this
	 */
	public function set_delegated( $delegated ) {
		$this->delegated = (bool) $delegated;
		return $this;
	}
}