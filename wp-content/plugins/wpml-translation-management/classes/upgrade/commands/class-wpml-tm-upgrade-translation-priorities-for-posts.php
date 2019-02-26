<?php

class WPML_TM_Upgrade_Translation_Priorities_For_Posts implements IWPML_Upgrade_Command {

	/** @var bool $result */
	private $result = true;

	const TRANSLATION_PRIORITY_TAXONOMY = 'translation_priority';

	/**
	 * Add the default terms for Translation Priority taxonomy
	 *
	 * @return bool
	 */
	private function run() {

		$translation_priorities_factory = new WPML_TM_Translation_Priorities_Factory();
		$translation_priorities_actions = $translation_priorities_factory->create();
		$translation_priorities_actions->register_translation_priority_taxonomy();

		WPML_TM_Translation_Priorities::insert_missing_default_terms();

		return $this->result;
	}


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
