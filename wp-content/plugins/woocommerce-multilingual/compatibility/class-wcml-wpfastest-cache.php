<?php

class WCML_WpFastest_Cache {

	public function add_hooks() {
		add_filter( 'wcml_is_cache_enabled_for_switching_currency', array(
			$this,
			'is_cache_enabled_for_switching_currency'
		) );
	}

	/**
	 * @param bool $cache_enabled
	 *
	 * @return bool
	 */
	public function is_cache_enabled_for_switching_currency( $cache_enabled ) {

		$wp_fastest_cache_options = json_decode( get_option( 'WpFastestCache' ) );

		if ( isset( $wp_fastest_cache_options->wpFastestCacheStatus ) && 'on' === $wp_fastest_cache_options->wpFastestCacheStatus ) {
			$cache_enabled = true;
		}

		return $cache_enabled;
	}

}

