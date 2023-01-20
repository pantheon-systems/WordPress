<?php

class Blocksy_Static_Css_Files {
	public function all_static_files() {

		$should_load_comments_css = (
			is_singular()
			&&
			(
				blocksy_has_comments()
				||
				is_customize_preview()
			)
		);

		$should_load_comments_css = apply_filters(
			'blocksy:static-files:ct-comments-styles',
			$should_load_comments_css
		);

		return [
			[
				'id' => 'ct-main-styles',
				'url' => '/static/bundle/main.min.css'
			],

			[
				'id' => 'ct-admin-frontend-styles',
				'url' => '/static/bundle/admin-frontend.min.css',
				'enabled' => (
					current_user_can('manage_options')
					||
					current_user_can('edit_theme_options')
				)
			],

			[
				'id' => 'ct-page-title-styles',
				'url' => '/static/bundle/page-title.min.css',
				'enabled' => (
					is_customize_preview()
					||
					blocksy_get_page_title_source()
				)
			],

			[
				'id' => 'ct-back-to-top-styles',
				'url' => '/static/bundle/back-to-top.min.css',
				'enabled' => (
					is_customize_preview()
					||
					get_theme_mod('has_back_top', 'no') === 'yes'
				)
			],

			[
				'id' => 'ct-main-rtl-styles',
				'url' => '/static/bundle/main-rtl.min.css',
				'enabled' => is_rtl()
			],

			[
				'id' => 'ct-forminator-styles',
				'url' => '/static/bundle/forminator.min.css',
				'deps' => ['ct-main-styles'],
				'enabled' => class_exists('Forminator')
			],

			[
				'id' => 'ct-getwid-styles',
				'url' => '/static/bundle/getwid.min.css',
				'deps' => ['ct-main-styles'],
				'enabled' => class_exists('Getwid\Getwid')
			],

			[
				'id' => 'ct-elementor-styles',
				'url' => '/static/bundle/elementor-frontend.min.css',
				'deps' => ['ct-main-styles'],
				'enabled' => did_action('elementor/loaded')
			],

			[
				'id' => 'ct-tutor-styles',
				'url' => '/static/bundle/tutor.min.css',
				'deps' => ['ct-main-styles'],
				'enabled' => function_exists('tutor_course_enrolled_lead_info')
			],

			[
				'id' => 'ct-sidebar-styles',
				'url' => '/static/bundle/sidebar.min.css',
				'deps' => ['ct-main-styles'],
				'enabled' => (
					blocksy_sidebar_position() !== 'none'
					||
					is_customize_preview()
					||
					(
						function_exists('is_woocommerce')
						&&
						is_woocommerce()
						&&
						get_theme_mod('has_woo_offcanvas_filter', 'no') === 'yes'
					)
				)
			],

			[
				'id' => 'ct-share-box-styles',
				'url' => '/static/bundle/share-box.min.css',
				'deps' => ['ct-main-styles'],
				'enabled' => (
					is_singular()
					&&
					(
						blocksy_has_share_box()
						||
						is_customize_preview()
						||
						(
							function_exists('is_product')
							&&
							is_product()
							&&
							get_theme_mod('product_has_share_box', 'no') === 'yes'
						)
						||
						is_page(
							get_theme_mod('woocommerce_wish_list_page', '__EMPTY__')
						)
						||
						(
							function_exists('is_account_page')
							&&
							is_account_page()
						)
					)
				)
			],

			[
				'id' => 'ct-comments-styles',
				'url' => '/static/bundle/comments.min.css',
				'deps' => ['ct-main-styles'],
				'enabled' => $should_load_comments_css
			],

			[
				'id' => 'ct-author-box-styles',
				'url' => '/static/bundle/author-box.min.css',
				'deps' => ['ct-main-styles'],
				'enabled' => (
					is_singular()
					&&
					(
						blocksy_has_author_box()
						||
						is_customize_preview()
					)
				)
			],

			[
				'id' => 'ct-posts-nav-styles',
				'url' => '/static/bundle/posts-nav.min.css',
				'deps' => ['ct-main-styles'],
				'enabled' => (
					is_singular()
					&&
					(
						blocksy_has_post_nav()
						||
						is_customize_preview()
					)
				)
			],

			[
				'id' => 'ct-flexy-styles',
				'url' => '/static/bundle/flexy.min.css',
				'deps' => ['ct-main-styles'],
				'enabled' => (
					function_exists('is_woocommerce')
					||
					is_singular('blc-product-review')
				)
			],

			// Integrations
			[
				'id' => 'ct-brizy-styles',
				'url' => '/static/bundle/brizy.min.css',
				'deps' => ['ct-main-styles'],
				'enabled' => function_exists('brizy_load')
			],

			[
				'id' => 'ct-jet-woo-builder-styles',
				'url' => '/static/bundle/jet-woo-builder.min.css',
				'deps' => ['ct-main-styles'],
				'enabled' => class_exists('Jet_Woo_Builder')
			],

			[
				'id' => 'ct-tribe-events-styles',
				'url' => '/static/bundle/tribe-events.min.css',
				'deps' => ['ct-main-styles'],
				'enabled' => class_exists('Tribe__Events__Main')
			],

			[
				'id' => 'ct-beaver-styles',
				'url' => '/static/bundle/beaver.min.css',
				'deps' => ['ct-main-styles'],
				'enabled' => class_exists('FLBuilderLoader')
			],

			[
				'id' => 'ct-divi-styles',
				'url' => '/static/bundle/divi.min.css',
				'deps' => ['ct-main-styles'],
				'enabled' => class_exists('ET_Builder_Plugin')
			],

			[
				'id' => 'ct-vc-styles',
				'url' => '/static/bundle/vc.min.css',
				'deps' => ['ct-main-styles'],
				'enabled' => defined('VCV_Version')
			],

			[
				'id' => 'ct-cf-7-styles',
				'url' => '/static/bundle/cf-7.min.css',
				'deps' => ['ct-main-styles'],
				'enabled' => defined('WPCF7_VERSION')
			],

			[
				'id' => 'ct-cf-7-styles',
				'url' => '/static/bundle/cf-7.min.css',
				'deps' => ['ct-main-styles'],
				'enabled' => defined('WPCF7_VERSION')
			],

			[
				'id' => 'ct-stackable-styles',
				'url' => '/static/bundle/stackable.min.css',
				'deps' => ['ct-main-styles'],
				'enabled' => function_exists('sugb_fs')
			],

			[
				'id' => 'ct-qubely-styles',
				'url' => '/static/bundle/qubely.min.css',
				'deps' => ['ct-main-styles'],
				'enabled' => defined('QUBELY_VERSION')
			],

			[
				'id' => 'ct-bbpress-styles',
				'url' => '/static/bundle/bbpress.min.css',
				'deps' => ['ct-main-styles'],
				'enabled' => function_exists('is_bbpress')
			],

			[
				'id' => 'ct-buddypress-styles',
				'url' => '/static/bundle/buddypress.min.css',
				'deps' => ['ct-main-styles'],
				'enabled' => function_exists('is_buddypress')
			],

			[
				'id' => 'ct-wpforms-styles',
				'url' => '/static/bundle/wpforms.min.css',
				'deps' => ['ct-main-styles'],
				'enabled' => defined('WPFORMS_VERSION')
			],

			[
				'id' => 'ct-dokan-styles',
				'url' => '/static/bundle/dokan.min.css',
				'deps' => ['ct-main-styles'],
				'enabled' => class_exists('WeDevs_Dokan')
			],

			[
				'id' => 'ct-page-scroll-to-id-styles',
				'url' => '/static/bundle/page-scroll-to-id.min.css',
				'deps' => ['ct-main-styles'],
				'enabled' => class_exists('malihuPageScroll2id')
			]
		];
	}

	public function enqueue_static_files($theme) {
		foreach ($this->all_static_files() as $internal_file) {
			$file = wp_parse_args($internal_file, [
				'enabled' => true,
				'deps' => [],
				'url' => ''
			]);

			$file['url'] = get_template_directory_uri() . $file['url'];

			if (! $file['enabled']) {
				wp_register_style(
					$file['id'],
					$file['url'],
					$file['deps'],
					$theme->get('Version')
				);

				continue;
			}

			wp_enqueue_style(
				$file['id'],
				$file['url'],
				$file['deps'],
				$theme->get('Version')
			);
		}
	}
}
