<?php

class WPML_Page_Builders_Media_Usage {

	/** @var WPML_Page_Builders_Media_Translate $media_translate */
	private $media_translate;

	/** @var WPML_Media_Usage_Factory $media_usage_factory */
	private $media_usage_factory;

	public function __construct(
		WPML_Page_Builders_Media_Translate $media_translate,
		WPML_Media_Usage_Factory $media_usage_factory
	) {
		$this->media_translate = $media_translate;
		$this->media_usage_factory = $media_usage_factory;
	}

	/** @param int $post_id */
	public function update( $post_id ) {
		$media_ids = $this->media_translate->get_translated_ids();

		foreach ( $media_ids as $media_id ) {
			$this->media_usage_factory->create( $media_id )->add_post( $post_id );
		}

		$this->media_translate->reset_translated_ids();
	}
}
