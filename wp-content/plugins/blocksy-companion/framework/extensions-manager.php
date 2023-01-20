<?php

namespace Blocksy;

class ExtensionsManager {
	/**
	 * Collection of all the activated extensions.
	 *
	 * @var array The array of all the extension objects.
	 */
	private $extensions = [];

	private function get_option_name() {
		return 'blocksy_active_extensions';
	}

	public function get($id, $args = []) {
		$args = wp_parse_args($args, [
			// regular | preboot
			'type' => 'regular',
		]);

		if (! isset($this->extensions[$id])) {
			return null;
		}

		if ($args['type'] === 'preboot') {
			if (! isset($this->extensions[$id]['__object_preboot'])) {
				return null;
			}

			return $this->extensions[$id]['__object_preboot'];
		}

		if (! isset($this->extensions[$id]['__object'])) {
			return null;
		}

		return $this->extensions[$id]['__object'];
	}

	/**
	 * Collect all available extensions and activate the ones that have to be so.
	 */
	public function __construct() {
		$this->read_installed_extensions();

		if ($this->is_dashboard_page()) {
			$this->do_extensions_preboot();
		}

		foreach ($this->get_activated_extensions() as $single_id) {
			$this->boot_activated_extension_for($single_id);
		}

		add_action(
			'activate_blocksy-companion/blocksy-companion.php',
			[$this, 'handle_activation'],
			11
		);

		add_action(
			'deactivate_blocksy-companion/blocksy-companion.php',
			[$this, 'handle_deactivation'],
			11
		);
	}

	public function handle_activation() {
		ob_start();

		foreach ($this->get_activated_extensions() as $id) {
			if (method_exists($this->get_class_name_for($id), "onActivation")) {
				call_user_func([
					$this->get_class_name_for($id),
					'onActivation'
				]);
			}
		}

		ob_get_clean();
	}

	public function handle_deactivation() {
		foreach ($this->get_activated_extensions() as $id) {
			if (method_exists($this->get_class_name_for($id), "onDeactivation")) {
				call_user_func([
					$this->get_class_name_for($id),
					'onDeactivation'
				]);
			}
		}
	}

	public function do_extensions_preboot() {
		foreach (array_keys($this->get_extensions()) as $single_id) {
			$this->maybe_do_extension_preboot($single_id);
		}
	}

	private function is_dashboard_page() {
		global $pagenow;

		$is_ct_settings =
			// 'themes.php' === $pagenow &&
			isset( $_GET['page'] ) && 'ct-dashboard' === $_GET['page'];

		return $is_ct_settings;
	}

	public function get_extensions($args = []) {
		$args = wp_parse_args($args, [
			'forced_reread' => false,
		]);

		if ($args['forced_reread']) {
			foreach ($this->extensions as $id => $extension) {
				$this->extensions[$id]['config'] = $this->read_config_for(
					$extension['path']
				);

				$this->extensions[$id]['readme'] = $this->read_readme_for(
					$extension['path']
				);
			}

			$this->register_fake_extensions();
		}

		return $this->extensions;
	}

	public function can( $capability = 'install_plugins' ) {
		$user = wp_get_current_user();

		// return array_intersect(['administrator'], $user->roles );

		if ( is_multisite() ) {
			// Only network admin can change files that affects the entire network.
			$can = current_user_can_for_blog( get_current_blog_id(), $capability );
		} else {
			$can = current_user_can( $capability );
		}

		if ( $can ) {
			// Also you can use this method to get the capability.
			$can = $capability;
		}

		return $can;
	}

	public function activate_extension($id) {
		if (! isset($this->extensions[$id])) {
			return;
		}

		if (! $this->extensions[$id]['path']) {
			return;
		}

		$activated = $this->get_activated_extensions();

		if (! in_array(strtolower($id), $activated)) {
			$path = $this->extensions[$id]['path'];
			require_once($path . '/extension.php');

			if (method_exists($this->get_class_name_for($id), "onActivation")) {
				call_user_func([
					$this->get_class_name_for($id),
					'onActivation'
				]);
			}

			$class = $this->get_class_name_for($id);

			// Init extension right away.
			new $class;
		}

		$activated[] = strtolower($id);


		update_option($this->get_option_name(), array_unique($activated));

		do_action('blocksy:dynamic-css:refresh-caches');
	}

