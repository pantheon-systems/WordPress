<?php
/**
 * Class Strong_Testimonials_Settings_Form
 */
class Strong_Testimonials_Settings_Form {

	const TAB_NAME = 'form';

	const OPTION_NAME = 'wpmtst_form_options';

	const GROUP_NAME = 'wpmtst-form-group';

	/**
	 * Strong_Testimonials_Settings_Form constructor.
	 */
	public function __construct() {}

	/**
	 * Initialize.
	 */
	public static function init() {
		self::add_actions();
	}

	/**
	 * Add actions and filters.
	 */
	public static function add_actions() {
	    add_action( 'wpmtst_register_settings', array( __CLASS__, 'register_settings' ) );
		add_action( 'wpmtst_settings_tabs', array( __CLASS__, 'register_tab' ), 2, 2 );
		add_filter( 'wpmtst_settings_callbacks', array( __CLASS__, 'register_settings_page' ) );

		add_action( 'wp_ajax_wpmtst_restore_default_messages', array( __CLASS__, 'restore_default_messages_function' ) );
		add_action( 'wp_ajax_wpmtst_restore_default_message', array( __CLASS__, 'restore_default_message_function' ) );

		add_action( 'wp_ajax_wpmtst_add_recipient', array( __CLASS__, 'add_recipient' ) );
	}

	/**
	 * Register settings tab.
	 *
	 * @param $active_tab
	 * @param $url
	 */
	public static function register_tab( $active_tab, $url ) {
		printf( '<a href="%s" class="nav-tab %s">%s</a>',
			esc_url( add_query_arg( 'tab', self::TAB_NAME, $url ) ),
			esc_attr( $active_tab == self::TAB_NAME ? 'nav-tab-active' : '' ),
			__( 'Form', 'strong-testimonials' )
		);
	}

	/**
	 * Register settings.
	 */
	public static function register_settings() {
		register_setting( self::GROUP_NAME, self::OPTION_NAME, array( __CLASS__, 'sanitize_options' ) );
	}

	/**
	 * Register settings page.
	 *
	 * @param $pages
	 *
	 * @return mixed
	 */
	public static function register_settings_page( $pages ) {
		$pages[ self::TAB_NAME ] = array( __CLASS__, 'settings_page' );
		return $pages;
	}

	/**
	 * Print settings page.
	 */
	public static function settings_page() {
		settings_fields( self::GROUP_NAME );
		include( WPMTST_ADMIN . 'settings/partials/form.php' );
	}

