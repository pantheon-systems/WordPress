<?php
/**
 * Class Strong_Testimonials_Menu_Views
 */
class Strong_Testimonials_Menu_Views {

	/**
	 * Strong_Testimonials_Menu_Views constructor.
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
		$pages[10] = self::get_submenu();
		return $pages;
	}

	/**
	 * Return submenu page parameters.
	 *
	 * @return array
	 */
	public static function get_submenu() {
		return array(
			'page_title' => __( 'Views', 'strong-testimonials' ),
	        'menu_title' => __( 'Views', 'strong-testimonials' ),
		    'capability' => 'strong_testimonials_views',
			'menu_slug'  => 'testimonial-views',
			'function'   => 'wpmtst_views_admin',
		);
	}

}

Strong_Testimonials_Menu_Views::init();
