<?php

/**
 * Class WPML_TM_Mail_Notification
 */
class WPML_TM_Mail_Notification {

	const JOB_COMPLETE_TEMPLATE = 'notification/job-completed.twig';

	private $mail_cache = array();
	private $process_mail_queue;

	/** @var wpdb $wpdb */
	private $wpdb;

	/** @var SitePress $sitepress */
	private $sitepress;

	/** @var WPML_Translation_Job_Factory $job_factory */
	private $job_factory;

	/** @var WPML_TM_Email_Notification_View $email_view */
	private $email_view;

	/** @var array $notification_settings */
	private $notification_settings;

	/** @var bool $has_active_remote_service */
	private $has_active_remote_service;

	public function __construct(
		SitePress $sitepress,
		wpdb $wpdb,
		WPML_Translation_Job_Factory $job_factory,
		WPML_TM_Email_Notification_View $email_view,
		array $notification_settings,
		$has_active_remote_service
	) {
		$this->wpdb                  = $wpdb;
		$this->sitepress             = $sitepress;
		$this->job_factory           = $job_factory;
		$this->email_view            = $email_view;
		$this->notification_settings = array_merge( array( 'resigned' => 0, 'completed' => 0 ),
		                                            $notification_settings );
		$this->has_active_remote_service = $has_active_remote_service;
	}

	public function init() {
		add_action( 'wpml_tm_empty_mail_queue', array( $this, 'send_queued_mails' ), 10, 0 );

		if ( $this->should_send_email_on_update() ) {
			add_action( 'wpml_tm_complete_job_notification', array( $this, 'wpml_tm_job_complete_mail' ), 10, 2 );
		}

		add_action( 'wpml_tm_remove_job_notification', array( $this, 'translator_removed_mail' ), 10, 2 );
		add_action( 'wpml_tm_resign_job_notification', array( $this, 'translator_resign_mail' ), 10, 2 );
		add_action( 'icl_ajx_custom_call', array( $this, 'send_queued_mails' ), 10, 0 );
		add_action( 'icl_pro_translation_completed', array( $this, 'send_queued_mails' ), 10, 0 );
	}

	/**
	 * @return bool
	 */
	private function should_send_email_on_update() {
		return ! isset( $this->notification_settings[ WPML_TM_Emails_Settings::COMPLETED_JOB_FREQUENCY ] ) ||
		       ( isset( $this->notification_settings[ WPML_TM_Emails_Settings::COMPLETED_JOB_FREQUENCY ] ) &&
		         WPML_TM_Emails_Settings::NOTIFY_IMMEDIATELY === (int) $this->notification_settings[ WPML_TM_Emails_Settings::COMPLETED_JOB_FREQUENCY ] );
	}

	public function send_queued_mails() {
		$tj_url = admin_url( 'admin.php?page=' . WPML_TM_FOLDER . '/menu/translations-queue.php' );

		foreach ( $this->mail_cache as $type => $mail_to_send ) {
			foreach ( $mail_to_send as $to => $subjects ) {
				$headers      = '';
				$body_to_send = '';

				foreach ( $subjects as $subject => $content ) {
					$body = $content['body'];
					$home_url = get_home_url();

					if ( 'completed' === $type ) {
						$headers = array(
							'Content-type: text/html; charset=UTF-8',
						);

						$body_to_send .= $body[0];

					} else {
						$body_to_send .= $body_to_send . "\n\n" . implode( "\n\n\n\n", $body ) . "\n\n\n\n";

						if ( $type === 'translator' ) {
							$footer = sprintf(
								          __( 'You can view your other translation jobs here: %s', 'wpml-translation-management' ),
								          $tj_url
							          ) . "\n\n--\n";
							$footer .= sprintf(
								__(
									"This message was automatically sent by Translation Management running on %s. To stop receiving these notifications contact the system administrator at %s.\n\nThis email is not monitored for replies.",
									'wpml-translation-management'
								),
								get_bloginfo( 'name' ),
								$home_url
							);
						} else {
							$footer = "\n--\n" . sprintf(
									__(
										"This message was automatically sent by Translation Management running on %s. To stop receiving these notifications, go to Notification Settings, or contact the system administrator at %s.\n\nThis email is not monitored for replies.",
										'wpml-translation-management'
									),
									get_bloginfo( 'name' ),
									$home_url
								);
						}

						$body_to_send .= $footer;
					}

					$attachments  = isset( $content['attachment'] ) ? $content['attachment'] : array();
					$attachments  = apply_filters( 'wpml_new_job_notification_attachments', $attachments );

					/**
					 * @deprecated Use 'wpml_new_job_notification_attachments' instead
					 */
					$attachments  = apply_filters( 'WPML_new_job_notification_attachments', $attachments );
					$this->sitepress->get_wp_api()->wp_mail( $to, $subject, $body_to_send, $headers, $attachments );
				}
			}
		}
		$this->mail_cache         = array();
		$this->process_mail_queue = false;
	}

