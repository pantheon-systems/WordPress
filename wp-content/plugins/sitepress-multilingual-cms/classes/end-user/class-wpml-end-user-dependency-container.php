<?php

class WPML_End_User_Dependency_Container {
	/** @var  WPML_End_User_Info_Aggregator_Repository */
	private $info_repository;

	/** @var WPML_End_User_Info_Model */
	private $info_model;

	/**
	 * @return WPML_End_User_Info_Aggregator_Repository
	 */
	public function get_info_repository() {
		if ( null === $this->info_repository ) {
			$this->info_repository = new WPML_End_User_Info_Aggregator_Repository( array(
				new WPML_End_User_Info_Site_Repository(),
				new WPML_End_User_Info_Theme_Repository(),
				new WPML_End_User_Info_WP_User_Repository(),
				new WPML_End_User_Info_Plugins_Repository( new WPML_Active_Plugin_Provider() ),
			) );
		}

		return $this->info_repository;
	}

	/**
	 * @return WPML_End_User_Info_Model
	 */
	public function get_info_model() {
		if ( null === $this->info_model ) {
			$this->info_model = new WPML_End_User_Info_Model();
		}

		return $this->info_model;
	}
}
