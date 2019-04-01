<?php

class WPML_Upgrade_Element_Type_Length_And_Collation implements IWPML_Upgrade_Command {

	/** @var bool $result */
	private $result = true;

	/** @var WPML_Upgrade_Schema */
	private $upgrade_schema;

	public function __construct( array $args ) {
		$this->upgrade_schema = $args[0];
	}

	/** @return bool */
	private function run() {
		$table  = 'icl_translations';
		$column = 'element_type';
		$column_attr = "VARCHAR(60) NOT NULL DEFAULT 'post_post'";

		if ( $this->upgrade_schema->does_table_exist( $table ) ) {
			$column_attr = $this->add_collation_from_post_type( $column_attr );
			$this->upgrade_schema->modify_column( $table, $column, $column_attr );
		}

		return $this->result;
	}

	private function add_collation_from_post_type( $element_type_attr ) {
		$collation = $this->upgrade_schema->get_column_collation( 'posts', 'post_type' );

		if ( $collation ) {
			$element_type_attr .= ' COLLATE ' . $collation;
		}

		return $element_type_attr;
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
