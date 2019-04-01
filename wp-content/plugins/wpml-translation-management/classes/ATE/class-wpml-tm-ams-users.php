<?php

class WPML_TM_AMS_Users {

	/**@var WPML_Translation_Manager_Records */
	private $manager_records;

	/** @var WPML_Translator_Records */
	private $translator_records;

	/** @var WPML_Translator_Admin_Records $translator_admin_records */
	private $translator_admin_records;

	public function __construct(
		WPML_Translation_Manager_Records $manager_records,
		WPML_Translator_Records $translator_records,
		WPML_Translator_Admin_Records $translator_admin_records
	) {
		$this->manager_records    = $manager_records;
		$this->translator_records = $translator_records;
		$this->translator_admin_records = $translator_admin_records;
	}

	public function get_translators() {
		$translators = $this->translator_records->get_users_with_capability();
		$translators = array_merge( $translators, $this->get_admins_that_are_not_translators() );
		return $translators;
	}

	public function get_managers() {
		return $this->manager_records->get_users_with_capability();
	}

	private function get_admins_that_are_not_translators() {
		return $this->translator_admin_records->search_for_users_without_capability();
	}

}