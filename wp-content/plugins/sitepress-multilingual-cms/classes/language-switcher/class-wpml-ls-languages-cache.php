<?php
/**
 * Created by PhpStorm.
 * User: bruce
 * Date: 17/10/17
 * Time: 5:18 PM
 */

class WPML_LS_Languages_Cache {

	private $cache_key;
	private $cache;

	public function __construct( $template_args, $current_language, $default_language, $wp_query ) {
		$cache_key_args   = $template_args ? array_filter( $template_args ) : array( 'default' );
		$cache_key_args[] = $current_language;
		$cache_key_args[] = $default_language;
		if ( isset( $wp_query->request ) ) {
			$cache_key_args[] = $wp_query->request;
		}
		$cache_key_args  = array_filter( $cache_key_args );
		$this->cache_key = md5( wp_json_encode( $cache_key_args ) );
		$cache_group     = 'ls_languages';
		$this->cache     = new WPML_WP_Cache( $cache_group );
	}

	public function get() {
		$found  = false;
		$result = $this->cache->get( $this->cache_key, $found );
		if ( $found ) {
			return $result;
		} else {
			return null;
		}
	}

	public function set( $ls_languages ) {
		$this->cache->set( $this->cache_key, $ls_languages );
	}
}