	/**
	 * @param WPML_Translation_Job|int $job_id
	 * @param bool|false               $update
	 *
	 * @return false|array representation of the email to be sent
	 */
	public function wpml_tm_job_complete_mail( $job_id, $update = false ) {
		if ( $this->notification_settings['completed'] != ICL_TM_NOTIFICATION_IMMEDIATELY ) {
			return false;
		}

		list( $manager_id, $job ) = $this->get_mail_elements( $job_id );

		if ( ! $job || ( $manager_id && $manager_id == $job->get_translator_id() ) ) {
			return false;
		}

		/** @var WPML_Translation_Job $job */
		$manager       = new WP_User( $manager_id );
		$translator    = new WP_User( $job->get_translator_id() );
		$user_language = $this->sitepress->get_user_admin_language( $manager->ID );

		$mail = array(
			'to' => $manager->display_name . ' <' . $manager->user_email . '>',
		);

		$this->sitepress->switch_locale( $user_language );

		list( $lang_from, $lang_to ) = $this->get_lang_to_from( $job, $user_language );

		$model = array(
			'view_jobs_text' => __( 'View translation jobs', 'wpml-translation-management' ),
			'username'       => $manager->display_name,
			'lang_from'      => $lang_from,
			'lang_to'        => $lang_to,
		);

		$document_title = $job->get_title();

		if ( 'string' !== strtolower( $job->get_type() ) ) {
			/** @var WPML_Post_Translation_Job $job */
			$model['translation_jobs_url'] = admin_url( 'admin.php?page=' . WPML_TM_FOLDER . '/menu/main.php&sm=jobs' );
			$document_title                = '<a href="' . $job->get_url( true ) . '">' . $document_title . '</a>';
			$model                         = $this->update_model_for_deadline( $model, $job );
		}

		if ( $update ) {
			$mail['subject']  = sprintf(
				__( 'Translator has updated translation job for %s', 'wpml-translation-management' ),
				get_bloginfo( 'name' )
			);
			$body_placeholder = esc_html__(
				'The translator %1$shas updated the translation job for "%2$s" from %3$s to %4$s.',
				'wpml-translation-management'
			);
		} else {
			$mail['subject']  = sprintf(
				__( 'Translator has completed translation job for %s', 'wpml-translation-management' ),
				get_bloginfo( 'name' ) );
			$body_placeholder = esc_html__(
				'The translator %1$shas completed the translation job for "%2$s" from %3$s to %4$s.',
				'wpml-translation-management'
			);
		}
		$translator_name  = ! empty( $translator->display_name ) ? '(' . $translator->display_name . ') ' : '';
		$model['message'] = sprintf( $body_placeholder, $translator_name, $document_title, $lang_from, $lang_to );
		$model['needs_help'] = array(
			'title' => __( 'Need help with translation?', 'wpml-translation-management' ),
			'options_or' => __( 'or', 'wpml-translation-management' ),
			'translators_link' => admin_url( 'admin.php?page=' . WPML_TM_FOLDER . '/menu/main.php&sm=translators' ),
			'translators_text' => __( 'Manage your translators', 'wpml-translation-management' ),
			'translation_services_link' => admin_url( 'admin.php?page=' . WPML_TM_FOLDER . '/menu/main.php&sm=translation-services' ),
			'translation_services_text' => __( 'try a translation service', 'wpml-translation-management' ),
		);
		$model['overdue_job'] = ! $job->is_completed_on_time();

		$mail['body']     = $this->email_view->render_model( $model, self::JOB_COMPLETE_TEMPLATE );
		$mail['type']     = 'completed';
		$this->enqueue_mail( $mail );

		$this->sitepress->switch_locale();

		return $mail;
	}

	/**
	 * @param array                        $model
	 * @param WPML_Element_Translation_Job $job
	 *
	 * @return string
	 */
	private function update_model_for_deadline( array $model,  WPML_Element_Translation_Job $job ) {
		if ( $job->is_completed_on_time() ) {
			$model['deadline_status'] = __( 'The translation job was completed on time.', 'wpml-translation-management' );
		} else {
			$overdue_days = $job->get_number_of_days_overdue();

			$model['deadline_status'] = sprintf(
				_n(
					'This translation job is overdue by %s day.',
					'This translation job is overdue by %s days.',
					$overdue_days,
					'wpml-translation-management'
				),
				$overdue_days
			);

			if ( $overdue_days >= 7 ) {
				$model['promote_translation_services'] = ! $this->has_active_remote_service;
			}
		}

		return $model;
	}

