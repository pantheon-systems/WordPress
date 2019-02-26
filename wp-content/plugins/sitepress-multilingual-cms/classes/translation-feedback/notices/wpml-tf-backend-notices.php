<?php

/**
 * Class WPML_TF_Backend_Notices
 *
 * @author OnTheGoSystems
 */
class WPML_TF_Backend_Notices {

	const GROUP        = 'wpml_tf_backend_notices';
	const BULK_UPDATED = 'bulk_updated';

	/** @var  WPML_Notices $admin_notices */
	private $admin_notices;

	/**
	 * @param array $updated_feedback_ids
	 * @parem string
	 */
	public function add_bulk_updated_notice( array $updated_feedback_ids, $action ) {
		$count_feedback = count( $updated_feedback_ids );

		$message = _n( '%d feedback was updated.', '%d feedback were updated.', $count_feedback, 'sitepress' );

		if ( 'trash' === $action ) {
			$permanent_trash_delay = defined( 'EMPTY_TRASH_DAYS' ) ? EMPTY_TRASH_DAYS : 30;

			$message = _n( '%d feedback was trashed.', '%d feedback were trashed.', $count_feedback, 'sitepress' );
			$message .= ' ' . sprintf(
				__( 'The trashed feedback will be permanently deleted after %d days.', 'sitepress' ),
				$permanent_trash_delay
			);
		} elseif ( 'untrash' === $action ) {
			$message = _n( '%d feedback was restored.', '%d feedback were restored.', $count_feedback, 'sitepress' );
		} elseif ( 'delete' === $action ) {
			$message = _n( '%d feedback was permanently deleted.', '%d feedback were permanently deleted.', $count_feedback, 'sitepress' );
		}

		$text = sprintf( $message, $count_feedback );

		$new_notice = $this->get_admin_notices()->get_new_notice( self::BULK_UPDATED, $text, self::GROUP );
		$new_notice->set_hideable( true );
		$new_notice->set_css_class_types( 'notice-success' );
		$this->get_admin_notices()->add_notice( $new_notice );
	}

	/**
	 * Add action to remove updated notice after display
	 */
	public function remove_bulk_updated_notice_after_display() {
		add_action( 'admin_notices', array( $this, 'remove_bulk_updated_notice' ), PHP_INT_MAX );
	}

	/**
	 * Remove bulk_updated notice
	 */
	public function remove_bulk_updated_notice() {
		$this->get_admin_notices()->remove_notice( self::GROUP, self::BULK_UPDATED );
	}

	/**
	 * @return WPML_Notices
	 */
	private function get_admin_notices() {
		if ( ! $this->admin_notices ) {
			$this->admin_notices = wpml_get_admin_notices();
		}

		return $this->admin_notices;
	}
}