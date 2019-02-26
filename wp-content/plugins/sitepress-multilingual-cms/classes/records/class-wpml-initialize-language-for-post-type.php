<?php

class WPML_Initialize_Language_For_Post_Type {

	private $wpdb;

	public function __construct( wpdb $wpdb ) {
		$this->wpdb = $wpdb;
	}

	public function run( $post_type, $default_language ) {

		do {
			$trid_max = $this->wpdb->get_var( "SELECT MAX(trid) FROM {$this->wpdb->prefix}icl_translations" ) + 1;
			$sql          = "INSERT IGNORE INTO {$this->wpdb->prefix}icl_translations (`element_type`, `element_id`, `trid`, `language_code`)" . PHP_EOL;
			$sql          .= "SELECT CONCAT('post_' , p.post_type) as element_type, p.ID as element_id, %d + p.ID as trid, %s as language_code" . PHP_EOL;
			$sql          .= "FROM {$this->wpdb->posts} p" . PHP_EOL;
			$sql          .= "LEFT OUTER JOIN {$this->wpdb->prefix}icl_translations t" . PHP_EOL;
			$sql          .= "ON t.element_id = p.ID AND t.element_type = CONCAT('post_', p.post_type)" . PHP_EOL;
			$sql          .= "WHERE p.post_type = %s AND t.translation_id IS NULL" . PHP_EOL;
			$sql          .= "LIMIT 500";
			$sql_prepared = $this->wpdb->prepare( $sql, array( $trid_max, $default_language, $post_type ) );
			$results      = $this->wpdb->query( $sql_prepared );
		} while ( $results && ! $this->wpdb->last_error );

		return $results === 0 && ! $this->wpdb->last_error;
	}
}
