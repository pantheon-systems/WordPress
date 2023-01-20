<?php

namespace Blocksy;

class DemoInstallContentEraser {
	protected $has_streaming = true;

	public function __construct($args = []) {
		$args = wp_parse_args($args, [
			'has_streaming' => true
		]);

		$this->has_streaming = $args['has_streaming'];
	}

	public function import() {
		if ($this->has_streaming) {
			Plugin::instance()->demo->start_streaming();
		}

		if (! current_user_can('edit_theme_options')) {
			/*
			Plugin::instance()->demo->emit_sse_message([
				'action' => 'complete',
				'error' => 'No permission.',
			]);

			exit;
			 */
		}

		$this->reset_widgets_data();
		$this->reset_customizer();
		$this->erase_default_pages();
		$this->reset_previous_posts();
		$this->reset_previous_terms();
		$this->reset_menus();

		if ($this->has_streaming) {
			Plugin::instance()->demo->emit_sse_message([
				'action' => 'complete',
				'error' => false,
			]);

			exit;
		}
	}

	private function reset_previous_posts() {
		if ($this->has_streaming) {
			Plugin::instance()->demo->emit_sse_message([
				'action' => 'erase_previous_posts',
				'error' => false,
			]);
		}

		global $wpdb;

		$post_ids = $wpdb->get_col(
			"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='blocksy_demos_imported_post'"
		);

		$_GET['force_delete_kit'] = '1';

		foreach ($post_ids as $post_id) {
			ob_start();
			wp_delete_post($post_id, true);
			ob_get_clean();
		}
	}

	private function reset_previous_terms() {
		if ($this->has_streaming) {
			Plugin::instance()->demo->emit_sse_message([
				'action' => 'erase_previous_terms',
				'error' => false,
			]);
		}

		global $wpdb;

		$term_ids = $wpdb->get_col(
			"SELECT term_id FROM {$wpdb->termmeta} WHERE meta_key='blocksy_demos_imported_term'"
		);

		foreach ($term_ids as $term_id) {
			if (! $term_id) {
				continue;
			}

			$term = get_term($term_id);

			if (! $term) continue;
			if (is_wp_error($term)) continue;

			wp_delete_term($term_id, $term->taxonomy);
		}
	}

	private function erase_default_pages() {
		if ($this->has_streaming) {
			Plugin::instance()->demo->emit_sse_message([
				'action' => 'erase_default_pages',
				'error' => false,
			]);
		}

		$sample_page = get_page_by_path('sample-page', OBJECT, 'page');
		$hello_world_post = get_page_by_path('hello-world', OBJECT, 'post');

		if ($sample_page) {
			wp_delete_post($sample_page->ID, true);
		}

		if ($hello_world_post) {
			wp_delete_post($hello_world_post->ID, true);
		}
	}

	private function reset_customizer() {
		global $wp_customize;

		if ($this->has_streaming) {
			Plugin::instance()->demo->emit_sse_message([
				'action' => 'erase_customizer_settings',
				'error' => false,
			]);
		}

		if (! $wp_customize) {
			return;
		}

		$settings = $wp_customize->settings();

		foreach ($settings as $single_setting) {
			if ('theme_mod' !== $single_setting->type) {
				continue;
			}

			remove_theme_mod($single_setting->id);
		}
	}

	private function reset_widgets_data() {
		if ($this->has_streaming) {
			Plugin::instance()->demo->emit_sse_message([
				'action' => 'erase_widgets_data',
				'error' => false,
			]);
		}

		$sidebars_widgets = get_option('sidebars_widgets', array());

		if (! isset($sidebars_widgets['wp_inactive_widgets'])) {
			$sidebars_widgets['wp_inactive_widgets'] = [];
		}

		foreach ($sidebars_widgets as $sidebar_id => $widgets) {
			if (! $widgets) continue;
			if ($sidebar_id === 'wp_inactive_widgets') {
				continue;
			}

			if ($sidebar_id === 'array_version') {
				continue;
			}

			foreach ($widgets as $widget_id) {
				$sidebars_widgets['wp_inactive_widgets'][] = $widget_id;
			}

			$sidebars_widgets[$sidebar_id] = [];
		}

		update_option('sidebars_widgets', $sidebars_widgets);
		unset($sidebars_widgets['array_version']);
		set_theme_mod('sidebars_widgets', [
			'time' => time(),
			'data' => $sidebars_widgets
		]);
	}

	private function reset_menus() {
		return;

		Plugin::instance()->demo->emit_sse_message([
			'action' => 'erase_menus_data',
			'error' => false,
		]);

		$menus = get_terms('nav_menu', ['hide_empty' => false]);

		foreach ($menus as $single_menu) {
			if (! isset($single_menu->term_id)) {
				continue;
			}

			wp_delete_nav_menu($single_menu->term_id);
		}
	}
}

