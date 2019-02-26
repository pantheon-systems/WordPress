<?php

/**
 * Class WPML_TF_Feedback_Edit
 *
 * @author OnTheGoSystems
 */
class WPML_TF_Feedback_Edit {

	/** @var WPML_TF_Feedback_Query */
	private $feedback_query;

	/** @var WPML_TF_Data_Object_Storage $feedback_storage */
	private $feedback_storage;

	/** @var WPML_TF_Data_Object_Storage $message_storage */
	private $message_storage;

	/** @var null|WPML_TP_Client_Factory $tp_client_factory */
	private $tp_client_factory;

	/** @var null|WPML_TP_Client $tp_client */
	private $tp_client;

	public function __construct(
		WPML_TF_Feedback_Query $feedback_query,
		WPML_TF_Data_Object_Storage $feedback_storage,
		WPML_TF_Data_Object_Storage $message_storage,
		WPML_TP_Client_Factory $tp_client_factory = null
	) {
		$this->feedback_query    = $feedback_query;
		$this->feedback_storage  = $feedback_storage;
		$this->message_storage   = $message_storage;
		$this->tp_client_factory = $tp_client_factory;
	}

	/**
	 * @param int   $feedback_id
	 * @param array $args
	 *
	 * @return null|WPML_TF_Feedback
	 *
	 * @throws WPML_TF_Feedback_Update_Exception
	 */
	public function update( $feedback_id, array $args ) {
		$feedback = $this->feedback_query->get_one( $feedback_id );

		if ( $feedback ) {
			$this->update_feedback_content( $feedback, $args );
			$this->add_message_to_feedback( $feedback, $args );
			$this->assign_feedback_to_reviewer( $feedback, $args );
			$this->update_feedback_status( $feedback, $args );
			$this->feedback_storage->persist( $feedback );

			$feedback = $this->feedback_query->get_one( $feedback_id, true );
		}

		return $feedback;
	}
	/**
	 * @param WPML_TF_Feedback $feedback
	 * @param array            $args
	 */
	private function update_feedback_content( WPML_TF_Feedback $feedback, array $args ) {
		if ( isset( $args['feedback_content'] ) && $this->is_admin_user() ) {
			$feedback->set_content( $args['feedback_content'] );
		}
	}

	/**
	 * @param WPML_TF_Feedback $feedback
	 * @param array            $args
	 */
	private function add_message_to_feedback( WPML_TF_Feedback $feedback, array $args ) {
		if ( isset( $args['message_content'] ) ) {
			$message_args = array(
				'feedback_id' => $feedback->get_id(),
				'content'     => $args['message_content'],
				'author_id'   => get_current_user_id(),
			);

			$message = new WPML_TF_Message( $message_args );
			$feedback->add_message( $message );
			$this->message_storage->persist( $message );
		}
	}

	/**
	 * @param WPML_TF_Feedback $feedback
	 * @param array            $args
	 */
	private function assign_feedback_to_reviewer( WPML_TF_Feedback $feedback, array $args ) {
		if ( isset( $args['feedback_reviewer_id'] ) && $this->is_admin_user() ) {
			$feedback->set_reviewer( $args['feedback_reviewer_id'] );
		}
	}

	/**
	 * @param WPML_TF_Feedback $feedback
	 * @param array            $args
	 */
	private function update_feedback_status( WPML_TF_Feedback $feedback, array $args ) {
		if ( isset( $args['feedback_status'] )
		     && in_array( $args['feedback_status'], $this->get_feedback_statuses(), true )
		) {
			if ( 'sent_to_translator' === $args['feedback_status'] && ! $feedback->is_local_translation() ) {
				$this->send_feedback_to_tp( $feedback );
			} elseif ( 'sent_to_ts_api' === $args['feedback_status'] ) {
				$this->update_feedback_status_from_tp( $feedback );
			} else {
				$feedback->set_status( $args['feedback_status'] );
			}
		}
	}

	/**
	 * @param int $feedback_id
	 *
	 * @return bool
	 */
	public function delete( $feedback_id ) {
		if ( $this->is_admin_user() ) {
			$this->feedback_storage->delete( $feedback_id );
			return true;
		}

		return false;
	}

