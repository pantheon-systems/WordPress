<?php

/**
 * Class WPML_TF_Backend_AJAX_Feedback_Edit_Hooks
 *
 * @author OnTheGoSystems
 */
class WPML_TF_Backend_AJAX_Feedback_Edit_Hooks implements IWPML_Action {

	/** @var WPML_TF_Feedback_Edit $feedback_edit */
	private $feedback_edit;

	/** @var WPML_TF_Backend_Feedback_Row_View $row_view */
	private $row_view;

	/** @var array $post_data */
	private $post_data;

	/**
	 * WPML_TF_Backend_AJAX_Feedback_Edit_Hooks constructor.
	 *
	 * @param WPML_TF_Feedback_Edit             $feedback_edit
	 * @param WPML_TF_Backend_Feedback_Row_View $row_view
	 * @param array                             $post_data
	 */
	public function __construct(
		WPML_TF_Feedback_Edit $feedback_edit,
		WPML_TF_Backend_Feedback_Row_View $row_view,
		array $post_data
	) {
		$this->feedback_edit = $feedback_edit;
		$this->row_view      = $row_view;
		$this->post_data     = $post_data;
	}

	public function add_hooks() {
		add_action(
			'wp_ajax_' . WPML_TF_Backend_AJAX_Feedback_Edit_Hooks_Factory::AJAX_ACTION,
			array( $this, 'edit_feedback_callback' )
		);
	}

	public function edit_feedback_callback() {
		try {
			$this->check_post_data_key( 'feedback_id' );
			$feedback_id = filter_var( $this->post_data['feedback_id'], FILTER_SANITIZE_NUMBER_INT );
			$feedback    = $this->feedback_edit->update( $feedback_id, $this->post_data );

			if ( ! $feedback ) {
				throw new WPML_TF_AJAX_Exception( esc_html__( 'Failed to update the feedback.', 'sitepress' ) );
			}

			$response = array(
				'summary_row' => $this->row_view->render_summary_row( $feedback ),
				'details_row' => $this->row_view->render_details_row( $feedback ),
			);

			wp_send_json_success( $response );

		} catch ( WPML_TF_AJAX_Exception $e ) {
			wp_send_json_error( $e->getMessage() );
		} catch ( WPML_TF_Feedback_Update_Exception $e ) {
			wp_send_json_error( $e->getMessage() );
		}
	}

	/**
	 * @param $key
	 *
	 * @throws WPML_TF_AJAX_Exception
	 */
	private function check_post_data_key( $key ) {
		if ( ! isset( $this->post_data[ $key ] ) ) {
			$message = sprintf(
				esc_html__( 'Missing key "%s".', 'sitepress' ),
				$key
			);

			throw new WPML_TF_AJAX_Exception( $message );
		}
	}
}
