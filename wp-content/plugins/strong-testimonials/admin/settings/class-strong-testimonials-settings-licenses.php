<?php
/**
 * Class Strong_Testimonials_Settings_Licenses
 */
class Strong_Testimonials_Settings_Licenses {

	const TAB_NAME = 'licenses';

	const OPTION_NAME = 'wpmtst_addons';

	const GROUP_NAME = 'wpmtst-license-group';

	/**
	 * Strong_Testimonials_Settings_Licenses constructor.
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
		add_action( 'wpmtst_settings_tabs', array( __CLASS__, 'register_tab' ), 90, 2 );
		add_filter( 'wpmtst_settings_callbacks', array( __CLASS__, 'register_settings_page' ) );
	}

	/**
	 * Check for active add-ons.
	 *
	 * @since 2.1
	 */
	public static function has_active_addons() {
		return has_action( 'wpmtst_licenses' );
	}

	/**
	 * Register settings tab.
	 *
	 * @param $active_tab
	 * @param $url
	 */
	public static function register_tab( $active_tab, $url ) {
		if ( self::has_active_addons() ) {
			printf( '<a href="%s" class="nav-tab %s">%s</a>',
				esc_url( add_query_arg( 'tab', self::TAB_NAME, $url ) ),
				esc_attr( $active_tab == self::TAB_NAME ? 'nav-tab-active' : '' ),
				__( 'Licenses', 'strong-testimonials' )
			);
		}
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
		include( WPMTST_ADMIN . 'settings/partials/licenses.php' );
	}

	/**
	 * Sanitize settings.
	 *
	 * @param $new_licenses
	 *
	 * @return array
	 */
	public static function sanitize_options( $new_licenses ) {
		$old_licenses = get_option( 'wpmtst_addons' );
		// Check existence. May have been erased by Reset plugin.
		if ( $old_licenses ) {
			foreach ( $new_licenses as $addon => $new_info ) {
				$old_license = isset( $old_licenses[ $addon ]['license'] ) ? $old_licenses[ $addon ]['license'] : '';
				if ( isset( $old_license['key'] ) && $old_license['key'] != $new_info['license']['key'] ) {
					// new license has been entered, so must reactivate
					unset( $new_licenses[ $addon ]['license']['status'] );
				}
			}
		}

		return $new_licenses;
	}

}

Strong_Testimonials_Settings_Licenses::init();
