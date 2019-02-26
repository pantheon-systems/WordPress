<?php

class WPML_Display_As_Translated_Default_Lang_Messages_View {

	const TEMPLATE = 'default-language-change.twig';

	/**
	 * @var WPML_Twig_Template
	 */
	private $template_service;

	public function __construct( WPML_Twig_Template $template_service ) {
		$this->template_service = $template_service;
	}

	/**
	 * @param string $prev_default_lang
	 * @param string $default_lang
	 */
	public function display( $prev_default_lang, $default_lang ) {
		echo $this->template_service->show( $this->get_model( $prev_default_lang, $default_lang ), self::TEMPLATE );
	}

	/**
	 * @param string $prev_default_lang
	 * @param string $default_lang
	 *
	 * @return array
	 */
	private function get_model( $prev_default_lang, $default_lang ) {
		return array(
			'before_message'   => __( "Changing the site's default language can cause some content to disappear.", 'sitepress' ),
			'after_message'    => sprintf(
				__( "If some content appears gone, it might be because you switched the site's default language from %s to %s.", 'sitepress' ),
				$prev_default_lang,
				$default_lang ),
			'help_text'        => __( 'Tell me more', 'sitepress' ),
			'help_link'        => 'https://wpml.org/?page_id=1451509',
			'got_it'           => __( 'Got it', 'sitepress' ),
			'lang_has_changed' => $prev_default_lang !== $default_lang,
		);
	}
}