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
function pantheon_multisite_install_finalize_message() { ?>
	<div class="notice notice-info is-dismissible">
		<?php
		if ( isset( $_ENV['PANTHEON_ENVIRONMENT'] ) ) {
			if ( getenv( 'FRAMEWORK' ) === 'wordpress_network' ) {
				?>
					<p><?php esc_html_e( 'Your WordPress Multisite is almost ready!', 'pantheon' ); ?></p>
					<p><?php echo sprintf( __( 'Visit <a href="%s">Pantheon Multisite Configuration</a> for documentation on how to finalize configuration of your site network.', 'pantheon' ), 'https://pantheon.io/docs/guides/multisite/config/#install-the-wordpress-site-network' ); ?></p>
				<?php
			} else {
				?>
					<p><?php esc_html_e( 'You are trying to configure a WordPress Multisite with a wrong upstream!', 'pantheon' ); ?></p>
					<p><?php echo sprintf( __( 'Make sure that you have the correct upstream configuration for WPMS. If you do not have that capability or check if you are eligible, please <a href="%s">Contact Support</a>.', 'pantheon' ), 'https://pantheon.io/support' ); ?></p>
				<?php
			}
		}
		?>
	</div>


	<?php
}

add_action( 'admin_notices', 'pantheon_multisite_install_finalize_message' );
