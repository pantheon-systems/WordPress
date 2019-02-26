<?php

class WPML_TM_Word_Calculator_Post_Packages implements IWPML_TM_Word_Calculator_Post {

	/** @var WPML_TM_Word_Count_Records $records */
	private $records;

	/** @var WPML_TM_Count_Composite[] $package_counts */
	private $package_counts;

	public function __construct( WPML_TM_Word_Count_Records $records ) {
		$this->records = $records;
	}

	/**
	 * @param WPML_Post_Element $post_element
	 * @param string|null       $lang
	 *
	 * @return int
	 */
	public function count_words( WPML_Post_Element $post_element, $lang = null ) {
		if ( $lang ) {
			return $this->get_package_counts( $post_element )->get_words_to_translate( $lang );
		} else {
			return $this->get_package_counts( $post_element )->get_total_words();
		}
	}

	/**
	 * @param WPML_Post_Element $post_element
	 *
	 * @return WPML_TM_Count_Composite
	 */
	private function get_package_counts( WPML_Post_Element $post_element ) {
		$post_id = $post_element->get_id();

		if ( ! isset( $this->package_counts[ $post_id ] ) ) {
			$counts = $this->records->get_packages_word_counts( $post_id );

			$word_count_composite = new WPML_TM_Count_Composite();

			foreach ( $counts as $count ) {
				$word_count_composite->add_count( $count );
			}

			$this->package_counts[ $post_id ] = $word_count_composite;
		}

		return $this->package_counts[ $post_id ];
	}
}
