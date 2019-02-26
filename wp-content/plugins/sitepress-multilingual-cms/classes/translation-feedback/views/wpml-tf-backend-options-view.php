<?php

/**
 * Class WPML_TF_Backend_Options_View
 *
 * @author OnTheGoSystems
 */
class WPML_TF_Backend_Options_View {

	const TEMPLATE_FOLDER         = '/templates/translation-feedback/backend/';
	const TEMPLATE                = 'options-ui.twig';
	const MAX_EXPIRATION_QUANTITY = 10;

	/** @var  IWPML_Template_Service $template_service */
	private $template_service;

	/** @var WPML_TF_Settings $settings */
	private $settings;

	/** @var SitePress $sitepress */
	private $sitepress;

	/**
	 * WPML_TF_Frontend_Hooks constructor.
	 *
	 * @param IWPML_Template_Service $template_service
	 * @param WPML_TF_Settings       $settings
	 * @param SitePress              $sitepress
	 */
	public function __construct(
		IWPML_Template_Service $template_service,
		WPML_TF_Settings $settings,
		SitePress $sitepress
	) {
		$this->template_service = $template_service;
		$this->settings         = $settings;
		$this->sitepress        = $sitepress;
	}

	/**
	 * @return string
	 */
	public function render() {
		$model = array(
			'strings'               => self::get_strings(),
			'action'                => WPML_TF_Backend_Options_AJAX_Hooks_Factory::AJAX_ACTION,
			'nonce'                 => wp_create_nonce( WPML_TF_Backend_Options_AJAX_Hooks_Factory::AJAX_ACTION ),
			'module_toggle'         => $this->get_module_toggle(),
			'button_modes'          => $this->get_button_modes(),
			'icon_styles'           => $this->get_icon_styles(),
			'languages_to'          => $this->get_languages_to(),
			'display_modes'         => $this->get_display_modes(),
			'expiration_modes'      => $this->get_expiration_modes(),
			'expiration_quantities' => $this->get_expiration_quantities(),
			'expiration_units'      => $this->get_expiration_units(),
		);

		return $this->template_service->show( $model, self::TEMPLATE );
	}

	/**
	 * @return array
	 */
	public static function get_strings() {
		return array(
			'section_title' => __( 'Translation Feedback', 'sitepress' ),
			'button_mode_section_title' => __( 'Translation Feedback button on front-end:', 'sitepress' ),
			'icon_style_section_title' => __( 'Icon style:', 'sitepress' ),
			'languages_to_section_title' => __( 'Show Translation Feedback module for these languages:', 'sitepress' ),
			'expiration_section_title' => __( 'Expiration date for Translation Feedback module:', 'sitepress' ),
		);
	}

	/**
	 * @return array
	 */
	private function get_module_toggle() {
		return array(
			'value'    => 1,
			'label'    =>  __( 'Enable Translation Feedback module', 'sitepress' ),
			'selected' => $this->settings->is_enabled(),
		);
	}

	/**
	 * @return array
	 */
	private function get_button_modes() {
		$modes =  array(
			WPML_TF_Settings::BUTTON_MODE_LEFT     => array(
				'value'    => WPML_TF_Settings::BUTTON_MODE_LEFT,
				'label'    => __( 'Show on the left side of the screen', 'sitepress' ),
				'selected' => false,
			),
			WPML_TF_Settings::BUTTON_MODE_RIGHT    => array(
				'value'    => WPML_TF_Settings::BUTTON_MODE_RIGHT,
				'label'    => __( 'Show on the right side of the screen', 'sitepress' ),
				'selected' => false,
			),
			WPML_TF_Settings::BUTTON_MODE_CUSTOM   => array(
				'value'    => WPML_TF_Settings::BUTTON_MODE_CUSTOM,
				'label'    => __( 'I will add it manually (%1sinstructions%2s)', 'sitepress' ),
				'link'     => 'https://wpml.org/wpml-hook/wpml_tf_feedback_open_link/',
				'selected' => false,
			),
			WPML_TF_Settings::BUTTON_MODE_DISABLED => array(
				'value'    => WPML_TF_Settings::BUTTON_MODE_DISABLED,
				'label'    => __( 'Do not show it', 'sitepress' ),
				'selected' => false,
			),
		);

		if ( isset( $modes[ $this->settings->get_button_mode() ] ) ) {
			$modes[ $this->settings->get_button_mode() ]['selected'] = true;
		}

		return $modes;
	}

