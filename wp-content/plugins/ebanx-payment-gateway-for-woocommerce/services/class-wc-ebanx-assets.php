<?php

/**
 * Class WC_EBANX_Assets
 */
class WC_EBANX_Assets {
	/**
	 * Renders the static assets needed to change admin panel to desired behavior
	 *
	 * @return void
	 */
	public static function render() {
		self::adjust_dynamic_admin_options_sections();
		self::resize_settings_menu_icon();
		self::disable_ebanx_gateways();
		self::render_iof_notice();
		self::render_manual_review_alert();
	}

	/**
	 * The EBANX logo SVG base64 encoded with data:image protocol
	 *
	 * @return string
	 */
	public static function get_logo() {
		return <<<SVG
data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz48c3ZnIHdpZHRoPSIxNnB4IiBoZWlnaHQ9IjIwcHgiIHZpZXdCb3g9IjAgMCAxNiAyMCIgdmVyc2lvbj0iMS4xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIj4gICAgICAgIDx0aXRsZT5lYmFueC1zdmc8L3RpdGxlPiAgICA8ZGVzYz5DcmVhdGVkIHdpdGggU2tldGNoLjwvZGVzYz4gICAgPGRlZnM+PC9kZWZzPiAgICA8ZyBpZD0iUGFnZS0xIiBzdHJva2U9Im5vbmUiIHN0cm9rZS13aWR0aD0iMSIgZmlsbD0ibm9uZSIgZmlsbC1ydWxlPSJldmVub2RkIj4gICAgICAgIDxnIGlkPSJlYmFueC1zdmciPiAgICAgICAgICAgIDxwb2x5Z29uIGlkPSJTaGFwZSIgZmlsbD0iIzFDNDE3OCIgcG9pbnRzPSIwLjExMTYyNzkwNyAwLjA5MDkwOTA5MDkgMTIuNTM5NTM0OSAxMCAwLjExMTYyNzkwNyAxOS45MDkwOTA5Ij48L3BvbHlnb24+ICAgICAgICAgICAgPHBvbHlnb24gaWQ9IlNoYXBlIiBmaWxsPSIjREFEQkRCIiBwb2ludHM9IjkuMTM0ODgzNzIgMTIuNzA5MDkwOSAwLjExMTYyNzkwNyAxOS45MDkwOTA5IDE1Ljk2Mjc5MDcgMTkuODkwOTA5MSI+PC9wb2x5Z29uPiAgICAgICAgICAgIDxwb2x5Z29uIGlkPSJTaGFwZSIgZmlsbD0iI0RBREJEQiIgcG9pbnRzPSIwLjExMTYyNzkwNyAwLjA5MDkwOTA5MDkgOS4xMzQ4ODM3MiA3LjI5MDkwOTA5IDE1Ljk2Mjc5MDcgMC4wOTA5MDkwOTA5Ij48L3BvbHlnb24+ICAgICAgICAgICAgPHBvbHlnb24gaWQ9IlNoYXBlIiBmaWxsPSIjMDA5M0QwIiBwb2ludHM9IjAuMTExNjI3OTA3IDE5LjkwOTA5MDkgOS4xMzQ4ODM3MiAxMi43MDkwOTA5IDYuNzUzNDg4MzcgMTAgMC4xMTE2Mjc5MDcgMTcuMiI+PC9wb2x5Z29uPiAgICAgICAgICAgIDxwb2x5Z29uIGlkPSJTaGFwZSIgZmlsbD0iIzAwQkNFNCIgcG9pbnRzPSIwLjExMTYyNzkwNyAyLjggMC4xMTE2Mjc5MDcgMTcuMiA2Ljc1MzQ4ODM3IDEwIj48L3BvbHlnb24+ICAgICAgICAgICAgPHBvbHlnb24gaWQ9IlNoYXBlIiBmaWxsPSIjMDA5M0QwIiBwb2ludHM9IjAuMTExNjI3OTA3IDAuMDkwOTA5MDkwOSA5LjEzNDg4MzcyIDcuMjkwOTA5MDkgNi43NTM0ODgzNyAxMCAwLjExMTYyNzkwNyAyLjgiPjwvcG9seWdvbj4gICAgICAgIDwvZz4gICAgPC9nPjwvc3ZnPg==
SVG;
	}

