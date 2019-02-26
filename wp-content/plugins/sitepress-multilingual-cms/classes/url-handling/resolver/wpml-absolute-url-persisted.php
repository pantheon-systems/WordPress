<?php

class WPML_Absolute_Url_Persisted {

	const OPTION_KEY = 'wpml_resolved_url_persist';

	private static $instance;

	private $urls;

	/**
	 * @return WPML_Absolute_Url_Persisted
	 */
	public static function get_instance()
	{
		if (null === self::$instance) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	protected function __construct() {}

	private function __clone() {}

	private function __wakeup() {}

	/**
	 * @return array
	 */
	private function get_urls() {
		if ( null === $this->urls ) {
			$this->urls = get_option( self::OPTION_KEY, array() );
		}

		return $this->urls;
	}

	/** @return bool */
	public function has_urls() {
		return (bool) $this->get_urls();
	}

	/**
	 * @param string       $original_url
	 * @param string       $lang
	 * @param string|false $converted_url A `false` value means that the URL could not be resolved
	 */
	public function set( $original_url, $lang, $converted_url ) {
		$this->get_urls();
		$this->urls[ $original_url ][ $lang ] = $converted_url;
		$this->persist_in_shutdown();
	}

	/**
	 * @param string $original_url
	 * @param string $lang
	 *
	 * @return string|false|null If the URL has already been processed but could not be resolved, it will return `false`
	 */
	public function get( $original_url, $lang ) {
		$this->get_urls();

		if ( isset( $this->urls[ $original_url ][ $lang ] ) ) {
			return $this->urls[ $original_url ][ $lang ];
		}

		return null;
	}

	/** @param string $url */
	public function delete( $url ) {
		if ( array_key_exists( $url, $this->get_urls() ) ) {
			unset( $this->urls[ $url ] );
		}

		foreach ( $this->urls as $original_url => $urls_per_lang ) {
			$lang = array_search( $url, $urls_per_lang, true );

			if ( $lang ) {
				unset( $this->urls[ $original_url ][ $lang ] );

				if ( empty( $this->urls[ $original_url ] ) ) {
					unset( $this->urls[ $original_url ] );
				}
			}
		}

		$this->persist_in_shutdown();
	}

	public function reset() {
		$this->urls = array();
		$this->persist();
		$this->urls = null;
	}

	public function persist() {
		update_option( self::OPTION_KEY, $this->urls );
	}

	private function persist_in_shutdown() {
		if ( ! has_action( 'shutdown', array( $this, 'persist' ) ) ) {
			add_action( 'shutdown', array( $this, 'persist' ) );
		}
	}
}
