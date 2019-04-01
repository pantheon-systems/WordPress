<?php

class WPML_API_Hook_Permalink implements IWPML_Action {

	/** @var WPML_URL_Converter $url_converter */
	private $url_converter;

	/** @var IWPML_Resolve_Object_Url $absolute_resolver */
	private $absolute_resolver;

	public function __construct( WPML_URL_Converter $url_converter, IWPML_Resolve_Object_Url $absolute_resolver ) {
		$this->url_converter     = $url_converter;
		$this->absolute_resolver = $absolute_resolver;
	}

	public function add_hooks() {
		add_filter( 'wpml_permalink', array( $this, 'wpml_permalink_filter' ), 10, 3 );
	}

	/**
	 * @param string      $url
	 * @param null|string $lang
	 * @param bool        $absolute_url If `true`, WPML will try to resolve the object behind the URL
	 *                                  and try to find the matching translation's URL.
	 *                                  WARNING: This is a heavy process which could lead to performance hit.
	 *
	 * @return string
	 */
	public function wpml_permalink_filter( $url, $lang = null, $absolute_url = false ) {
		if ( $absolute_url ) {
			$new_url = $this->absolute_resolver->resolve_object_url( $url, $lang );

			if ( $new_url ) {
				$url = $new_url;
			}
		} else {
			$url = $this->url_converter->convert_url( $url, $lang );
		}

		return $url;
	}
}
