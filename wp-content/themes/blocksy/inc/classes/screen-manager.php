<?php

class Blocksy_Screen_Manager {
	private $prefixes = [];

	public function wipe_caches() {
		$this->prefixes = [];
	}

	public function uses_woo_default_template() {
		global $blocksy_is_quick_view;

		/**
		 * Treat product filtering requests as being default woo templates.
		 */
		if (
			isset($_POST['action'])
			&&
			strpos($_POST['action'], 'prdctfltr') !== false
		) {
			return true;
		}

		if ($blocksy_is_quick_view) {
			return true;
		}

		global $blocksy_is_floating_cart;

		if ($blocksy_is_floating_cart) {
			return true;
		}

		if (! function_exists('WC')) {
			return false;
		}

		$current_template = blocksy_manager()->get_current_template();

		if (! $current_template) {
			return false;
		}

		$result = strpos(
			$current_template,
			WC()->plugin_path() . '/templates/'
		) !== false || strpos(
			$current_template,
			get_template_directory()
		) !== false;

		return apply_filters(
			'blocksy:woocommerce:general:default-template-used',
			$result,
			$current_template
		);
	}

	public function get_prefix($args = []) {
		$args = wp_parse_args($args, [
			'allowed_prefixes' => null,
			'default_prefix' => null
		]);

		$args_key = md5(json_encode($args));

		if (! isset($this->prefixes[$args_key])) {
			$this->prefixes[$args_key] = $this->compute_prefix($args);
		}

		return $this->prefixes[$args_key];
	}

	public function get_prefix_addition($args = []) {
		$prefix = $this->get_prefix($args);

		if (
			$prefix === 'elementor_library_single'
			||
			$prefix === 'jet-woo-builder_single'
			||
			$prefix === 'brizy_template_single'
			||
			$prefix === 'ct_content_block_single'
		) {
			return ':preview-mode';
		}

		return '';
	}

	public function process_allowed_prefixes($actual_prefix, $args = []) {
		$args = wp_parse_args($args, [
			'actual_prefix' => null,
			'allowed_prefixes' => null,
			'default_prefix' => null
		]);

		if (
			! $actual_prefix
			|| (
				$args['allowed_prefixes'] && ! in_array(
					$actual_prefix,
					$args['allowed_prefixes']
				) && strpos($actual_prefix, '_archive') === false
			)
		) {
			if (! $args['default_prefix']) {
				return '';
			}

			return $args['default_prefix'];
		}

		return $actual_prefix;
	}

	public function get_single_prefixes($args = []) {
		$result = ['single_blog_post', 'single_page'];

		$args = wp_parse_args(
			$args,
			[
				'has_bbpress' => false,
				'has_buddy_press' => false
			]
		);

		$custom_post_types = blocksy_manager()->post_types->get_supported_post_types();

		foreach ($custom_post_types as $cpt) {
			$result[] = $cpt . '_single';
		}

		return $result;
	}

	public function get_admin_prefix($post_type) {
		if ($post_type === 'post') {
			return 'single_blog_post';
		}

		if ($post_type === 'page') {
			return 'single_page';
		}

		return $post_type . '_single';
	}

	public function get_archive_prefixes($args = []) {
		$result = ['blog'];

		$args = wp_parse_args(
			$args,
			[
				'has_woocommerce' => false,
				'has_categories' => false,
				'has_author' => false,
				'has_search' => false
			]
		);

		if ($args['has_categories']) {
			$result[] = 'categories';
		}

		if ($args['has_author']) {
			$result[] = 'author';
		}

		if ($args['has_search']) {
			$result[] = 'search';
		}

		if ($args['has_woocommerce'] && function_exists('is_product')) {
			$result[] = 'woo_categories';
		}

		$custom_post_types = blocksy_manager()->post_types->get_supported_post_types();

		foreach ($custom_post_types as $cpt) {
			$result[] = $cpt . '_archive';
		}

		return $result;
	}

