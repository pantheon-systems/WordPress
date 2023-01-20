<?php

class Blocksy_Manager {
	public static $instance = null;

	public $builder = null;

	public $header_builder = null;
	public $footer_builder = null;

	public $post_types = null;

	public $screen = null;
    public $hooks = null;

	public $dynamic_css = null;
	public $dynamic_styles_descriptor = null;

	private $current_template = null;

	private $scripts_enqueued = null;

	public static function instance() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function get_current_template() {
		if (!$this->current_template) {
			// return apply_filters('template_include', '__DEFAULT__');
		}

		return $this->current_template;
	}

	private function __construct() {
		$this->early_init();
	}

	private function early_init() {
		$this->builder = new Blocksy_Customizer_Builder();

		$this->header_builder = new Blocksy_Header_Builder();
		$this->footer_builder = new Blocksy_Footer_Builder();

		$this->post_types = new Blocksy_Custom_Post_Types();
		$this->screen = new Blocksy_Screen_Manager();
		$this->hooks = new \Blocksy\WpHooksManager();

		$this->dynamic_css = new Blocksy_Dynamic_Css();

		add_action(
			'init',
			function () {
				$this->screen->wipe_caches();
				$this->post_types->wipe_caches();
			},
			PHP_INT_MAX
		);

		add_filter('block_parser_class', function () {
			return 'Blocksy_WP_Block_Parser';
		});

		add_filter('template_include', function ($template) {
			$this->current_template = $template;
			return $template;
		}, 900000000);

		add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts'], 50);

		add_action(
			'wp_head',
			function () {
				if (defined('IFRAME_REQUEST') && IFRAME_REQUEST) {
					return;
				}

				$this->dynamic_css->load_frontend_css([
					'descriptor' => $this->dynamic_styles_descriptor
				]);
			},
			10
		);
	}

	public function enqueue_scripts() {
		if ($this->scripts_enqueued) {
			return;
		}

		$this->scripts_enqueued = true;

		$theme = blocksy_get_wp_parent_theme();

		$m = new Blocksy_Fonts_Manager();

		$this->dynamic_styles_descriptor = $this
			->dynamic_css
			->get_dynamic_styles_descriptor();

		$m->load_dynamic_google_fonts($this->dynamic_styles_descriptor['google_fonts']);

		$static_files = new Blocksy_Static_Css_Files();
		$static_files->enqueue_static_files($theme);

		wp_register_script(
			'ct-events',
			get_template_directory_uri() . '/static/bundle/events.js',
			[],
			$theme->get('Version'),
			true
		);

		wp_enqueue_script(
			'ct-scripts',
			get_template_directory_uri() . '/static/bundle/main.js',
			[],
			$theme->get('Version'),
			true
		);

		$data = apply_filters('blocksy:general:ct-scripts-localizations', [
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('ct-ajax-nonce'),
			'public_url' => blocksy_cdn_url(
				get_template_directory_uri() . '/static/bundle/'
			),
			'rest_url' => get_rest_url(),
			'search_url' => get_search_link('QUERY_STRING'),
			'show_more_text' => __('Show more', 'blocksy'),
			'more_text' => __('More', 'blocksy'),
			'search_live_results' => __('Search results', 'blocksy'),

			'search_live_no_result' => __('No results', 'blocksy'),
			'search_live_one_result' => _n(
				'You got %s result. Please press Tab to select it.',
				'You got %s results. Please press Tab to select one.',
				1,
				'blocksy'
			),
			'search_live_many_results' => _n(
				'You got %s result. Please press Tab to select it.',
				'You got %s results. Please press Tab to select one.',
				5,
				'blocksy'
			),

			'expand_submenu' => __('Expand dropdown menu', 'blocksy'),
			'collapse_submenu' => __('Collapse dropdown menu', 'blocksy'),

			'dynamic_js_chunks' => blocksy_manager()->get_dynamic_js_chunks(),

			'dynamic_styles' => [
				'lazy_load' => blocksy_cdn_url(
					get_template_directory_uri() . '/static/bundle/non-critical-styles.min.css'
				),
				'search_lazy' => blocksy_cdn_url(
					get_template_directory_uri() . '/static/bundle/non-critical-search-styles.min.css'
				)
			],

			'dynamic_styles_selectors' => []
		]);

		$maybe_current_language = blocksy_get_current_language('slug');

		if ($maybe_current_language !== '__NOT_KNOWN__') {
			$data['lang'] = $maybe_current_language;
		}

		if (is_customize_preview()) {
			$data['customizer_sync'] = blocksy_customizer_sync_data();
		}

		wp_localize_script(
			'ct-scripts',
			'ct_localizations',
			$data
		);

		if (defined('WP_DEBUG') && WP_DEBUG) {
			wp_localize_script(
				'ct-scripts',
				'WP_DEBUG',
				['debug' => true]
			);
		}

		if (is_singular() && comments_open() && get_option('thread_comments')) {
			wp_enqueue_script('comment-reply');
		}
	}

	public function get_dynamic_js_chunks() {
		$all_chunks = apply_filters(
			'blocksy:frontend:dynamic-js-chunks',
			[]
		);

		global $wp_scripts;

		foreach ($all_chunks as $index => $chunk) {
			if (!isset($chunk['deps'])) {
				continue;
			}

			$deps_data = [];

			foreach ($chunk['deps'] as $dep_id) {
				if (!isset($wp_scripts->registered[$dep_id])) {
					continue;
				}

				$src = $wp_scripts->registered[$dep_id]->src;
				$deps_data[$dep_id] = '';

				if (strpos($src, site_url()) === false) {
					$deps_data[$dep_id] = site_url();
				}

				$deps_data[$dep_id] .= $wp_scripts->registered[$dep_id]->src;
			}

			$all_chunks[$index]['deps_data'] = $deps_data;
		}

		return $all_chunks;
	}
}

function blocksy_manager() {
	return Blocksy_Manager::instance();
}
