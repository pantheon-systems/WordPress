<?php
/**
 * Class Strong_Testimonials_Settings_General
 */
class Strong_Testimonials_Settings_General {

	const TAB_NAME = 'general';

	const OPTION_NAME = 'wpmtst_options';

	const GROUP_NAME = 'wpmtst-settings-group';

	/**
	 * Strong_Testimonials_Settings_General constructor.
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
	    add_action( 'wpmtst_register_settings', array( __CLASS__, 'register_settings' ) );
	    add_action( 'wpmtst_settings_tabs', array( __CLASS__, 'register_tab' ), 1, 2 );
	    add_filter( 'wpmtst_settings_callbacks', array( __CLASS__, 'register_settings_page' ) );
	}

	/**
	 * Register settings tab.
	 *
	 * @param $active_tab
	 * @param $url
	 */
	public static function register_tab( $active_tab, $url ) {
		printf( '<a href="%s" class="nav-tab %s">%s</a>',
			esc_url( add_query_arg( 'tab', self::TAB_NAME, $url ) ),
			esc_attr( $active_tab == self::TAB_NAME ? 'nav-tab-active' : '' ),
			_x( 'General', 'adjective', 'strong-testimonials' )
		);
	}

	/**
	 * Register settings.
	 */
	public static function register_settings() {
		register_setting( self::GROUP_NAME, self::OPTION_NAME, array( __CLASS__, 'sanitize_options' ) );
	}

	/**
     * Register settings page.
     *
	 * @param $pages
	 *
	 * @return mixed
	 */
	public static function register_settings_page( $pages ) {
	    $pages[ self::TAB_NAME ] = array( __CLASS__, 'settings_page' );
	    return $pages;
	}

	/**
	 * Print settings page.
	 */
	public static function settings_page() {
		settings_fields( self::GROUP_NAME );
		include( WPMTST_ADMIN . 'settings/partials/general.php' );
	}

	/**
	 * Sanitize settings.
	 *
	 * @param $input
	 *
	 * @return array
	 */
	public static function sanitize_options( $input ) {
		$input['embed_width']           = $input['embed_width'] ? (int) sanitize_text_field( $input['embed_width'] ) : '';
		$input['load_font_awesome']     = wpmtst_sanitize_checkbox( $input, 'load_font_awesome' );
		$input['nofollow']              = wpmtst_sanitize_checkbox( $input, 'nofollow' );
		$input['pending_indicator']     = wpmtst_sanitize_checkbox( $input, 'pending_indicator' );
		$input['remove_whitespace']     = wpmtst_sanitize_checkbox( $input, 'remove_whitespace' );
		$input['reorder']               = wpmtst_sanitize_checkbox( $input, 'reorder' );
		$input['scrolltop']             = wpmtst_sanitize_checkbox( $input, 'scrolltop' );
		$input['scrolltop_offset']      = (int) sanitize_text_field( $input['scrolltop_offset'] );
		$input['support_comments']      = wpmtst_sanitize_checkbox( $input, 'support_comments' );
		$input['support_custom_fields'] = wpmtst_sanitize_checkbox( $input, 'support_custom_fields' );
		$input['no_lazyload']           = wpmtst_sanitize_checkbox( $input, 'no_lazyload' );
		$input['touch_enabled']         = wpmtst_sanitize_checkbox( $input, 'touch_enabled' );

		return $input;
	}

}

Strong_Testimonials_Settings_General::init();