	public function deactivate_extension($id) {
		if (! isset($this->extensions[$id])) {
			return;
		}

		if (! $this->extensions[$id]['path']) {
			return;
		}

		$activated = $this->get_activated_extensions();

		if (in_array(strtolower($id), $activated)) {
			if (method_exists($this->get_class_name_for($id), "onDeactivation")) {
				call_user_func([
					$this->get_class_name_for($id),
					'onDeactivation'
				]);
			}
		}

		update_option($this->get_option_name(), array_diff(
			$activated,
			[$id]
		));

		do_action('blocksy:dynamic-css:refresh-caches');
	}

	private function read_installed_extensions() {
		$paths_to_look_for_extensions = apply_filters(
			'blocksy_extensions_paths',
			[
				BLOCKSY_PATH . 'framework/extensions'
			]
		);

		foreach ($paths_to_look_for_extensions as $single_path) {
			$all_extensions = glob($single_path . '/*', GLOB_ONLYDIR);

			foreach ($all_extensions as $single_extension) {
				$this->register_extension_for($single_extension);
			}
		}

		$this->register_fake_extensions();
	}

	private function register_fake_extensions() {
		if (function_exists('blc_fs') && blc_fs()->can_use_premium_code()) {
			return;
		}

		$this->extensions['adobe-typekit'] = [
			'path' => null,
			'__object' => null,
			'config' => [
				'name' => __('Adobe Typekit', 'blocksy-companion'),
				'description' => __('Connect your Typekit account and use your fonts in any typography option.', 'blocksy-companion'),
				'pro' => true
			],
			'readme' => '',
			'data' => null
		];

		$this->extensions['custom-code-snippets'] = [
			'path' => null,
			'__object' => null,
			'config' => [
				'name' => __('Custom Code Snippets', 'blocksy-companion'),
				'description' => __('Add custom code snippets in your header and footer, globally and per post or page individually.', 'blocksy-companion'),
				'pro' => true
			],
			'readme' => '',
			'data' => null
		];

		$this->extensions['custom-fonts'] = [
			'path' => null,
			'__object' => null,
			'config' => [
				'name' => __('Custom Fonts', 'blocksy-companion'),
				'description' => __('Upload unlimited number of custom fonts or variable fonts and use them in any typography option.', 'blocksy-companion'),
				'pro' => true
			],
			'readme' => '',
			'data' => null
		];

		$this->extensions['local-google-fonts'] = [
			'path' => null,
			'__object' => null,
			'config' => [
				'name' => __('Local Google Fonts', 'blocksy-companion'),
				'description' => __('Serve Google Fonts from your own server for full GDPR compliancy.', 'blocksy-companion'),
				'pro' => true
			],
			'readme' => '',
			'data' => null
		];

		$this->extensions['advanced-menu'] = [
			'path' => null,
			'__object' => null,
			'config' => [
				'name' => __('Advanced Menu', 'blocksy-companion'),
				'description' => __('Create beautiful mega menus, assign icons add badges to menu items, and content blocks inside menu items.', 'blocksy-companion'),
				'pro' => true
			],
			'readme' => '',
			'data' => null
		];

		$this->extensions['shortcuts-bar'] = [
			'path' => null,
			'__object' => null,
			'config' => [
				'name' => __('Shortcuts Bar', 'blocksy-companion'),
				'description' => __('Transform your website into a app like by displaying a neat shortcuts bar at the bottom of the vieport.', 'blocksy-companion'),
				'pro' => true
			],
			'readme' => '',
			'data' => null
		];

		$this->extensions['multiple-sidebars'] = [
			'path' => null,
			'__object' => null,
			'config' => [
				'name' => __('Multiple Sidebars', 'blocksy-companion'),
				'description' => __('Create unlimited number of sidebars and display them conditionaly on any page or post.', 'blocksy-companion'),
				'pro' => true
			],
			'readme' => '',
			'data' => null
		];

		$this->extensions['white-label'] = [
			'path' => null,
			'__object' => null,
			'config' => [
				'name' => __('White Label (Agency Package)', 'blocksy-companion'),
				'description' => __('Change the theme and companion plugin branding to your own custom one.', 'blocksy-companion'),
				'pro' => true
			],
			'readme' => '',
			'data' => null
		];

		$this->extensions['woocommerce-extra'] = [
			'path' => null,
			'__object' => null,
			'config' => [
				'name' => __('WooCommerce Extra', 'blocksy-companion'),
				'description' => __('Increase the conversion rate by adding a product quick view modal, a floating cart. Control the single product gallery/slider and the layout, add a wishlits page.', 'blocksy-companion'),
				'pro' => true
			],
			'readme' => '',
			'data' => null
		];
	}

