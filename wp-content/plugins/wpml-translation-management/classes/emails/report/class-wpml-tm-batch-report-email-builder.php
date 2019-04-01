<?php

/**
 * Class WPML_TM_Batch_Report_Email
 */
class WPML_TM_Batch_Report_Email_Builder {

	/**
	 * @var WPML_TM_Batch_Report
	 */
	private $batch_report;

	/**
	 * @var array
	 */
	private $emails;

	/**
	 * @var WPML_TM_Email_Jobs_Summary_View
	 */
	private $email_template;

	/**
	 * WPML_TM_Notification_Batch_Email constructor.
	 *
	 * @param WPML_TM_Batch_Report            $batch_report
	 * @param WPML_TM_Email_Jobs_Summary_View $email_template
	 */
	public function __construct( WPML_TM_Batch_Report $batch_report, WPML_TM_Email_Jobs_Summary_View $email_template ) {
		$this->batch_report   = $batch_report;
		$this->email_template = $email_template;
		$this->emails         = array();
	}

	/**
	 * @param array $batch_jobs
	 */
	public function prepare_assigned_jobs_emails( $batch_jobs ) {
		foreach ( $batch_jobs as $translator_id => $language_pairs ) {

			if ( 0 !== $translator_id ) {

				$translator = get_userdata( $translator_id );
				$title            = __( 'You have been assigned to new translation job(s):', 'wpml-translation-management' );
				$render_jobs_list = $this->email_template->render_jobs_list( $language_pairs, $translator_id, $title );

				if ( null === $render_jobs_list ) {
					continue;
				}

				$body       = $this->email_template->render_header( $translator->display_name );
				$body      .= $render_jobs_list;

				$assigned_jobs  = $this->email_template->get_assigned_jobs();
				$title_singular = __( 'There is 1 job, which you can take (not specifically assigned to you):', 'wpml-translation-management' );
				$title_plural   = __( 'There are %s jobs, which you can take (not specifically assigned to you):', 'wpml-translation-management' );
				$unassigned_jobs_body = $this->email_template->render_jobs_list(
					$this->batch_report->get_unassigned_jobs(),
					$translator_id,
					$title_singular,
					$title_plural
				);

				if ( null !== $unassigned_jobs_body ) {
					$body .= $unassigned_jobs_body;
				}

				$body          .= $this->email_template->render_footer();
				$email['body']  = $body;
				$email          = $this->add_attachments( $email, $assigned_jobs );
				$this->emails[] = array(
					'translator_id' => $translator->ID,
					'email'         => $translator->user_email,
					'subject'       => $this->get_subject_assigned_job(),
					'body'          => $body,
					'attachment'    => array_key_exists( 'attachment', $email ) ? $email['attachment'] : array(),
				);
			}
		}
	}

	/**
	 * @param array $batch_jobs
	 */
	public function prepare_unassigned_jobs_emails( $batch_jobs ) {
		if ( array_key_exists( 0, $batch_jobs ) ) {

			$unassigned_jobs = $batch_jobs[0];
			$translators     = $this->batch_report->get_unassigned_translators();
			$title_singular  = __( 'There is 1 job waiting for a translator:', 'wpml-translation-management' );
			$title_plural    = __( 'There are %s jobs waiting for a translator:', 'wpml-translation-management' );

			foreach ( $translators as $translator ) {

				$translator_user = get_userdata( $translator );
				$render_jobs_list = $this->email_template->render_jobs_list( $unassigned_jobs, $translator_user->ID, $title_singular, $title_plural );

				if ( null !== $render_jobs_list ) {
					$body            = $this->email_template->render_header( $translator_user->display_name );
					$body           .= $render_jobs_list;
					$body           .= $this->email_template->render_footer();

					$this->emails[] = array(
						'translator_id' => $translator_user->ID,
						'email'         => $translator_user->user_email,
						'subject'       => $this->get_subject_unassigned_job(),
						'body'          => $body,
					);
				}
			}
		}
	}

	/**
	 * @param array $email
	 * @param array $jobs
	 *
	 * @return array
	 */
	private function add_attachments( $email, $jobs ) {
		$attachments = array();
		foreach ( $jobs as $job ) {
			if ( 'post' === $job['type'] ) {
				$email = apply_filters( 'wpml_new_job_notification', $email, $job['job_id'] );
				if ( array_key_exists( 'attachment', $email ) ) {
					$attachments[] = $email['attachment'];
				}
			}
		}

		if ( $attachments ) {
			$attachments          = apply_filters( 'wpml_new_job_notification_attachments', $attachments );
			if ( count( $attachments ) > 0 ) {
				$attachment_values   = array_values( $attachments );
				$email['attachment'] = $attachment_values[0];
			}
		}
		
		return $email;
	}

	/**
	 * @return string
	 */
	private function get_subject_assigned_job() {
		return sprintf( __( 'New translation job from %s', 'wpml-translation-management' ), get_bloginfo( 'name' ) );
	}

	/**
	 * @return string
	 */
	private function get_subject_unassigned_job() {
		return sprintf( __( 'Job waiting for a translator in %s', 'wpml-translation-management' ), get_bloginfo( 'name' ) );
	}

	/**
	 * @return array
	 */
	public function get_emails() {
		return $this->emails;
	}
}