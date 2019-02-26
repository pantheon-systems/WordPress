<?php

/**
 * @author OnTheGo Systems
 */
class WPML_TM_ICL20_Migration_Container {
	private $acknowledge;
	private $project;
	private $token;

	public function __construct(
		WPML_TM_ICL20_Token $token,
		WPML_TM_ICL20_Project $project,
		WPML_TM_ICL20_Acknowledge $ack
	) {
		$this->token       = $token;
		$this->project     = $project;
		$this->acknowledge = $ack;
	}

	/**
	 * @return WPML_TM_ICL20_Acknowledge
	 */
	public function get_acknowledge() {
		return $this->acknowledge;
	}

	/**
	 * @return WPML_TM_ICL20_Project
	 */
	public function get_project() {
		return $this->project;
	}

	/**
	 * @return WPML_TM_ICL20_Token
	 */
	public function get_token() {
		return $this->token;
	}
}