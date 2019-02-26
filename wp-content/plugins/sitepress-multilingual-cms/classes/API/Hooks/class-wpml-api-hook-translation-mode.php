<?php

class WPML_API_Hook_Translation_Mode implements IWPML_Action {

	const OPTION_KEY = 'custom_posts_sync_option';

	/** Allowed modes */
	const DO_NOT_TRANSLATE      = 'do_not_translate';
	const TRANSLATE             = 'translate';
	const DISPLAY_AS_TRANSLATED = 'display_as_translated';

	/** @var WPML_Settings_Helper $settings */
	private $settings;

	public function __construct( WPML_Settings_Helper $settings  ) {
		$this->settings = $settings;
	}

	public function add_hooks() {
		if ( is_admin() ) {
			add_action( 'wpml_set_translation_mode_for_post_type', array( $this, 'set_mode_for_post_type' ), 10, 2 );
		}
	}

	/**
	 * @param string $post_type
	 * @param string $translation_mode any of
	 *                                 `WPML_API_Hook_Translation_Mode::DO_NOT_TRANSLATE`,
	 *                                 `WPML_API_Hook_Translation_Mode::TRANSLATE`,
	 *                                 `WPML_API_Hook_Translation_Mode::DISPLAY_AS_TRANSLATED`
	 */
	public function set_mode_for_post_type( $post_type, $translation_mode ) {
		switch ( $translation_mode ) {
			case self::DO_NOT_TRANSLATE:
				$this->settings->set_post_type_not_translatable( $post_type );
				break;

			case self::TRANSLATE:
				$this->settings->set_post_type_translatable( $post_type );
				break;

			case self::DISPLAY_AS_TRANSLATED:
				$this->settings->set_post_type_display_as_translated( $post_type );
				break;
		}
	}
}