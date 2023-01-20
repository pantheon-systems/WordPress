<?php

if (! function_exists('blocksy_switch_theme')) {
	function blocksy_switch_theme() {
		switch_theme( WP_DEFAULT_THEME );
		unset( $_GET['activated'] );
		add_action( 'admin_notices', 'blocksy_upgrade_notice' );
	}
}
add_action( 'after_switch_theme', 'blocksy_switch_theme' );

if (! function_exists('blocksy_upgrade_notice')) {
	function blocksy_upgrade_notice() {
		$message = sprintf(
			// translators: placeholder here is the actual PHP version.
			__( 'Blocksy requires at least PHP version 5.7.0. You are running version %s. Please upgrade and try again.', 'blocksy' ),
			PHP_VERSION
		);

		printf( '<div class="error"><p>%s</p></div>', wp_kses_post($message) );
	}
}

if (! function_exists('blocksy_customize')) {
	function blocksy_customize() {
		wp_die( sprintf(
			// translators: placeholder here is the actual PHP version.
			esc_html(__( 'Blocksy requires at least PHP version 5.7.0. You are running version %s. Please upgrade and try again.', 'blocksy' )),
			PHP_VERSION
		), '', array(
			'back_link' => true,
		) );
	}
}
add_action( 'load-customize.php', 'blocksy_customize' );

if (! function_exists('blocksy_preview')) {
	function blocksy_preview() {
		if ( isset( $_GET['preview'] ) ) {
			wp_die( sprintf(
				// translators: placeholder here is the actual PHP version.
				esc_html(__( 'Blocksy requires at least PHP version 5.7.0. You are running version %s. Please upgrade and try again.', 'blocksy' )),
				PHP_VERSION
			) );
		}
	}
}
add_action( 'template_redirect', 'blocksy_preview' );
