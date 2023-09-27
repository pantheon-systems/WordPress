<?php

namespace Blocksy;

class DemoInstall {
	protected $ajax_actions = [
		'blocksy_demo_export',
		'blocksy_demo_list',
		'blocksy_demo_install_child_theme',
		'blocksy_demo_activate_plugins',
		'blocksy_demo_fake_step',
		'blocksy_demo_erase_content',
		'blocksy_demo_install_widgets',
		'blocksy_demo_install_options',
		'blocksy_demo_install_content',
		'blocksy_demo_register_current_demo',
		'blocksy_demo_deregister_current_demo',
		'blocksy_demo_deactivate_plugins',
		'blocksy_demo_install_finish',

		// 'blocksy_extension_activate',
		// 'blocksy_extension_deactivate',
	];

	public function has_mock() {
		return true;
	}

	public function __construct() {
		$this->attach_ajax_actions();

		add_filter(
			'blocksy_dashboard_localizations',
			function ($d) {
				$d['has_demo_install'] = apply_filters(
					'blocksy_ext_demo_install_enabled',
					'yes'
				);

				return $d;
			}
		);

		// add_filter( 'woocommerce_enable_setup_wizard', '__return_false' );
		// add_filter( 'woocommerce_show_admin_notice', '__return_false' );
		// add_filter( 'woocommerce_prevent_automatic_wizard_redirect', '__return_false' );
	}

	public function blocksy_demo_install_child_theme() {
		$m = new DemoInstallChildThemeInstaller();
		$m->import();
	}

	public function blocksy_demo_erase_content() {
		$plugins = new DemoInstallContentEraser();
		$plugins->import();
	}

	public function blocksy_demo_install_widgets() {
		$plugins = new DemoInstallWidgetsInstaller();
		$plugins->import();
	}

	public function blocksy_demo_install_options() {
		$plugins = new DemoInstallOptionsInstaller();
		$plugins->import();
	}

	public function blocksy_demo_install_content() {
		$plugins = new DemoInstallContentInstaller();
		$plugins->import();
	}

	public function blocksy_demo_activate_plugins() {
		$plugins = new DemoInstallPluginsInstaller();
		$plugins->import();
	}

	public function blocksy_demo_fake_step() {
		$plugins = new DemoInstallFakeContentEraser();
		$plugins->import();
	}

	public function blocksy_demo_register_current_demo() {
		$this->start_streaming();

		if (! isset($_REQUEST['demo_name']) || !$_REQUEST['demo_name']) {
			Plugin::instance()->demo->emit_sse_message([
				'action' => 'complete',
				'error' => 'No demo name passed.',
			]);

			exit;
		}

		$demo_name = explode(':', $_REQUEST['demo_name']);

		if (! isset($demo_name[1])) {
			$demo_name[1] = '';
		}

		$demo = $demo_name[0];
		$builder = $demo_name[1];

		$this->set_current_demo($demo . ':' . $builder);

		Plugin::instance()->demo->emit_sse_message([
			'action' => 'complete',
			'error' => false
		]);

		exit;
	}

	public function blocksy_demo_deregister_current_demo() {
		$this->start_streaming();

		update_option('blocksy_ext_demos_current_demo', null);

		Plugin::instance()->demo->emit_sse_message([
			'action' => 'complete',
			'error' => false
		]);

		exit;
	}

	public function blocksy_demo_deactivate_plugins() {
		$plugins = new DemoInstallPluginsUninstaller();
		$plugins->import();
	}

	public function blocksy_demo_install_finish() {
		$finish = new DemoInstallFinalActions();
		$finish->import();
	}

	public function get_current_demo() {
		return get_option('blocksy_ext_demos_current_demo', null);
	}

	public function set_current_demo($demo) {
		update_option('blocksy_ext_demos_current_demo', [
			'demo' => $demo
		]);
	}

	public function fetch_single_demo($args = []) {
		$args = wp_parse_args(
			$args,
			[
				'demo' => $args['demo'],
				'builder' => '',
				'field' => ''
			]
		);

		$request = wp_remote_get('https://demo.creativethemes.com/?' . http_build_query([
			'route' => 'get_single',
			'demo' => $args['demo'] . ':' . $args['builder'],
			'field' => $args['field']
		]), [
			'sslverify' => false
		]);

		if (is_wp_error($request)) {
			return false;
		}

		$body = wp_remote_retrieve_body( $request );

		$body = json_decode($body, true);

		if (! $body) {
			return false;
		}

		return $body;
	}

