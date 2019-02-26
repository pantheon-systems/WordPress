<?php

/**
 * @return WPML_TM_Element_Translations
 */
function wpml_tm_load_element_translations() {
	global $wpml_tm_element_translations, $wpdb, $wpml_post_translations, $wpml_term_translations;

	if ( ! isset( $wpml_tm_element_translations ) ) {
		require_once WPML_TM_PATH . '/inc/core/wpml-tm-element-translations.class.php';
		$tm_records                   = new WPML_TM_Records( $wpdb, $wpml_post_translations, $wpml_term_translations );
		$wpml_tm_element_translations = new WPML_TM_Element_Translations( $tm_records );
		$wpml_tm_element_translations->init_hooks();
	}

	return $wpml_tm_element_translations;
}

function wpml_tm_load_status_display_filter() {
	global $wpml_tm_status_display_filter, $iclTranslationManagement, $sitepress, $wpdb;

	$blog_translators = wpml_tm_load_blog_translators();
	$tm_api           = new WPML_TM_API( $blog_translators, $iclTranslationManagement );
	$tm_api->init_hooks();
	if ( ! isset( $wpml_tm_status_display_filter ) ) {
		$status_helper                 = wpml_get_post_status_helper();
		$job_factory                   = wpml_tm_load_job_factory();
		$wpml_tm_status_display_filter = new WPML_TM_Translation_Status_Display( $wpdb,
		                                                                         $sitepress,
		                                                                         $status_helper,
		                                                                         $job_factory,
		                                                                         $tm_api );
	}

	$wpml_tm_status_display_filter->init();
}

/**
 * @return WPML_Translation_Proxy_Basket_Networking
 */
function wpml_tm_load_basket_networking() {
	global $iclTranslationManagement, $wpdb;

	require_once WPML_TM_PATH . '/inc/translation-proxy/wpml-translationproxy-basket-networking.class.php';

	$basket = new WPML_Translation_Basket( $wpdb );

	return new WPML_Translation_Proxy_Basket_Networking( $basket, $iclTranslationManagement );
}

/**
 * @return WPML_Translation_Proxy_Networking
 */
function wpml_tm_load_tp_networking() {
	global $wpml_tm_tp_networking;

	if ( ! isset( $wpml_tm_tp_networking ) ) {
		$tp_lock_factory       = new WPML_TP_Lock_Factory();
		$wpml_tm_tp_networking = new WPML_Translation_Proxy_Networking( new WP_Http(), $tp_lock_factory->create() );
	}

	return $wpml_tm_tp_networking;
}

/**
 * @return WPML_TM_Blog_Translators
 */
function wpml_tm_load_blog_translators() {
	global $wpdb, $sitepress, $wpml_post_translations, $wpml_term_translations;

	$tm_records = new WPML_TM_Records( $wpdb, $wpml_post_translations, $wpml_term_translations );
	$translator_records = new WPML_Translator_Records( $wpdb, new WPML_WP_User_Query_Factory() );

	return new WPML_TM_Blog_Translators( $sitepress, $tm_records, $translator_records );
}

/**
 * @return WPML_TM_Mail_Notification
 */
