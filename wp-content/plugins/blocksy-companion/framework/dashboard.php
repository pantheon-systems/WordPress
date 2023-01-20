<?php

namespace Blocksy;

class Dashboard {
	public function __construct() {
		add_action(
			'admin_menu',
			[$this, 'setup_framework_page'],
			5
		);

		add_action(
			'admin_menu',
			function () {
				if (function_exists('blocksy_get_wp_parent_theme')) {
					return;
				}

				$menu_slug = plugin_basename('ct-dashboard');
				$hookname = get_plugin_page_hookname('ct-dashboard', '');
				remove_all_actions($hookname);

				add_action(
					$hookname,
					function () {
						$this->welcome_page_template();
					}
				);
			},
			99999999999
		);

		add_action(
			'admin_enqueue_scripts',
			[$this, 'enqueue_static'],
			100
		);

		add_action('admin_body_class', function ($class) {
			if (! function_exists('blocksy_get_wp_parent_theme')) {
				return $class;
			}

			if (function_exists('blc_fs') && blc_fs()->is_activation_mode()) {
				$class .= ' blocksy-fs-optin-dashboard';
			}

			return $class;
		});

		if (function_exists('blc_fs')) {
			blc_fs()->add_filter('hide_plan_change', '__return_true');
			blc_fs()->add_filter(
				'plugin_icon',
				function ($url) {
					return BLOCKSY_PATH . '/static/img/logo.jpg';
				}
			);

			blc_fs()->add_filter(
				'permission_diagnostic_default',
				'__return_false'
			);

			blc_fs()->add_filter(
				'show_deactivation_feedback_form',
				'__return_false'
			);

			blc_fs()->add_filter('hide_freemius_powered_by', '__return_true');

			blc_fs()->add_filter(
				'connect-message_on-premium',
				function ($text) {
					if (strpos($text, '<br>') !== false) {
						$exploded_message = explode('<br>', $text);

						$text = '<span>' . $exploded_message[0] . '</span>' . $exploded_message[1];
					}

					return $text;
				}
			);

			blc_fs()->add_filter(
				'connect_message_on_update',
				function (
					$message,
					$user_first_name,
					$product_title,
					$user_login,
					$site_link,
					$freemius_link
				) {
					$is_network_upgrade_mode = ( fs_is_network_admin() && blc_fs()->is_network_upgrade_mode() );
					$slug = blc_fs()->get_slug();
					$is_gdpr_required = \FS_GDPR_Manager::instance()->is_required();
					$hey_x_text = esc_html( sprintf( fs_text_x_inline( 'Hey %s,', 'greeting', 'hey-x', $slug ), $user_first_name ) );

					$default_optin_message = $is_gdpr_required ?
						fs_text_inline( 'Never miss an important update - opt in to our security & feature updates notifications, educational content, offers, and non-sensitive diagnostic tracking with %4$s. If you skip this, that\'s okay! %1$s will still work just fine.', 'connect-message_on-update', $slug ) :
						fs_text_inline( 'Never miss an important update - opt in to our security & feature updates notifications, and non-sensitive diagnostic tracking with %4$s. If you skip this, that\'s okay! %1$s will still work just fine.', 'connect-message_on-update', $slug );

					$default_optin_message = 'Never miss an important update - opt in to our security & feature updates notifications, educational content, offers, and non-sensitive diagnostic tracking with.';

					return (($is_network_upgrade_mode ?
						'' :
						/* translators: %s: name (e.g. Hey John,) */
						'<span>' . $hey_x_text . '</span>'
					) .
					sprintf(
						esc_html( $default_optin_message ),
						'<b>' . esc_html( blc_fs()->get_plugin_name() ) . '</b>',
						'<b>' . $user_login . '</b>',
						$site_link,
						$freemius_link
					));

				}, 10, 6
			);

			blc_fs()->add_action('connect/before', function () {
				$path = dirname(__FILE__) . '/views/optin.php';

				echo blc_call_fn(
					['fn' => 'blocksy_render_view'],
					$path,
					[]
				);
			});

			blc_fs()->add_action('connect/after', function () {
				echo '</div>';
			});

			add_action(
				'wp_ajax_blocksy_fs_connect_again',
				function () {
					if (! current_user_can('edit_theme_options')) {
						wp_send_json_error();
					}

					blc_fs()->connect_again();
					wp_send_json_success();
				}
			);
		}

		add_filter(
			'blocksy_dashboard_localizations',
			function ($d) {
				$result = [
					'is_pro' => false,
					'is_anonymous' => false,
					'connect_template' => ''
				];

				if (function_exists('blc_fs')) {
					$is_anonymous = blc_fs()->is_anonymous();
					$connect_template = '';

					if ($is_anonymous) {
						ob_start();
						blc_fs()->_connect_page_render();
						$connect_template = ob_get_clean();
					}

					$result = [
						'is_pro' => blc_fs()->can_use_premium_code(),
						'is_anonymous' => $is_anonymous,
						'connect_template' => $connect_template
					];
				}

				if (
					Plugin::instance()->premium
					&&
					is_callable([
						Plugin::instance()->premium,
						'user_wants_beta_updates'
					])
				) {
					$result['has_beta_consent'] = Plugin::instance()->premium->user_wants_beta_updates();
				}

				return array_merge($result, $d);
			}
		);

		add_action('admin_init', function ($plugin) {
			if (wp_doing_ajax()) {
				return;
			}

			if (! is_admin()) {
				return;
			}

			if (! is_user_logged_in()) {
				return;
			}

			if (is_network_admin()) {
				return;
			}

			if (intval(get_option('blc_activation_redirect', false)) === wp_get_current_user()->ID) {
				delete_option('blc_activation_redirect');
				exit(wp_redirect(admin_url('admin.php?page=ct-dashboard')));
			}
		});
	}

