<?php

/**
 * Class WPML_TF_Backend_Post_List_Hooks
 *
 * @author OnTheGoSystems
 */
class WPML_TF_Backend_Post_List_Hooks implements IWPML_Action {

	const RATING_COLUMN_ID = 'translation_rating';

	/** @var WPML_TF_Post_Rating_Metrics $post_rating_metrics*/
	private $post_rating_metrics;

	/** @var WPML_TF_Document_Information $document_information */
	private $document_information;

	/** @var WPML_TF_Backend_Styles $styles */
	private $styles;

	public function __construct(
		WPML_TF_Post_Rating_Metrics $post_rating_metrics,
		WPML_TF_Document_Information $document_information,
		WPML_TF_Backend_Styles $styles
	) {
		$this->post_rating_metrics  = $post_rating_metrics;
		$this->document_information = $document_information;
		$this->styles               = $styles;
	}

	/** @return array */
	private function get_post_types() {
		return array(
			'edit-post' => 'posts',
			'edit-page' => 'pages',
		);
	}

	public function add_hooks() {
		foreach ( $this->get_post_types() as $screen_id => $post_type ) {
			add_filter( 'manage_' . $post_type . '_columns' , array( $this, 'add_rating_column_header' ) );
			add_action( 'manage_' . $post_type . '_custom_column', array( $this, 'add_rating_column_content' ), 10, 2 );
			add_filter( 'manage_' . $screen_id .'_sortable_columns', array( $this, 'add_rating_sortable_column' ) );
		}

		add_action( 'pre_get_posts', array( $this, 'order_posts_by_rating' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts_action' ) );
	}

	/**
	 * @param array $columns
	 *
	 * @return array
	 */
	public function add_rating_column_header( array $columns ) {
		$key_to_insert_before = 'date';
		$column_name          = __( 'Translation rating', 'sitepress' );

		if ( array_key_exists( $key_to_insert_before, $columns ) ) {
			$insert_position   = array_search( $key_to_insert_before, array_keys( $columns ) );
			$columns_before    = array_slice( $columns, 0, $insert_position, true );
			$columns_to_insert = array( self::RATING_COLUMN_ID => $column_name );
			$columns_after     = array_slice( $columns, $insert_position, null, true );
			$columns           = array_merge( $columns_before, $columns_to_insert, $columns_after );
		} else {
			$columns[ self::RATING_COLUMN_ID ] = $column_name;
		}

		return $columns;
	}

	/**
	 * @param string $column_name
	 * @param int    $post_id
	 */
	public function add_rating_column_content( $column_name, $post_id ) {
		if ( self::RATING_COLUMN_ID !== $column_name ) {
			return;
		}

		$post_type = get_post_type( $post_id );
		$this->document_information->init( $post_id, 'post_' . $post_type );

		if ( $this->document_information->get_source_language() ) {
			echo $this->post_rating_metrics->get_display( $post_id );
		}
	}

	/**
	 * @param array $columns
	 *
	 * @return array
	 */
	public function add_rating_sortable_column( array $columns ) {
		$columns[ self::RATING_COLUMN_ID ] = self::RATING_COLUMN_ID;
		return $columns;
	}

	public function order_posts_by_rating( WP_Query $query ) {
		$orderby = $query->get( 'orderby');

		if( self::RATING_COLUMN_ID === $orderby ) {
			$query->set( 'meta_key', WPML_TF_Post_Rating_Metrics::AVERAGE_KEY );
			$query->set( 'orderby', 'meta_value_num' );
			$query->set( 'meta_type', 'NUMERIC' );
		}
	}

	public function admin_enqueue_scripts_action() {
		$current_screen = get_current_screen();

		if ( isset( $current_screen->id ) && array_key_exists( $current_screen->id, $this->get_post_types() ) ) {
			$this->styles->enqueue();
		}
	}
}
