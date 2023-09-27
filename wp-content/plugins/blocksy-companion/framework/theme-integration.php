<?php

namespace Blocksy;

class ThemeIntegration {
	public function __construct() {
		add_filter('blocksy:frontend:dynamic-js-chunks', function ($chunks) {
			$render = new \Blocksy_Header_Builder_Render();

			if (
				$render->contains_item('account')
				||
				is_customize_preview()
			) {
				$deps = [];
				$global_data = [];

				if (class_exists('woocommerce')) {
					$deps = [
						'blocksy-zxcvbn',
						'wp-hooks',
						'wp-i18n',
						'password-strength-meter',
					];

					$global_data = [
						[
							'var' => 'wc_password_strength_meter_params',
							'data' => [
								'min_password_strength' => apply_filters(
									'woocommerce_min_password_strength',
									3
								),
								'stop_checkout' => apply_filters(
									'woocommerce_enforce_password_strength_meter_on_checkout',
									false
								),
								'i18n_password_error'=> esc_attr__(
									'Please enter a stronger password.',
									'woocommerce'
								),
								'i18n_password_hint' => addslashes(wp_get_password_hint()),
							]
						],

						[
							'var' => 'pwsL10n',
							'data' => [
								'unknown'  => _x( 'Password strength unknown', 'password strength' ),
								'short'    => _x( 'Very weak', 'password strength' ),
								'bad'      => _x( 'Weak', 'password strength' ),
								'good'     => _x( 'Medium', 'password strength' ),
								'strong'   => _x( 'Strong', 'password strength' ),
								'mismatch' => _x( 'Mismatch', 'password mismatch' ),
							]
						]
					];
				}

				$chunks[] = [
					'id' => 'blocksy_account',
					'selector' => implode(', ', [
						'.ct-header-account[href*="account-modal"]',
						'.must-log-in a'
					]),
					'url' => blc_call_fn(
						[
							'fn' => 'blocksy_cdn_url',
							'default' => BLOCKSY_URL . 'static/bundle/account.js'
						],
						BLOCKSY_URL . 'static/bundle/account.js'
					),
					'deps' => $deps,
					'global_data' => $global_data,

					'trigger' => 'click',
					'has_modal_loader' => [
						'skip_if_no_template' => true,
						'id' => 'account-modal'
					]
				];
			}

			$chunks[] = [
				'id' => 'blocksy_dark_mode',
				'selector' => '[data-id="dark-mode-switcher"]',
				'url' => blc_call_fn(
					[
						'fn' => 'blocksy_cdn_url',
						'default' => BLOCKSY_URL . 'static/bundle/dark-mode.js'
					],
					BLOCKSY_URL . 'static/bundle/dark-mode.js'
				),
				'trigger' => 'click'
			];

			$chunks[] = [
				'id' => 'blocksy_sticky_header',
				'selector' => 'header [data-sticky]',
				'url' => blc_call_fn(
					[
						'fn' => 'blocksy_cdn_url',
						'default' => BLOCKSY_URL . 'static/bundle/sticky.js'
					],
					BLOCKSY_URL . 'static/bundle/sticky.js'
				),
			];

			return $chunks;
		});

		add_shortcode('blocksy_posts', function ($args, $content) {
			$args = wp_parse_args(
				$args,
				[
					'post_type' => 'post',
					'limit' => 5,

					// post_date | comment_count
					'orderby' => 'post_date',
					'order' => 'DESC',
					'meta_value' => '',
					'meta_key' => '',

					// yes | no
					'has_pagination' => 'yes',

					// yes | no
					'ignore_sticky_posts' => 'no',

					'term_ids' => null,
					'exclude_term_ids' => null,
					'post_ids' => null,

					// archive | slider
					'view' => 'archive',
					'slider_image_ratio' => '2/1',
					'slider_autoplay' => 'no',

					'filtering' => false,

					// 404 | skip
					'no_results' => '404',

					'class' => ''
				]
			);

			$file_path = dirname(__FILE__) . '/views/blocksy-posts.php';

			return blc_call_fn(
				['fn' => 'blocksy_render_view'],
				$file_path,
				[
					'args' => $args,
					'content' => $content
				]
			);
		});

		add_filter('blocksy:general:ct-scripts-localizations', function ($data) {
			$data['dynamic_styles_selectors'][] = [
				'selector' => '#account-modal',
				'url' => blc_call_fn(
					[
						'fn' => 'blocksy_cdn_url',
						'default' => BLOCKSY_URL . 'static/bundle/account-lazy.min.css'
					],
					BLOCKSY_URL . 'static/bundle/account-lazy.min.css'
				)
			];

			return $data;
		});

		add_shortcode('blocksy_breadcrumbs', function ($args, $content) {
			if (! class_exists('Blocksy_Breadcrumbs_Builder')) {
				return '';
			}

			$breadcrumbs_builder = new \Blocksy_Breadcrumbs_Builder();
			return $breadcrumbs_builder->render([
				'class' => 'ct-breadcrumbs-shortcode'
			]);
		});

		add_action('wp_ajax_blocksy_conditions_get_all_taxonomies', function () {
			if (! current_user_can('manage_options')) {
				wp_send_json_error();
			}

			$cpts = blocksy_manager()->post_types->get_supported_post_types();

			$cpts[] = 'post';
			$cpts[] = 'page';
			$cpts[] = 'product';

			$taxonomies = [];

			foreach ($cpts as $cpt) {
				$taxonomies = array_merge($taxonomies, array_values(array_diff(
					get_object_taxonomies($cpt),
					['post_format']
				)));
			}

			$terms = [];

			foreach ($taxonomies as $taxonomy) {
				$taxonomy_object = get_taxonomy($taxonomy);

				if (! $taxonomy_object->public) {
					continue;
				}

				$local_terms = array_map(function ($tax) {
					return [
						'id' => $tax->term_id,
						'name' => $tax->name
					];
				}, get_terms(['taxonomy' => $taxonomy, 'lang' => '']));

				if (empty($local_terms)) {
					continue;
				}

				$terms[] = [
					'id' => $taxonomy,
					'name' => $taxonomy,
					'group' => get_taxonomy($taxonomy)->label
				];

				$terms = array_merge($terms, $local_terms);
			}

			$languages = [];

			if (function_exists('blocksy_get_current_language')) {
				$languages = blocksy_get_all_i18n_languages();
			}

			$users = [];

			foreach (get_users([
				'number' => 500
			]) as $user) {
				$users[] = [
					'id' => $user->ID,
					'name' => $user->user_nicename
				];
			}

			wp_send_json_success([
				'taxonomies' => $terms,
				'languages' => $languages,
				'users' => $users
			]);
		});

		add_action('wp_ajax_blocksy_conditions_get_all_posts', function () {
			if (! current_user_can('manage_options')) {
				wp_send_json_error();
			}

			$maybe_input = json_decode(file_get_contents('php://input'), true);

			if (! $maybe_input) {
				wp_send_json_error();
			}

			if (! isset($maybe_input['post_type'])) {
				wp_send_json_error();
			}

			$query_args = [
				'posts_per_page' => 10,
				'post_type' => $maybe_input['post_type'],
				'suppress_filters' => true,
				'lang' => ''
			];

			if (
				isset($maybe_input['search_query'])
				&&
				! empty($maybe_input['search_query'])
			) {
				if (intval($maybe_input['search_query'])) {
					$query_args['p'] = intval($maybe_input['search_query']);
				} else {
					$query_args['s'] = $maybe_input['search_query'];
				}
			}

			$initial_query_args_post_type = $query_args['post_type'];

			if (strpos($initial_query_args_post_type, 'ct_cpt') !== false) {
				$query_args['post_type'] = array_diff(
					get_post_types(['public' => true]),
					['post', 'page', 'attachment', 'ct_content_block']
				);
			}

			if (strpos($initial_query_args_post_type, 'ct_all_posts') !== false) {
				$query_args['post_type'] = array_diff(
					get_post_types(['public' => true]),
					['product', 'attachment', 'ct_content_block']
				);
			}

			$query = new \WP_Query($query_args);

			$posts_result = $query->posts;

			if (isset($maybe_input['alsoInclude'])) {
				$maybe_post = get_post($maybe_input['alsoInclude'], 'display');

				if ($maybe_post) {
					$posts_result[] = $maybe_post;
				}
			}

			wp_send_json_success([
				'posts' => $posts_result
			]);
		});

		add_filter(
			'blocksy:dashboard:redirect-after-activation',
			function ($url) {
				return add_query_arg(
					'page',
					'ct-dashboard',
					admin_url('admin.php')
				);
			}
		);

		add_filter(
			'blocksy_add_menu_page',
			function ($res, $options) {
				add_menu_page(
					$options['title'],
					$options['menu-title'],
					$options['permision'],
					$options['top-level-handle'],
					$options['callback'],
					$options['icon-url'],
					2
				);

				return true;
			},
			10, 2
		);

		add_action('rest_api_init', function () {
			return;

			register_rest_field('post', 'images', [
				'get_callback' => function () {
					return wp_prepare_attachment_for_js($object->id);
				},
				'update_callback' => null,
				'schema' => null,
			]);
		});

		add_filter(
			'user_contactmethods',
			function ( $field ) {
				$fields['facebook'] = __( 'Facebook', 'blocksy-companion' );
				$fields['twitter'] = __( 'Twitter', 'blocksy-companion' );
				$fields['linkedin'] = __( 'LinkedIn', 'blocksy-companion' );
				$fields['dribbble'] = __( 'Dribbble', 'blocksy-companion' );
				$fields['instagram'] = __( 'Instagram', 'blocksy-companion' );
				$fields['pinterest'] = __( 'Pinterest', 'blocksy-companion' );
				$fields['wordpress'] = __( 'WordPress', 'blocksy-companion' );
				$fields['github'] = __( 'GitHub', 'blocksy-companion' );
				$fields['medium'] = __( 'Medium', 'blocksy-companion' );
				$fields['youtube'] = __( 'YouTube', 'blocksy-companion' );
				$fields['vimeo'] = __( 'Vimeo', 'blocksy-companion' );
				$fields['vkontakte'] = __( 'VKontakte', 'blocksy-companion' );
				$fields['odnoklassniki'] = __( 'Odnoklassniki', 'blocksy-companion' );
				$fields['tiktok'] = __( 'TikTok', 'blocksy-companion' );

				return $fields;
			}
		);

		add_filter(
			'wp_check_filetype_and_ext',
			function ($data=null, $file=null, $filename=null, $mimes=null) {
				if (strpos($filename, '.svg') !== false) {
					$data['type'] = 'image/svg+xml';
					$data['ext'] = 'svg';
				}

				return $data;
			},
			75, 4
		);

		add_filter('upload_mimes', function ($mimes) {
			$mimes['svg'] = 'image/svg+xml';
			return $mimes;
		});

		add_filter('wp_get_attachment_image_src', function ($image, $attachment_id, $size, $icon) {
			if (! isset($attachment_id)) {
				return $image;
			}

			$mime = get_post_mime_type($attachment_id);

			if ('image/svg+xml' === $mime) {
				$default_height = 100;
				$default_width = 100;

				$dimensions = $this->svg_dimensions(get_attached_file($attachment_id));

				if ($dimensions) {
					$default_height = $dimensions['height'];
					$default_width = $dimensions['width'];
				}

				$image[2] = $default_height;
				$image[1] = $default_width;
			}

			return $image;
		}, 10, 4);

		add_filter('blocksy_changelogs_list', function ($changelogs) {
			$changelog = null;
			$access_type = get_filesystem_method();

			if ($access_type === 'direct') {
				$creds = request_filesystem_credentials(
					site_url() . '/wp-admin/',
					'', false, false,
					[]
				);

				if ( WP_Filesystem($creds) ) {
					global $wp_filesystem;

					$readme = $wp_filesystem->get_contents(
						BLOCKSY_PATH . '/readme.txt'
					);

					if ($readme) {
						$readme = explode('== Changelog ==', $readme);

						if (isset($readme[1])) {
							$changelogs[] = [
								'title' => __('Companion', 'blocksy-companion'),
								'changelog' => trim($readme[1])
							];
						}
					}

					if (
						function_exists('blc_fs')
						&&
						blc_fs()->can_use_premium_code()
						&&
						BLOCKSY_PATH . '/framework/premium/changelog.txt'
					) {
						$pro_changelog = $wp_filesystem->get_contents(
							BLOCKSY_PATH . '/framework/premium/changelog.txt'
						);

						$changelogs[] = [
							'title' => __('PRO', 'blocksy-companion'),
							'changelog' => trim($pro_changelog)
						];
					}
				}
			}

			return $changelogs;
		});

		add_action('wp_enqueue_scripts', function () {
			if (! function_exists('get_plugin_data')){
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}

			$data = get_plugin_data(BLOCKSY__FILE__);

			if (is_admin()) return;

			/*
			wp_enqueue_style(
				'blocksy-companion-styles',
				BLOCKSY_URL . 'static/bundle/min.css',
				['ct-main-styles'],
				$data['Version']
			);
			 */
		}, 50);

		add_action(
			'customize_preview_init',
			function () {
				if (! function_exists('get_plugin_data')){
					require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
				}

				$data = get_plugin_data(BLOCKSY__FILE__);

				wp_enqueue_script(
					'blocksy-companion-sync-scripts',
					BLOCKSY_URL . 'static/bundle/sync.js',
					['customize-preview', 'ct-scripts', 'wp-date', 'ct-scripts', 'ct-customizer'],
					$data['Version'],
					true
				);
			}
		);
	}

