<?php

class WPML_User_Admin_Language {

	const CACHE_GROUP = 'get_user_admin_language';

	/** @var SitePress */
	private $sitepress;

	public function __construct( SitePress $sitepress ) {
		$this->sitepress = $sitepress;
	}

	/**
	 * @param int|string $user_id
	 * @param bool       $reload
	 *
	 * @return bool|mixed|null|string
	 */
	public function get( $user_id, $reload = false ) {
		$user_id = (int) $user_id;
		$lang    = wp_cache_get( $user_id, self::CACHE_GROUP );

		if ( ! $lang || $reload ) {
			$lang = $this->get_from_user_settings( $user_id );

			if ( ! $lang ) {
				$lang = $this->get_from_global_settings();
			}

			wp_cache_set( $user_id, $lang, self::CACHE_GROUP );
		}

		return $lang;
	}

	/**
	 * @param $user_id
	 *
	 * @return null|false|string
	 */
	private function get_from_user_settings( $user_id ) {
		if ( get_user_meta( $user_id, 'icl_admin_language_for_edit', true ) ) {
			$lang = $this->sitepress->get_current_language();
		} else {
			$lang = get_user_meta( $user_id, 'icl_admin_language', true );

			if ( ! $lang ) {
				$user_locale = get_user_meta( $user_id, 'locale', true );

				if ( $user_locale ) {
					$lang = $this->sitepress->get_language_code_from_locale( $user_locale );
				}
			}
		}

		return $lang;
	}

	/**
	 * @return string
	 */
	private function get_from_global_settings() {
		$lang = $this->sitepress->get_setting( 'admin_default_language' );

		if ( ! $lang || $lang === '_default_' ) {
			$default = $this->sitepress->get_default_language();
			$lang    = $default ? $default : 'en';
		}

		return $lang;
	}
}
