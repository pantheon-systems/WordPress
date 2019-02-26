<?php

class WPML_ST_String_Status_Update {
	/** @var int */
	private $number_of_secondary_languages;

	/** @var wpdb */
	private $wpdb;

	/**
	 * @param int $number_of_secondary_languages
	 * @param wpdb $wpdb
	 */
	public function __construct( $number_of_secondary_languages, wpdb $wpdb ) {
		$this->number_of_secondary_languages = $number_of_secondary_languages;
		$this->wpdb                          = $wpdb;
	}

	public function add_hooks() {
		add_action( 'wpml-st-mo-post-import', array( $this, 'update_string_statuses' ), 10, 1 );
	}

	public function update_string_statuses( WPML_ST_MO_File $file ) {
		if ( ! in_array( $file->get_status(), array( WPML_ST_MO_File::IMPORTED, WPML_ST_MO_File::FINISHED ), true ) ) {
			return;
		}

		$sql = "
			UPDATE {$this->wpdb->prefix}icl_strings s
			SET s.status = CASE (
			   SELECT COUNT(t.id) FROM {$this->wpdb->prefix}icl_string_translations t 
			   WHERE t.string_id = s.id AND (t.status = %d OR t.mo_string IS NOT NULL)  
			  )
			  WHEN %d THEN %d
			  WHEN 0 THEN %d 
			  ELSE %d 
			  END
				  
			WHERE s.context = %s
		";

		$sql = $this->wpdb->prepare(
			$sql,
			ICL_TM_COMPLETE,
			$this->number_of_secondary_languages,
			ICL_TM_COMPLETE,
			ICL_TM_NOT_TRANSLATED,
			ICL_TM_IN_PROGRESS,
			$file->get_domain()
		);

		$this->wpdb->query( $sql );
	}
}