function wpml_tm_init_mail_notifications() {
	global $wpml_tm_mailer, $sitepress, $wpdb, $iclTranslationManagement, $wpml_translation_job_factory, $wp_api;

	if ( null === $wp_api ) {
		$wp_api = new WPML_WP_API();
	}

	if ( is_admin() ) {
		$blog_translators            = wpml_tm_load_blog_translators();
		$email_twig_factory          = new WPML_TM_Email_Twig_Template_Factory();
		$batch_report                = new WPML_TM_Batch_Report( $blog_translators );
		$batch_report_email_template = new WPML_TM_Email_Jobs_Summary_View( $email_twig_factory->create(),
		                                                                    $blog_translators,
		                                                                    $sitepress );
		$batch_report_email_builder  = new WPML_TM_Batch_Report_Email_Builder( $batch_report,
		                                                                       $batch_report_email_template );
		$batch_report_email_process  = new WPML_TM_Batch_Report_Email_Process( $batch_report,
		                                                                       $batch_report_email_builder );
		$batch_report_hooks          = new WPML_TM_Batch_Report_Hooks( $batch_report, $batch_report_email_process );
		$batch_report_hooks->add_hooks();

		$wpml_tm_unsent_jobs = new WPML_TM_Unsent_Jobs( $blog_translators, $sitepress );
		$wpml_tm_unsent_jobs->add_hooks();

		$wpml_tm_unsent_jobs_notice       = new WPML_TM_Unsent_Jobs_Notice( $wp_api );
		$wpml_tm_unsent_jobs_notice_hooks = new WPML_TM_Unsent_Jobs_Notice_Hooks( $wpml_tm_unsent_jobs_notice,
		                                                                          $wp_api,
		                                                                          WPML_Notices::DISMISSED_OPTION_KEY );
		$wpml_tm_unsent_jobs_notice_hooks->add_hooks();

		$user_jobs_notification_settings = new WPML_User_Jobs_Notification_Settings();
		$user_jobs_notification_settings->add_hooks();

		$email_twig_factory    = new WPML_Twig_Template_Loader( array( WPML_TM_PATH . '/templates/user-profile/' ) );
		$notification_template = new  WPML_User_Jobs_Notification_Settings_Template( $email_twig_factory->get_template() );

		$user_jobs_notification_settings_render = new WPML_User_Jobs_Notification_Settings_Render( $notification_template );
		$user_jobs_notification_settings_render->add_hooks();
	}

	if ( ! isset( $wpml_tm_mailer ) ) {
		$iclTranslationManagement = $iclTranslationManagement ? $iclTranslationManagement : wpml_load_core_tm();
		if ( empty( $iclTranslationManagement->settings ) ) {
			$iclTranslationManagement->init();
		}
		$settings = isset( $iclTranslationManagement->settings['notification'] )
			? $iclTranslationManagement->settings['notification'] : array();

		$email_twig_factory      = new WPML_TM_Email_Twig_Template_Factory();
		$email_notification_view = new WPML_TM_Email_Notification_View( $email_twig_factory->create() );

		$has_active_remote_service = TranslationProxy::is_current_service_active_and_authenticated();

		$wpml_tm_mailer = new WPML_TM_Mail_Notification( $sitepress,
		                                                 $wpdb,
		                                                 $wpml_translation_job_factory,
		                                                 $email_notification_view,
		                                                 $settings,
		                                                 $has_active_remote_service

		);
	}
	$wpml_tm_mailer->init();

	return $wpml_tm_mailer;
}

/**
 * @return WPML_Dashboard_Ajax
 */
function wpml_tm_load_tm_dashboard_ajax() {
	global $wpml_tm_dashboard_ajax, $sitepress;

	if ( ! isset( $wpml_tm_dashboard_ajax ) ) {
		require_once WPML_TM_PATH . '/menu/dashboard/wpml-tm-dashboard-ajax.class.php';
		$wpml_tm_dashboard_ajax = new WPML_Dashboard_Ajax( new WPML_Super_Globals_Validation() );

		if ( defined( 'OTG_TRANSLATION_PROXY_URL' ) && defined( 'ICL_SITEPRESS_VERSION' ) ) {
			$wpml_tp_communication = new WPML_TP_Communication( OTG_TRANSLATION_PROXY_URL, new WP_Http() );
			$wpml_tp_api           = new WPML_TP_API( $wpml_tp_communication, '1.1', new WPML_TM_Log() );

			$translation_service  = $sitepress->get_setting( 'translation_service' );
			$translation_projects = $sitepress->get_setting( 'icl_translation_projects' );
			$tp_project           = new WPML_TP_Project( $translation_service, $translation_projects );
			$wpml_tp_api_ajax     = new WPML_TP_Refresh_Language_Pairs( $wpml_tp_api, $tp_project );
			$wpml_tp_api_ajax->add_hooks();
		}
	}

	return $wpml_tm_dashboard_ajax;
}

function wpml_tm_load_and_intialize_dashboard_ajax() {
	if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
		if ( defined( 'DOING_AJAX' ) ) {
			$wpml_tm_dashboard_ajax = wpml_tm_load_tm_dashboard_ajax();
			add_action( 'init', array( $wpml_tm_dashboard_ajax, 'init_ajax_actions' ) );
		} elseif ( is_admin() && isset( $_GET['page'] ) && $_GET['page'] == WPML_TM_FOLDER . '/menu/main.php'
		           && ( ! isset( $_GET['sm'] ) || $_GET['sm'] === 'dashboard' ) ) {
			$wpml_tm_dashboard_ajax = wpml_tm_load_tm_dashboard_ajax();
			add_action( 'wpml_tm_scripts_enqueued', array( $wpml_tm_dashboard_ajax, 'enqueue_js' ) );
		}
	}
}

add_action( 'plugins_loaded', 'wpml_tm_load_and_intialize_dashboard_ajax' );

/**
 * @return WPML_Translation_Job_Factory
 */
