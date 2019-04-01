<?php

class WPML_TM_Field_Content_Action extends WPML_TM_Job_Factory_User {

	/** @var  int $job_id */
	protected $job_id;

	/**
	 * WPML_TM_Field_Content_Action constructor.
	 *
	 * @param WPML_Translation_Job_Factory $job_factory
	 * @param int                          $job_id
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __construct( $job_factory, $job_id ) {
		parent::__construct( $job_factory );
		if ( ! ( is_int( $job_id ) && $job_id > 0 ) ) {
			throw new InvalidArgumentException( 'Invalid job id provided, received: ' . serialize( $job_id ) );
		}
		$this->job_id = $job_id;
	}

	/**
	 * Returns an array containing job fields
	 *
	 * @return array
	 * @throws \RuntimeException
	 */
	public function run() {
		try {
			$job = $this->job_factory->get_translation_job( $this->job_id, false, 1 );
			if ( ! $job ) {
				throw new RuntimeException( 'No job found for id: ' . $this->job_id );
			}

			return $this->content_from_elements( $job );
		} catch ( Exception $e ) {
			throw new RuntimeException(
				'Could not retrieve field contents for job_id: ' . $this->job_id,
				0, $e
			);
		}
	}

	/**
	 * Extracts the to be retrieved content from given job elements
	 *
	 * @param stdClass $job
	 *
	 * @return array
	 */
	private function content_from_elements( $job ) {
		/**
		 * @var array    $elements
		 * @var array    $previous_version_element
		 * @var stdClass $element
		 */

		$elements                  = $job->elements;
		$previous_version_elements = isset( $job->prev_version ) ? $job->prev_version->elements : array();
		$data = array();
		foreach ( $elements as $index => $element ) {
			$previous_element = null;
			if ( array_key_exists( $index, $previous_version_elements ) ) {
				$previous_element = $previous_version_elements[ $index ];
			}
			$data[] = array(
				'field_type'            => sanitize_title( str_replace( WPML_TM_Field_Type_Encoding::CUSTOM_FIELD_KEY_SEPARATOR, '-', $element->field_type ) ),
				'tid'                   => $element->tid,
				'field_style'           => $element->field_type === 'body' ? '2' : '0',
				'field_finished'        => $element->field_finished,
				'field_data'            => $this->sanitize_field_content( $element->field_data ),
				'field_data_translated' => $this->sanitize_field_content( $element->field_data_translated ),
				'diff'                  => $this->get_diff( $element, $previous_element ),
			);
		}

		return $data;
	}

	private function has_diff( $element, $previous_element ) {
		if ( null === $previous_element ) {
			return false;
		}
		$current_data  = $this->sanitize_field_content( $element->field_data );
		$previous_data = $this->sanitize_field_content( $previous_element->field_data );

		return $current_data !== $previous_data;
	}

	private function get_diff( $element, $previous_element ) {
		if ( null === $previous_element || ! $this->has_diff( $element, $previous_element ) ) {
			return null;
		}
		$current_data  = $this->sanitize_field_content( $element->field_data );
		$previous_data = $this->sanitize_field_content( $previous_element->field_data );

		return wp_text_diff( $previous_data, $current_data, $element->field_format );
	}

	/**
	 * @param string $content base64-encoded translation job field content
	 *
	 * @return string base64-decoded field content, with linebreaks turned into
	 * paragraph html tags
	 */
	private function sanitize_field_content( $content ) {
		$decoded = base64_decode( $content );

		if ( ! $this->is_html( $decoded ) && false !== strpos( $decoded, '\n' ) ) {
			$decoded = wpautop( $decoded );
		}

		return $decoded;
	}

	private function is_html( $string ) {
		return $string !== strip_tags( $string );
	}
}