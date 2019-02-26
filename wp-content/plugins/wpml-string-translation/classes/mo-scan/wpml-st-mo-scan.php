<?php

class WPML_ST_MO_Scan {
	/**
	 * @var WPML_ST_MO_Unicode_Characters_Filter
	 */
	private $unicode_characters_filter;

	/**
	 * @param WPML_ST_MO_Unicode_Characters_Filter $unicode_characters_filter
	 */
	public function __construct( WPML_ST_MO_Unicode_Characters_Filter $unicode_characters_filter = null ) {
		$this->unicode_characters_filter = $unicode_characters_filter;
	}

	/**
	 * @param string $mo_file
	 *
	 * @return WPML_ST_MO_Translation[]
	 */
	public function load_translations( $mo_file ) {
		if ( ! file_exists( $mo_file ) ) {
			return array();
		}

		$translations = array();
		$mo           = new MO();
		$pomo_reader  = new POMO_CachedFileReader( $mo_file );

		$mo->import_from_reader( $pomo_reader );

		foreach ( $mo->entries as $str => $v ) {
			$str            = str_replace( "\n", '\n', $v->singular );
			$translations[] = new WPML_ST_MO_Translation( $str, $v->translations[0], $v->context );

			if ( $v->is_plural ) {
				$str            = str_replace( "\n", '\n', $v->plural );
				$translation    = ! empty( $v->translations[1] ) ? $v->translations[1] : $v->translations[0];
				$translations[] = new WPML_ST_MO_Translation( $str, $translation, $v->context );
			}
		}

		if ( $this->unicode_characters_filter ) {
			$translations = $this->unicode_characters_filter->filter( $translations );
		}

		return $translations;
	}
}