	/**
	 * Sanitize settings.
	 *
	 * @param $input
	 *
	 * @return array
	 */
	public static function sanitize_options( $input ) {
		$input['post_status']       = sanitize_text_field( $input['post_status'] );
		$input['admin_notify']      = wpmtst_sanitize_checkbox( $input, 'admin_notify' );
		$input['mail_queue']        = wpmtst_sanitize_checkbox( $input, 'mail_queue' );
		$input['sender_name']       = sanitize_text_field( $input['sender_name'] );
		$input['sender_site_email'] = intval( $input['sender_site_email'] );
		$input['sender_email']      = sanitize_email( $input['sender_email'] );
		if ( ! $input['sender_email'] && ! $input['sender_site_email'] ) {
			$input['sender_site_email'] = true;
		}

		/**
		 * Multiple recipients.
		 *
		 * @since 1.18
		 */
		$new_recipients = array();
		foreach ( $input['recipients'] as $recipient ) {

			if ( isset( $recipient['primary'] ) ) {
				$recipient['primary'] = true;
				if ( isset( $recipient['admin_site_email'] ) && ! $recipient['admin_site_email'] ) {
					if ( ! $recipient['admin_email'] ) {
						$recipient['admin_site_email'] = true;
					}
				}
			} else {
				// Don't save if both fields are empty.
				if ( ! isset( $recipient['admin_name'] ) && ! isset( $recipient['admin_email'] ) ) {
					continue;
				}
				if ( ! $recipient['admin_name'] && ! $recipient['admin_email'] ) {
					continue;
				}
			}

			if ( isset( $recipient['admin_name'] ) ) {
				$recipient['admin_name'] = sanitize_text_field( $recipient['admin_name'] );
			}

			if ( isset( $recipient['admin_email'] ) ) {
				$recipient['admin_email'] = sanitize_email( $recipient['admin_email'] );
			}

			$new_recipients[] = $recipient;

		}
		$input['recipients'] = $new_recipients;

		$input['default_recipient'] = maybe_unserialize( $input['default_recipient'] );
		$input['email_subject']     = isset( $input['email_subject'] ) ? wp_kses_post( $input['email_subject'] ) : '';
		$input['email_message']     = isset( $input['email_message'] ) ? wp_kses_post( $input['email_message'] ) : '';

		$input['honeypot_before']   = wpmtst_sanitize_checkbox( $input, 'honeypot_before' );
		$input['honeypot_after']    = wpmtst_sanitize_checkbox( $input, 'honeypot_after' );
		$input['captcha']           = sanitize_text_field( $input['captcha'] );

		foreach ( $input['messages'] as $key => $message ) {
			if ( 'submission-success' == $key ) {
				$input['messages'][ $key ]['text'] = $message['text'];
			} else {
				if ( 'required-field' == $key ) {
					$input['messages'][ $key ]['enabled'] = wpmtst_sanitize_checkbox( $input['messages'][ $key ], 'enabled' );
				}
				$input['messages'][ $key ]['text'] = wp_kses_data( $message['text'] );
			}
		}

		$input['scrolltop_error']          = wpmtst_sanitize_checkbox( $input, 'scrolltop_error' );
		$input['scrolltop_error_offset']   = intval( sanitize_text_field( $input['scrolltop_error_offset'] ) );
		$input['scrolltop_success']        = wpmtst_sanitize_checkbox( $input, 'scrolltop_success' );
		$input['scrolltop_success_offset'] = intval( sanitize_text_field( $input['scrolltop_success_offset'] ) );

		/**
		 * Success redirect
		 * @since 2.18.0
		 */

		$input['success_action'] = sanitize_text_field( $input['success_action'] );

		if ( filter_var( $input['success_redirect_url'], FILTER_VALIDATE_URL ) ) {
			$input['success_redirect_url'] = wp_validate_redirect( $input['success_redirect_url'] );
		} else {
			$input['success_redirect_url'] = '';
		}

		// Check the "ID or slug" field next
		if ( isset( $input['success_redirect_2']) && $input['success_redirect_2'] ) {

			// is post ID?
			$id = sanitize_text_field( $input['success_redirect_2'] );
			if ( is_numeric( $id ) ) {
				if ( ! get_posts( array( 'p' => $id, 'post_type' => array( 'page' ), 'post_status' => 'publish' ) ) ) {
					$id = null;
				}
			} else {
				// is post slug?
				$target = get_posts( array( 'name' => $id, 'post_type' => array( 'page' ), 'post_status' => 'publish' ) );
				if ( $target ) {
					$id = $target[0]->ID;
				}
			}

			if ( $id ) {
				$input['success_redirect_id'] = $id;
			}

		} else {

			if ( isset( $input['success_redirect_id'] ) ) {
				$input['success_redirect_id'] = (int) sanitize_text_field( $input['success_redirect_id'] );
			}

		}

		unset( $input['success_redirect_2'] );
		//ksort( $input );

		return $input;
	}

	/**
	 * [Restore Default Messages] Ajax receiver.
	 *
	 * @since 1.13
	 */
	public static function restore_default_messages_function() {
		$default_form_options = Strong_Testimonials_Defaults::get_form_options();
		$messages = $default_form_options['messages'];
		echo json_encode( $messages );
		wp_die();
	}

	/**
	 * [Restore Default] for single message Ajax receiver.
	 *
	 * @since 1.13
	 */
	public static function restore_default_message_function() {
		$input = str_replace( '_', '-', $_REQUEST['field'] );
		$default_form_options = Strong_Testimonials_Defaults::get_form_options();
		$message = $default_form_options['messages'][$input];
		echo json_encode( $message );
		wp_die();
	}

	/**
	 * [Add Recipient] Ajax receiver
	 */
	public static function add_recipient() {
		$key          = $_REQUEST['key'];
		$form_options = get_option( 'wpmtst_form_options' );
		$recipient    = $form_options['default_recipient'];
		include WPMTST_ADMIN . 'settings/partials/recipient.php';
		wp_die();
	}

}

Strong_Testimonials_Settings_Form::init();
