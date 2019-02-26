<?php

class WPML_TM_Translation_Basket_Validation_Notice {

	const TEMPLATE_FILE = 'validation-notice.twig';

	private $template_service;
	private $basket_validation;
	private $basket;

	public function __construct(
		IWPML_Template_Service $template_service,
		WPML_Translation_Basket_Validation $basket_validation,
		WPML_Translation_Basket $basket
	) {
		$this->template_service  = $template_service;
		$this->basket_validation = $basket_validation;
		$this->basket            = $basket;
	}

	public function add_hooks() {
		add_action( 'wpml_tm_before_basket_items_display', array( $this, 'render' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	public function enqueue_scripts() {
		wp_enqueue_script( 'wpml-tm-invalid-fields-dialog', WPML_TM_URL . '/res/js/translation-basket/invalid-fields-dialog.js', array( 'jquery-ui-dialog' ) );
	}

	public function render() {
		if ( $this->basket_validation->get_invalid_documents() ) {
			echo $this->template_service->show( $this->get_model(), self::TEMPLATE_FILE );

			foreach ( $this->basket_validation->get_invalid_documents() as $document ) {
				$this->basket->remove_item( $document['id'], $document['type'] );
			}
		}
	}

	private function get_model() {
		return array(
			'strings'   => array(
				'title'          => __( 'WPML cannot send some of the content to translation', 'wpml-translation-management' ),
				'message'        => __( 'Some of the content that you selected for translation includes fields that are encoded. Translators will not be able to work on this kind of content, so we removed it from the job.', 'wpml-translation-management' ),
				'message_bottom' => __( 'To fix this problem, you need to tell WPML how these fields are encoded, so that WPML can decode them before sending for translation.', 'wpml-translation-management' ),
				'show_fields'    => __( 'show fields', 'wpml-translation-management' ),
				'documentation'  => array(
					'link' => 'https://wpml.org/documentation/translating-your-contents/page-builders/how-to-fix-encoding-of-fields/?utm_source=wpmlplugin&utm_campaign=content-translation&utm_medium=translation-basket&utm_term=how-to-fix-encoding-of-fields',
					'text' => 'How to indicate to WPML that it needs to decode fields',
				),
			),
			'documents' => $this->get_formatted_documents(),
		);
	}

	/**
	 * @return array
	 */
	private function get_formatted_documents() {
		$invalid_documents = $this->basket_validation->get_invalid_documents();

		foreach ( $invalid_documents as $id => $invalid_document ) {
			if ( array_key_exists( 'fields', $invalid_document ) ) {
				$invalid_documents[ $id ]['fields'] = json_encode(
					array_map( 'json_encode', $invalid_documents[ $id ]['fields'] )
				);
			}
		}

		return $invalid_documents;
	}
}