	/**
	 * @param int                      $translator_id
	 * @param WPML_Translation_Job|int $job
	 *
	 * @return bool
	 */
	public function translator_removed_mail( $translator_id, $job ) {
		/** @var WPML_Translation_Job $job */
		list( $manager_id, $job ) = $this->get_mail_elements( $job );
		if ( ! $job || $manager_id == $translator_id ) {
			return false;
		}
		$translator    = new WP_User( $translator_id );
		$manager       = new WP_User( $manager_id );
		$user_language = $this->sitepress->get_user_admin_language( $manager->ID );
		$doc_title     = $job->get_title();
		$this->sitepress->switch_locale( $user_language );
		list( $lang_from, $lang_to ) = $this->get_lang_to_from( $job, $user_language );
		$mail['to']      = $translator->display_name . ' <' . $translator->user_email . '>';
		$mail['subject'] = sprintf( __( 'Removed from translation job on %s', 'wpml-translation-management' ), get_bloginfo( 'name' ) );
		$mail['body']    = sprintf(
			__( 'You have been removed from the translation job "%s" for %s to %s.', 'wpml-translation-management' ),
			$doc_title,
			$lang_from,
			$lang_to
		);
		$mail['type']    = 'translator';
		$this->enqueue_mail( $mail );
		$this->sitepress->switch_locale();

		return $mail;
	}

	/**
	 * @param int                      $translator_id
	 * @param int|WPML_Translation_Job $job_id
	 *
	 * @return array|bool
	 */
	public function translator_resign_mail( $translator_id, $job_id ) {
		/** @var WPML_Translation_Job $job */
		list( $manager_id, $job ) = $this->get_mail_elements( $job_id );
		if ( ! $job || $manager_id == $translator_id ) {
			return false;
		}
		$translator    = new WP_User( $translator_id );
		$manager       = new WP_User( $manager_id );
		$tj_url        = admin_url( 'admin.php?page=' . WPML_TM_FOLDER . '/menu/main.php&sm=jobs' );
		$doc_title     = $job->get_title();
		$user_language = $this->sitepress->get_user_admin_language( $manager->ID );
		$this->sitepress->switch_locale( $user_language );
		list( $lang_from, $lang_to ) = $this->get_lang_to_from( $job, $user_language );
		$mail = array();
		if ( $this->notification_settings['resigned'] == ICL_TM_NOTIFICATION_IMMEDIATELY ) {
			$mail['to']         = $manager->display_name . ' <' . $manager->user_email . '>';
			$mail['subject']    = sprintf( __( 'Translator has resigned from job on %s', 'wpml-translation-management' ),
			                               get_bloginfo( 'name' ) );
			$original_doc_title = $doc_title ? $doc_title : __( 'Deleted', 'wpml-translation-management');
			$mail['body']       = sprintf(
				__(
					'Translator %s has resigned from the translation job "%s" for %s to %s.%sView translation jobs: %s',
					'wpml-translation-management'
				),
				$translator->display_name,
				$original_doc_title,
				$lang_from,
				$lang_to,
				"\n",
				$tj_url
			);
			$mail['type']       = 'admin';
			$this->enqueue_mail( $mail );
		}
		//restore locale
		$this->sitepress->switch_locale();

		return $mail;
	}

	private function enqueue_mail( $mail ) {
		if ( $mail !== 'empty_queue' ) {
			$this->mail_cache[ $mail['type'] ][ $mail['to'] ][ $mail['subject'] ]['body'][] = $mail['body'];
			if ( isset( $mail['attachment'] ) ) {
				$this->mail_cache[ $mail['type'] ][ $mail['to'] ][ $mail['subject'] ]['attachment'][] = $mail['attachment'];
			}
			$this->process_mail_queue = true;
		}
	}

	/**
	 * @param int|WPML_Translation_Job $job_id
	 *
	 * @return array
	 */
	private function get_mail_elements( $job_id ) {
		$job = is_object( $job_id ) ? $job_id : $this->job_factory->get_translation_job( $job_id,
		                                                                                 false,
		                                                                                 0,
		                                                                                 true );
		if ( is_object( $job ) ) {
			$data       = $job->get_basic_data();
			$manager_id = isset( $data->manager_id ) ? $data->manager_id : - 1;
		} else {
			$job        = false;
			$manager_id = false;
		}

		return array( $manager_id, $job );
	}

	/**
	 * @param WPML_Translation_Job $job
	 * @param string               $user_language
	 *
	 * @return array
	 */
	private function get_lang_to_from( $job, $user_language ) {
		$sql       = "SELECT name FROM {$this->wpdb->prefix}icl_languages_translations WHERE language_code=%s AND display_language_code=%s LIMIT 1";
		$lang_from = $this->wpdb->get_var( $this->wpdb->prepare( $sql,
		                                                         $job->get_source_language_code(),
		                                                         $user_language ) );
		$lang_to   = $this->wpdb->get_var( $this->wpdb->prepare( $sql, $job->get_language_code(), $user_language ) );

		return array( $lang_from, $lang_to );
	}
}