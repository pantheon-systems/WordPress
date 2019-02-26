<?php

class WPML_WP_Cache_Item {

	/** @var string $key */
	private $key;

	/** @var WPML_WP_Cache $cache */
	private $cache;

	/**
	 * WPML_WP_Cache_Item constructor.
	 *
	 * @param WPML_WP_Cache $cache
	 * @param string|array $key
	 */
	public function __construct( WPML_WP_Cache $cache, $key ) {
		if ( is_array( $key ) ) {
			$key = md5( json_encode( $key ) );
		}
		$this->cache = $cache;
		$this->key = $key;
	}

	/**
	 * @return bool
	 */
	public function exists() {

		$found = false;
		$this->cache->get( $this->key, $found );
		return $found;
	}

	/**
	 * @return mixed
	 */
	public function get() {
		$found = false;
		return $this->cache->get( $this->key, $found );
	}

	/**
	 * @param mixed $value
	 */
	public function set( $value ) {
		$this->cache->set( $this->key, $value );
	}

}
