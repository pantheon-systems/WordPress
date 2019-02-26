<?php

/**
 * Class WPML_TF_Feedback_Status
 *
 * @author OnTheGoSystems
 */
class WPML_TF_Feedback_Status {

	/** @var string $status */
	private $status = 'pending';

	/**
	 * WPML_TF_Feedback_Status constructor.
	 *
	 * @param string $status
	 */
	public function __construct( $status = null ) {
		if ( $status ) {
			$this->set_value( $status );
		}
	}

	/** @param string $status*/
	public function set_value( $status ) {
		$this->status = sanitize_text_field( $status );
	}

	/** @return string */
	public function get_value() {
		return $this->status;
	}

	/** @return null|string */
	public function get_display_text() {
		switch ( $this->get_value() ) {
			case 'pending':
				return __( 'New', 'sitepress' );

			case 'sent_to_translator':
				if ( $this->is_admin_user() ) {
					return __( 'Sent to translator', 'sitepress' );
				}

				return __( 'New', 'sitepress' );

			case 'translator_replied':
				if ( $this->is_admin_user() ) {
					return __( 'Translator replied', 'sitepress' );
				}

				return __( 'Replied', 'sitepress' );

			case 'admin_replied':
				if ( $this->is_admin_user() ) {
					return __( 'Sent to translator', 'sitepress' );
				}

				return __( 'Admin replied', 'sitepress' );

			case 'sent_to_ts_api':
			case 'sent_to_ts_manual':
				return __( 'Sent to translation service', 'sitepress' );

			case 'sent_to_ts_email':
				return __( 'E-mail sent to translation service', 'sitepress' );

			case 'fixed':
				return __( 'Translation fixed', 'sitepress' );

			case 'publish':
				return __( 'Approved', 'sitepress' );
		}

		return null;
	}

	/** @return bool */
	private function is_admin_user() {
		return current_user_can( 'manage_options' );
	}

	/**
	 * This is used by the blue button on the feedback list
	 *
	 * @return array|null
	 */
	public function get_next_status() {
		if ( $this->is_admin_user() ) {
			switch ( $this->get_value() ) {
				case 'pending':
				case 'sent_to_translator':
					return array(
						'value' => 'sent_to_translator',
						'label' => __( 'Send to translator', 'sitepress' ),
					);

				case 'translator_replied':
					return array(
						'value' => 'admin_replied',
						'label' => __( 'Reply to translator', 'sitepress' ),
					);

				case 'admin_replied':
					return array(
						'value' => 'admin_replied',
						'label' => __( 'Send to translator', 'sitepress' ),
					);
			}
		} else {
			switch ( $this->get_value() ) {
				case 'sent_to_translator':
				case 'translator_replied':
				case 'admin_replied':
					return array(
						'value' => 'translator_replied',
						'label' => __( 'Reply to admin', 'sitepress' ),
					);
			}
		}

		return null;
	}

	/** @return bool */
	public function is_pending() {
		$pending_statuses = array( 'pending' );

		if ( current_user_can( 'manage_options' ) ) {
			$pending_statuses[] = 'translator_replied';
		} else {
			$pending_statuses[] = 'admin_replied';
			$pending_statuses[] = 'sent_to_translator';
		}

		return in_array( $this->status, $pending_statuses, true );
	}
}
