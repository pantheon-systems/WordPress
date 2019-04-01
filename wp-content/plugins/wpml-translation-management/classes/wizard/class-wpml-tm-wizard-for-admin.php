<?php

class WPML_TM_Wizard_For_Admin extends WPML_Wizard {

	protected function initialize_steps() {
		$this->add_step(
			'tm_select_manager',
			__( 'Select the Translation Manager', 'wpml-translation-management' )
		);
		$this->add_step(
			'tm_finish',
			__( 'Finish', 'wpml-translation-management' )
		);
		$this->set_current_step( 'tm_select_manager' );
	}

	protected function enqueue_scripts() {

		wp_register_script( 'wpml-tm-wizard', WPML_TM_URL . '/dist/js/wizard/app.js', array( 'wpml-wizard', 'wpml-tooltip' ), WPML_TM_VERSION, true );
		wp_enqueue_script( 'wpml-tm-wizard' );
		wp_enqueue_style( 'wpml-tooltip' );

		wp_enqueue_script( 'wpml-select-2', ICL_PLUGIN_URL . '/lib/select2/select2.min.js', array( 'jquery' ), ICL_SITEPRESS_VERSION, true );

		wp_enqueue_script( 'wpml-tm-translation-roles-select2',
			WPML_TM_URL . '/res/js/translation-roles-select2.js',
			array(),
			WPML_TM_VERSION );

		wp_enqueue_script( 'wpml-tm-set-translation-roles',
			WPML_TM_URL . '/res/js/set-translation-role.js',
			array(),
			WPML_TM_VERSION );

		wp_register_style( 'wpml-tm-wizard', WPML_TM_URL . '/res/css/wpml-tm-wizard.css' );
		wp_enqueue_style( 'wpml-tm-wizard' );

	}

}