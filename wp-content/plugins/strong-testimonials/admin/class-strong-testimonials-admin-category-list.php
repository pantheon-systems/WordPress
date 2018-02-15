<?php
/**
 * Class Strong_Testimonials_Admin_Category_List
 *
 * @since 2.28.0
 */
class Strong_Testimonials_Admin_Category_List {

	/**
	 * Strong_Testimonials_Admin_list constructor.
	 */
	public function __construct() {}

	/**
	 * Initialize.
	 */
	public static function init() {
		self::add_actions();
	}

	/**
	 * Add actions and filters.
	 */
	public static function add_actions() {
		add_filter( 'manage_edit-wpm-testimonial-category_columns', array( __CLASS__, 'manage_categories' ) );
		add_filter( 'manage_wpm-testimonial-category_custom_column', array( __CLASS__, 'manage_columns' ), 10, 3 );
	}

	/**
	 * Add columns to the testimonials categories screen
	 *
	 * @param $columns
	 *
	 * @return array
	 */
	public static function manage_categories( $columns ) {
		$new_columns = array(
			'cb'    => '<input type="checkbox">',
			'ID'    => __( 'ID', 'strong-testimonials' ),
			'name'  => __( 'Name', 'strong-testimonials' ),
			'slug'  => __( 'Slug', 'strong-testimonials' ),
			'posts' => __( 'Posts', 'strong-testimonials' ),
		);

		return $new_columns;
	}

	/**
	 * Show custom column
	 *
	 * @param $out
	 * @param $column_name
	 * @param $id
	 *
	 * @return string
	 */
	public static function manage_columns( $out, $column_name, $id ) {
		if ( 'ID' == $column_name ) {
			$output = $id;
		} else {
			$output = '';
		}

		return $output;
	}

}

Strong_Testimonials_Admin_Category_List::init();
