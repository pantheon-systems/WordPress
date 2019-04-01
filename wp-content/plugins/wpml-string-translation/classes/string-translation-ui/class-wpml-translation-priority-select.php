<?php

/**
 * Created by OnTheGoSystems
 */
class WPML_Translation_Priority_Select extends WPML_Templates_Factory {

	const NONCE = 'wpml_change_string_translation_priority_nonce';

	public function get_model() {

		$model = array(
			'translation_priorities' => get_terms( array( 'taxonomy'   => 'translation_priority',
			                                              'hide_empty' => false
			) ),
			'nonce'                  => wp_nonce_field( self::NONCE, self::NONCE ),
			'strings'                => array(
				'empty_text' => __( 'Change translation priority of selected strings', 'wpml-string-translation' ),
			)
		);

		$this->enqueue_scripts();

		return $model;
	}

	public function init_template_base_dir() {
		$this->template_paths = array(
			WPML_ST_PATH. '/templates/translation-priority/',
		);
	}

	public function get_template() {
		return 'translation-priority-select.twig';
	}

	private function enqueue_scripts() {
		if ( ! wp_script_is( 'wpml-select-2' ) ) {
			// Enqueue in the footer because this is usually called late.
			wp_enqueue_script( 'wpml-select-2', ICL_PLUGIN_URL . '/lib/select2/select2.min.js', array( 'jquery' ), ICL_SITEPRESS_VERSION, true );
		}
	}
}