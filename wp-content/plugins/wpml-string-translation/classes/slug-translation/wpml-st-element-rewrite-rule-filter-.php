<?php

abstract class WPML_ST_Element_Rewrite_Rule_Filter implements IWPML_ST_Rewrite_Rule_Filter {

	/** @var SitePress $sitepress */
	protected $sitepress;

	/** @var WPML_Slug_Translation_Records $slug_records */
	protected $slug_records;

	public function __construct( WPML_Slug_Translation_Records $slug_records, SitePress $sitepress ) {
		$this->slug_records = $slug_records;
		$this->sitepress    = $sitepress;
	}

	/**
	 * @param array  $rules
	 * @param string $type
	 *
	 * @return array
	 */
	protected function filter_rules_by_type( $rules, $type ) {
			$slug = $this->get_slug_by_type( $type );

			if ( $slug === false ) {
				return $rules;
			}

			$display_as_translated_mode = $this->is_display_as_translated( $type );
			$slug_translation           = $this->get_slug_translation( $type, $slug, $display_as_translated_mode );

			$using_tags = false;
			/* case of slug using %tags% - PART 1 of 2 - START */
			if ( preg_match( '#%([^/]+)%#', $slug ) ) {
				$slug       = preg_replace( '#%[^/]+%#', '.+?', $slug );
				$using_tags = true;
			}

			if ( preg_match( '#%([^/]+)%#', $slug_translation ) ) {
				$slug_translation = preg_replace( '#%[^/]+%#', '.+?', $slug_translation );
				$using_tags       = true;
			}
			/* case of slug using %tags% - PART 1 of 2 - END */

			$buff_value = array();
			foreach ( (array) $rules as $match => $query ) {

				if ( $slug && $slug != $slug_translation ) {
					$new_match = $this->adjust_key( $match, $slug_translation, $slug );
					$buff_value[ $new_match ] = $query;

					if ( $new_match != $match && $display_as_translated_mode ) {
						$buff_value[ $match ] = $query;
					}

				} else {
					$buff_value[ $match ] = $query;
				}
			}

			$rules = $buff_value;
			unset( $buff_value );

			/* case of slug using %tags% - PART 2 of 2 - START */
			if ( $using_tags ) {
				if ( preg_match( '#\.\+\?#', $slug ) ) {
					$slug = preg_replace( '#\.\+\?#', '(.+?)', $slug );
				}

				if ( preg_match( '#\.\+\?#', $slug_translation ) ) {
					$slug_translation = preg_replace( '#\.\+\?#', '(.+?)', $slug_translation );
				}

				$buff_value = array();
				foreach ( $rules as $match => $query ) {

					if ( trim( $slug ) && trim( $slug_translation ) && $slug != $slug_translation ) {
						$match = $this->adjust_key( $match, $slug_translation, $slug );
					}

					$buff_value[ $match ] = $query;
				}

				$rules = $buff_value;
				unset( $buff_value );
			}
			/* case of slug using %tags% - PART 2 of 2 - END */

		return $rules;
	}

	/**
	 * @param string $type
	 * @param string $slug
	 * @param bool   $display_as_translated_mode
	 *
	 * @return string
	 */
	private function get_slug_translation( $type, $slug, $display_as_translated_mode ) {
		$current_language           = $this->sitepress->get_current_language();
		$default_language           = $this->sitepress->get_default_language();
		$slug_translation           = $this->slug_records->get_translation( $type, $current_language );

		if ( ! $slug_translation ) {
			// check original
			$slug_translation = $this->slug_records->get_original( $type, $current_language );
		}

		if ( $display_as_translated_mode && ( ! $slug_translation || $slug_translation === $slug ) && $default_language != 'en' ) {
			$slug_translation = $this->slug_records->get_translation( $type, $default_language );
		}

		return trim( $slug_translation, '/' );
	}

	/**
	 * @param string $type
	 *
	 * @return null|string
	 */
	protected function get_slug_by_type( $type ) {
		return $this->slug_records->get_original( $type );
	}

	/**
	 * @param string $k
	 * @param string $slug_translation
	 * @param string $slug
	 *
	 * @return string
	 */
	protected function adjust_key( $k, $slug_translation, $slug ) {
		if ( (bool) $slug_translation === true
		     && preg_match( '#^[^/]*/?' . preg_quote( $slug ) . '/#', $k ) && $slug != $slug_translation
		) {
			$k = preg_replace( '#^' . addslashes($slug) . '/#', $slug_translation . '/', $k );
		}

		return $k;
	}

	/**
	 * @param string $type
	 *
	 * @return bool
	 */
	abstract protected function is_display_as_translated( $type );
}
