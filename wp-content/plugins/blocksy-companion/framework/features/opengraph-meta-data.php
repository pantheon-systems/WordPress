<?php

namespace Blocksy;

class OpenGraphMetaData {
	public function __construct() {
		add_filter(
			'blocksy_engagement_general_end_customizer_options',
			function ($opts) {

				$opts[] = [
					blocksy_rand_md5() => [
						'type' => 'ct-divider',
					],

					'enable_opengraph' => [
						'label' => __( 'OpenGraph Meta Data', 'blocksy-companion' ),
						'type' => 'ct-switch',
						'value' => 'no',
						'desc' => __( 'Enable the OpenGraph rich meta data features for your website.', 'blocksy-companion' ),
					],

					blocksy_rand_md5() => [
						'type' => 'ct-condition',
						'condition' => [ 'enable_opengraph' => 'yes' ],
						'options' => [

							'opengraph_facebook_page_url' => [
								'label' => __( 'Facebook Page URL', 'blocksy-companion' ),
								'type' => 'text',
								'design' => 'inline',
								'value' => ''
							],

							'opengraph_facebook_app_id' => [
								'label' => __( 'Facebook App ID', 'blocksy-companion' ),
								'type' => 'text',
								'design' => 'inline',
								'value' => ''
							],

							'opengraph_twitter_username' => [
								'label' => __( 'Twitter Username', 'blocksy-companion' ),
								'type' => 'text',
								'design' => 'inline',
								'value' => ''
							],

						],
					],
				];

				return $opts;
			}
		);

		add_action('wp_head', array($this, 'output_meta_tags'), 1 );
	}

	public function output_meta_tags() {
		if (defined('WPSEO_VERSION')) {
			return;
		}

		if (defined('RANK_MATH_FILE')) {
			return;
		}

		if (get_theme_mod('enable_opengraph', 'no') !== 'yes') {
			return;
		}

		$facebook_url = get_theme_mod('opengraph_facebook_page_url', '');

		add_filter('jetpack_disable_twitter_cards', '__return_true', 999999);
		add_filter('jetpack_enable_open_graph', '__return_false', 999999);
		add_filter('jetpack_enable_opengraph', '__return_false', 999999);

		// Type
		if (is_front_page() || is_home()) {
			$type = 'website';
		} else if (is_singular()) {
			$type = 'article';
		} else {
			$type = 'object';
		}

		if (is_singular()) {
			$title = get_the_title();
		} else {
			$title = get_the_archive_title();
		}

		$title = wp_strip_all_tags($title);

		// Description
		if (is_category() || is_tag() || is_tax()) {
			$description = wp_strip_all_tags(term_description());
		} else if (is_singular()) {
			$description = wp_strip_all_tags(blocksy_entry_excerpt(40));
		}

		// Image
		$image = '';
		$has_img = false;

		if (function_exists('is_product_category') && is_product_category()) {
			global $wp_query;

			$cat = $wp_query->get_queried_object();
			$thumbnail_id = get_term_meta($cat->term_id, 'thumbnail_id', true);
			$get_image = wp_get_attachment_url($thumbnail_id);

			if ($get_image) {
				$image = $get_image;
				$has_img = true;
			}
		} else if (is_singular()) {
			$get_image = wp_get_attachment_image_src(
				get_post_thumbnail_id(get_the_ID()),
				'full'
			);

			if ($get_image) {
				$image = $get_image[0];
			}
			$has_img = true;
		}

		// Post author
		if ($facebook_url) {
			$author = $facebook_url;
		}

		// Facebook publisher URL
		if (! empty($facebook_url)) {
			$publisher = $facebook_url;
		}

		$fb_app_id = get_theme_mod('opengraph_facebook_app_id', '');

		$twitter_handle = '@' . str_replace('@', '', get_theme_mod('opengraph_twitter_username'));

		$output = $this->get_open_graph_tag('property', 'og:type', trim($type));
		$output .= $this->get_open_graph_tag('property', 'og:title', trim($title));

		if (isset($description) && ! empty($description)) {
			$output .= $this->get_open_graph_tag('property', 'og:description', trim($description));
		}

		if ($has_img && $get_image) {
			$output .= $this->get_open_graph_tag('property', 'og:image', trim($image));
			$output .= $this->get_open_graph_tag('property', 'og:image:width', absint($get_image[1]));
			$output .= $this->get_open_graph_tag('property', 'og:image:height', absint($get_image[2]));
		}

		$output .= $this->get_open_graph_tag('property', 'og:url', trim(get_permalink()));
		$output .= $this->get_open_graph_tag('property', 'og:site_name', trim(get_bloginfo('name')));

		if (is_singular() && ! is_front_page()) {
			if (isset($author) && !empty($author)) {
				$output .= $this->get_open_graph_tag('property', 'article:author', trim($author));
			}

			if (is_singular('post')) {
				$output .= $this->get_open_graph_tag('property', 'article:published_time', trim(get_post_time('c')));
				$output .= $this->get_open_graph_tag('property', 'article:modified_time', trim(get_post_modified_time('c')));
				$output .= $this->get_open_graph_tag('property', 'og:updated_time', trim(get_post_modified_time('c')));
			}
		}

		if (is_singular()) {
			$tags = get_the_tags();

			if (! is_wp_error($tags) && (is_array($tags) && $tags !== [])) {
				foreach ($tags as $tag) {
					$output .= $this->get_open_graph_tag('property', 'article:tag', trim($tag->name));
				}
			}

			$terms = get_the_category();

			if (! is_wp_error($terms) && (is_array($terms) && $terms !== array())) {
				// We can only show one section here, so we take the first one.
				$output .= $this->get_open_graph_tag('property', 'article:section', trim($terms[0]->name));
			}
		}

		if (isset($publisher) && ! empty($publisher)) {
			$output .= $this->get_open_graph_tag('property', 'article:publisher', trim($publisher));
		}

		if (isset($fb_app_id) && ! empty($fb_app_id)) {
			$output .= $this->get_open_graph_tag('property', 'fb:app_id', trim($fb_app_id));
		}

		// Twitter
		$output .= $this->get_open_graph_tag('name', 'twitter:title', trim($title));

		if (isset($description) && !empty($description)) {
			$output .= $this->get_open_graph_tag('name', 'twitter:description', trim($description));
		}

		if ($has_img) {
			$output .= $this->get_open_graph_tag('name', 'twitter:card', 'summary_large_image');
			$output .= $this->get_open_graph_tag('name', 'twitter:image', trim($image));
		}

		if (isset($twitter_handle) && ! empty($twitter_handle) && strlen($twitter_handle) > 1) {
			$output .= $this->get_open_graph_tag('name', 'twitter:site', trim($twitter_handle));
			$output .= $this->get_open_graph_tag('name', 'twitter:creator', trim($twitter_handle));
		}

		echo $output;
	}

	private function get_open_graph_tag($attr, $property, $content) {
		return blocksy_html_tag(
			'meta',
			[
				$attr => $property,
				'content' => $content
			]
		) . "\n";
	}
}
