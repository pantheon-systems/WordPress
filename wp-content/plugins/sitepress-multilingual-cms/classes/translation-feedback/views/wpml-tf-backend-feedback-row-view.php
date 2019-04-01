<?php

/**
 * Class WPML_TF_Backend_Feedback_Row_View
 */
class WPML_TF_Backend_Feedback_Row_View {

	const TEMPLATE_FOLDER  = '/templates/translation-feedback/backend/';
	const SUMMARY_TEMPLATE = 'feedback-list-page-table-row.twig';
	const DETAILS_TEMPLATE = 'feedback-list-page-table-row-details.twig';

	/** @var IWPML_Template_Service $template_service */
	private $template_service;

	/**
	 * WPML_TF_Backend_Feedback_Row_View constructor.
	 *
	 * @param IWPML_Template_Service $template_service
	 */
	public function __construct( IWPML_Template_Service $template_service ) {
		$this->template_service = $template_service;
	}

	/** @param WPML_TF_Feedback $feedback */
	public function render_summary_row( WPML_TF_Feedback $feedback ) {
		$model = array(
			'strings'  => self::get_summary_strings(),
			'has_admin_capabilities' => current_user_can( 'manage_options' ),
			'feedback' => $feedback,
		);

		return $this->template_service->show( $model, self::SUMMARY_TEMPLATE );
	}

	/** @param WPML_TF_Feedback $feedback */
	public function render_details_row( WPML_TF_Feedback $feedback ) {
		$model = array(
			'strings'  => self::get_details_strings(),
			'has_admin_capabilities' => current_user_can( 'manage_options' ),
			'feedback' => $feedback,
		);

		return $this->template_service->show( $model, self::DETAILS_TEMPLATE );
	}

	/** @return array */
	public static function get_columns_strings() {
		return array(
			'feedback'        => __( 'Feedback', 'sitepress' ),
			'rating'          => __( 'Rating', 'sitepress' ),
			'status'          => __( 'Status', 'sitepress' ),
			'document'        => __( 'Translated post', 'sitepress' ),
			'date'            => __( 'Date', 'sitepress' ),
		);
	}

	/** @return array */
	public static function get_summary_strings() {
		return array(
			'select_validation' => __( 'Select Validation', 'sitepress' ),
			'inline_actions' => array(
				'review'    => __( 'Review', 'sitepress' ),
				'trash'     => __( 'Trash', 'sitepress' ),
				'view_post' => __( 'View post', 'sitepress' ),
				'untrash'   => __( 'Restore', 'sitepress' ),
				'delete'    => __( 'Delete permanently', 'sitepress' ),
			),
			'columns' => self::get_columns_strings(),
		);
	}

	/** @return array */
	public static function get_details_strings() {
		return array(
			'title' => __( 'Translation Feedback', 'sitepress' ),
			'translation' => __( 'Translation:', 'sitepress' ),
			'edit_translation' => __( 'Edit translation', 'sitepress' ),
			'original_post' => __( 'Original post:', 'sitepress' ),
			'rating' => __( 'Rating:', 'sitepress' ),
			'feedback' => __( 'Feedback:', 'sitepress' ),
			'edit_feedback' => __( 'Edit feedback', 'sitepress' ),
			'status' => __( 'Status:', 'sitepress' ),
			'refresh_status' => __( 'Check for updates', 'sitepress' ),
			'translated_by' => __( 'Translated by:', 'sitepress' ),
			'corrected_by' => __( 'Corrected by:', 'sitepress' ),
			'reviewed_by' => __( 'Reviewed by:', 'sitepress' ),
			'add_note_to_translator' => __( 'Add a note to the translator', 'sitepress' ),
			'add_note_to_admin' => __( 'Add a note to the administrator', 'sitepress' ),
			'communication' => __( 'Communication:', 'sitepress' ),
			'reply_to_translator_label' => __( 'Reply to translator:', 'sitepress' ),
			'reply_to_admin_label' => __( 'Reply to admin:', 'sitepress' ),
			'cancel_button' => __( 'Cancel', 'sitepress' ),
			'trash_button' => __( 'Trash', 'sitepress' ),
			'translation_fixed_button' => __( 'Mark as fixed', 'sitepress' ),
			'no_translator_available' => __( 'No translator available for this language pair.', 'sitepress' )
		);
	}
}