function wpml_tm_load_job_factory() {
	global $wpml_translation_job_factory, $wpdb, $wpml_post_translations, $wpml_term_translations;

	if ( ! $wpml_translation_job_factory ) {
		$tm_records                   = new WPML_TM_Records( $wpdb, $wpml_post_translations, $wpml_term_translations );
		$wpml_translation_job_factory = new WPML_Translation_Job_Factory( $tm_records );
		$wpml_translation_job_factory->init_hooks();
	}

	return $wpml_translation_job_factory;
}

function tm_after_load() {
	global $wpml_tm_translation_status, $wpdb, $wpml_post_translations, $wpml_term_translations;

	if ( ! isset( $wpml_tm_translation_status ) ) {
		require_once WPML_TM_PATH . '/inc/actions/wpml-tm-action-helper.class.php';
		require_once WPML_TM_PATH . '/inc/translation-jobs/collections/wpml-abstract-job-collection.class.php';
		require_once WPML_TM_PATH . '/inc/translation-proxy/wpml-translation-basket.class.php';
		require_once WPML_TM_PATH . '/inc/translation-jobs/wpml-translation-batch.class.php';
		require_once WPML_TM_PATH . '/inc/translation-proxy/translationproxy.class.php';
		require_once WPML_TM_PATH . '/inc/ajax.php';
		wpml_tm_load_job_factory();
		wpml_tm_init_mail_notifications();
		wpml_tm_load_element_translations();
		$tm_records                 = new WPML_TM_Records( $wpdb, $wpml_post_translations, $wpml_term_translations );
		$wpml_tm_translation_status = new WPML_TM_Translation_Status( $tm_records );
		$wpml_tm_translation_status->init();
		add_action( 'wpml_pre_status_icon_display', 'wpml_tm_load_status_display_filter' );
		require_once WPML_TM_PATH . '/inc/wpml-private-actions.php';
	}
}

/**
 * @return WPML_TM_Records
 */
function wpml_tm_get_records() {
	global $wpdb, $wpml_post_translations, $wpml_term_translations;

	return new WPML_TM_Records( $wpdb, $wpml_post_translations, $wpml_term_translations );
}

/**
 * @return WPML_TM_Xliff_Frontend
 */
function setup_xliff_frontend() {
	global $sitepress, $xliff_frontend;

	$job_factory    = wpml_tm_load_job_factory();
	$xliff_frontend = new WPML_TM_Xliff_Frontend( $job_factory, $sitepress );
	add_action( 'init', array( $xliff_frontend, 'init' ), $xliff_frontend->get_init_priority() );

	return $xliff_frontend;
}

/**
 * @param int $job_id
 *
 * @return WPML_TM_ATE_Models_Job_Create
 */
function wpml_tm_create_ATE_job_creation_model( $job_id ) {
	$job_factory     = wpml_tm_load_job_factory();
	$translation_job = $job_factory->get_translation_job( $job_id, false, 0, true );

	$job                        = new WPML_TM_ATE_Models_Job_Create();
	$job->source_id             = $job_id;
	$job->source_language->code = $translation_job->get_source_language_code();
	$job->source_language->name = $translation_job->get_source_language_code( true );
	$job->target_language->code = $translation_job->get_language_code();
	$job->target_language->name = $translation_job->get_language_code( true );
	$job->deadline              = strtotime( $translation_job->get_deadline_date() );

	$job->permalink = '#';
	if ( 'Post' === $translation_job->get_type() ) {
		$job->permalink = get_permalink( $translation_job->get_original_element_id() );
	}

	$job->notify_enabled = true;
	$job->notify_url     = wpml_tm_get_wpml_rest()->get_discovery_url() . '/ate/jobs/receive/' . $job_id;

	$job->site_identifier = wpml_get_site_id();

	$encoded_xliff = base64_encode( wpml_tm_get_job_xliff( $job_id ) );

	$job->file->type = 'data:application/x-xliff;base64';
	$job->file->name = $translation_job->get_title();

	$job->file->content = $encoded_xliff;

	return $job;
}

/**
 * @param int $job_id
 *
 * @return string
 */
function wpml_tm_get_job_xliff( $job_id ) {
	static $xliff_writer;

	if ( ! $xliff_writer ) {
		$job_factory  = wpml_tm_load_job_factory();
		$xliff_writer = new WPML_TM_Xliff_Writer( $job_factory );
	}

	return $xliff_writer->generate_job_xliff( $job_id );
}

function wpml_tm_get_wpml_rest() {
	static $wpml_rest;

	if ( ! $wpml_rest ) {
		$http      = new WP_Http();
		$wpml_rest = new WPML_Rest( $http );
	}

	return $wpml_rest;
}