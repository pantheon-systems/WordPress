<?php

class WPML_Beaver_Builder_Media_Node_Gallery extends WPML_Beaver_Builder_Media_Node {

	public function translate( $node_data, $target_lang, $source_lang ) {
		foreach ( $node_data->photos as &$photo ) {
			$photo = $this->media_translate->translate_id( $photo, $target_lang );
		}

		foreach ( $node_data->photo_data as &$photo_data ) {
			$translated_id = $this->media_translate->translate_id( $photo_data->id, $target_lang );

			if ( $translated_id !== $photo_data->id ) {
				$translation_data        = wp_prepare_attachment_for_js( $translated_id );
				$photo_data->id          = $translated_id;
				$photo_data->alt         = $translation_data['alt'];
				$photo_data->caption     = $translation_data['caption'];
				$photo_data->description = $translation_data['description'];
				$photo_data->title       = $translation_data['title'];
				$photo_data->src         = $translation_data['url'];
				$photo_data->link        = $translation_data['url'];
			}
		}

		return $node_data;
	}
}
