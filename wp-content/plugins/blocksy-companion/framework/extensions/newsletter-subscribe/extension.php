<?php

require_once dirname(__FILE__) . '/helpers.php';
require_once dirname(__FILE__) . '/includes/BlocksyNewsletterManager.php';
require_once dirname(__FILE__) . '/includes/BlocksyMailchimpManager.php';
require_once dirname(__FILE__) . '/includes/BlocksyMailerliteManager.php';

class BlocksyExtensionNewsletterSubscribe {
	public function __construct() {

		add_filter('blocksy-options-scripts-dependencies', function ($d) {
			$d[] = 'blocksy-ext-newsletter-subscribe-admin-scripts';
			return $d;
		});

		add_action('admin_enqueue_scripts', function () {
			if (! function_exists('get_plugin_data')) {
				require_once(ABSPATH . 'wp-admin/includes/plugin.php');
			}

			$data = get_plugin_data(BLOCKSY__FILE__);

			wp_register_script(
				'blocksy-ext-newsletter-subscribe-admin-scripts',
				BLOCKSY_URL . 'framework/extensions/newsletter-subscribe/admin-static/bundle/main.js',
				[],
				$data['Version'],
				true
			);

			wp_localize_script(
				'blocksy-ext-newsletter-subscribe-admin-scripts',
				'blocksy_ext_newsletter_subscribe_localization',
				[
					'public_url' => BLOCKSY_URL . 'framework/extensions/newsletter-subscribe/admin-static/bundle/',
				]
			);
		});

		add_action('customize_controls_enqueue_scripts', function () {
			if (! function_exists('get_plugin_data')) {
				require_once(ABSPATH . 'wp-admin/includes/plugin.php');
			}

			$data = get_plugin_data(BLOCKSY__FILE__);

			wp_register_script(
				'blocksy-ext-newsletter-subscribe-admin-scripts',
				BLOCKSY_URL . 'framework/extensions/newsletter-subscribe/admin-static/bundle/main.js',
				[],
				$data['Version'],
				true
			);

			wp_localize_script(
				'blocksy-ext-newsletter-subscribe-admin-scripts',
				'blocksy_ext_newsletter_subscribe_localization',
				[
					'public_url' => BLOCKSY_URL . 'framework/extensions/newsletter-subscribe/admin-static/bundle/',
				]
			);
		});

		add_action('wp_enqueue_scripts', function () {
			if (! function_exists('get_plugin_data')) {
				require_once(ABSPATH . 'wp-admin/includes/plugin.php');
			}

			$data = get_plugin_data(BLOCKSY__FILE__);

			if (is_admin()) {
				return;
			}

			wp_enqueue_style(
				'blocksy-ext-newsletter-subscribe-styles',
				BLOCKSY_URL . 'framework/extensions/newsletter-subscribe/static/bundle/main.min.css',
				['ct-main-styles'],
				$data['Version']
			);
		}, 50);

		add_filter('blocksy:frontend:dynamic-js-chunks', function ($chunks) {
			$chunks[] = [
				'id' => 'blocksy_ext_newsletter_subscribe',
				'selector' => implode(', ', [
					'.ct-newsletter-subscribe-widget-form:not([data-skip-submit])',
					'.ct-newsletter-subscribe-block-form:not([data-skip-submit])'
				]),
				'url' => blc_call_fn(
					[
						'fn' => 'blocksy_cdn_url',
						'default' => BLOCKSY_URL . 'framework/extensions/newsletter-subscribe/static/bundle/main.js'
					],
					BLOCKSY_URL . 'framework/extensions/newsletter-subscribe/static/bundle/main.js'
				),
				'trigger' => 'submit'
			];

			return $chunks;
		});

		add_filter('blocksy_widgets_paths', function ($all_widgets) {
			$all_widgets[] = dirname(__FILE__) . '/ct-newsletter-subscribe';
			return $all_widgets;
		});

		add_filter(
			'blocksy_single_posts_end_customizer_options',
			function ($opts, $prefix) {
				if ($prefix !== 'single_blog_post') {
					return $opts;
				}

				$opts['newsletter_subscribe_single_post_enabled'] = blc_call_fn(
					['fn' => 'blocksy_get_options'],
					dirname( __FILE__ ) . '/customizer.php',
					[], false
				);

				return $opts;
			},
			10, 2
		);

		add_filter('blocksy_extensions_metabox_post:elements:before', function ($opts) {
			$opts['disable_subscribe_form'] = [
					'label' => __( 'Disable Subscribe Form', 'blocksy-companion' ),
					'type' => 'ct-switch',
					'value' => 'no',
			];

			return $opts;
		}, 5);

		add_action(
			'customize_preview_init',
			function () {
				if (! function_exists('get_plugin_data')) {
					require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
				}

				$data = get_plugin_data(BLOCKSY__FILE__);

				wp_enqueue_script(
					'blocksy-newsletter-subscribe-customizer-sync',
					BLOCKSY_URL . 'framework/extensions/newsletter-subscribe/admin-static/bundle/sync.js',
					[ 'customize-preview', 'ct-customizer' ],
					$data['Version'],
					true
				);
			}
		);

		add_action(
			'wp_ajax_blc_newsletter_subscribe_process_mailerlite_subscribe',
			[$this, 'newsletter_subscribe_process_mailerlite_subscribe']
		);

		add_action(
			'wp_ajax_nopriv_blc_newsletter_subscribe_process_mailerlite_subscribe',
			[
				$this,
				'newsletter_subscribe_process_mailerlite_subscribe'
			]
		);

		add_shortcode('blocksy_newsletter_subscribe', function ($args, $content) {
			$args = wp_parse_args(
				$args,
				[
					'has_title' => false,
					'has_description' => false,

					'button_text' => __('Subscribe', 'blocksy-companion'),

					// no | yes
					'has_name' => 'no',

					'name_label' => __('Your name', 'blocksy-companion'),
					'email_label' => __('Your email', 'blocksy-companion'),
					'list_id' => '',
					'class' => ''
				]
			);

			$args['class'] = 'ct-newsletter-subscribe-shortcode ' . $args['class'];

			return blc_ext_newsletter_subscribe_output_form($args);
		});

		add_action(
			'blocksy:global-dynamic-css:enqueue',
			'BlocksyExtensionNewsletterSubscribe::add_global_styles',
			10, 3
		);
	}

	static public function add_global_styles($args) {
		blocksy_theme_get_dynamic_styles(array_merge([
			'path' => dirname(__FILE__) . '/global.php',
			'chunk' => 'global',
		], $args));
	}

	static public function onDeactivation() {
		remove_action(
			'blocksy:global-dynamic-css:enqueue',
			'BlocksyExtensionNewsletterSubscribe::add_global_styles',
			10, 3
		);
	}

	public function newsletter_subscribe_process_mailerlite_subscribe() {
		if (! isset($_POST['EMAIL'])) {
			wp_send_json_error();
		}

		if (! isset($_POST['GROUP'])) {
			wp_send_json_error();
		}

		$email = $_POST['EMAIL'];
		$name = '';
		$group = $_POST['GROUP'];

		if (isset($_POST['FNAME'])) {
			$name = $_POST['FNAME'];
		}

		$manager = BlocksyNewsletterManager::get_for_settings();

		$result = $manager->subscribe_form([
			'email' => $email,
			'name' => $name,
			'group' => $group
		]);

		wp_send_json_success($result);
	}
}

