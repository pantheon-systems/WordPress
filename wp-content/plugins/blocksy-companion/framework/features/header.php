<?php

namespace Blocksy;

class HeaderAdditions {
	private $has_transparent_header = '__DEFAULT__';
	private $has_sticky_header = '__DEFAULT__';
	private $has_account_modal = '__empty__';

	public function __construct() {
		add_action(
			'customize_controls_enqueue_scripts',
			function () {
				$this->enqueue_static();
			}
		);

		add_action(
			'admin_enqueue_scripts',
			function () {
				$this->enqueue_static();
			},
			50
		);

		add_action('plugins_loaded', function () {
			if (function_exists('dokan')) {
				add_action('wp_enqueue_scripts', function () {
					wp_enqueue_script('dokan-form-validate');
					wp_enqueue_script('dokan-vendor-registration');
				});
			}
		});

		add_filter('blocksy:header:selective_refresh', function ($selective_refresh) {

			$selective_refresh[] = [
				'id' => 'header_placements_item:account',
				'fallback_refresh' => false,
				'container_inclusive' => true,
				'selector' => 'header [data-id="account"]',
				'settings' => ['header_placements'],
				'render_callback' => function () {
					$header = new \Blocksy_Header_Builder_Render();
					echo $header->render_single_item('account');
				}
			];

			$selective_refresh[] = [
				'id' => 'header_placements_item:account:offcanvas',
				'fallback_refresh' => false,
				'container_inclusive' => false,
				'selector' => '#offcanvas',
				'loader_selector' => '[data-id="account"]',
				'settings' => ['header_placements'],
				'render_callback' => function () {
					$elements = new \Blocksy_Header_Builder_Elements();

					echo $elements->render_offcanvas([
						'has_container' => false
					]);
				}
			];

			return $selective_refresh;
		});

		add_filter('blocksy:header:device-wrapper-attr', function ($attr, $device) {
			$transparent_result = $this->current_screen_has_transparent();

			if (!$transparent_result) {
				return $attr;
			}

			if (in_array($device, $transparent_result)) {
				$attr['data-transparent'] = '';
			}

			return $attr;
		}, 10, 2);

		add_filter('blocksy:header:item-template-args', function ($args) {
			$args['has_transparent_header'] = $this->current_screen_has_transparent();
			$args['has_sticky_header'] = $this->current_screen_has_sticky();

			return $args;
		});

		add_filter('blocksy:header:row-wrapper-attr', function ($attr, $row, $device) {
			$current_section = blocksy_manager()->header_builder->get_current_section();

			if (!isset($current_section['settings'])) {
				$current_section['settings'] = [];
			}

			$atts = $current_section['settings'];

			$transparent_result = $this->current_screen_has_transparent();

			if ($transparent_result) {
				if (in_array($device, $transparent_result)) {
					$attr['data-transparent-row'] = 'yes';
				}
			}

			return $attr;
		}, 10, 3);

		add_filter(
			'blocksy:header:rows-render',
			function ($custom_content, $rows, $device) {
				$sticky_result = $this->current_screen_has_sticky();

				if (!$sticky_result) {
					return $custom_content;
				}

				if (!in_array($device, $sticky_result['devices'])) {
					return $custom_content;
				}

				$start_html = '<div class="ct-sticky-container">';
				$start_html .= '<div data-sticky="' . $sticky_result['effect'] . '">';

				$end_html = '</div></div>';

				if (
					$sticky_result['behaviour'] === 'top_middle'
					&&
					(isset($rows['top-row'])
						||
						isset($rows['middle-row'])
					)
				) {
					if (isset($rows['top-row'])) {
						$rows['top-row'] = $start_html . $rows['top-row'];
					} else {
						$rows['middle-row'] = $start_html . $rows['middle-row'];
					}

					if (isset($rows['middle-row'])) {
						$rows['middle-row'] = $rows['middle-row'] . $end_html;
					} else {
						$rows['top-row'] = $rows['top-row'] . $end_html;
					}

					return implode('', array_values($rows));
				}

				if (
					$sticky_result['behaviour'] === 'middle_bottom'
					&&
					(isset($rows['middle-row'])
						||
						isset($rows['bottom-row'])
					)
				) {
					if (isset($rows['middle-row'])) {
						$rows['middle-row'] = $start_html . $rows['middle-row'];
					} else {
						$rows['bottom-row'] = $start_html . $rows['bottom-row'];
					}

					if (isset($rows['bottom-row'])) {
						$rows['bottom-row'] = $rows['bottom-row'] . $end_html;
					} else {
						$rows['middle-row'] = $rows['middle-row'] . $end_html;
					}

					return implode('', array_values($rows));
				}

				if (
					$sticky_result['behaviour'] === 'middle'
					&&
					isset($rows['middle-row'])
				) {
					$rows['middle-row'] = $start_html . $rows['middle-row'] . $end_html;
					return implode('', array_values($rows));
				}

				if (
					$sticky_result['behaviour'] === 'bottom'
					&&
					isset($rows['bottom-row'])
				) {
					$rows['bottom-row'] = $start_html . $rows['bottom-row'] . $end_html;
					return implode('', array_values($rows));
				}

				if (
					$sticky_result['behaviour'] === 'top'
					&&
					isset($rows['top-row'])
				) {
					$rows['top-row'] = $start_html . $rows['top-row'] . $end_html;
					return implode('', array_values($rows));
				}

				if (
					$sticky_result['behaviour'] === 'entire_header'
				) {
					return $start_html . implode('', array_values($rows)) . $end_html;
				}

				return null;
			},
			10,
			3
		);

		add_filter('blocksy:general:body-header-attr', function ($attr) {
			$sticky_result = $this->current_screen_has_sticky();

			if ($sticky_result) {
				$attr .= ':sticky';

				if ($sticky_result['effect'] === 'auto-hide') {
					$attr .= ':auto';
				}
			}

			return $attr;
		});

		add_filter('blocksy:header:dynamic-styles-args', function ($args) {
			$check_transparent_conditions = false;

			if (isset($args['check_transparent_conditions'])) {
				$check_transparent_conditions = $args['check_transparent_conditions'];
			}

			$args['has_transparent_header'] = $this->current_screen_has_transparent(
				$check_transparent_conditions,
				$args['section_id']
			);

			$args['has_sticky_header'] = $this->current_screen_has_sticky($args['section_id']);

			return $args;
		});

		add_filter('blocksy:header:items-paths', function ($paths) {
			$paths[] = dirname(__FILE__) . '/header/items';
			return $paths;
		});

		add_filter('blocksy:header:settings', function ($opt) {
			$opt = blc_call_fn(
				[
					'fn' => 'blocksy_get_options',
					'default' => 'array'
				],
				dirname(__FILE__) . '/header/header-options.php',
				[],
				false
			);

			return $opt;
		});

		add_filter(
			'blocksy:footer:offcanvas-drawer',
			function ($els) {
				global $blocksy_has_default_header;

				if (
					isset($blocksy_has_default_header)
					&&
					$blocksy_has_default_header
					&&
					$this->has_account_modal()
				) {
					$els[] = $this->retrieve_account_modal();
				}

				return $els;
			}
		);
	}

