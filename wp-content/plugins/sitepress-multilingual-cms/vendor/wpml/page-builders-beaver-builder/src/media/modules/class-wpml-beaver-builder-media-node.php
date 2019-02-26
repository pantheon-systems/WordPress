<?php

abstract class WPML_Beaver_Builder_Media_Node {

	/** @var WPML_Page_Builders_Media_Translate $media_translate */
	protected $media_translate;

	public function __construct( WPML_Page_Builders_Media_Translate $media_translate ) {
		$this->media_translate = $media_translate;
	}

	abstract function translate( $node_data, $target_lang, $source_lang );
}