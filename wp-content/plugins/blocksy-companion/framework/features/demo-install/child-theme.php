<?php

namespace Blocksy;

class DemoInstallChildThemeInstaller {
	public function import() {
		Plugin::instance()->demo->start_streaming();

		if (! current_user_can('edit_theme_options')) {
			Plugin::instance()->demo->emit_sse_message([
				'action' => 'complete',
				'error' => 'No permission.',
			]);

			exit;
		}

		$theme = wp_get_theme();

		if (is_child_theme()) {
			Plugin::instance()->demo->emit_sse_message([
				'action' => 'complete',
				'error' => false,
			]);

			exit;
		}

		$name = $theme . ' Child';
		$slug = sanitize_title($name);

		$path = get_theme_root() . '/' . $slug;

		WP_Filesystem();
		global $wp_filesystem;

		Plugin::instance()->demo->emit_sse_message([
			'action' => 'import_install_child',
			'error' => false,
		]);

		if (! $wp_filesystem->exists($path)) {
			$wp_filesystem->mkdir( $path );

			$wp_filesystem->put_contents(
				$path . '/style.css',
				$this->get_style_css()
			);

			$wp_filesystem->put_contents(
				$path . '/functions.php',
				$this->get_functions_php()
			);

			$this->make_screenshot($path);
			$allowed_themes = get_option('allowedthemes');
			$allowed_themes[$slug] = true;

			update_option('allowedthemes', $allowed_themes);
		}

		Plugin::instance()->demo->emit_sse_message([
			'action' => 'import_activate_child',
			'error' => false,
		]);

		switch_theme($slug);

		Plugin::instance()->demo->emit_sse_message([
			'action' => 'complete',
			'error' => false,
		]);

		exit;
	}

	private function get_style_css() {
		return '/**
 * Theme Name: Blocksy Child
 * Description: Blocksy Child theme
 * Author: Creative Themes
 * Template: blocksy
 * Text Domain: blocksy
 */';
	}

	private function get_functions_php() {
		return "<?php

if (! defined('WP_DEBUG')) {
	die( 'Direct access forbidden.' );
}

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
});
";
	}

	private function make_screenshot($path) {
		$base_path = get_parent_theme_file_path();

		global $wp_filesystem;

		if ($wp_filesystem->exists($base_path . '/screenshot.png')) {
			$screenshot = $base_path . '/screenshot.png';
			$screenshot_ext = 'png';
		} elseif ($wp_filesystem->exists($base_path . '/screenshot.jpg')) {
			$screenshot = $base_path . '/screenshot.jpg';
			$screenshot_ext = 'jpg';
		}

		if (! empty($screenshot) && $wp_filesystem->exists($screenshot)) {
			$copied = $wp_filesystem->copy(
				$screenshot,
				$path . '/screenshot.' . $screenshot_ext
			);
		}
	}
}

