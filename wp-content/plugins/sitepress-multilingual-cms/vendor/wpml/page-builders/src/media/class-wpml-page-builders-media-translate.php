<?php

class WPML_Page_Builders_Media_Translate {

	/** @var WPML_Translation_Element_Factory $element_factory */
	private $element_factory;

	/** @var WPML_Media_Image_Translate $image_translate */
	protected $image_translate;

	/** @var array $translated_urls */
	protected $translated_urls = array();

	/** @var WP_Post[] $translated_posts */
	protected $translated_posts = array();

	/** @var array $translated_ids */
	private $translated_ids = array();

	public function __construct(
		WPML_Translation_Element_Factory $element_factory,
		WPML_Media_Image_Translate $image_translate
	) {
		$this->element_factory = $element_factory;
		$this->image_translate = $image_translate;
	}

	/**
	 * @param string $url
	 * @param string $lang
	 * @param string $source_lang
	 *
	 * @return string
	 */
	public function translate_image_url( $url, $lang, $source_lang ) {
		$key = $url . $lang . $source_lang;

		if ( ! array_key_exists( $key, $this->translated_urls ) ) {
			$this->translated_urls[ $key ] = $url;

			$attachment_id = $this->image_translate->get_attachment_id_by_url( $url, $source_lang );

			if ( $attachment_id ) {
				$this->add_translated_id( $attachment_id );
				$translated_url = $this->image_translate->get_translated_image_by_url( $url, $source_lang, $lang );
				$this->translated_urls[ $key ] = $url;

				if ( $translated_url ) {
					$this->translated_urls[ $key ] = $translated_url;
				}
			}
		}

		return $this->translated_urls[ $key ];
	}

	/**
	 * @param int    $id
	 * @param string $lang
	 *
	 * @return int
	 */
	public function translate_id( $id, $lang ) {
		if ( (int) $id < 1 ) {
			return $id;
		}

		$this->add_translated_id( $id );
		$translated_attachment = $this->get_translated_attachment( $id, $lang );

		if ( isset( $translated_attachment->ID ) ) {
			return $translated_attachment->ID;
		}

		return $id;
	}

	/**
	 * @param int    $id
	 * @param string $lang
	 *
	 * @return WP_Post|null
	 */
	private function get_translated_attachment( $id, $lang ) {
		$key = $id . $lang;

		if ( ! array_key_exists( $key, $this->translated_posts ) ) {
			$this->translated_posts[ $key ] = null;
			$element                       = $this->element_factory->create_post( $id );
			$translation                   = $element->get_translation( $lang );

			if ( $translation ) {
				$this->translated_posts[ $key ] = $translation->get_wp_object();
			}
		}


		return $this->translated_posts[ $key ];
	}

	/** @param int */
	private function add_translated_id( $id ) {
		if ( ! in_array( $id, $this->translated_ids, true ) ) {
			$this->translated_ids[] = $id;
		}
	}

	public function reset_translated_ids() {
		$this->translated_ids = array();
	}

	/** @return array */
	public function get_translated_ids() {
		return $this->translated_ids;
	}
}
