<?php

class WPML_Translation_Basket_Validation {

	private $basket;
	private $encoding_validation;
	private $package_helper;

	public function __construct(
		WPML_Translation_Basket $basket,
		WPML_Encoding_Validation $encoding_validation,
		WPML_Element_Translation_Package $package_helper
	) {
		$this->basket              = $basket;
		$this->encoding_validation = $encoding_validation;
		$this->package_helper      = $package_helper;
	}

	/**
	 * @return array
	 */
	public function get_invalid_documents() {
		$basket            = $this->basket->get_basket();
		$invalid_documents = array();
		$invalid_documents = $this->get_base64_encoded_documents( $invalid_documents, $basket, 'post' );
		$invalid_documents = $this->get_base64_encoded_documents( $invalid_documents, $basket, 'package' );

		return $invalid_documents;
	}

	/**
	 * @param array $invalid_documents
	 * @param array $basket
	 * @param string $kind
	 *
	 * @return array
	 */
	private function get_base64_encoded_documents( $invalid_documents, $basket, $kind ) {
		if ( array_key_exists( $kind, $basket ) ) {
			foreach ( $basket[ $kind ] as $id => $document ) {

				if ( ! get_post( $id ) ) {
					continue;
				}

				$package = $this->package_helper->create_translation_package( $id );
				foreach ( $package['contents'] as $slug => $field ) {
					if ( array_key_exists( 'format', $field )
					     && 'base64' === $field['format']
					     && $this->encoding_validation->is_base64( base64_decode( $field['data'] ) )
					) {
						if ( ! array_key_exists( $id, $invalid_documents ) ) {
							$invalid_documents[ $id ] = array(
								'error_type'    => 'base64',
								'error_message' => __( 'There are base64 encoded fields in this document', 'wpml-translation-management' ),
								'id'            => $id,
								'title'         => $package['title'],
								'type'          => $kind,
							);
						}

						$string_slug                          = new WPML_TM_Page_Builders_Field_Wrapper( $slug );
						$invalid_documents[ $id ]['fields'][] = array(
							'title'   => $string_slug->get_string_title(),
							'content' => base64_decode( $field['data'] ),
						);
					}
				}
			}
		}

		return $invalid_documents;
	}
}