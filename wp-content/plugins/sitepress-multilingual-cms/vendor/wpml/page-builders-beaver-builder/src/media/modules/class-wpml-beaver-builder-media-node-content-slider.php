<?php

class WPML_Beaver_Builder_Media_Node_Content_Slider extends WPML_Beaver_Builder_Media_Node {

	private $property_prefixes = array(
		'bg_', // i.e. `bg_photo` for an ID or `bg_photo_src` for a URL
		'fg_',
		'r_',
	);

	public function translate( $node_data, $target_lang, $source_lang ) {
		if ( ! isset( $node_data->slides ) || ! is_array( $node_data->slides ) ) {
			return $node_data;
		}

		foreach ( $node_data->slides as &$slide ) {

			foreach ( $this->property_prefixes as $prefix ) {
				$id_prop  = $prefix . 'photo';
				$src_prop = $prefix . 'photo_src';

				if ( isset( $slide->{$id_prop} ) && $slide->{$id_prop} ) {
					$slide->{$id_prop} = $this->media_translate->translate_id( $slide->{$id_prop}, $target_lang );
				}

				if ( isset( $slide->{$src_prop} ) && $slide->{$src_prop} ) {
					$slide->{$src_prop} = $this->media_translate->translate_image_url( $slide->{$src_prop}, $target_lang, $source_lang );
				}
			}
		}

		return $node_data;
	}
}
