<?php

class WPML_Beaver_Builder_Media_Node_Photo extends WPML_Beaver_Builder_Media_Node {

	public function translate( $node_data, $target_lang, $source_lang ) {
		$translated_id = $this->media_translate->translate_id( $node_data->photo, $target_lang );

		if ( $translated_id !== $node_data->photo ) {
			$node_data->photo     = $translated_id;
			$node_data->photo_src = $this->media_translate->translate_image_url( $node_data->photo_src, $target_lang, $source_lang );
			$node_data->data      = wp_prepare_attachment_for_js( $translated_id );
		}

		return $node_data;
	}
}
