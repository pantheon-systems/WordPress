<?php

if (! function_exists('blocksy_has_schema_org_markup')) {
	function blocksy_has_schema_org_markup() {
		return get_theme_mod('enable_schema_org_markup', 'yes') === 'yes';
	}
}

if (! function_exists('blocksy_schema_org_definitions')) {
	function blocksy_schema_org_definitions($place, $args = []) {
		$args = wp_parse_args(
			$args,
			[
				'array' => false,
				'to_merge' => []
			]
		);

		$value = [];

		if (!blocksy_has_schema_org_markup()) {
			if ($args['array']) {
				return $value;
			}

			return blocksy_attr_to_html($value);
		}

		if ($place === 'ratingValue') {
			$value = [
				'itemprop' => 'ratingValue'
			];
		}

		if ($place === 'reviewRating') {
			$value = [
				'itemscope' => 'itemscope',
				'itemtype' => 'https://schema.org/Rating',
				'itemprop' => 'reviewRating'
			];
		}

		if ($place === 'itemReviewed') {
			$value = [
				'itemprop' => 'itemReviewed',
				'itemscope' => '',
				'itemtype' => "https://schema.org/Thing"
			];
		}

		if ($place === 'reviewBody') {
			$value = [
				'itemprop' => 'reviewBody'
			];
		}

		if ($place === 'positiveNotes') {
			$value = [
				'itemprop' => 'positiveNotes'
			];
		}

		if ($place === 'negativeNotes') {
			$value = [
				'itemprop' => 'negativeNotes'
			];
		}

		if ($place === 'single') {
			if (is_page()) {
				$value = [
					'itemscope' => 'itemscope',
					'itemtype' => 'https://schema.org/WebPage'
				];
			} else if (function_exists('is_product') && is_product()) {
				$value = [
					'itemscope' => 'itemscope',
					'itemtype' => 'https://schema.org/WebPage'
				];
			} else if (is_single()) {
				$value = [
					'itemscope' => 'itemscope',
					'itemtype' => 'https://schema.org/Blog'
				];
			}
		}

		if ($place === 'creative_work') {
			if (is_singular('blc-product-review')) {
				$value = [
					'itemscope' => 'itemscope',
					'itemtype' => 'https://schema.org/Review'
				];
			} else if (is_single()) {
				$value = [
					'itemscope' => 'itemscope',
					'itemtype' => 'https://schema.org/CreativeWork'
				];
			} else {
				if (is_home() || is_archive()) {
					$value = [
						'itemscope' => 'itemscope',
						'itemtype' => 'https://schema.org/CreativeWork'
					];
				}
			}
		}

		if ($place === 'header') {
			$value = [
				'itemscope' => '',
				'itemtype' => 'https://schema.org/WPHeader'
			];
		}

		if ($place === 'logo') {
			$value = [
				'itemscope' => 'itemscope',
				'itemtype' => 'https://schema.org/Organization'
			];
		}

		// Navigation
		if ($place === 'navigation') {
			$value = [
				'itemscope' => '',
				'itemtype' => 'https://schema.org/SiteNavigationElement'
			];
		}

		// Main
		if ($place === 'blog') {
			// return '';
			// return 'itemtype="https://schema.org/Blog" itemscope';
		}

		if ($place === 'breadcrumb') {
			$value = [
				'itemscope' => '',
				'itemtype' => 'https://schema.org/BreadcrumbList'
			];
		}

		if ( $place === 'breadcrumb_list') {
			$value = [
				'itemprop' => 'itemListElement',
				'itemscope' => '',
				'itemtype' => 'https://schema.org/ListItem'
			];
		}

		if ($place === 'breadcrumb_itemprop') {
			$value = [
				'itemprop' => 'breadcrumb',
			];
		}

		if ($place === 'sidebar') {
			$value = [
				'itemtype' => 'https://schema.org/WPSideBar',
				'itemscope' => 'itemscope',
			];
		}

		if ($place === 'footer') {
			$value = [
				'itemscope' => '',
				'itemtype' => 'https://schema.org/WPFooter'
			];
		}

		if ($place === 'headline') {
			$value = [
				'itemprop' => 'headline'
			];
		}

		if ( $place === 'entry_content') {
			$value = [
				'itemprop' => 'text'
			];
		}

		if ($place === 'publish_date') {
			$value = [
				'itemprop' => 'datePublished'
			];
		}

		if ($place === 'modified_date') {
			$value = [
				'itemprop' => 'dateModified'
			];
		}

		if ($place === 'author') {
			$value = [
				'itemprop' => 'author',
				'itemscope' => '',
				'itemtype' => 'https://schema.org/Person'
			];
		}

		if ($place === 'author_name') {
			$value = [
				'itemprop' => 'name'
			];
		}

		if ($place === 'author_link') {
			$value = [
				'itemprop' => 'author',
			];
		}

		if ($place === 'author_url') {
			$value = [
				'itemprop' => 'url',
			];
		}

		if ($place === 'publisher') {
			$value = [
				'itemprop' => 'publisher'
			];
		}

		if ($place === 'item') {
			$value = [
				'itemprop' => 'item'
			];
		}

		if ($place === 'url') {
			$value = [
				'itemprop' => 'url'
			];
		}

		if ($place === 'name') {
			$value = [
				'itemprop' => 'name'
			];
		}

		if ($place === 'description') {
			$value = [
				'itemprop' => 'description'
			];
		}

		if ($place === 'position') {
			$value = [
				'itemprop' => 'position'
			];
		}

		if ($place === 'image') {
			$value = [
				'itemprop' => 'image'
			];
		}

		if ($place === 'breadcrumb_list') {
			$value = [
				'itemscope' => '',
				'itemtype' => "https://schema.org/BreadcrumbList"
			];
		}

		if ($place === 'breadcrumb_item') {
			$value = [
				'itemscope' => '',
				'itemprop' => "itemListElement",
				'itemtype' => "https://schema.org/ListItem"
			];
		}

		$value = array_merge($value, $args['to_merge']);

		if ($args['array']) {
			return $value;
		}

		return blocksy_attr_to_html($value);
	}
}

