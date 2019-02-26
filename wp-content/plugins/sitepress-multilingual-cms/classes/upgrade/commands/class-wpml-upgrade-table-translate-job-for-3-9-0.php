<?php

class WPML_Upgrade_Table_Translate_Job_For_3_9_0 implements IWPML_Upgrade_Command {

	/** @var bool $result */
	private $result = true;

	/** @var WPML_Upgrade_Schema */
	private $upgrade_schema;

	public function __construct( array $args ) {
		$this->upgrade_schema = $args[0];
	}

	/** @return bool */
	private function run() {
		$table   = 'icl_translate_job';
		$columns = array(
			'title'          => 'VARCHAR(160) NULL',
			'deadline_date'  => 'DATETIME NULL',
			'completed_date' => 'DATETIME NULL'
		);

		if ( $this->upgrade_schema->does_table_exist( $table ) ) {
			foreach ( $columns as $column => $attribute_string ) {
				if ( ! $this->upgrade_schema->does_column_exist( $table, $column ) ) {
					$this->upgrade_schema->add_column( $table, $column, $attribute_string );
				}
			}
		}

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