	private function compute_prefix($args = []) {
		$args = wp_parse_args($args, [
			'allowed_prefixes' => null,
			'default_prefix' => null
		]);

		if (isset($_GET['blocksy_prefix'])) {
			return $_GET['blocksy_prefix'];
		}

		if (function_exists('is_lifterlms') && is_lifterlms()) {
			return 'lms';
		}

		$actual_prefix = null;

		if (function_exists('is_bbpress') && (
			get_post_type() === 'forum'
			||
			get_post_type() === 'topic'
			||
			get_post_type() === 'reply'
			||
			get_query_var('post_type') === 'forum'
			||
			bbp_is_single_user_profile()
			||
			bbp_is_topic_tag()
			||
			bbp_is_topic_tag_edit()
		)) {
			$actual_prefix = 'bbpress_single';
		}

		if (function_exists('is_buddypress') && (
			is_buddypress()
		)) {
			$actual_prefix = 'buddypress_single';
		}

		if (get_post_type() === 'jet-woo-builder') {
			$actual_prefix  = 'jet-woo-builder_single';
		}

		if (blocksy_is_page([
			'shop_is_page' => false,
			'blog_is_page' => false
		]) || is_single() && ! is_tax()) {
			if (is_single()) {
				$post_type = blocksy_manager()->post_types->is_supported_post_type();

				if ($post_type) {
					$actual_prefix = $post_type . '_single';
				}
			}

			if (! $actual_prefix) {
				$actual_prefix = blocksy_is_page() ? 'single_page' : 'single_blog_post';
			}
		}

		if (get_post_type() === 'elementor_library') {
			$actual_prefix  = 'elementor_library_single';
		}

		if (get_post_type() === 'brizy_template') {
			$actual_prefix  = 'brizy_template_single';
		}

		if (get_post_type() === 'ct_content_block') {
			$actual_prefix = 'ct_content_block_single';
		}

		if (function_exists('is_product_category') && ! is_author()) {
			$tax_obj = get_queried_object();

			if (
				is_product_category()
				||
				is_product_tag()
				||
				is_shop()
				||
				is_product_taxonomy()
				||
				(
					is_tax()
					&&
					function_exists( 'taxonomy_is_product_attribute')
					&&
					$tax_obj
					&&
					taxonomy_is_product_attribute($tax_obj->taxonomy)
				)
			) {
				$actual_prefix = 'woo_categories';
			}

			if (is_product()) {
				$actual_prefix = 'product';
			}
		}

		if (
			(
				is_category()
				||
				is_tag()
				||
				is_tax()
				||
				is_archive()
				||
				is_post_type_archive()
			) && ! is_author() && ! $actual_prefix
		) {
			$post_type = blocksy_manager()->post_types->is_supported_post_type();

			if ($post_type) {
				$actual_prefix = $post_type . '_archive';
			} else {
				$actual_prefix = 'categories';
			}
		}

		if (is_home()) {
			$post_type = blocksy_manager()->post_types->is_supported_post_type();

			if ($post_type) {
				$actual_prefix = $post_type . '_archive';
			} else {
				$actual_prefix = 'blog';
			}
		}

		$actual_post_type = get_query_var('post_type');

		if (empty($actual_post_type) && isset($_GET['ct_post_type'])) {
			$actual_post_type = explode(':', $_GET['ct_post_type']);
		}

		if (is_search()) {
			$actual_prefix = 'search';

			if (
				is_array($actual_post_type)
				&&
				count($actual_post_type) === 1
				&&
				$actual_post_type[0] !== 'page'
			) {
				if ($actual_post_type[0] === 'post') {
					$actual_prefix = 'blog';
				}

				$post_type = blocksy_manager()->post_types->is_supported_post_type();

				if ($post_type) {
					$actual_prefix = $post_type . '_archive';
				}
			}
		}

		if (is_author()) {
			$actual_prefix = 'author';
		}

		return $this->process_allowed_prefixes($actual_prefix, $args);
	}
}

/**
 * Treat non-posts home page as a simple page.
 */
if (! function_exists('blocksy_is_page')) {
	function blocksy_is_page($args = []) {
		$args = wp_parse_args(
			$args,
			[
				'shop_is_page' => true,
				'blog_is_page' => true
			]
		);

		static $static_result = null;

		if ($static_result !== null) {
		}

		$result = (
			(
				$args['blog_is_page']
				&&
				is_home()
				&&
				! is_front_page()
			) || is_page() || (
				$args['shop_is_page'] && function_exists('is_shop') && is_shop()
			) || is_attachment()
		);

		if ($result) {
			$post_id = strval(get_the_ID());

			if (is_home() && !is_front_page()) {
				$post_id = get_option('page_for_posts');
			}

			if (function_exists('is_shop') && is_shop()) {
				$post_id = get_option('woocommerce_shop_page_id');
			}

			if (get_post_type($post_id) !== 'page') {
				$post_id = get_queried_object_id();
			}

			$static_result = $post_id;

			if ($post_id === '0') {
				return true;
			}

			return $post_id;
		}

		$static_result = false;
		return false;
	}
}