	private function get_icon_styles() {
		$css_classes = WPML_TF_Frontend_Feedback_View::get_icon_css_classes();

		$styles = array(
			WPML_TF_Settings::ICON_STYLE_LEGACY   => array(
				'value'       => WPML_TF_Settings::ICON_STYLE_LEGACY,
				'image_class' => $css_classes[ WPML_TF_Settings::ICON_STYLE_LEGACY ],
				'selected'    => false,
			),
			WPML_TF_Settings::ICON_STYLE_STAR     => array(
				'value'       => WPML_TF_Settings::ICON_STYLE_STAR,
				'image_class' => $css_classes[ WPML_TF_Settings::ICON_STYLE_STAR ],
				'selected'    => false,
			),
			WPML_TF_Settings::ICON_STYLE_THUMBSUP => array(
				'value'       => WPML_TF_Settings::ICON_STYLE_THUMBSUP,
				'image_class' => $css_classes[ WPML_TF_Settings::ICON_STYLE_THUMBSUP ],
				'selected'    => false,
			),
			WPML_TF_Settings::ICON_STYLE_BULLHORN => array(
				'value'       => WPML_TF_Settings::ICON_STYLE_BULLHORN,
				'image_class' => $css_classes[ WPML_TF_Settings::ICON_STYLE_BULLHORN ],
				'selected'    => false,
			),
			WPML_TF_Settings::ICON_STYLE_COMMENT  => array(
				'value'       => WPML_TF_Settings::ICON_STYLE_COMMENT,
				'image_class' => $css_classes[ WPML_TF_Settings::ICON_STYLE_COMMENT ],
				'selected'    => false,
			),
			WPML_TF_Settings::ICON_STYLE_QUOTE    => array(
				'value'       => WPML_TF_Settings::ICON_STYLE_QUOTE,
				'image_class' => $css_classes[ WPML_TF_Settings::ICON_STYLE_QUOTE ],
				'selected'    => false,
			),
		);

		if ( isset( $styles[ $this->settings->get_icon_style() ] ) ) {
			$styles[ $this->settings->get_icon_style() ]['selected'] = true;
		}

		return $styles;
	}

	/**
	 * @return array
	 */
	private function get_languages_to() {
		$languages_to      = array();
		$active_languages  = $this->sitepress->get_active_languages();
		$allowed_languages = $this->settings->get_languages_to();

		if ( null === $allowed_languages ) {
			$allowed_languages = array_keys( $active_languages );
		}

		foreach ( $active_languages as $code => $language ) {
			$languages_to[ $code ] = array(
				'value'     => $code,
				'label'     => $language['display_name'],
				'flag_url'  => $this->sitepress->get_flag_url( $code ),
				'selected'  => false,
			);

			if ( in_array( $code, $allowed_languages ) ) {
				$languages_to[ $code ]['selected'] = true;
			}
		}

		return $languages_to;
	}

	/**
	 * @return array
	 */
	private function get_display_modes() {
		$modes = array(
			WPML_TF_Settings::DISPLAY_CUSTOM => array(
				'value'   => WPML_TF_Settings::DISPLAY_CUSTOM,
				'label'   => esc_html__( 'Ask for feedback about translated content that was %1s in the last %2s %3s', 'sitepress' ),
				'selected' => false,
			),
			WPML_TF_Settings::DISPLAY_ALWAYS => array(
				'value'   => WPML_TF_Settings::DISPLAY_ALWAYS,
				'label'   => __( 'Always ask for feedback (no time limit for feedback)', 'sitepress' ),
				'selected' => false,
			),
		);

		if ( isset( $modes[ $this->settings->get_display_mode() ] ) ) {
			$modes[ $this->settings->get_display_mode() ]['selected'] = true;
		}

		return $modes;
	}

	/**
	 * @return array
	 */
	private function get_expiration_modes() {
		$modes = array(
			WPML_TF_Settings::EXPIRATION_ON_PUBLISH_OR_UPDATE => array(
				'value'    => WPML_TF_Settings::EXPIRATION_ON_PUBLISH_OR_UPDATE,
				'label'    => __( 'published or updated', 'sitepress' ),
				'selected' => false,
			),
			WPML_TF_Settings::EXPIRATION_ON_PUBLISH_ONLY      => array(
				'value'    => WPML_TF_Settings::EXPIRATION_ON_PUBLISH_ONLY,
				'label'    => __( 'published', 'sitepress' ),
				'selected' => false,
			),
			WPML_TF_Settings::EXPIRATION_ON_UPDATE_ONLY       => array(
				'value'    => WPML_TF_Settings::EXPIRATION_ON_UPDATE_ONLY,
				'label'    => __( 'updated', 'sitepress' ),
				'selected' => false,
			),
		);

		if ( isset( $modes[ $this->settings->get_expiration_mode() ] ) ) {
			$modes[ $this->settings->get_expiration_mode() ]['selected'] = true;
		}

		return $modes;
	}

	/**
	 * @return array
	 */
	private function get_expiration_quantities() {
		$quantities = array();

		for ( $i = 1; $i < self::MAX_EXPIRATION_QUANTITY + 1; $i++ ) {
			$quantities[ $i ] = array(
				'value' => $i,
				'selected' => false,
			);
		}

		if ( isset( $quantities[ $this->settings->get_expiration_delay_quantity() ] ) ) {
			$quantities[ $this->settings->get_expiration_delay_quantity() ]['selected'] = true;
		}

		return $quantities;
	}

	/**
	 * @return array
	 */
	private function get_expiration_units() {
		$units = array(
			WPML_TF_Settings::DELAY_DAY => array(
				'value'    => WPML_TF_Settings::DELAY_DAY,
				'label'    => __( 'day(s)', 'sitepress' ),
				'selected' => false,
			),
			WPML_TF_Settings::DELAY_WEEK => array(
				'value'    => WPML_TF_Settings::DELAY_WEEK,
				'label'    => __( 'week(s)', 'sitepress' ),
				'selected' => false,
			),
			WPML_TF_Settings::DELAY_MONTH => array(
				'value'    => WPML_TF_Settings::DELAY_MONTH,
				'label'    => __( 'month(s)', 'sitepress' ),
				'selected' => false,
			),
		);

		if ( isset( $units[ $this->settings->get_expiration_delay_unit() ] ) ) {
			$units[ $this->settings->get_expiration_delay_unit() ]['selected'] = true;
		}

		return $units;
	}
}