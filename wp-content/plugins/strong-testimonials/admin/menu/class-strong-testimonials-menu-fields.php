<?php
/**
 * Class Strong_Testimonials_Menu_Fields
 */
class Strong_Testimonials_Menu_Fields {

	/**
	 * Strong_Testimonials_Menu_Fields constructor.
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
		add_filter( 'wpmtst_submenu_pages', array( __CLASS__, 'add_submenu' ) );
	}

	/**
	 * Add submenu page.
	 *
	 * @param $pages
	 *
	 * @return mixed
	 */
	public static function add_submenu( $pages ) {
		$pages[20] = self::get_submenu();
		return $pages;
	}

	/**
	 * Return submenu page parameters.
	 *
	 * @return array
	 */
	public static function get_submenu() {
		return array(
			'page_title' => apply_filters( 'wpmtst_fields_page_title', __( 'Fields', 'strong-testimonials' ) ),
	        'menu_title' => apply_filters( 'wpmtst_fields_menu_title', __( 'Fields', 'strong-testimonials' ) ),
		    'capability' => 'strong_testimonials_fields',
			'menu_slug'  => 'testimonial-fields',
			'function'   => 'wpmtst_form_admin',
		);
	}

}

Strong_Testimonials_Menu_Fields::init();
