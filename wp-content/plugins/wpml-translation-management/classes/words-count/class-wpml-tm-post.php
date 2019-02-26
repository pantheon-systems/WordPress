<?php

class WPML_TM_Post extends WPML_TM_Translatable_Element {

	/** @var array|null|WP_Post */
	private $wp_post;

	protected function init( $id ) {
		$this->wp_post = get_post( $id );
	}

	protected function get_type() {
		return 'post';
	}

	protected function get_total_words() {
		return $this->word_count_records->get_post_word_count( $this->id )->get_total_words();
	}

	public function get_type_name( $label = null ) {
		$post_type = $this->wp_post->post_type;

		$post_type_label = ucfirst( $post_type );

		$post_type_object = get_post_type_object( $post_type );

		if ( isset( $post_type_object ) ) {
			$post_type_object_item = $post_type_object;
			$temp_post_type_label  = '';
			if ( isset( $post_type_object_item->labels->$label ) ) {
				$temp_post_type_label = $post_type_object_item->labels->$label;
			}
			if ( trim( $temp_post_type_label ) == '' ) {
				if ( isset( $post_type_object_item->labels->singular_name ) ) {
					$temp_post_type_label = $post_type_object_item->labels->singular_name;
				} elseif ( $label && $post_type_object_item->labels->name ) {
					$temp_post_type_label = $post_type_object_item->labels->name;
				}
			}
			if ( trim( $temp_post_type_label ) != '' ) {
				$post_type_label = $temp_post_type_label;
			}
		}

		return $post_type_label;
	}
}