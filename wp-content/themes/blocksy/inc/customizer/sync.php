<?php

function blocksy_get_frontend_selector_for_prefix($prefix = '') {
	$prefix = trim($prefix, '_');

	if ($prefix === 'blog') {
		// return 'body:not(.woocommerce)';
		return 'body.blog';
	}

	if ($prefix === 'woo') {
		return 'body.woocommerce';
	}

	if ($prefix === 'single_blog_post') {
		return 'body.single.single-post';
	}

	if ($prefix === 'single_page') {
		return 'body.page';
	}

	if ($prefix === 'woo_categories') {
		return ['body.post-type-archive-product', 'body.woocommerce-archive'];
	}

	if (substr_compare(
		$prefix,
		'_single',
		strlen($prefix)-strlen('_single'),
		strlen('_single')) === 0
	) {
		return 'body.single-' . str_replace(
			'_single',
			'',
			$prefix
		);
	}

	if (substr_compare(
		$prefix,
		'_archive',
		strlen($prefix)-strlen('_archive'),
		strlen('_archive')) === 0
	) {
		return 'body.tax-' . str_replace(
			'_archive',
			'',
			$prefix
		);
	}

	return '';
}

function blocksy_sync_single_post_container($args = []) {
	$selector = 'article[id*="post"][class*="type-"]';

	if (isset($args['prefix']) && strpos($args['prefix'], 'product') !== false) {
		$selector = 'main#main';

		if (! isset($args['loader_selector'])) {
			$args['loader_selector'] = '.product.type-product';
		}
	}

	if (
		isset($args['prefix'])
		&&
		strpos($args['prefix'], 'product-review') !== false
	) {
		$selector = 'main#main';

		if (! isset($args['loader_selector'])) {
			$args['loader_selector'] = 'article[id*="post"]';
		}
	}

	return array_merge([
		'selector' => $selector,
		'render' => function () use ($args) {
			if (
				isset($args['prefix'])
				&&
				strpos($args['prefix'], 'product') !== false
			) {
				echo blocksy_replace_current_template();
				return;
			}

			if (have_posts()) {
				the_post();
			}

			echo blocksy_single_content();
		}
	], $args);
}

function blocksy_replace_current_template() {
	$tag_templates = [
		'is_embed'             => 'get_embed_template',
		'is_404'               => 'get_404_template',
		'is_search'            => 'get_search_template',
		'is_front_page'        => 'get_front_page_template',
		'is_home'              => 'get_home_template',
		'is_privacy_policy'    => 'get_privacy_policy_template',
		'is_post_type_archive' => 'get_post_type_archive_template',
		'is_tax'               => 'get_taxonomy_template',
		'is_attachment'        => 'get_attachment_template',
		'is_single'            => 'get_single_template',
		'is_page'              => 'get_page_template',
		'is_singular'          => 'get_singular_template',
		'is_category'          => 'get_category_template',
		'is_tag'               => 'get_tag_template',
		'is_author'            => 'get_author_template',
		'is_date'              => 'get_date_template',
		'is_archive'           => 'get_archive_template',
	];

	$template = false;

	foreach ($tag_templates as $tag => $template_getter) {
		if (call_user_func($tag)) {
			$template = call_user_func($template_getter);
		}

		if ($template) {
			break;
		}
	}

	if (! $template) {
		$template = get_index_template();
	}

	$template = apply_filters('template_include', $template);
	$theme_directory = get_template_directory();

	if (
		is_singular()
		&&
		strpos($template, $theme_directory) !== false
		&&
		(
			! is_singular('courses')
			&&
			function_exists('tutor_course_enrolled_lead_info')
			||
			! function_exists('tutor_course_enrolled_lead_info')
		)
	) {
		ob_start();

		echo '<main ' . blocksy_main_attr() . '>';

		blocksy_before_current_template();
		get_template_part('template-parts/single');
		blocksy_after_current_template();

		echo '</main>';

		return ob_get_clean();
	}


	if (
		strpos($template, $theme_directory) !== false
		&&
		(
			basename($template) === 'index.php'
		)
	) {
		ob_start();

		echo '<main ' . blocksy_main_attr() . '>';

		blocksy_before_current_template();
		get_template_part('template-parts/archive');
		blocksy_after_current_template();

		echo '</main>';

		return ob_get_clean();
	}

	if ($template) {
		ob_start();

		include $template;

		$content = ob_get_clean();

		preg_match('/<main id="main".*?\\>/s', $content, $result);

		$without_header = preg_split('/<main id="main".*?\\>/s', $content)[1];
		$without_footer = preg_split(
			'/<footer id="footer" class="ct-footer".*?\\>/s',
			$without_header
		)[0];

		return $result[0] . $without_footer . '</main>';
	}

	return '';
}

add_filter('customize_dynamic_partial_class', function ($class, $id, $args ) {
	return 'Blocksy_WP_Customize_Partial';
}, 10, 3);

class Blocksy_WP_Customize_Partial extends WP_Customize_Partial {
	public $loader_selector;

	public function __construct( WP_Customize_Selective_Refresh $component, $id, $args = array() ) {
		parent::__construct($component, $id, $args);

		if (isset($args['loader_selector'])) {
			$this->loader_selector = $args['loader_selector'];
		}
	}

	public function json () {
		$json = parent::json();

		if ($this->loader_selector) {
			$json['loader_selector'] = $this->loader_selector;
		}

		return $json;
	}
}