	public function enqueue_static() {
		if (! $this->is_dashboard_page()) return;

		$data = get_plugin_data(BLOCKSY__FILE__);

		$deps = apply_filters('blocksy-dashboard-scripts-dependencies', [
			'wp-i18n',
			'ct-events',
			'ct-options-scripts'
		]);

		if (function_exists('blocksy_get_wp_parent_theme')) {
			wp_enqueue_script(
				'blocksy-dashboard-scripts',
				BLOCKSY_URL . 'static/bundle/dashboard.js',
				$deps,
				$data['Version'],
				false
			);
		} else {
			wp_enqueue_script(
				'blocksy-dashboard-scripts',
				BLOCKSY_URL . 'static/bundle/dashboard-no-theme.js',
				[
					'underscore',
					'react',
					'react-dom',
					'wp-element',
					'wp-date',
					'wp-i18n',
					'updates'
				],
				$data['Version'],
				false
			);

			$slug = 'blocksy';
			wp_localize_script(
				'blocksy-dashboard-scripts',
				'ctDashboardLocalizations',
				[
					'activate'=> current_user_can('switch_themes') ? wp_nonce_url(admin_url('themes.php?action=activate&amp;stylesheet=' . $slug), 'switch-theme_' . $slug) : null,
				]
			);
		}


		wp_enqueue_style(
			'blocksy-dashboard-styles',
			BLOCKSY_URL . 'static/bundle/dashboard.min.css',
			[],
			$data['Version']
		);
	}

	public function setup_framework_page() {
		if (function_exists('blocksy_get_wp_parent_theme')) {
			return;
		}

		if (! current_user_can('activate_plugins')) {
			return;
		}

		$data = get_plugin_data(BLOCKSY__FILE__);

		$options = [
			'title' => __('Blocksy', 'blocksy-companion'),
			'menu-title' => __('Blocksy', 'blocksy-companion'),
			'permision' => 'activate_plugins',
			'top-level-handle' => 'ct-dashboard',
			'callback' => [$this, 'welcome_page_template'],
			'icon-url' => apply_filters(
				'blocksy:dashboard:icon-url',
				BLOCKSY_URL . 'static/img/navigation.svg'
			),
			'position' => 2,
		];


		add_menu_page(
			$options['title'],
			$options['menu-title'],
			$options['permision'],
			$options['top-level-handle'],
			$options['callback'],
			$options['icon-url'],
			2
		);
	}

	public function is_dashboard_page() {
		global $pagenow;

		if (is_network_admin()) {
			$is_ct_settings =
				// 'themes.php' === $pagenow &&
				isset( $_GET['page'] ) && 'blocksy-companion' === $_GET['page'];

			return $is_ct_settings;
		}

		$is_ct_settings =
			// 'themes.php' === $pagenow &&
			isset( $_GET['page'] ) && 'ct-dashboard' === $_GET['page'];

		return $is_ct_settings;
	}

	public function welcome_page_template() {
		if (! current_user_can('manage_options')) {
			wp_die(
				esc_html(
					__( 'You do not have sufficient permissions to access this page.', 'blocksy-companion' )
				)
			);
		}

		echo '<div id="ct-dashboard"></div>';
	}
}
