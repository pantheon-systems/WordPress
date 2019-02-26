<?php

class WPML_ST_Fastest_Settings_Notice {

	/**
	 * @var SitePress
	 */
	private $sitepress;

	/**
	 * @var array
	 */
	private $settings;

	/**
	 * @var WPML_Notices
	 */
	private $admin_notices;

	public function __construct( SitePress $sitepress, WPML_Notices $wpml_admin_notices ) {
		$this->sitepress = $sitepress;
		$this->admin_notices = $wpml_admin_notices;
		$this->set_settings();
	}

	/**
	 * @return array
	 */
	public function get_mismatched_settings() {
		$missing_settings = array();

		if ( 'en' !== $this->sitepress->get_default_language() ) {
			return $missing_settings;
		}

		foreach( $this->settings as $key => $setting ) {
			if ( 'core' === $setting['type'] ) {
				if ( (int) $setting['value'] !== (int) $this->sitepress->get_setting( $key ) ) {
					$missing_settings[$key] = $setting;
				}
			} elseif ( (int) $setting['value'] !== (int) get_option( $key ) ) {
				$missing_settings[$key] = $setting;
			}
		}

		return $missing_settings;
	}

	private function set_settings() {
		$this->settings = array(
			'theme_localization_type' => array(
				'title' => __( "Translate strings with ST and don't load .mo files", 'wpml-string-translation' ),
				'description' => __( 'Some themes and plugins have huge .mo files that take a long time to load. WPML already knows the strings in these files and loads only the necessary strings to display each page.', 'wpml-string-translation' ),
				'value' => 3,
				'type' => 'core',
			),
			WPML_ST_Gettext_Hooks_Factory::ALL_STRINGS_ARE_IN_ENGLISH_OPTION => array(
				'title' => WPML_ST_Theme_Plugin_Localization_Options_UI::get_all_strings_option_text(),
				'description' => __( "Almost all themes and plugins have texts in English. Reducing the check for the string's language simplifies and shortens the string translation process.", 'wpml-string-translation' ),
				'value' => 1,
				'type' => 'option',
			)
		);
	}

	public function add() {
		$missing_settings = $this->get_mismatched_settings();

		if ( count( $missing_settings ) ) {
			$message = $this->get_message( $missing_settings );

			$themes_and_plugins_settings = new WPML_ST_Themes_And_Plugins_Settings();
			$notice = $this->admin_notices->get_new_notice(
				WPML_ST_Themes_And_Plugins_Updates::WPML_ST_FASTER_SETTINGS_NOTICE_ID,
				$message,
				$themes_and_plugins_settings->get_notices_group()
			);

			$notice->set_css_class_types( 'info' );
			$notice->set_nonce_action( 'wpml-localization-options-nonce' );
			$notice->add_capability_check( array( 'manage_options' ) );
			$notice->add_display_callback( array( __CLASS__, 'only_display_notice_if_this_class_exists' ) );
			$notice->add_action( $this->admin_notices->get_new_notice_action( __( 'Apply those changes', 'wpml-string-translation' ), '#', false, false, 'button-primary' ) );
			$notice->add_action( $this->admin_notices->get_new_notice_action( __( 'Skip', 'wpml-string-translation' ), '#', false, true ) );
			$this->admin_notices->add_notice( $notice );
		}
	}

	/**
	 * @param array $missing_settings
	 *
	 * @return string
	 */
	private function get_message( $missing_settings ) {
		$message  = '<h3>' . __( 'Your site can run faster', 'wpml-string-translation' ) . '</h3>';
		$message .= __( 'This version of WPML includes new settings that will help your site run faster. We recommend changing the following settings, which are all available in WPML->Theme and plugins localization:', 'wpml-string-translation' );

		$message .= '<br /><ul>';
		foreach( $missing_settings as $setting ) {
			$message .= '<li><strong>' . $setting['title'] . '</strong>: ' . $setting['description'] . '</li>';
		}
		$message .= '</ul>';

		$message .= '<div style="display:none" class="js-done">' . __( 'Settings saved', 'wpml-string-translation' ) . '</div>';
		$message .= '<div style="display:none" class="js-error">' . __( 'Error', 'wpml-string-translation' ) . '</div>';

		return $message;
	}

	public function remove() {
		$this->admin_notices->remove_notice( WPML_ST_Themes_And_Plugins_Settings::NOTICES_GROUP, WPML_ST_Themes_And_Plugins_Updates::WPML_ST_FASTER_SETTINGS_NOTICE_ID );
	}

	/** @return bool */
	public static function only_display_notice_if_this_class_exists() {
		return true;
	}
}