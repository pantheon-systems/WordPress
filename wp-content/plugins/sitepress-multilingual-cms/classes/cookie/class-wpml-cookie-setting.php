<?php

/**
 * Class WPML_Frontend_Cookie_Setting
 */
class WPML_Cookie_Setting {

	const COOKIE_SETTING_FIELD = 'store_frontend_cookie';

	/**
	 * @var SitePress
	 */
	private $sitepress;

	/**
	 * WPML_Frontend_Cookie_Setting constructor.
	 *
	 * @param SitePress $sitepress
	 */
	public function __construct( SitePress $sitepress ) {
		$this->sitepress = $sitepress;
	}

	/**
	 * @return bool|mixed
	 */
	public function get_setting() {
		return $this->sitepress->get_setting( self::COOKIE_SETTING_FIELD );
	}

	/**
	 * @param $value
	 */
	public function set_setting( $value ) {
		$this->sitepress->set_setting( self::COOKIE_SETTING_FIELD, $value );
		$this->sitepress->save_settings();
	}
}