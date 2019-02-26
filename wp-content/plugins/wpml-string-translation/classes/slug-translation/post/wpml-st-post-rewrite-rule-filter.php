<?php

class WPML_ST_Post_Rewrite_Rule_Filter extends WPML_ST_Element_Rewrite_Rule_Filter {

	/**
	 * @param array|false|null $rules
	 *
	 * @return array
	 */
	public function rewrite_rules_filter( $rules ) {
		if ( empty( $rules ) ) {
			return $rules;
		}

		$queryable_post_types           = get_post_types( array( 'publicly_queryable' => true ) );
		$post_slug_translation_settings = $this->sitepress->get_setting( 'posts_slug_translation', array() );

		foreach ( $queryable_post_types as $type ) {
			if ( ! isset( $post_slug_translation_settings['types'][ $type ] )
			     || ! $post_slug_translation_settings['types'][ $type ]
			     || ! $this->sitepress->is_translated_post_type( $type )
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
		return $this->sitepress->is_display_as_translated_post_type( $type );
	}
}