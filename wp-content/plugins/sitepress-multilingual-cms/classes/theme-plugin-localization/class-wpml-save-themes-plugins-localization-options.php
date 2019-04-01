<?php

class WPML_Save_Themes_Plugins_Localization_Options {

	/** @var SitePress */
	private $sitepress;

	/**
	 * WPML_Save_Themes_Plugins_Localization_Options constructor.
	 *
	 * @param SitePress $sitepress
	 */
	public function __construct( SitePress $sitepress ) {
		$this->sitepress = $sitepress;
	}

	/** @param array $settings */
	public function save_settings( $settings ) {

		foreach ( $this->get_settings() as $key => $setting ) {
			if ( array_key_exists( $key, $settings ) ) {
				$value = filter_var( $settings[ $key ], $setting['filter'] );
				if ( 'setting' === $setting['type'] ) {
					if ( $setting['st_setting'] ) {
						$st_settings = $this->sitepress->get_setting( 'st' );
						$st_settings[ $setting['settings_var'] ] = $value;
						$this->sitepress->set_setting( 'st', $st_settings );
					} else {
						$this->sitepress->set_setting( $setting['settings_var'], $value );
					}
				} elseif( 'option' === $setting['type'] ){
					update_option( $setting['settings_var'], $value );
				}
			}
		}

		$this->sitepress->save_settings();

		do_action( 'theme_plugin_localization_settings_saved', $settings );
	}

	/** @return array */
	private function get_settings() {
		$settings = array();
		$settings['theme_localization_type'] = array(
			'settings_var' => 'theme_localization_type',
			'filter' => FILTER_SANITIZE_NUMBER_INT,
			'type' => 'setting',
			'st_setting' => false,
		);
		$settings['theme_localization_load_textdomain'] = array(
			'settings_var' => 'theme_localization_load_textdomain',
			'filter' => FILTER_SANITIZE_NUMBER_INT,
			'type' => 'setting',
			'st_setting' => false,
		);
		$settings['gettext_theme_domain_name'] = array(
			'settings_var' => 'gettext_theme_domain_name',
			'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'type' => 'setting',
			'st_setting' => false,
		);

		/**
		 * @param array: array of settings rendered in theme/plugin localization screen
		 */
		return apply_filters( 'wpml_localization_options_settings', $settings );
	}
}