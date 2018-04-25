<?php
/**
 * Class Strong_Testimonials_Menu_Settings
 */
class Strong_Testimonials_Menu_Settings {

    public static $callbacks;

	/**
	 * Strong_Testimonials_Menu_Settings constructor.
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
		$pages[30] = self::get_submenu();
		return $pages;
	}

	/**
     * Return submenu page parameters.
     *
	 * @return array
	 */
	public static function get_submenu() {
		return array(
			'page_title' => __( 'Settings', 'strong-testimonials' ),
	        'menu_title' => __( 'Settings', 'strong-testimonials' ),
		    'capability' => 'strong_testimonials_options',
			'menu_slug'  => 'testimonial-settings',
			'function'   => array( 'Strong_Testimonials_Settings', 'settings_page' ),
		);
	}

}

Strong_Testimonials_Menu_Settings::init();
