<?php

/**
 * @author OnTheGo Systems
 */
class WPML_XML_Config_Log_Notice {
	const NOTICE_ERROR_GROUP = 'wpml-config-update';
	const NOTICE_ERROR_ID    = 'wpml-config-update-error';

	/** @var WPML_Config_Update_Log */
	private $log;

	public function __construct( WPML_Log $log ) {
		$this->log = $log;
	}

	public function add_hooks() {
		add_action( 'wpml_loaded', array( $this, 'refresh_notices' ) );
	}

	public function refresh_notices() {
		$notices = wpml_get_admin_notices();

		if ( $this->log->is_empty() ) {
			$notices->remove_notice( self::NOTICE_ERROR_GROUP, self::NOTICE_ERROR_ID );

			return;
		}

		$text = '<p>' . esc_html__( 'WPML could not load configuration files, which your site needs.', 'sitepress' ) . '</p>';

		$notice = $notices->create_notice( self::NOTICE_ERROR_ID, $text, self::NOTICE_ERROR_GROUP );
		$notice->set_css_class_types( array( 'error' ) );

		$log_url = add_query_arg( array( 'page' => WPML_Config_Update_Log::get_support_page_log_section() ), get_admin_url( null, 'admin.php#xml-config-log' ) );

		$show_logs = $notices->get_new_notice_action( __( 'Detailed error log', 'sitepress' ), $log_url );

		$return_url = null;
		if ( $this->is_admin_user_action() ) {
			$admin_uri  = preg_replace( '#^/wp-admin/#', '', $_SERVER['SCRIPT_NAME'] );
			$return_url = get_admin_url( null, $admin_uri );

			$return_url_qs = $_GET;
			unset( $return_url_qs[ self::NOTICE_ERROR_GROUP . '-action' ], $return_url_qs[ self::NOTICE_ERROR_GROUP . '-nonce' ] );
			$return_url = add_query_arg( $return_url_qs, $return_url );
		}

		$retry_url = add_query_arg( array( self::NOTICE_ERROR_GROUP . '-action' => 'wpml_xml_update_refresh', self::NOTICE_ERROR_GROUP . '-nonce' => wp_create_nonce( 'wpml_xml_update_refresh' ) ), $return_url );
		$retry     = $notices->get_new_notice_action( __( 'Retry', 'sitepress' ), $retry_url, false, false, true );

		$notice->add_action( $show_logs );
		$notice->add_action( $retry );
		$notice->set_dismissible( true );
		$notice->set_restrict_to_page_prefixes( array(
			                                        'sitepress-multilingual-cms',
			                                        'wpml-translation-management',
			                                        'wpml-package-management',
			                                        'wpml-string-translation',
		                                        ) );

		$notice->set_restrict_to_screen_ids( array( 'dashboard', 'plugins', 'themes' ) );
		$notice->add_exclude_from_page( WPML_Config_Update_Log::get_support_page_log_section() );
		$notices->add_notice( $notice );
	}

	/**
	 * @return bool
	 */
	private function is_admin_user_action() {
		return is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX )
		       && ( 'heartbeat' !== filter_input( INPUT_POST, 'action', FILTER_SANITIZE_STRING ) )
		       && ( ! defined( 'DOING_CRON' ) || ! DOING_CRON );
	}
}