	public function enqueue_static() {
		if (!function_exists('get_plugin_data')) {
			require_once(ABSPATH . 'wp-admin/includes/plugin.php');
		}

		global $wp_customize;

		$data = get_plugin_data(BLOCKSY__FILE__);

		$deps = ['ct-options-scripts'];

		$current_screen = get_current_screen();

		if ($current_screen && $current_screen->id === 'customize') {
			$deps = ['ct-customizer-controls'];
		}

		wp_enqueue_script(
			'blocksy-admin-scripts',
			BLOCKSY_URL . 'static/bundle/options.js',
			$deps,
			$data['Version'],
			true
		);

		$conditions_manager = new ConditionsManager();

		$localize = array_merge(
			[
				'all_condition_rules' => $conditions_manager->get_all_rules(),
				'singular_condition_rules' => $conditions_manager->get_all_rules([
					'filter' => 'singular'
				]),
				'archive_condition_rules' => $conditions_manager->get_all_rules([
					'filter' => 'archive'
				]),
				'ajax_url' => admin_url('admin-ajax.php'),
				'rest_url' => get_rest_url(),
			]
		);

		wp_localize_script(
			'blocksy-admin-scripts',
			'blocksy_admin',
			$localize
		);

		wp_enqueue_style(
			'blocksy-styles',
			BLOCKSY_URL . 'static/bundle/options.min.css',
			[],
			$data['Version']
		);
	}

