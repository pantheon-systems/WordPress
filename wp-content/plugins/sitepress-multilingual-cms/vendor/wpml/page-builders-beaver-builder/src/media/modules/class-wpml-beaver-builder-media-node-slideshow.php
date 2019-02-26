<?php

/**
 * @group media
 */
class WPML_Beaver_Builder_Media_Node_Slideshow extends WPML_Beaver_Builder_Media_Node {

	private $url_properties = array(
		'largeURL',
		'x3largeURL',
		'thumbURL',
	);

	public function translate( $node_data, $target_lang, $source_lang ) {
		if ( ! isset( $node_data->photos ) || ! is_array( $node_data->photos ) ) {
			return $node_data;
		}

		foreach ( $node_data->photos as &$photo ) {
			$photo = $this->media_translate->translate_id( $photo, $target_lang );
		}

		foreach ( $node_data->photo_data as  $photo_id => $photo_data ) {
			$translated_id = $this->media_translate->translate_id( $photo_id, $target_lang );

			if ( $translated_id !== $photo_id ) {
				$translation_data    = wp_prepare_attachment_for_js( $translated_id );
				$photo_data->caption = $translation_data['caption'];

				foreach ( $this->url_properties as $property ) {

					if ( isset( $photo_data->{$property} ) && $photo_data->{$property} ) {
						$photo_data->{$property} = $this->media_translate->translate_image_url( $photo_data->{$property}, $target_lang, $source_lang );
					}
				}

				$node_data->photo_data[ $translated_id ] = $photo_data;
				unset( $node_data->photo_data[ $photo_id ] );
			}
		}

		return $node_data;
	}
}
