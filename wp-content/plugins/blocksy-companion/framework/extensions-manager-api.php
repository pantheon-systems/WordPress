<?php

namespace Blocksy;

class ExtensionsManagerApi {
	public function __construct() {
		$this->attach_ajax_actions();

		if (wp_doing_ajax()) {
			$manager = Plugin::instance()->extensions;
			$manager->do_extensions_preboot();
		}
	}

	protected $ajax_actions = [
		'blocksy_extensions_status',
		'blocksy_extension_activate',
		'blocksy_extension_deactivate',
	];

	public function blocksy_extensions_status() {
		$this->check_capability('edit_theme_options');
		$manager = Plugin::instance()->extensions;

		$maybe_input = json_decode(file_get_contents('php://input'), true);

		$data = $manager->get_extensions([
			'forced_reread' => true
		]);

		if (
			$maybe_input
			&&
			isset($maybe_input['extension'])
			&&
			isset($maybe_input['extAction'])
		) {
			$ext_preboot = $manager->get($maybe_input['extension'], [
				'type' => 'preboot'
			]);

			if (method_exists(
				$ext_preboot, 'ext_action'
			)) {
				$result = $ext_preboot->ext_action($maybe_input['extAction']);

				if ($result) {
					$data[$maybe_input['extension']]['data'] = $result;
				}
			}
		}

		wp_send_json_success($data);
	}

	public function blocksy_extension_activate() {
		$this->check_capability('edit_theme_options');
		$manager = Plugin::instance()->extensions;

		$manager->activate_extension($this->get_extension_from_request());

		wp_send_json_success();
	}

	public function blocksy_extension_deactivate() {
		$this->check_capability('edit_theme_options');
		$manager = Plugin::instance()->extensions;

		$manager->deactivate_extension($this->get_extension_from_request());

		wp_send_json_success();
	}

	public function check_capability( $cap = 'install_plugins' ) {
		$manager = Plugin::instance()->extensions;

		if ( ! $manager->can( $cap ) ) {
			wp_send_json_error();
		}

		return true;
	}

	public function get_extension_from_request() {
		if ( ! isset( $_POST['ext'] ) ) {
			wp_send_json_error();
		}

		return addslashes($_POST['ext']);
	}

	public function attach_ajax_actions() {
		foreach ( $this->ajax_actions as $action ) {
			add_action(
				'wp_ajax_' . $action,
				[ $this, $action ]
			);
		}
	}
}

