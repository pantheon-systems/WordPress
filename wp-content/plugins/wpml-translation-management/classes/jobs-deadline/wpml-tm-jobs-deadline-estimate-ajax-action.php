<?php

class WPML_TM_Jobs_Deadline_Estimate_AJAX_Action implements IWPML_Action {

	/** @var WPML_TM_Jobs_Deadline_Estimate $deadline_estimate */
	private $deadline_estimate;

	/** @var array $translation_basket */
	private $translation_basket;

	/** @var array $post_data */
	private $post_data;

	public function __construct(
		WPML_TM_Jobs_Deadline_Estimate $deadline_estimate,
		array $translation_basket,
		array $post_data
	) {
		$this->deadline_estimate  = $deadline_estimate;
		$this->translation_basket = $translation_basket;
		$this->post_data          = $post_data;
	}

	public function add_hooks() {
		add_action(
			'wp_ajax_' . 'wpml-tm-jobs-deadline-estimate-ajax-action',
			array( $this, 'refresh' )
		);
	}

	public function refresh() {
		if ( isset( $this->post_data['translators'] ) ) {
			$deadline_per_lang = array();

			foreach ( $this->post_data['translators'] as $lang_to => $translator_data ) {
				list( $translator_id, $service ) = $this->parse_translator_data( $translator_data );

				$translator_args = array(
					'translator_id'  => $translator_id,
					'service'        => $service,
				    'to'             => $lang_to,
				);

				$deadline_per_lang[ $lang_to ] = $this->deadline_estimate->get( $this->translation_basket, $translator_args );
			}

			$response_data = array(
				'deadline' => max( $deadline_per_lang ),
			);

			wp_send_json_success( $response_data );
		}
	}

	/**
	 * The translator data for a remote service will be like "ts-7" with 7 the ID of the remote service
	 * For a local translator, the translation data will be the ID
	 *
	 * @param string $translator_data
	 *
	 * @return array
	 */
	private function parse_translator_data( $translator_data ) {
		$translator_id = $translator_data;
		$service       = 'local';

		if ( false !== strpos( $translator_data, 'ts-' ) ) {
			$translator_id = 0;
			$service       = (int) preg_replace( '/^ts-/', '', $translator_data );
		}

		return array( $translator_id, $service );
	}
}
