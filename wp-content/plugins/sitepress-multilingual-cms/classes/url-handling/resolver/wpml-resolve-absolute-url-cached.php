<?php

class WPML_Resolve_Absolute_Url_Cached implements IWPML_Resolve_Object_Url {

	/** @var WPML_Absolute_Url_Persisted $url_persisted */
	private $url_persisted;

	/** @var WPML_Resolve_Absolute_Url $resolve_url */
	private $resolve_url;

	public function __construct( WPML_Absolute_Url_Persisted $url_persisted, WPML_Resolve_Absolute_Url $resolve_url ) {
		$this->url_persisted = $url_persisted;
		$this->resolve_url   = $resolve_url;
	}

	/**
	 * @param string $url
	 * @param string $lang
	 *
	 * @return false|string Will return `false` if the URL could not be resolved
	 */
	public function resolve_object_url( $url, $lang ) {
		$resolved_url = $this->url_persisted->get( $url, $lang );

		if ( null === $resolved_url ) {
			$resolved_url = $this->resolve_url->resolve_object_url( $url, $lang );
			$this->url_persisted->set( $url, $lang, $resolved_url );
		}

		return $resolved_url;
	}
}