	public function fetch_all_demos() {
		$request = wp_remote_get('https://demo.creativethemes.com/?route=get_all', [
			'sslverify' => false
		]);

		// $request = wp_remote_get('https://demo.creativethemes.BROKEN/?route=get_all');

		if (is_wp_error($request)) {
			return false;
		}

		$body = wp_remote_retrieve_body($request);

		$body = json_decode($body, true);

		if (! $body) {
			return false;
		}

		return $body;
	}

	public function blocksy_demo_list() {
		$demos = $this->fetch_all_demos();

		if (! $demos) {
			wp_send_json_error();
		}

		$plugins = [
			'coblocks' => false,
			'contact-form-7' => false,
			'woocommerce' => false,
			'brizy' => false,
			'elementor' => false,
		];

		foreach ($plugins as $plugin_name => $status) {
			$plugins_manager = $this->get_plugins_manager();

			$path = $plugins_manager->is_plugin_installed( $plugin_name );

			if ($path) {
				if ($plugins_manager->is_plugin_active($path)) {
					$plugins[$plugin_name] = true;
				}
			}
		}

		$has_demo_error = false;

		if (! extension_loaded('xml') && ! extension_loaded('simplexml')) {
			$has_demo_error = __("Your PHP installation doesn't have support for XML. Please install the <i>xml</i> or <i>simplexml</i> PHP extension in order to be able to install starter sites. You might need to contact your hosting provider to assist you in doing so.", 'blocksy-companion');
		}

		wp_send_json_success([
			'demos' => $demos,
			'active_plugins' => $plugins,
			'current_installed_demo' => $this->get_current_demo(),
			'demo_error' => $has_demo_error
		]);
	}

	public function blocksy_demo_export() {
		if (! current_user_can('edit_theme_options')) {
			wp_send_json_error();
		}

		global $wp_customize;

		$name = sanitize_text_field($_REQUEST['name']);
		$builder = sanitize_text_field($_REQUEST['builder']);
		$plugins = sanitize_text_field($_REQUEST['plugins']);
		$url = sanitize_text_field($_REQUEST['url']);
		$is_pro = sanitize_text_field($_REQUEST['is_pro']) === 'true';

		$plugins = explode(',', preg_replace('/\s+/', '', $plugins));

		$options_data = new DemoInstallOptionsExport();

		$widgets_data = new DemoInstallWidgetsExport();
		$widgets_data = $widgets_data->export();

		add_filter(
			'export_wp_all_post_types',
			function ($post_types) {
				$post_types['wpforms'] = 'wpforms';
				return $post_types;
			}
		);

		$content_data = new DemoInstallContentExport();
		$content_data = $content_data->export();

		wp_send_json_success([
			'demo' => [
				'name' => $name,
				'options' => $options_data->export(),
				'widgets' => $widgets_data,
				'content' => $content_data,

				'pages_ids_options' => $options_data->export_pages_ids_options(),
				'created_at' => date('d-m-Y'),

				'url' => $url,
				'is_pro' => !!$is_pro,
				'builder' => $builder,
				'plugins' => $plugins
			]
		]);
	}

	public function attach_ajax_actions() {
		foreach ($this->ajax_actions as $action) {
			add_action(
				'wp_ajax_' . $action,
				[ $this, $action ]
			);
		}
	}

	public function get_plugins_manager() {
		if (! class_exists('Blocksy_Plugin_Manager')) {
			require_once get_template_directory() . '/admin/dashboard/plugins/ct-plugin-manager.php';
		}

		return new \Blocksy_Plugin_Manager();
	}

	public function start_streaming() {
		// Turn off PHP output compression
		// $previous = error_reporting(error_reporting() ^ E_WARNING);
		ini_set('output_buffering', 'off');
		ini_set('zlib.output_compression', false);
		// error_reporting( $previous );
		// error_reporting(0);

		if ($GLOBALS['is_nginx']) {
			// Setting this header instructs Nginx to disable fastcgi_buffering
			// and disable gzip for this request.
			header('X-Accel-Buffering: no');
			header('Content-Encoding: none');
		}

		// Start the event stream.
		header('Content-Type: text/event-stream, charset=UTF-8');

		flush();

		// 2KB padding for IE
		echo ':' . str_repeat(' ', 2048) . "\n\n";
		// Time to run the import!
		set_time_limit(0);

		remove_action('shutdown', 'wp_ob_end_flush_all', 1);

		add_action('shutdown', function() {
			while (@ob_end_flush());
		});
    }

	public function emit_sse_message( $data ) {
		echo "event: message\n";
		echo 'data: ' . wp_json_encode( $data ) . "\n\n";
		// Extra padding.
		echo ':' . str_repeat( ' ', 2048 ) . "\n\n";
		flush();
	}
}
