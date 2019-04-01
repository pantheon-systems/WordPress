<?php

/**
 * @author OnTheGoSystems
 */
class WPML_TF_Feedback_Reviewer {

	/** @var int $id WP_User ID */
	private $id;

	/**
	 * WPML_TF_Feedback_Reviewer constructor.
	 *
	 * @param int $id
	 */
	public function __construct( $id ) {
		$this->id = (int) $id;
	}

	/**
	 * @return int|null
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function get_reviewer_display_name() {
		$display_name = __( 'Unknown reviewer', 'sitepress' );

		$reviewer = get_user_by( 'id', $this->get_id() );

		if ( isset( $reviewer->display_name ) ) {
			$display_name = $reviewer->display_name;
		}

		return $display_name;
	}
}
