<?php

namespace WPForms\Integrations\SMTP;

use WPForms\Integrations\IntegrationInterface;

/**
 * Notifications class.
 *
 * @since 1.7.6
 */
class Notifications implements IntegrationInterface {

	/**
	 * Determine if the class is allowed to load.
	 *
	 * @since 1.7.6
	 *
	 * @return bool
	 */
	public function allow_load() {

		return wpforms_is_admin_page( 'builder' );
	}

	/**
	 * Load the class.
	 *
	 * @since 1.7.6
	 */
	public function load() {

		$this->hooks();
	}

	/**
	 * Hooks.
	 *
	 * @since 1.7.6
	 */
	private function hooks() {

		add_filter( 'wpforms_builder_notifications_sender_address_settings', [ $this, 'change_from_email_settings' ], 10, 3 );
	}

	/**
	 * Add warning message when email doesn't match site domain.
	 *
	 * @since 1.7.6
	 *
	 * @param array $args      Field settings.
	 * @param array $form_data Form data.
	 * @param int   $id        Notification ID.
	 *
	 * @return array
	 */
	public function change_from_email_settings( $args, $form_data, $id ) {

		$email = empty( $form_data['settings']['notifications'][ $id ]['sender_address'] ) ? '{admin_email}' : $form_data['settings']['notifications'][ $id ]['sender_address'];

		if ( $this->email_domain_matches_site_domain( $email ) || $this->has_active_smtp_plugin() ) {
			return $args;
		}

		$site_domain               = wp_parse_url( get_bloginfo( 'wpurl' ) )['host'];
		$email_does_not_match_text = sprintf( /* translators: %1$s - WordPress site domain. */
			__( 'The current \'From Email\' address does not match your website domain name (%1$s). This can cause your notification emails to be blocked or marked as spam.', 'wpforms-lite' ),
			esc_html( $site_domain )
		);
		$install_wp_mail_smtp_text = sprintf(
			wp_kses( /* translators: %1$s - WP Mail SMTP install page URL. */
				__(
					'We strongly recommend that you install the free <a href="%1$s" target="_blank">WP Mail SMTP</a> plugin! The Setup Wizard makes it easy to fix your emails.',
					'wpforms-lite'
				),
				[
					'a' => [
						'href'   => [],
						'target' => [],
					],
				]
			),
			esc_url( admin_url( 'admin.php?page=wpforms-smtp' ) )
		);

		$address_match_text      = sprintf( /* translators: %1$s - WordPress site domain. */
			__( 'Alternately, try using a From Address that matches your website domain (no-reply@%1$s).', 'wpforms-lite' ),
			esc_html( $site_domain )
		);
		$fix_email_delivery_text = sprintf(
			wp_kses( /* translators: %1$s - Fixing email delivery issues doc URL. */
				__(
					'Please check out our <a href="%1$s" target="_blank" rel="noopener noreferrer">doc on fixing email delivery issues</a> for more details.',
					'wpforms-lite'
				),
				[
					'a' => [
						'href'   => [],
						'target' => [],
						'rel'    => [],
					],
				]
			),
			esc_url( wpforms_utm_link( 'https://wpforms.com/docs/how-to-fix-wordpress-contact-form-not-sending-email-with-smtp/', 'Block Settings', 'Fixing Email Delivery Issues' ) )
		);

		$from_email_after = sprintf(
			'<p>%1$s</p> <p>%2$s</p> <p>%3$s</p> <p>%4$s</p>',
			$email_does_not_match_text,
			$install_wp_mail_smtp_text,
			$address_match_text,
			$fix_email_delivery_text
		);

		$args['after'] = '<div class="wpforms-alert wpforms-alert-warning wpforms-alert-warning-wide">' . $from_email_after . '</div>';

		$args['class'] .= ' wpforms-panel-field-warning';

		return $args;
	}

	/**
	 * Check if the domain name in an email address matches the WordPress site domain.
	 *
	 * @since 1.7.6
	 *
	 * @param string $email The email address to check against the WordPress site domain.
	 *
	 * @return bool
	 */
	private function email_domain_matches_site_domain( $email ) {

		// Process smart tags if they are used as a value.
		$email = wpforms_process_smart_tags( $email, [] );

		// Skip processing when email is empty or does not set.
		// e.g. {field_id="3"} which we don't have at the moment.
		if ( empty( $email ) ) {
			return true;
		}

		$email_domain = substr( strrchr( $email, '@' ), 1 );
		$site_domain  = wp_parse_url( get_bloginfo( 'wpurl' ) )['host'];

		// Check if From email domain ends with site domain.
		return ! empty( $email_domain ) && preg_match( "/\b{$email_domain}$/", $site_domain ) === 1;
	}

	/**
	 * Check if the site has any active SMTP plugins.
	 *
	 * @since 1.7.6
	 *
	 * @return bool
	 */
	private function has_active_smtp_plugin() {

		// List of plugins from \WPMailSMTP\Conflicts.
		$smtp_plugin_list = [
			'branda-white-labeling/ultimate-branding.php',
			'bws-smtp/bws-smtp.php',
			'cimy-swift-smtp/cimy_swift_smtp.php',
			'disable-emails/disable-emails.php',
			'easy-wp-smtp/easy-wp-smtp.php',
			'fluent-smtp/fluent-smtp.php',
			'gmail-smtp/main.php',
			'mailgun/mailgun.php',
			'my-smtp-wp/my-smtp-wp.php',
			'post-smtp/postman-smtp.php',
			'postman-smtp/postman-smtp.php',
			'postmark-approved-wordpress-plugin/postmark.php',
			'sar-friendly-smtp/sar-friendly-smtp.php',
			'sendgrid-email-delivery-simplified/wpsendgrid.php',
			'smtp-mail/index.php',
			'smtp-mailer/main.php',
			'sparkpost/wordpress-sparkpost.php',
			'turbosmtp/turbo-smtp-plugin.php',
			'woocommerce-sendinblue-newsletter-subscription/woocommerce-sendinblue.php',
			'wp-amazon-ses-smtp/wp-amazon-ses.php',
			'wp-easy-smtp/wp-easy-smtp.php',
			'wp-gmail-smtp/wp-gmail-smtp.php',
			'wp-html-mail/wp-html-mail.php',
			'wp-mail-bank/wp-mail-bank.php',
			'wp-mail-booster/wp-mail-booster.php',
			'wp-mail-smtp-mailer/wp-mail-smtp-mailer.php',
			'wp-mail-smtp-pro/wp_mail_smtp.php',
			'wp-mail-smtp/wp_mail_smtp.php',
			'wp-mailgun-smtp/wp-mailgun-smtp.php',
			'wp-offload-ses/wp-offload-ses.php',
			'wp-sendgrid-smtp/wp-sendgrid-smtp.php',
			'wp-ses/wp-ses.php',
			'wp-smtp/wp-smtp.php',
			'wp-yahoo-smtp/wp-yahoo-smtp.php',
		];

		foreach ( $smtp_plugin_list as $smtp_plugin ) {
			if ( is_plugin_active( $smtp_plugin ) ) {
				return true;
			}
		}

		return false;
	}
}
