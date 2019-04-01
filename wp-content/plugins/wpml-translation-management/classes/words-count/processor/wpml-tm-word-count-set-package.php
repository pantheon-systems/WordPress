<?php

class WPML_TM_Word_Count_Set_Package {

	/** @var WPML_ST_Package_Factory $package_factory */
	private $package_factory;

	/** @var WPML_TM_Word_Count_Records $records */
	private $records;

	/** @var array $active_langs */
	private $active_langs;

	public function __construct(
		WPML_ST_Package_Factory $package_factory,
		WPML_TM_Word_Count_Records $records,
		array $active_langs
	) {
		$this->package_factory = $package_factory;
		$this->records         = $records;
		$this->active_langs    = $active_langs;
	}

	/** @param int $package_id */
	public function process( $package_id ) {
		$package = $this->package_factory->create( $package_id );
		$word_counts = new WPML_TM_Count();

		foreach ( $this->active_langs as $lang ) {
			if ( $package->get_package_language() === $lang ) {
				$word_counts->set_total_words(
					$this->records->get_string_words_to_translate_per_lang( null, $package_id )
				);
			} else {
				$word_counts->set_words_to_translate(
					$lang,
					$this->records->get_string_words_to_translate_per_lang( $lang, $package_id )
				);
			}
		}

		$this->records->set_package_word_count( $package_id, $word_counts );
	}
}