	/** @return bool */
	private function is_admin_user() {
		return current_user_can( 'manage_options' );
	}

	/** @return array */
	private function get_feedback_statuses() {
		return array(
			'pending',
			'sent_to_translator',
			'translator_replied',
			'admin_replied',
			'fixed',
			'sent_to_ts_api',
		);
	}

	/**
	 * @param WPML_TF_Feedback $feedback
	 *
	 * @throws WPML_TF_Feedback_Update_Exception
	 */
	private function send_feedback_to_tp( WPML_TF_Feedback $feedback ) {
		$current_user = wp_get_current_user();

		$args = array(
			'email' => $current_user->user_email,
		);

		$tp_feedback_id = $this->get_tp_client()->feedback()->send( $feedback, $args );

		if ( ! $tp_feedback_id ) {
			throw new WPML_TF_Feedback_Update_Exception( $this->get_communication_error_message( 'send' ) );
		}

		$feedback->get_tp_responses()->set_feedback_id( $tp_feedback_id );
		$active_service = $this->get_tp_client()->services()->get_active();
		$feedback->get_tp_responses()->set_feedback_forward_method( $active_service->get_feedback_forward_method() );
		$new_status = 'sent_to_ts_' . $active_service->get_feedback_forward_method();
		$feedback->set_status( $new_status );
	}

	/**
	 * @param WPML_TF_Feedback $feedback
	 *
	 * @throws WPML_TF_Feedback_Update_Exception
	 */
	private function update_feedback_status_from_tp( WPML_TF_Feedback $feedback ) {
		$tp_feedback_status = $this->get_tp_client()->feedback()->status( $feedback );

		if ( ! $tp_feedback_status ) {
			throw new WPML_TF_Feedback_Update_Exception( $this->get_communication_error_message( 'status' ) );
		} elseif ( 'closed' === $tp_feedback_status ) {
			$feedback->set_status( 'fixed' );
		}
	}

	/**
	 * @param string $endpoint
	 *
	 * @return string
	 */
	private function get_communication_error_message( $endpoint ) {
		$active_service = $this->get_tp_client()->services()->get_active();
		$service_name = isset( $active_service->name ) ? $active_service->name : esc_html( 'Translation Service', 'sitepress' );

		if ( 'send' === $endpoint ) {
			$error_message = sprintf(
				esc_html__( 'Could not send the report to %s.', 'sitepress' ),
				$service_name
			);

			$error_message .= ' ' . sprintf(
					esc_html__( "This means that %s isn't yet aware of the problem in the translation and cannot fix it.", 'sitepress' ),
					$service_name
				);
		} else {
			$error_message = sprintf(
				esc_html__( 'Could not fetch the status from %s.', 'sitepress' ),
				$service_name
			);
		}

		$error_message .= ' ' . sprintf(
			esc_html__( "Let's get it working for you. Please contact %1sWPML support%2s and give them the following error details:", 'sitepress' ),
			'<a href="https://wpml.org/forums/forum/english-support/" target="_blank">',
			'</a>'
		);

		$error_message .= '<br><div class="js-wpml-tf-error-details"><a href="#">' .
		                  esc_html__( 'Show details', 'sitepress' ) . '</a>' .
		                  '<pre style="display:none;">' . esc_html( $this->get_tp_client()->feedback()->get_error_message() ) .
		                  '</pre></div>';

		return $error_message;
	}

	/**
	 * @return null|WPML_TP_Client
	 *
	 * @throws WPML_TF_Feedback_Update_Exception
	 */
	private function get_tp_client() {
		if ( ! $this->tp_client && $this->tp_client_factory ) {
			$this->tp_client = $this->tp_client_factory->create();

			if ( ! $this->tp_client ) {
				throw new WPML_TF_Feedback_Update_Exception(
					esc_html__( 'WPML cannot communicate with the remote translation service. Please make sure WPML Translation Management is active.', 'sitepress' )
				);
			}
		}

		return $this->tp_client;
	}
}
