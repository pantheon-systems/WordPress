<?php

/**
 * Class WPML_TF_Promote_Notices
 *
 * @author OnTheGoSystems
 */
class WPML_TF_Promote_Notices {

	const NOTICE_GROUP    = 'wpml-tf-promote';
	const NOTICE_NEW_SITE = 'notice-new-site';
	const DOC_URL         = 'https://wpml.org/documentation/getting-started-guide/getting-visitor-feedback-about-your-sites-translations/';

	/** @var SitePress $sitepress */
	private $sitepress;

	public function __construct( SitePress $sitepress ) {
		$this->sitepress = $sitepress;
	}

	/**
	 * @param int $user_id
	 */
	public function show_notice_for_new_site( $user_id ) {
		$notices      = wpml_get_admin_notices();
		$settings_url = admin_url( '?page=' . WPML_PLUGIN_FOLDER . '/menu/languages.php#wpml-translation-feedback-options' );

		$user_lang = $this->sitepress->get_user_admin_language( $user_id );
		$this->sitepress->switch_lang( $user_lang );

		$text = '<h2>' . __( 'Want to know if recent translations you received have problems?', 'sitepress' ) . '</h2>';
		$text .= '<p>';
		$text .= __( 'You got back several jobs from translation and they now appear on your site.', 'sitepress' );
		$text .= ' ' . __( 'WPML lets you open these pages for feedback, so that visitors can tell you if they notice anything wrong.', 'sitepress' );
		$text .= '<br><br>';
		$text .= '<a href="' . $settings_url . '" class="button-secondary">' . __( 'Enable Translation Feedback', 'sitepress' ) . '</a>';
		$text .= ' <a href="' . self::DOC_URL . '" target="_blank">' . __( 'Learn more about translation feedback', 'sitepress' ) . '</a>';
		$text .= '</p>';

		$notice  = $notices->get_new_notice( self::NOTICE_NEW_SITE, $text, self::NOTICE_GROUP );
		$notice->set_dismissible( true );
		$notice->set_css_class_types( 'notice-info' );
		$notice->add_user_restriction( $user_id );

		if ( ! $notices->is_notice_dismissed( $notice ) ) {
			$notices->add_notice( $notice );
		}

		$this->sitepress->switch_lang( null );
	}

	public function remove() {
		$notices = wpml_get_admin_notices();
		$notices->remove_notice( self::NOTICE_GROUP, self::NOTICE_NEW_SITE );
	}
}
