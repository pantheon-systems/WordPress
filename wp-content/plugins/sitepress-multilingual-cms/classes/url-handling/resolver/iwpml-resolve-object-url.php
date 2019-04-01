<?php

interface IWPML_Resolve_Object_Url {

	/**
	 * @param string $url
	 * @param string $lang
	 *
	 * @return string|false Will return the resolved URL or `false` if it could not be resolved.
	 */
	public function resolve_object_url( $url, $lang );

}