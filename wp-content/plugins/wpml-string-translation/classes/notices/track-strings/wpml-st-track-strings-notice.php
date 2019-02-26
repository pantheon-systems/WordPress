<?php

class WPML_ST_Track_Strings_Notice {

	const NOTICE_ID = 'wpml-st-tracking-all-strings-as-english-notice';
	const NOTICE_GROUP = 'wpml-st-strings-tracking';

	/**
	 * @var WPML_Notices
	 */
	private $admin_notices;

	public function __construct( WPML_Notices $admin_notices ) {
		$this->admin_notices = $admin_notices;
	}

	/**
	 * @param int $track_strings
	 */
	public function add( $track_strings ) {
		if ( ! $track_strings ) {
			return;
		}
		$options_nonce = 'wpml-localization-options-nonce';

		$message  = __( 'For String Tracking to work, the option', 'wpml-string-translation' );
		$message .= '<strong> ' . WPML_ST_Theme_Plugin_Localization_Options_UI::get_all_strings_option_text() . ' </strong>';
		$message .= __( 'was automatically disabled. To enable it back, go to WPML->Theme and Plugins localization.', 'wpml-string-translation' );
		$message .= '<input type="hidden" id="' . $options_nonce . '" name="' . $options_nonce . '" value="' . wp_create_nonce( $options_nonce ) . '">';

		$notice = $this->admin_notices->get_new_notice( self::NOTICE_ID, $message, self::NOTICE_GROUP );
		$notice->set_css_class_types( 'info' );
		$notice->add_action( $this->admin_notices->get_new_notice_action( __( 'Cancel and undo changes', 'wpml-string-translation' ), '#', false, false, 'button-primary' ) );
		$notice->add_action( $this->admin_notices->get_new_notice_action( __( 'Skip', 'wpml-string-translation' ), '#', false, true ) );
		$this->admin_notices->add_notice( $notice );
	}

	public function remove() {
		$this->admin_notices->remove_notice(
			self::NOTICE_GROUP,
			self::NOTICE_ID
		);
	}
}