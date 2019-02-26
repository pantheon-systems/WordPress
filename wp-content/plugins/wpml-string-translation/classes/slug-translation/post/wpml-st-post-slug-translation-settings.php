<?php

/**
 * @todo: Move these settings to an independent option
 *      like WPML_ST_Tax_Slug_Translation_Settings::OPTION_NAME
 */
class WPML_ST_Post_Slug_Translation_Settings extends WPML_ST_Slug_Translation_Settings {

	const KEY_IN_SITEPRESS_SETTINGS = 'posts_slug_translation';

	/** @var SitePress $sitepress */
	private $sitepress;

	private $settings;

	public function __construct( SitePress $sitepress ) {
		$this->sitepress = $sitepress;
		$this->settings  = $sitepress->get_setting( self::KEY_IN_SITEPRESS_SETTINGS, array( ) );
	}

	/** @param bool $enabled */
	public function set_enabled( $enabled ) {
		parent::set_enabled( $enabled );

		/**
		 * Backward compatibility with 3rd part plugins
		 * The `on` key has been replaced by an independent option
		 * WPML_ST_Slug_Translation_Settings::KEY_ENABLED_GLOBALLY
		 */
		$this->settings['on'] = (int) $enabled;
	}

	/**
	 * @param string $type
	 *
	 * @return bool
	 */
	public function is_translated( $type ) {
		return ! empty( $this->settings['types'][ $type ] );
	}

	/**
	 * @param string $type
	 * @param bool   $is_enabled
	 */
	public function set_type( $type, $is_enabled ) {
		if ( $is_enabled ) {
			$this->settings['types'][ $type ] = 1;
		} else {
			unset( $this->settings['types'][ $type ] );
		}
	}

	public function save() {
		$this->sitepress->set_setting( self::KEY_IN_SITEPRESS_SETTINGS, $this->settings, true );
	}
}