	private function register_extension_for($path) {
		$id = str_replace('_', '-', basename($path));

		if (isset($this->extensions[$id])) return;

		$this->extensions[$id] = [
			'path' => $path,
			'__object' => null,
			'config' => $this->read_config_for($path),
			'readme' => $this->read_readme_for($path),
			'data' => null
		];
	}

	private function maybe_do_extension_preboot($id) {
		if (! isset($this->extensions[$id])) return false;
		if (isset($this->extensions[$id]['__object_preboot'])) return;

		$class_name = explode( '-', $id );
		$class_name = array_map( 'ucfirst', $class_name );
		$class_name = 'BlocksyExtension' . implode( '', $class_name ) . 'PreBoot';

		$path = $this->extensions[$id]['path'];

		if (! $path) {
			return;
		}

		if (! @is_readable($path . '/pre-boot.php')) {
			return;
		}

		if (! file_exists($path . '/pre-boot.php')) {
			return;
		}

		require_once($path . '/pre-boot.php');

		$this->extensions[$id]['__object_preboot'] = new $class_name();

		if (method_exists(
			$this->extensions[$id]['__object_preboot'], 'ext_data'
		)) {
			$this->extensions[$id]['data'] = $this->extensions[
				$id
			]['__object_preboot']->ext_data();
		}
	}

	private function boot_activated_extension_for($id) {
		if (! isset($this->extensions[$id])) return false;
		if (! isset($this->extensions[$id]['path'])) return false;
		if (! $this->extensions[$id]['path']) return false;

		if (
			isset($this->extensions[$id]['config']['hidden'])
			&&
			$this->extensions[$id]['config']['hidden']
		) {
			return;
		}

		if (isset($this->extensions[$id]['__object'])) return;

		$class_name = explode( '-', $id );
		$class_name = array_map( 'ucfirst', $class_name );
		$class_name = 'BlocksyExtension' . implode( '', $class_name );

		$path = $this->extensions[$id]['path'];

		if (! $path) {
			return;
		}

		if (! @is_readable($path . '/extension.php')) {
			return;
		}

		if (! file_exists($path . '/extension.php')) {
			return;
		}

		require_once($path . '/extension.php');

		$this->extensions[$id]['__object'] = new $class_name();
	}

	private function get_class_name_for($id) {
		$class_name = explode( '-', $id );
		$class_name = array_map( 'ucfirst', $class_name );
		return 'BlocksyExtension' . implode( '', $class_name );
	}

	private function read_readme_for($path) {
		if (! $path) {
			return null;
		}

		$readme = '';

		ob_start();

		if (is_readable($path . '/readme.php')) {
			require $path . '/readme.php';
		}

		$readme = ob_get_clean();

		if (empty(trim($readme))) {
			return null;
		}

		return trim($readme);
	}

	private function read_config_for( $file_path ) {
		$_extract_variables = [ 'config' => [] ];

		if (is_readable($file_path . '/config.php')) {
			require $file_path . '/config.php';

			foreach ($_extract_variables as $variable_name => $default_value) {
				if (isset($$variable_name)) {
					$_extract_variables[ $variable_name ] = $$variable_name;
				}
			}
		}

		$name = explode('-', basename($file_path));
		$name = array_map('ucfirst', $name);
		$name = implode(' ', $name);

		$_extract_variables['config'] = array_merge(
			[
				'name' => $name,
				'description' => '',
				'pro' => false,
				'hidden' => false
			],
			$_extract_variables['config']
		);

		return $_extract_variables['config'];
	}

	private function get_activated_extensions() {
		return get_option($this->get_option_name(), []);
	}
}

