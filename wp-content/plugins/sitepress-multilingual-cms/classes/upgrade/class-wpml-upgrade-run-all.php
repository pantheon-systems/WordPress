<?php

abstract class WPML_Upgrade_Run_All implements IWPML_Upgrade_Command {

	/** @var bool $result */
	protected $result = true;

	abstract protected function run();

	public function run_admin() {
		return $this->run();
	}

	public function run_ajax() {
		return $this->run();
	}

	public function run_frontend() {
		return $this->run();
	}

	/** @return bool */
	public function get_results() {
		return $this->result;
	}
}
