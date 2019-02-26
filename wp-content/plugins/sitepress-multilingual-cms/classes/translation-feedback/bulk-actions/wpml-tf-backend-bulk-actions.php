<?php

/**
 * Class WPML_TF_Backend_Bulk_Actions
 *
 * @author OnTheGoSystems
 */
class WPML_TF_Backend_Bulk_Actions {

	/** @var  WPML_TF_Data_Object_Storage $feedback_storage */
	private $feedback_storage;

	/** @var WPML_WP_API $wp_api */
	private $wp_api;

	/** @var  WPML_TF_Backend_Notices $backend_notices */
	private $backend_notices;

	/**
	 * WPML_TF_Feedback_List_Bulk_Action_Hooks constructor.
	 *
	 * @param WPML_TF_Data_Object_Storage $feedback_storage
	 * @param WPML_WP_API                 $wp_api
	 * @param WPML_TF_Backend_Notices     $backend_notices
	 */
	public function __construct(
		WPML_TF_Data_Object_Storage $feedback_storage,
		WPML_WP_API $wp_api,
		WPML_TF_Backend_Notices $backend_notices
	) {
		$this->feedback_storage = $feedback_storage;
		$this->wp_api           = $wp_api;
		$this->backend_notices  = $backend_notices;
	}

	/**
	 * Method bulk_action_callback
	 */
	public function process() {

		if ( $this->is_valid_request() && current_user_can( 'manage_options' ) ) {
			$feedback_ids = array_map( 'intval', $_GET['feedback_ids'] );
			$bulk_action  = filter_var( $_GET['bulk_action'], FILTER_SANITIZE_STRING );

			$updated_feedback_ids = $feedback_ids;

			switch ( $bulk_action ) {
				case 'trash':
					$this->delete( $feedback_ids );
					break;

				case 'untrash':
					$this->untrash( $feedback_ids );
					break;

				case 'delete':
					$this->delete( $feedback_ids, true );
					break;

				default:
					$updated_feedback_ids = $this->change_status( $feedback_ids, $bulk_action );
					break;
			}

			$this->backend_notices->add_bulk_updated_notice( $updated_feedback_ids, $bulk_action );
			$this->redirect();
		} else {
			$this->backend_notices->remove_bulk_updated_notice_after_display();
		}
	}

	private function change_status( array $feedback_ids, $new_status ) {
		$updated_feedback_ids = array();

		foreach ( $feedback_ids as $feedback_id ) {
			$feedback = $this->feedback_storage->get( $feedback_id );

			if ( $feedback ) {
				/** @var WPML_TF_Feedback $feedback */
				$feedback->set_status( $new_status );
				$updated_feedback_ids[] = $this->feedback_storage->persist( $feedback );
			}
		}

		return $updated_feedback_ids;
	}

	private function delete( array $feedback_ids, $force_delete = false ) {
		foreach ( $feedback_ids as $feedback_id ) {
			$this->feedback_storage->delete( $feedback_id, $force_delete );
		}
	}

	private function untrash( array $feedback_ids ) {
		foreach ( $feedback_ids as $feedback_id ) {
			$this->feedback_storage->untrash( $feedback_id );
		}
	}

	/**
	 * @return bool
	 */
	private function is_valid_request() {
		$is_valid = false;

		if ( isset( $_GET['bulk_action'], $_GET['bulk_action2'] ) ) {
			if ( '-1' === $_GET['bulk_action'] ) {
				$_GET['bulk_action'] = $_GET['bulk_action2'];
			}

			if ( isset( $_GET['nonce'], $_GET['feedback_ids'] ) ) {
				$is_valid = $this->is_valid_action( $_GET['bulk_action'] )
				            && wp_verify_nonce( $_GET['nonce'], WPML_TF_Backend_Hooks::PAGE_HOOK );
			}
		}

		return $is_valid;
	}

	private function is_valid_action( $action ) {
		return in_array( $action, array( 'pending', 'fixed', 'trash', 'untrash', 'delete' ), true );
	}

	/**
	 * Redirect after processing the bulk action
	 */
	private function redirect() {
		$args_to_remove = array( 'feedback_ids', 'bulk_action', 'bulk_action2' );
		$url            = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
		$url            = remove_query_arg( $args_to_remove, $url );
		$this->wp_api->wp_safe_redirect( $url );
	}
}