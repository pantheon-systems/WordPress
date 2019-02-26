<?php

/**
 * This code is inspired by WPML Widgets (https://wordpress.org/plugins/wpml-widgets/),
 * created by Jeroen Sormani
 *
 * @author OnTheGo Systems
 */
class WPML_Widgets_Support_Factory implements IWPML_Backend_Action_Loader, IWPML_Frontend_Action_Loader, IWPML_Deferred_Action_Loader {

	public function get_load_action() {
		return 'wpml_loaded';
	}

	/**
	 * @return WPML_Widgets_Support_Backend|WPML_Widgets_Support_Frontend|null
	 */
	public function create() {
		global $sitepress;

		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		if ( ! is_plugin_active( 'wpml-widgets/wpml-widgets.php' ) && $this->is_human_page( $sitepress ) ) {
			if ( $sitepress->get_wp_api()->is_admin() ) {
				return $this->create_backend_ui( $sitepress );
			}

			return $this->create_frontend_ui( $sitepress );
		}

		return null;
	}

	private function create_backend_ui( SitePress $sitepress ) {
		$template_paths = array(
			WPML_PLUGIN_PATH . '/templates/widgets/',
		);

		$template_loader = new WPML_Twig_Template_Loader( $template_paths );

		return new WPML_Widgets_Support_Backend( $sitepress->get_active_languages(), $template_loader->get_template() );
	}

	public function create_frontend_ui( SitePress $sitepress ) {
		return new WPML_Widgets_Support_Frontend( $sitepress->get_current_language() );
	}

	/**
	 * @param SitePress $sitepress
	 *
	 * @return bool
	 */
	private function is_human_page( SitePress $sitepress ) {
		$wpml_wp_api = $sitepress->get_wp_api();

		return ! $wpml_wp_api->is_cron_job() && ! $wpml_wp_api->is_heartbeat();
	}
}
