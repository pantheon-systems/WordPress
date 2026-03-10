<?php
/**
 * If a site has multisite enabled, but has not had the final installation
 * steps completed, alert the user and provide links.
 *
 * @package pantheon
 */

/**
 * Detects if a user is using the correct upstream and framework and give them appropriate next steps to finalize WPMS setup.
 *
 * @return void
 */
function pantheon_multisite_install_finalize_message() {
	if ( ! isset( $_ENV['PANTHEON_ENVIRONMENT'] ) ) {
		return;
	}

	if ( getenv( 'FRAMEWORK' ) === 'wordpress_network' ) {
		$docs_url = 'https://pantheon.io/docs/guides/multisite/config/#install-the-wordpress-site-network';
		$message = sprintf(
			// translators: %s is the link to the Pantheon Multisite Configuration documentation.
			__( 'Visit <a href="%s">Pantheon Multisite Configuration</a> for documentation on how to finalize configuration of your site network.', 'pantheon' ),
			$docs_url
		);

		Pantheon\_pantheon_render_notice(
			[
				'type'        => 'info',
				'heading'     => __( 'Your WordPress Multisite is almost ready!', 'pantheon' ),
				'message'     => $message,
				'button_text' => __( 'View Documentation', 'pantheon' ),
				'button_url'  => $docs_url,
				'dismissible' => true,
			]
		);
	} else {
		$support_url = 'https://pantheon.io/support';
		$message = sprintf(
			// translators: %s is the link to the Pantheon Support page.
			__( 'Make sure that you have the correct upstream configuration for WPMS. If you do not have that capability or to check if you are eligible, please <a href="%s">Contact Support</a>.', 'pantheon' ),
			$support_url
		);

		Pantheon\_pantheon_render_notice(
			[
				'type'        => 'warning',
				'heading'     => __( 'You are trying to configure a WordPress Multisite with a wrong upstream!', 'pantheon' ),
				'message'     => $message,
				'button_text' => __( 'Contact Support', 'pantheon' ),
				'button_url'  => $support_url,
				'dismissible' => true,
			]
		);
	}
}

add_action( 'admin_notices', 'pantheon_multisite_install_finalize_message' );
