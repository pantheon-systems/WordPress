<?php
/**
 * Class Strong_Testimonials_Menu
 */
class Strong_Testimonials_Menu {

	/**
	 * The common parent slug.
	 */
    const PARENT_SLUG = 'edit.php?post_type=wpm-testimonial';

	/**
	 * Strong_Testimonials_Menu constructor.
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
	    add_action( 'admin_menu', array( __CLASS__, 'settings_menu' ) );
    }

	/**
	 * Add submenu pages.
	 */
    public static function settings_menu() {
        $submenu_pages = apply_filters( 'wpmtst_submenu_pages', array() );
        ksort( $submenu_pages );
        foreach ( $submenu_pages as $key => $submenu ) {
	        add_submenu_page(
                self::PARENT_SLUG,
		        $submenu['page_title'],
		        $submenu['menu_title'],
		        $submenu['capability'],
		        $submenu['menu_slug'],
		        $submenu['function']
            );
        }
    }

}

Strong_Testimonials_Menu::init();