	public function svg_dimensions($svg) {
		$svg = file_get_contents($svg);

		$attributes = new \stdClass();

		if ($svg && function_exists('simplexml_load_string')) {
			$svg = @simplexml_load_string($svg);

			if ($svg) {
				$attributes = $svg->attributes();
			}
		}

		if (
			! isset($attributes->width)
			&&
			$svg
			&&
			function_exists('xml_parser_create')
		) {
			$xml = xml_parser_create('UTF-8');

			$svgData = new \stdClass();

			xml_parser_set_option($xml, XML_OPTION_CASE_FOLDING, false);
			xml_set_element_handler(
				$xml,
				function ($parser, $name, $attrs) use (&$svgData) {
					if ($name === 'SVG') {
						if (isset($attrs['WIDTH'])) {
							$attrs['width'] = $attrs['WIDTH'];
						}

						if (isset($attrs['HEIGHT'])) {
							$attrs['height'] = $attrs['HEIGHT'];
						}

						if (isset($attrs['VIEWBOX'])) {
							$attrs['viewBox'] = $attrs['VIEWBOX'];
						}

						foreach ($attrs as $key => $value) {
							$svgData->{$key} = $value;
						}
					}
				},
				'tag_close'
			);

			if (xml_parse($xml, $svg, true)) {
				$attributes = $svgData;
			}

			xml_parser_free($xml);
		}


		$width = 0;
		$height = 0;

		if (empty($attributes)) {
			return false;
		}

		if (
			isset($attributes->width, $attributes->height)
			&&
			is_numeric($attributes->width)
			&&
			is_numeric($attributes->height)
		) {
			$width = floatval($attributes->width);
			$height = floatval($attributes->height);
		} elseif (isset($attributes->viewBox)) {
			$sizes = explode(' ', $attributes->viewBox);

			if (isset($sizes[2], $sizes[3])) {
				$width = floatval($sizes[2]);
				$height = floatval($sizes[3]);
			}
		} else {
			return false;
		}

		return [
			'width' => $width,
			'height' => $height,
			'orientation' => ($width > $height) ? 'landscape' : 'portrait'
		];
	}
}

