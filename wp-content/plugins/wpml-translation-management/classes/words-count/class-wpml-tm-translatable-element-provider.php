<?php

class WPML_TM_Translatable_Element_Provider {

	/** @var WPML_TM_Word_Count_Records $word_count_records */
	private $word_count_records;

	/** @var WPML_TM_Word_Count_Single_Process $single_process */
	private $single_process;

	/** @var null|WPML_ST_Package_Factory $st_package_factory */
	private $st_package_factory;

	public function __construct(
		WPML_TM_Word_Count_Records $word_count_records,
		WPML_TM_Word_Count_Single_Process $single_process,
		WPML_ST_Package_Factory $st_package_factory = null
	) {
		$this->word_count_records = $word_count_records;
		$this->single_process     = $single_process;
		$this->st_package_factory = $st_package_factory;
	}

	/**
	 * @param WPML_Translation_Job $job
	 *
	 * @return null|WPML_TM_Package_Element|WPML_TM_Post|WPML_TM_String
	 */
	public function get_from_job( WPML_Translation_Job $job ) {
		$id = $job->get_original_element_id();

		if ( $job instanceof WPML_Post_Translation_Job ) {
			return new WPML_TM_Post( $id, $this->word_count_records, $this->single_process );
		}

		if ( $job instanceof WPML_String_Translation_Job ) {
			return new WPML_TM_String( $id, $this->word_count_records, $this->single_process );
		}

		if ( $job instanceof WPML_External_Translation_Job ) {
			return new WPML_TM_Package_Element( $id, $this->word_count_records, $this->single_process, $this->st_package_factory );
		}

		return null;
	}

	/**
	 * @param string $type
	 * @param int    $id
	 *
	 * @return null|WPML_TM_Package_Element|WPML_TM_Post|WPML_TM_String
	 */
	public function get_from_type( $type, $id ) {
		switch ( $type ) {
			case 'post':
				return new WPML_TM_Post( $id, $this->word_count_records, $this->single_process );

			case 'string':
				return new WPML_TM_String( $id, $this->word_count_records, $this->single_process );

			case 'package':
				return new WPML_TM_Package_Element( $id, $this->word_count_records, $this->single_process, $this->st_package_factory );
		}

		return null;
	}
}
