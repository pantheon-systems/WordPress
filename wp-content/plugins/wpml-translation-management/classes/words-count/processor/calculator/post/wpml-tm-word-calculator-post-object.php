<?php

class WPML_TM_Word_Calculator_Post_Object implements IWPML_TM_Word_Calculator_Post {

	/** @var WPML_TM_Word_Calculator $calculator */
	private $calculator;

	/** @var WPML_TM_Word_Calculator_Post_Packages $packages_calculator */
	private $packages_calculator;

	public function __construct(
		WPML_TM_Word_Calculator $calculator,
		WPML_TM_Word_Calculator_Post_Packages $packages_calculator
	) {
		$this->calculator          = $calculator;
		$this->packages_calculator = $packages_calculator;
	}

	/**
	 * @param WPML_Post_Element $post_element
	 * @param string            $lang
	 *
	 * @return int
	 */
	public function count_words( WPML_Post_Element $post_element, $lang = null ) {
		$words       = 0;
		$wp_post     = $post_element->get_wp_object();
		$source_lang = $post_element->get_language_code();

		if ( $wp_post ) {
			$words += $this->calculator->count_words( $wp_post->post_title, $source_lang );
			$words += $this->calculator->count_words( $wp_post->post_excerpt, $source_lang );
			$words += $this->calculator->count_words( $wp_post->post_name, $source_lang );

			if ( $this->has_string_packages( $post_element ) ) {
				$words += $this->packages_calculator->count_words( $post_element, $lang );
			} else {
				$words += $this->calculator->count_words( $wp_post->post_content, $source_lang );
			}
		}

		return $words;
	}

	private function has_string_packages( WPML_Post_Element $post_element ) {
		return (bool) $this->packages_calculator->count_words( $post_element, null );
	}
}
