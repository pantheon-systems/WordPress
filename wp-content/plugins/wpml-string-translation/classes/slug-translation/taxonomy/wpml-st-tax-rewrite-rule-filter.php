<?php

class WPML_ST_Tax_Rewrite_Rule_Filter extends WPML_ST_Element_Rewrite_Rule_Filter {

	/** @var WPML_ST_Tax_Slug_Translation_Settings $settings */
	private $settings;

	public function __construct(
		WPML_Slug_Translation_Records $slug_records,
		SitePress $sitepress,
		WPML_ST_Tax_Slug_Translation_Settings $settings
	) {
		parent::__construct( $slug_records, $sitepress );
		$this->settings = $settings;
	}

	/**
	 * @param array|false|null $rules
	 *
	 * @return array
	 */
	public function rewrite_rules_filter( $rules ) {
		if ( empty( $rules ) ) {
			return $rules;
		}

		$taxonomies     = get_taxonomies( array( 'publicly_queryable' => true ) );
		$types_settings = $this->settings->get_types();

		foreach ( $taxonomies as $type ) {
			if ( ! isset( $types_settings[ $type ] )
				|| ! $types_settings[ $type ]
				|| ! $this->sitepress->is_translated_taxonomy( $type )
			) {
				continue;
			}

			$rules = $this->filter_rules_by_type( $rules, $type );
		}

		return $rules;
	}

	/**
	 * @param string $type
	 *
	 * @return bool
	 */
	protected function is_display_as_translated( $type ) {
		return $this->sitepress->is_display_as_translated_taxonomy( $type );
	}
}