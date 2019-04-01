<?php

class WPML_TM_Package_Element extends WPML_TM_Translatable_Element {

	/** @var WPML_ST_Package_Factory $st_package_factory */
	private $st_package_factory;

	/** @var WPML_Package $st_package */
	private $st_package;

	/**
	 * @param int                               $id
	 * @param WPML_TM_Word_Count_Records        $word_count_records
	 * @param WPML_TM_Word_Count_Single_Process $single_process
	 * @param WPML_ST_Package_Factory|null      $st_package_factory
	 */
	public function __construct(
		$id,
		WPML_TM_Word_Count_Records $word_count_records,
		WPML_TM_Word_Count_Single_Process $single_process,
		WPML_ST_Package_Factory $st_package_factory = null
	) {
		$this->st_package_factory = $st_package_factory;
		parent::__construct( $id, $word_count_records, $single_process );

	}

	/** @param int $id */
	protected function init( $id ) {
		if ( $this->st_package_factory ) {
			$this->st_package = $this->st_package_factory->create( $id );
		}
	}

	protected function get_type() {
		return 'package';
	}

	/** @return int */
	protected function get_total_words() {
		return $this->word_count_records->get_package_word_count( $this->id )->get_total_words();
	}

	/**
	 * @param null $label
	 *
	 * @return string
	 */
	public function get_type_name( $label = null ) {
		if ( $this->st_package ) {
			return $this->st_package->kind;
		}

		return __( 'Unknown string Package', 'wpml-translation-management' );
	}
}
