<?php

/**
 * Created by PhpStorm.
 * User: bruce
 * Date: 28/08/17
 * Time: 11:54 AM
 */
class WPML_TM_Editor_Notice extends WPML_Notice {

	public function is_different( WPML_Notice $other_notice ) {
		if ( $this->get_id() !== $other_notice->get_id() ||
		     $this->get_group() !== $other_notice->get_group()
		) {
			return true;
		}

		return $this->strip_nonce_field( $this->get_text() ) !== $this->strip_nonce_field( $other_notice->get_text() );
	}

	private function strip_nonce_field( $text ) {
		return preg_replace( '/<input type="hidden" name="wpml_set_translation_editor_nonce" value=".*?">/', '', $text );
	}
}