	public function current_screen_has_transparent($check_conditions = true, $current_section_id = null) {
		if (
			true
			||
			$this->has_transparent_header === '__DEFAULT__'
			||
			!$check_conditions
		) {
			$current_section = blocksy_manager()->header_builder->get_current_section(
				$current_section_id
			);

			if (!isset($current_section['settings'])) {
				$current_section['settings'] = [];
			}

			$atts = $current_section['settings'];

			if (blocksy_akg('has_transparent_header', $atts, 'no') === 'no') {
				$this->has_transparent_header = false;
				return false;
			}

			$transparent_behaviour = blocksy_akg(
				'transparent_behaviour',
				$atts,
				[
					'desktop' => true,
					'tablet' => true,
					'mobile' => true,
				]
			);

			$transparent_result = [];

			foreach ($transparent_behaviour as $device => $value) {
				if (!$value) {
					continue;
				}

				$transparent_result[] = $device;
			}

			$conditions_manager = new \Blocksy\ConditionsManager();

			$this->has_transparent_header = false;

			if (
				count($transparent_result) > 0
				&&
				(
					($current_section['id'] === 'type-1'
						&&
						$conditions_manager->condition_matches(blocksy_akg(
							'transparent_conditions',
							$atts,
							[
								[
									'type' => 'include',
									'rule' => 'everywhere'
								],

								[
									'type' => 'exclude',
									'rule' => '404'
								],

								[
									'type' => 'exclude',
									'rule' => 'search'
								],

								[
									'type' => 'exclude',
									'rule' => 'archives'
								]
							]
						))
					)
					||
					$current_section['id'] !== 'type-1'
					||
					!$check_conditions
				)
				&&
				apply_filters(
					'blocksy:header:transparent:current-screen-allowed',
					true,
					$current_section,
					$transparent_result
				)
			) {
				$this->has_transparent_header = $transparent_result;
			}
		}

		return $this->has_transparent_header;
	}

	public function current_screen_has_sticky($section_id = null) {
		if (
			$this->has_sticky_header !== '__DEFAULT__'
			&&
			!$section_id
		) {
			return $this->has_sticky_header;
		}

		$current_section = blocksy_manager()->header_builder->get_current_section(
			$section_id
		);

		if (!isset($current_section['settings'])) {
			$current_section['settings'] = [];
		}

		$atts = $current_section['settings'];

		if (blocksy_akg('has_sticky_header', $atts, 'no') === 'no') {
			$has_sticky_header_result = false;
		} else {
			$atts = $current_section['settings'];

			$sticky_behaviour = blocksy_akg(
				'sticky_behaviour',
				$atts,
				[
					'desktop' => true,
					'mobile' => true,
				]
			);

			$has_sticky_header_result = [
				'devices' => [],

				// top
				// middle
				// bottom
				// middle_bottom
				// entire_header
				// 'behaviour' => 'middle_bottom'
				// 'behaviour' => 'middle'
				// 'behaviour' => 'middle_bottom'
				'behaviour' => blocksy_akg('sticky_rows', $atts, 'middle'),
				'effect' => blocksy_akg('sticky_effect', $atts, 'shrink')
			];

			foreach ($sticky_behaviour as $device => $value) {
				if (!$value) {
					continue;
				}

				$has_sticky_header_result['devices'][] = $device;
			}
		}

		if ($section_id) {
			$this->has_sticky_header = $has_sticky_header_result;
		}

		return $has_sticky_header_result;
	}