	// PRIVATE.
	/**
	 * Renders the script to manage the admin options script part of ebanx gateway configuration
	 *
	 * @return void
	 */
	private static function adjust_dynamic_admin_options_sections() {
		if ( ! self::is_in_ebanx_settings() ) {
			return;
		}

		self::render_style( 'toggleable-options' );

		self::render_script( 'payments-options', array( 'jquery' ) );
		self::render_script( 'advanced-options', array( 'jquery' ) );
	}

	/**
	 * Renders the style tag to resize the menu icon to the correct size
	 *
	 * @return void
	 */
	private static function resize_settings_menu_icon() {
		self::render_style( 'settings-menu-icon' );
	}

	/**
	 * Renders the style tag to resize the menu icon to the correct size
	 *
	 * @return void
	 */
	private static function render_iof_notice() {
		self::render_script( 'iof-options', array( 'jquery' ) );
		self::localize_script( 'iof-options', array( 'confirm_message' => __( 'You need to validate this change with EBANX, only deselecting or selecting the box will not set this to your customer. Contact your EBANX Account Manager or Business Development Expert.', 'woocommerce-gateway-ebanx' ) ) );
	}

	/**
	 * Renders the style tag to resize the menu icon to the correct size
	 *
	 * @return void
	 */
	private static function render_manual_review_alert() {
		self::render_script( 'manual-reviews-options', array( 'jquery' ) );
		self::localize_script( 'manual-reviews-options', array( 'confirm_message' => __( 'You have to validate the Manual Review with your EBANX’s contact, if you don’t do this an error message (BP-DR-116) will appear to your customer. Just selecting or deselecting this option will not change the approval process of your transactions.', 'woocommerce-gateway-ebanx' ) ) );
	}

	/**
	 * Disable all EBANX gateways so only our global settings one is displayed
	 *
	 * @return void
	 */
	private static function disable_ebanx_gateways() {
		self::render_style( 'disable-ebanx-gateways' );
		self::render_script( 'disable-ebanx-gateways' );
	}

	/**
	 * When ebanx settings page is open
	 *
	 * @return boolean
	 * @throws Exception Shows missing param message.
	 */
	private static function is_in_ebanx_settings() {
		return WC_EBANX_Request::has( 'section' )
			&& WC_EBANX_Request::read( 'section' ) === 'ebanx-global';
	}

	/**
	 * Enqueues a script for rendering at the bottom of page body
	 * Files must be under assets/js/
	 *
	 * @param  string $filename     Filename without extension.
	 * @param  array  $dependencies An array with wp script names of dependencies.
	 * @return void
	 */
	private static function render_script( $filename, $dependencies = array() ) {
		$script_name = 'woocommerce_ebanx_' . str_replace( '-', '_', $filename );
		$file_path   = plugins_url( 'assets/js/' . $filename . '.js', WC_EBANX::DIR );

		wp_enqueue_script(
			$script_name,
			$file_path,
			$dependencies,
			WC_EBANX::get_plugin_version(),
			true
		);
	}

	/**
	 * Adds a script block with some the variables from $var_data on page head.
	 *
	 * @param  string $handle     Handle name.
	 * @param  array  $var_data An array with the data that you want to pass to the script.
	 * @return void
	 */
	private static function localize_script( $handle, $var_data ) {
		$script_name = 'woocommerce_ebanx_' . str_replace( '-', '_', $handle );

		wp_localize_script( $script_name, $script_name, $var_data );
	}

	/**
	 * Prints a style tag with a css content to the page header
	 * Files must be under assets/css/
	 *
	 * @param  string $filename Filename without extension.
	 * @return void
	 */
	private static function render_style( $filename ) {
		$style_name = 'woocommerce_ebanx_' . str_replace( '-', '_', $filename );
		$file_path  = plugins_url( 'assets/css/' . $filename . '.css', WC_EBANX::DIR );

		wp_enqueue_style(
			$style_name,
			$file_path
		);
	}
}
