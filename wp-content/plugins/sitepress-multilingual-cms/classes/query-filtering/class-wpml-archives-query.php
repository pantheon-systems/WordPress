<?php
/**
 * Created by PhpStorm.
 * User: bruce
 * Date: 26/10/17
 * Time: 5:22 PM
 */

class WPML_Archives_Query implements IWPML_Action {

	/** @var wpdb $wpdb */
	private $wpdb;
	/** @var WPML_Language_Where_Clause $language_where_clause */
	private $language_where_clause;
	/** @var string $post_type */
	private $post_type = 'post';

	public function __construct( wpdb $wpdb, WPML_Language_Where_Clause $language_where_clause ) {
		$this->wpdb                  = $wpdb;
		$this->language_where_clause = $language_where_clause;
	}

	public function add_hooks() {
		add_filter( 'getarchives_join', array( $this, 'get_archives_join' ), 10, 2 );
		add_filter( 'getarchives_where', array( $this, 'get_archives_where' ), 10, 1 );
	}

	/**
	 * @param string $join
	 * @param array $args
	 *
	 * @return string
	 */
	public function get_archives_join( $join, $args ) {

		$this->post_type = array_key_exists( 'post_type', $args ) ? $args['post_type'] : 'post';
		$this->post_type = esc_sql( $this->post_type );

		return $join . " JOIN {$this->wpdb->prefix}icl_translations wpml_translations ON wpml_translations.element_id = {$this->wpdb->posts}.ID AND wpml_translations.element_type='post_" . $this->post_type . "'";
	}

	/**
	 * @param string $where_clause
	 *
	 * @return string
	 */
	public function get_archives_where( $where_clause ) {
		return $where_clause . $this->language_where_clause->get( $this->post_type );
	}

}