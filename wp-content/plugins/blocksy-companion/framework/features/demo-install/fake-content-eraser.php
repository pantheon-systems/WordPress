<?php

namespace Blocksy;

class DemoInstallFakeContentEraser {
	public function import() {
		Plugin::instance()->demo->start_streaming();

		if (! current_user_can('edit_theme_options')) {
			/*
			Plugin::instance()->demo->emit_sse_message([
				'action' => 'complete',
				'error' => 'No permission.',
			]);

			exit;
			 */
		}

		if (class_exists('WC_REST_System_Status_Tools_V2_Controller')) {
			define('WP_CLI', true);

			$s = new \WC_REST_System_Status_Tools_V2_Controller();

			$s->execute_tool('clear_transients');
			if (function_exists('wc_update_product_lookup_tables')) {
				wc_update_product_lookup_tables();
			}
			$s->execute_tool('clear_transients');
			$s->execute_tool('db_update_routine');
		}

		// $this->reset_widgets_data();
		// $this->reset_customizer();
		// $this->erase_default_pages();
		// $this->reset_previous_posts();
		// $this->reset_previous_terms();
		// $this->reset_menus();

		Plugin::instance()->demo->emit_sse_message([
			'action' => 'complete',
			'error' => false,
		]);

		exit;
	}

	private function reset_previous_posts() {
		Plugin::instance()->demo->emit_sse_message([
			'action' => 'erase_previous_posts',
			'error' => false,
		]);

		global $wpdb;

		$post_ids = $wpdb->get_col(
			"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='blocksy_demos_imported_post'"
		);

		foreach ($post_ids as $post_id) {
			wp_delete_post($post_id, true);
		}
	}

	private function reset_previous_terms() {
		Plugin::instance()->demo->emit_sse_message([
			'action' => 'erase_previous_terms',
			'error' => false,
		]);

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

			wp_delete_term($term_id, $term->taxonomy);
		}
	}

	private function erase_default_pages() {
		Plugin::instance()->demo->emit_sse_message([
			'action' => 'erase_default_pages',
			'error' => false,
		]);

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

		Plugin::instance()->demo->emit_sse_message([
			'action' => 'erase_customizer_settings',
			'error' => false,
		]);

		if (! $wp_customize) {
			return;
		}

		$settings = $wp_customize->settings();

		foreach ($settings as $single_setting) {
			if ('theme_mod' !== $single_setting->type) {
				continue;
			}

			remove_theme_mod( $single_setting->id );
		}
	}

	private function reset_widgets_data() {
		Plugin::instance()->demo->emit_sse_message([
			'action' => 'erase_widgets_data',
			'error' => false,
		]);

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

			foreach ($widgets as $widget_key => $widget_data) {
				list(
					$name, $option_index, $sidebar_id, $sidebar_index
				) = $this->get_widget_data($widget_data);

				if ($sidebar_id === 'wp_inactive_widgets') {
					continue;
				}

				$this->move_sidebar_widget(
					$widget_data,
					$sidebar_id,
					'wp_inactive_widgets',
					$sidebar_index,
					0
				);
			}
		}
	}

	private function get_widget_data( $widget_id ) {
		$parts = explode('-', $widget_id);
		$option_index = array_pop($parts);
		$name = implode('-', $parts);
		$sidebar_id = false;
		$sidebar_index = false;
		$all_widgets = $this->wp_get_sidebars_widgets();

		foreach ($all_widgets as $s_id => &$widgets) {
			$key = array_search($widget_id, $widgets, true);

			if (false !== $key) {
				$sidebar_id = $s_id;
				$sidebar_index = $key;
				break;
			}
		}

		return [$name, $option_index, $sidebar_id, $sidebar_index];
	}

	private function move_sidebar_widget(
		$widget_id,
		$current_sidebar_id,
		$new_sidebar_id,
		$current_index, $new_index
	) {
		$all_widgets = $this->wp_get_sidebars_widgets();
		$needs_placement = true;

		// Existing widget
		if ($current_sidebar_id && !is_null($current_index)) {
			$widgets = $all_widgets[$current_sidebar_id];
			if ($current_sidebar_id !== $new_sidebar_id) {
				unset($widgets[$current_index]);
			} else {
				$part = array_splice($widgets, $current_index, 1);
				array_splice($widgets, $new_index, 0, $part);
				$needs_placement = false;
			}

			$all_widgets[$current_sidebar_id] = array_values($widgets);
		}

		if ($needs_placement) {
			$widgets = !empty($all_widgets[$new_sidebar_id]) ? $all_widgets[$new_sidebar_id] : [];
			$before = array_slice($widgets, 0, $new_index, true);
			$after = array_slice($widgets, $new_index, count($widgets), true);
			$widgets = array_merge($before, [$widget_id], $after);
			$all_widgets[$new_sidebar_id] = array_values($widgets);
		}

		update_option('sidebars_widgets', $all_widgets);

		$widgets = get_theme_mod('sidebars_widgets');

		if (! $widgets) {
			$widgets = [];
		}

		set_theme_mod('sidebars_widgets', array_merge(
			$widgets,
			[
				'time' => time(),
				'data' => $all_widgets
			]
		));
	}

	private function wp_get_sidebars_widgets() {
		$sidebars_widgets = get_option('sidebars_widgets', []);

		if (is_array($sidebars_widgets) && isset($sidebars_widgets['array_version'])) {
			unset($sidebars_widgets['array_version']);
		}

		return $sidebars_widgets;
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



