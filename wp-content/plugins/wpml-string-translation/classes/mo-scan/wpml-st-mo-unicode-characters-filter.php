<?php

class WPML_ST_MO_Unicode_Characters_Filter {
	/** @var string */
	private $pattern;

	public function __construct() {
		$parts = array(
			'([0-9|#][\x{20E3}])',
			'[\x{00ae}|\x{00a9}|\x{203C}|\x{2047}|\x{2048}|\x{2049}|\x{3030}|\x{303D}|\x{2139}|\x{2122}|\x{3297}|\x{3299}][\x{FE00}-\x{FEFF}]?',
			'[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?',
			'[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?',
			'[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?',
			'[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?',
			'[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?',
			'[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?',
			'[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?',
			'[\x{1F000}-\x{1F6FF}][\x{FE00}-\x{FEFF}]?',
		);

		$this->pattern = '/' . implode( '|', $parts ) . '/u';
	}

	/**
	 * @param WPML_ST_MO_Translation[] $translations
	 *
	 * @return WPML_ST_MO_Translation[]
	 */
	public function filter( array $translations ) {
		return array_filter( $translations, array( $this, 'is_valid' ) );
	}

	/**
	 * @param WPML_ST_MO_Translation $translations
	 *
	 * @return bool
	 */
	public function is_valid( WPML_ST_MO_Translation $translation ) {
		if ( preg_match( $this->pattern, $translation->get_original() ) ||
		     preg_match( $this->pattern, $translation->get_translation() ) ) {
			return false;
		}

		return true;
	}
}