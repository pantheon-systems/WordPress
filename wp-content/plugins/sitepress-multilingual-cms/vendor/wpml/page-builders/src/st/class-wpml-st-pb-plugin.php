<?php

class WPML_ST_PB_Plugin {
	function check_requirements() {
		if ( defined( 'WPML_PAGE_BUILDERS_VERSION' ) ) {
			add_action( 'admin_notices', array( $this, 'disable_old_pb_notice' ) );
		}
	}

	function is_active() {
		return defined( 'WPML_PAGE_BUILDERS_VERSION' );
	}

	function ask_to_deactivate() {
		add_action( 'admin_notices', array( $this, 'disable_old_pb_notice' ) );
	}

	function disable_old_pb_notice() {
		$plugin_name = plugin_basename( WPML_PAGE_BUILDERS_PATH . '/plugin.php' );
		$plugins_url = admin_url( '/plugins.php' );
		$plugins_url = add_query_arg(
			array(
				'action'        => 'deactivate',
				'plugin_status' => 'inactive',
				'_wpnonce'      => urlencode( wp_create_nonce( 'deactivate-plugin_' . $plugin_name ) ),
				'plugin'        => urlencode( $plugin_name ),
			),
			$plugins_url
		);
		?>
			<div class="message error">
				<p>
			<?php esc_html_e( "The WPML Page Builders plugin that you're using is now part of WPML.", 'sitepress' ); ?>
				</p>
				<p>
			<?php esc_html_e( 'You need to deactivate the separate plugin.', 'sitepress' ); ?>
				</p>
				<p>
			<?php esc_html_e( 'No worries, the full functionality is preserved in WPML String Translation.', 'sitepress' ); ?>
				</p>
				<p>
					<a class="button-primary" href="<?php echo esc_url( $plugins_url ); ?>"><?php esc_html_e( 'Deactivate WPML Page Builders', 'sitepress' ); ?></a>
				</p>
			</div>
		<?php
	}
}
