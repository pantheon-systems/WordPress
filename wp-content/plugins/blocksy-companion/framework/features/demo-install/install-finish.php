<?php

namespace Blocksy;

class DemoInstallFinalActions {
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

			if (! current_user_can('edit_theme_options')) {
				Plugin::instance()->demo->emit_sse_message([
					'action' => 'complete',
					'error' => false,
				]);
				exit;
			}
		}

		$wpforms_settings = get_option('wpforms_settings', []);
		$wpforms_settings['disable-css'] = '2';
		update_option('wpforms_settings', $wpforms_settings);

		$this->replace_urls();

		do_action('customize_save_after');
		do_action('blocksy:dynamic-css:refresh-caches');
		Plugin::instance()->cache_manager->run_cache_purge();

		if (class_exists('WC_REST_System_Status_Tools_V2_Controller')) {
			if (! defined('WP_CLI')) {
				define('WP_CLI', true);
			}

			$s = new \WC_REST_System_Status_Tools_V2_Controller();

			$s->execute_tool('clear_transients');
			if (function_exists('wc_update_product_lookup_tables')) {
				wc_update_product_lookup_tables();
			}
			$s->execute_tool('clear_transients');
		}

		$this->handle_brizy_posts();

		global $wp_rewrite;
		$wp_rewrite->flush_rules();

		if (get_option('qubely_global_options')) {
			$maybe_presets = json_decode(
				get_option('qubely_global_options'),
				true
			);

			if (
				$maybe_presets
				&&
				isset($maybe_presets['activePreset'])
				&&
				$maybe_presets['activePreset'] !== 'theme'
			) {
				$maybe_presets['activePreset'] = 'theme';

				update_option(
					'qubely_global_options',
					json_encode($maybe_presets)
				);
			}
		}

		$this->maybe_activate_elementor_experimental_container();

		if ($this->has_streaming) {
			Plugin::instance()->demo->emit_sse_message([
				'action' => 'complete',
				'error' => false,
			]);
			exit;
		}
	}

	/**
	 * Replace elementor URLs
	 */
	public function replace_urls() {
		$current_demo = Plugin::instance()->demo->get_current_demo();

		if (! $current_demo) {
			return;
		}

		if (! isset($current_demo['demo'])) {
			return;
		}

		$demo_name = explode(':', $current_demo['demo']);

		if (! isset($demo_name[1])) {
			$demo_name[1] = '';
		}

		$demo = $demo_name[0];
		$builder = $demo_name[1];

		$demo_content = Plugin::instance()->demo->fetch_single_demo([
			'demo' => $demo,
			'builder' => $builder
		]);

		if (! $demo_content) {
			return;
		}

		if (! isset($demo_content['url'])) {
			return;
		}

		$from = $demo_content['url'];

		$to = get_site_url();

		$wp_uploads = wp_upload_dir();

		if (isset($wp_uploads['baseurl'])) {
			$from .= '/wp-content/uploads';
			$to = $wp_uploads['baseurl'];
		}

		$from = trim($from);
		$to = trim($to);

		if (
			! filter_var($from, FILTER_VALIDATE_URL)
			||
			! filter_var($to, FILTER_VALIDATE_URL)
		) {
			return;
		}

		global $wpdb;

		// @codingStandardsIgnoreStart cannot use `$wpdb->prepare` because it remove's the backslashes
		$wpdb->query(
			"UPDATE {$wpdb->postmeta} " .
			"SET `meta_value` = REPLACE(`meta_value`, '" . str_replace( '/', '\\\/', $from ) . "', '" . str_replace( '/', '\\\/', $to ) . "') " .
			"WHERE `meta_key` = '_elementor_data' AND `meta_value` LIKE '[%' ;"
		); // meta_value LIKE '[%' are json formatted
		// @codingStandardsIgnoreEnd

		$option_keys = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT option_name from {$wpdb->options} WHERE `option_value` LIKE %s;",
				'%%' . $from . '%%'
			)
		);

		foreach ($option_keys as $single_key) {
			update_option($single_key->option_name, json_decode(str_replace(
				str_replace('/', '\\/', $from),
				str_replace('/', '\\/', $to),
				json_encode(get_option($single_key->option_name))
			), true));
		}
	}

	private function handle_brizy_posts() {
		if (! is_callable('Brizy_Editor_Storage_Common::instance')) {
			return;
		}

		$post_types = \Brizy_Editor_Storage_Common::instance()->get('post-types');

		if (empty($post_types) && ! is_array($post_types)) {
			return;
		}

		$post_ids = $this->get_pages($post_types);

		if (empty($post_ids) && ! is_array($post_ids)) {
			return;
		}

		foreach ($post_ids as $post_id) {
			$this->import_single_post($post_id);
		}
	}

	public function import_single_post($post_id = 0) {
		$is_brizy_post = get_post_meta($post_id, 'brizy_post_uid', true);

		if (! $is_brizy_post) {
			return;
		}

		update_post_meta($post_id, 'brizy_enabled', true);

		$post = \Brizy_Editor_Post::get((int) $post_id);
		$editor_data = $post->get_editor_data();

		$post->set_editor_data( $editor_data );
		$post->set_editor_version(BRIZY_EDITOR_VERSION);
		$post->set_needs_compile(true);
		$post->saveStorage();
	}

	private function get_pages($post_types = array()) {
		if (! $post_types) {
			return null;
		}

		$args = array(
			'post_type' => $post_types,

			// Query performance optimization.
			'fields' => 'ids',
			'no_found_rows' => true,
			'post_status' => 'publish',
			'posts_per_page' => -1,
		);

		$query = new \WP_Query($args);

		if (! $query->have_posts()) {
			return null;
		}

		return $query->posts;
	}

	public function maybe_activate_elementor_experimental_container() {
		if (! defined('ELEMENTOR_VERSION')) {
			return;
		}

		$current_demo = Plugin::instance()->demo->get_current_demo();

		if (! $current_demo) {
			return;
		}

		if (! isset($current_demo['demo'])) {
			return;
		}

		$demo_name = explode(':', $current_demo['demo']);

		if (! isset($demo_name[1])) {
			$demo_name[1] = '';
		}

		$demo = $demo_name[0];
		$builder = $demo_name[1];

		$demo_content = Plugin::instance()->demo->fetch_single_demo([
			'demo' => $demo,
			'builder' => $builder
		]);

		if (! $demo_content) {
			return;
		}

		if ($demo_content['builder'] !== 'elementor') {
			return;
		}

		if (! isset($demo_content['elementor_experiment_container'])) {
			return;
		}

		update_option('elementor_experiment-container', 'active');
	}
}

