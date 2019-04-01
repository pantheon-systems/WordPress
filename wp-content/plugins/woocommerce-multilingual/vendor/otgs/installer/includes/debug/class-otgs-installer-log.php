<?php

class OTGS_Installer_Log {

	private $time;
	private $request_url;
	private $request_args;
	private $response;
	private $component;

	/**
	 * @return string
	 */
	public function get_time() {
		return $this->time;
	}

	/**
	 * @param string $time
	 *
	 * @return $this
	 */
	public function set_time( $time ) {
		$this->time = $time;
		return $this;
	}

	/**
	 * @return string
	 */
	public function get_request_url() {
		return $this->request_url;
	}

	/**
	 * @param string $request_url
	 *
	 * @return $this
	 */
	public function set_request_url( $request_url ) {
		$this->request_url = $request_url;
		return $this;
	}

	/**
	 * @return array
	 */
	public function get_request_args() {
		return $this->request_args;
	}

	/**
	 * @param array $request_args
	 *
	 * @return $this
	 */
	public function set_request_args( $request_args ) {
		$this->request_args = $request_args;
		return $this;
	}

	/**
	 * @return string
	 */
	public function get_response() {
		return $this->response;
	}

	/**
	 * @param string $response
	 *
	 * @return $this
	 */
	public function set_response( $response ) {
		$this->response = $response;
		return $this;
	}

	/**
	 * @return string
	 */
	public function get_component() {
		return $this->component;
	}

	/**
	 * @param string $component
	 *
	 * @return $this
	 */
	public function set_component( $component ) {
		$this->component = $component;
		return $this;
	}
}