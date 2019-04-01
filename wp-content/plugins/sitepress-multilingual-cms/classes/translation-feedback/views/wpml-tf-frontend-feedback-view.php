<?php

/**
 * Class WPML_TF_Frontend_Feedback_View
 * @author OnTheGoSystems
 */
class WPML_TF_Frontend_Feedback_View {

	const TEMPLATE_FOLDER           = '/templates/translation-feedback/frontend/';
	const FORM_TEMPLATE             = 'feedback-form.twig';
	const OPEN_TEMPLATE             = 'feedback-open-button.twig';
	const CUSTOM_OPEN_LINK_TEMPLATE = 'feedback-custom-open-link.twig';
	const JS_OPEN_NODE_CLASS        = 'js-wpml-tf-feedback-icon';

	/** @var  IWPML_Template_Service $template_service */
	private $template_service;

	/** @var SitePress $sitepress */
	private $sitepress;

	/** @var WPML_Queried_Object $queried_object */
	private $queried_object;

	/** @var WPML_TF_Settings $settings */
	private $settings;

	/**
	 * WPML_TF_Frontend_Hooks constructor.
	 *
	 * @param IWPML_Template_Service $template_service
	 * @param SitePress              $sitepress
	 * @param WPML_Queried_Object    $queried_object
	 * @param WPML_TF_Settings       $settings
	 */
	public function __construct(
		IWPML_Template_Service $template_service,
		SitePress $sitepress,
		WPML_Queried_Object $queried_object,
		WPML_TF_Settings $settings
	) {
		$this->template_service = $template_service;
		$this->sitepress        = $sitepress;
		$this->queried_object   = $queried_object;
		$this->settings         = $settings;
	}

	/**
	 * @return string
	 */
	public function render_form() {
		$model = array(
			'strings' => array(
				'dialog_title'      => __( 'Rate translation', 'sitepress' ),
				'thank_you_rating'  => __( 'Thank you for your rating!', 'sitepress' ),
				'thank_you_comment' => __( 'Thank you for your rating and comment!', 'sitepress' ),
				'translated_from'   => __( 'This page was translated from:', 'sitepress' ),
				'please_rate'       => __( 'Please rate this translation:', 'sitepress' ),
				'your_rating'       => __( 'Your rating:', 'sitepress' ),
				'star5_title'       => __( 'It is perfect!', 'sitepress' ),
				'star4_title'       => __( 'It is OK', 'sitepress' ),
				'star3_title'       => __( 'It could be improved', 'sitepress' ),
				'star2_title'       => __( 'I can see a lot of language errors', 'sitepress' ),
				'star1_title'       => __( "I can't understand anything", 'sitepress' ),
				'change_rating'     => __( 'Change', 'sitepress' ),
				'error_examples'    => __( 'Please give some examples of errors and how would you improve them:', 'sitepress' ),
				'send_button'       => __( 'Send', 'sitepress' ),
				'honeypot_label'    => __( 'If you are a human, do not fill in this field.', 'sitepress' ),
			),
			'flag_url'      => $this->sitepress->get_flag_url( $this->queried_object->get_source_language_code() ),
			'language_name' => $this->sitepress->get_display_language_name( $this->queried_object->get_source_language_code() ),
			'document_id'   => $this->queried_object->get_id(),
			'document_type' => $this->queried_object->get_element_type(),
			'action'        => WPML_TF_Frontend_AJAX_Hooks_Factory::AJAX_ACTION,
			'nonce'         => wp_create_nonce( WPML_TF_Frontend_AJAX_Hooks_Factory::AJAX_ACTION ),
			'source_url'    => $this->queried_object->get_source_url(),
		);

		return $this->template_service->show( $model, self::FORM_TEMPLATE );
	}

	/**
	 * @return string
	 */
	public function render_open_button() {
		$rendering = '';

		if ( WPML_TF_Settings::BUTTON_MODE_CUSTOM !== $this->settings->get_button_mode() ) {
			$model = array(
				'strings' => array(
					'form_open_title'   => __( 'Rate translation of this page', 'sitepress' ),
				),
				'wrapper_css_classes' => $this->get_wrapper_css_classes(),
				'icon_css_class'      => $this->get_icon_css_class(),
			);

			$rendering = $this->template_service->show( $model, self::OPEN_TEMPLATE );
		}

		return $rendering;
	}

	/** @return string */
	private function get_wrapper_css_classes() {
		$base_classes = self::JS_OPEN_NODE_CLASS . ' wpml-tf-feedback-icon ';
		$class        = $base_classes . 'wpml-tf-feedback-icon-left';

		if ( WPML_TF_Settings::BUTTON_MODE_RIGHT === $this->settings->get_button_mode() ) {
			$class = $base_classes . 'wpml-tf-feedback-icon-right';
		}

		return $class;
	}

	/** @return string */
	private function get_icon_css_class() {
		$icon_style  = $this->settings->get_icon_style();
		$css_classes = self::get_icon_css_classes();

		if ( array_key_exists( $icon_style,$css_classes ) ) {
			return $css_classes[ $icon_style ];
		}

		return $css_classes[ WPML_TF_Settings::ICON_STYLE_LEGACY ];
	}

	/**
	 * @param string|array $args
	 *
	 * @return string
	 */
	public function render_custom_open_link( $args ) {
		$model = wp_parse_args( $args, self::get_default_arguments_for_open_link() );

		$model['classes'] = self::JS_OPEN_NODE_CLASS . ' ' . $model['classes'];

		return $this->template_service->show( $model, self::CUSTOM_OPEN_LINK_TEMPLATE );
	}

	/** @return array */
	public static function get_default_arguments_for_open_link() {
		return array(
			'title'   => __( 'Rate translation of this page', 'sitepress' ),
			'classes' => 'wpml-tf-feedback-custom-link',
		);
	}

	/** @return array */
	public static function get_icon_css_classes() {
		return array(
			WPML_TF_Settings::ICON_STYLE_LEGACY   => 'otgs-ico-translation',
			WPML_TF_Settings::ICON_STYLE_STAR     => 'otgs-ico-star',
			WPML_TF_Settings::ICON_STYLE_THUMBSUP => 'otgs-ico-thumbsup',
			WPML_TF_Settings::ICON_STYLE_BULLHORN => 'otgs-ico-bullhorn',
			WPML_TF_Settings::ICON_STYLE_COMMENT  => 'otgs-ico-comment',
			WPML_TF_Settings::ICON_STYLE_QUOTE    => 'otgs-ico-quote',
		);
	}
}
