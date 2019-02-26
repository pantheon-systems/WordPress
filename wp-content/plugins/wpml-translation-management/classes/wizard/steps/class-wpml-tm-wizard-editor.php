<?php

class WPML_TM_Wizard_Translation_Editor_Step extends WPML_Twig_Template_Loader {

	private $model = array(
		'editor_types' => array(
			'ate'     => ICL_TM_TMETHOD_ATE,
			'classic' => ICL_TM_TMETHOD_EDITOR,
			'manual'  => ICL_TM_TMETHOD_MANUAL,
		)
	);
	/**
	 * @var WPML_TM_MCS_ATE
	 */
	private $mscs_ate;

	public function __construct( WPML_TM_MCS_ATE $mcs_ate ) {
		$this->mscs_ate = $mcs_ate;

		parent::__construct( array(
				WPML_TM_PATH . '/templates/wizard',
				$mcs_ate->get_template_path(),
			)
		);
	}

	public function render() {
		$this->add_strings();

		return $this->get_template()->show( $this->model, 'translation-editor-step.twig' );
	}

	public function add_strings() {

		$this->model['strings'] = array(
			'title'          => __( 'Choose Your Translation Editor', 'wpml-translation-management' ),
			'summary'        => __( 'Quick Demo', 'wpml-translation-management' ),
			'quick_demo_url' => 'https://wpml.org/documentation/translating-your-contents/advanced-translation-editor/?utm_source=wpmlplugin&utm_campaign=tm-setup-wizard&utm_medium=quick-demo-link&utm_term=advanced-translation-editor#see-how-it-works',
			'options'        => array(
				'classic' => array(
					'heading'    => __( 'Classic Translation Editor', 'wpml-translation-management' ),

				),
				'ate'     => array(
					'heading'        => __( 'Advanced Translation Editor', 'wpml-translation-management' ),
					'extra_template' => array(
						'template' => 'mcs-ate-controls.twig',
						'model'    => $this->mscs_ate->get_model(),
					)
				),
			),

			'features' => array(
				array(
					'label'   => __( 'Support for all content types', 'wpml-translation-management' ),
					'classic' => true,
					'ate'     => true,
				),
				array(
					'label'   => __( 'Spell checker', 'wpml-translation-management' ),
					'classic' => false,
					'ate'     => true,
				),
				array(
					'label'   => __( 'Translation Memory', 'wpml-translation-management' ),
					'classic' => false,
					'ate'     => true,
				),
				array(
					'label'   => __( 'Machine Translation', 'wpml-translation-management' ),
					'classic' => false,
					'ate'     => true,
				),
				array(
					'label'   => __( 'HTML-free editing', 'wpml-translation-management' ),
					'classic' => false,
					'ate'     => true,
				),
				array(
					'label'   => __( 'Translator preview', 'wpml-translation-management' ),
					'classic' => false,
					'ate'     => true,
				)
			),

			'ate' => $this->mscs_ate->get_model(),

			'select'   => __( 'Select', 'wpml-translation-management' ),
			'continue' => __( 'Continue', 'wpml-translation-management' ),
			'go_back'  => __( 'Back to adding translators', 'wpml-translation-management' ),


		);
	}

}