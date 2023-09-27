<?php

namespace Blocksy;

class CustomizerOptionsManager {
	public function __construct() {
		add_filter('blocksy:options:manage-options:top', function ($options) {
			$options['importer'] = [
				'type' => 'blocksy-customizer-options-manager',
				'design' => 'none',
			];

			return $options;
		});

		add_action('wp_ajax_blocksy_customizer_export', function () {
			if (! current_user_can('manage_options')) {
				wp_send_json_error();
			}

			if (! isset($_POST['strategy'])) {
				wp_send_json_error();
			}

			wp_send_json_success([
				'data' => serialize($this->get_data(null, $_POST['strategy'])),
				'site_url' => get_site_url()
			]);
		});

		add_action('wp_ajax_blocksy_customizer_import', function () {
			if (! current_user_can('manage_options')) {
				wp_send_json_error();
			}

			if (! isset($_REQUEST['nonce'])) {
				wp_send_json_error();
			}

			if (! wp_verify_nonce($_REQUEST['nonce'], 'ct-customizer-reset')) {
				wp_send_json_error();
			}

			$data = json_decode(
				file_get_contents('php://input'),
				true
			);

			// The above code will only import stuff from $data['mods']
			// that is actually a customizer control. Everything else will be ignored
			if (isset($data['mods'])) {
				$importer = new DemoInstallOptionsInstaller([
					'has_streaming' => false
				]);

				$demo_data = null;

				if (isset($data['site_url']) && is_string($data['site_url'])) {
					$demo_data = [
						'url' => $data['site_url']
					];
				}

				$importer->import_options($data, $demo_data);
			}

			// Will only import $data['blocksy_widgets'] that has an actual
			// widget. Everything else is going to be ignored from being
			// processed.
			if (isset($data['blocksy_widgets'])) {
				$importer = new DemoInstallWidgetsInstaller([
					'has_streaming' => false
				]);

				$importer->import_data($data['blocksy_widgets']);
			}

			wp_send_json_success([]);
		});

		add_action('wp_ajax_blocksy_customizer_copy_options', function () {
			if (! current_user_can('manage_options')) {
				wp_send_json_error();
			}

			if (! isset($_POST['strategy'])) {
				wp_send_json_error();
			}

			$theme_for_data = get_option('stylesheet');

			if ($_POST['strategy'] === 'parent') {
				foreach (wp_get_themes() as $id => $theme) {
					if (! $theme->parent()) {
						continue;
					}

					if ($theme->parent()->get_stylesheet() === 'blocksy') {
						$theme_for_data = $theme->parent()->get_stylesheet();
					}
				}
			}

			if ($_POST['strategy'] === 'child') {
				foreach (wp_get_themes() as $id => $theme) {
					if (! $theme->parent()) {
						continue;
					}

					if ($theme->parent()->get_stylesheet() === 'blocksy') {
						$theme_for_data = $theme->get_stylesheet();
					}
				}
			}

			$data = $this->get_data($theme_for_data);

			$importer = new DemoInstallOptionsInstaller([
				'has_streaming' => false
			]);

			$importer->import_options($data);

			wp_send_json_success([]);
		});
	}

	private function get_data($theme_slug = null, $strategy = 'options') {
		$data = [];

		if (strpos($strategy, 'options') !== false) {
			if (! $theme_slug) {
				$theme_slug = get_option('stylesheet');
			}

			global $wp_customize;

			$mods = $this->get_theme_mods($theme_slug);
			$data = [
				'template' => $theme_slug,
				'site_url' => get_site_url(),
				'mods' => $mods ? $mods : array(),
				'options' => array()
			];

			$core_options = [
				'blogname',
				'blogdescription',
				'show_on_front',
				'page_on_front',
				'page_for_posts',
			];

			$settings = $wp_customize->settings();

			foreach ($settings as $key => $setting) {
				if ('option' === $setting->type) {
					// Don't save widget data.
					if ('widget_' === substr(strtolower($key), 0, 7)) {
						continue;
					}

					// Don't save sidebar data.
					if ('sidebars_' === substr(strtolower($key), 0, 9)) {
						continue;
					}

					// Don't save core options.
					if (in_array($key, $core_options)) {
						continue;
					}

					$data['options'][$key] = $setting->value();
				}
			}

			if (function_exists('wp_get_custom_css_post')) {
				$data['wp_css'] = wp_get_custom_css();
			}
		}

		if (strpos($strategy, 'widgets') !== false) {
			$widgets = new DemoInstallWidgetsExport();
			$data['blocksy_widgets'] = json_decode($widgets->export());
		}

		return $data;
	}

	private function get_theme_mods($theme_slug = null) {
		if (! $theme_slug) {
			$theme_slug = get_option('stylesheet');
		}

		$mods = get_option("theme_mods_$theme_slug");

		if (false === $mods) {
			$theme_name = wp_get_theme($theme_slug)->get( 'Name' );

			$mods = get_option( "mods_$theme_name" ); // Deprecated location.

			if ( is_admin() && false !== $mods ) {
				update_option( "theme_mods_$theme_slug", $mods );
				delete_option( "mods_$theme_name" );
			}
		}

		return $mods;
	}
}
