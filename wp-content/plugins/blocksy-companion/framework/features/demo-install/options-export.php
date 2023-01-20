<?php

namespace Blocksy;

class DemoInstallOptionsExport {
	public function get_exported_options_keys() {
		return [
			'blocksy_ext_mailchimp_credentials',
			'blocksy_active_extensions'
		];
	}

	private $core_options = array(
		'blogname',
		'blogdescription',
		// 'show_on_front',
		// 'page_on_front',
		// 'page_for_posts',
	);

	private $page_ids = [
		'woocommerce_shop_page_id',
		'woocommerce_cart_page_id',
		'woocommerce_checkout_page_id',
		'woocommerce_pay_page_id',
		'woocommerce_thanks_page_id',
		'woocommerce_myaccount_page_id',
		'woocommerce_edit_address_page_id',
		'woocommerce_view_order_page_id',
		'woocommerce_change_password_page_id',
		'woocommerce_logout_page_id',
		'woocommerce_lost_password_page_id',
		'page_on_front',
		'page_for_posts'
	];

	public function export() {
		$theme = get_stylesheet();
		$template = get_template();
		$charset = get_option( 'blog_charset' );
		$mods = get_theme_mods();

		$data = [
			'template' => $template,
			'mods' => $mods ? $mods : [],
			'options' => []
		];

		global $wp_customize;

		// Get options from the Customizer API.
		$settings = $wp_customize->settings();

		foreach ($settings as $key => $setting) {
			if ('option' == $setting->type) {
				if ('widget_' === substr(strtolower($key), 0, 7)) {
					continue;
				}

				if ('sidebars_' === substr(strtolower($key), 0, 9)) {
					continue;
				}

				if (in_array($key, $this->core_options)) {
					continue;
				}

				$data['options'][$key] = $setting->value();
			}
		}

		$option_keys = $this->get_exported_options_keys();

		foreach ($option_keys as $option_key) {
			$data['options'][$option_key] = get_option($option_key);
		}

		if (function_exists('wp_get_custom_css_post')) {
			$data['wp_css'] = wp_get_custom_css();
		}

		/**
		 * Temporary work around until Elementor comes up with something better
		 */
		if (class_exists('\Elementor\Plugin')) {
			$default_post_id = \Elementor\Plugin::$instance->kits_manager->get_active_id();

			if (! empty($default_post_id)) {
				$global_data = get_post_meta(
					$default_post_id,
					'_elementor_page_settings',
					true
				);

				$data['elementor_active_kit_settings'] = $global_data;
			}
		}

		return $data;

		return serialize($data);
	}

	public function export_pages_ids_options() {
		$result = [];

		foreach ($this->page_ids as $single_page_id) {
			$id = get_option($single_page_id, null);

			$title = false;

			if ($id) {
				$title = get_the_title($id);
			}

			$result[$single_page_id] = $title;
		}

		return $result;
	}
}


