<?php

class WPML_End_User_Info_Aggregator_Repository implements WPML_End_User_Info_Repository {
	/** @var  WPML_End_User_Info_Repository[] */
	private $repositories;

	/**
	 * @param WPML_End_User_Info_Repository[] $repositories
	 */
	public function __construct( array $repositories ) {
		$this->repositories = $repositories;
	}


	/**
	 * @return array
	 */
	public function get_data() {
		$result = array();

		foreach ( $this->repositories as $repository ) {
			$result[ $repository->get_data_id() ] = $repository->get_data();
		}

		return $result;
	}

	/**
	 * @return string
	 */
	public function get_data_id() {
		return 'info';
	}
}