	public function patch_conditions($post_id, $old_post_id) {
		$conditions = $this->get_conditions();

		foreach ($conditions as $index => $single_condition) {
			$particular_conditions = $single_condition['conditions'];

			foreach ($particular_conditions as $nested_index => $single_particular_condition) {
				if (
					($single_particular_condition['rule'] === 'page_ids'
						||
						$single_particular_condition['rule'] === 'post_ids'
					) && (isset($single_particular_condition['payload'])
						&&
						isset($single_particular_condition['payload']['post_id'])
						&&
						intval(
							$single_particular_condition['payload']['post_id']
						) === $old_post_id
					)
				) {
					$particular_conditions[$nested_index]['payload']['post_id'] = $post_id;
				}
			}

			$conditions[$index]['conditions'] = $particular_conditions;
		}

		$this->set_conditions($conditions);

		$section_value = blocksy_manager()->header_builder->get_section_value();

		foreach ($section_value['sections'] as $index => $current_section) {
			if (!isset($current_section['settings'])) {
				continue;
			}

			if (!isset($current_section['settings']['transparent_conditions'])) {
				continue;
			}

			foreach ($current_section['settings']['transparent_conditions'] as $cond_index => $single_condition) {
				$particular_conditions = $single_condition;

				if (
					($single_condition['rule'] === 'page_ids'
						||
						$single_condition['rule'] === 'post_ids'
					) && (isset($single_condition['payload'])
						&&
						isset($single_condition['payload']['post_id'])
						&&
						intval(
							$single_condition['payload']['post_id']
						) === $old_post_id
					)
				) {
					$single_condition['payload']['post_id'] = $post_id;
				}

				$section_value['sections'][$index]['settings']['transparent_conditions'][$cond_index] = $single_condition;
			}
		}

		set_theme_mod('header_placements', $section_value);
	}

	public function get_conditions() {
		$option = get_theme_mod('blocksy_premium_header_conditions', []);

		if (empty($option)) {
			return [];
		}

		return $option;
	}

	public function set_conditions($conditions) {
		set_theme_mod('blocksy_premium_header_conditions', $conditions);
	}

	public function retrieve_account_modal() {
		if (function_exists('wc_lostpassword_url')) {
			remove_filter('lostpassword_url', 'wc_lostpassword_url', 10, 1);
		}

		$body = json_decode(file_get_contents('php://input'), true);

		$header_id = null;

		if (isset($_REQUEST['header_id'])) {
			$header_id = $_REQUEST['header_id'];
		}

		$render = new \Blocksy_Header_Builder_Render([
			'current_section_id' => $header_id
		]);

		$atts = $render->get_item_data_for('account');

		$html = blc_call_fn(
			['fn' => 'blocksy_render_view'],
			dirname(__FILE__) . '/header/account-modal.php',
			[
				'current_url' => blocksy_current_url(),
				'header_id' => $header_id,
				'atts' => $atts
			]
		);

		if (function_exists('wc_lostpassword_url')) {
			add_filter('lostpassword_url', 'wc_lostpassword_url', 10, 1);
		}

		return $html;
	}

	public function has_account_modal() {
		if (is_customize_preview()) {
			return true;
		}

		if (!class_exists('Blocksy_Header_Builder_Render')) {
			return false;
		}

		if ($this->has_account_modal !== '__empty__') {
			return $this->has_account_modal;
		}

		if (get_current_user_id()) {
			$this->has_account_modal = false;
			return false;
		}

		$render = new \Blocksy_Header_Builder_Render();

		if (!$render->contains_item('account')) {
			$this->has_account_modal = false;
			return false;
		}

		$atts = $render->get_item_data_for('account');

		if (blocksy_akg('login_account_action', $atts, 'modal') !== 'modal') {
			$this->has_account_modal = false;
			return false;
		}

		$this->has_account_modal = true;
		return true;
	}
}
