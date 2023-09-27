<?php
/**
 * Admin
 *
 * @copyright 2019-present Creative Themes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

require_once get_template_directory() . '/admin/dashboard/plugins/ct-plugin-manager.php';
require_once get_template_directory() . '/admin/helpers/jed-locale-data.php';

if (is_admin() && defined('DOING_AJAX') && DOING_AJAX) {
	require_once get_template_directory() . '/admin/dashboard/api.php';
	require_once get_template_directory() . '/admin/dashboard/plugins/ct-plugin-manager.php';
	require_once get_template_directory() . '/admin/dashboard/plugins/plugins-api.php';
}

require get_template_directory() . '/admin/dashboard/core.php';
require get_template_directory() . '/admin/blocks-editor.php';

require get_template_directory() . '/admin/notices/templates.php';
if (defined('DOING_AJAX') && DOING_AJAX) {
	require get_template_directory() . '/admin/notices/api.php';
}

add_action(
	'init',
	function () {
		blocksy_get_jed_locale_data('blocksy');
	}
);

add_action(
	'admin_enqueue_scripts',
	function () {
		$theme = blocksy_get_wp_parent_theme();

		$current_screen = get_current_screen();

		if (
			$current_screen->id
			&&
			strpos($current_screen->id, 'forminator') !== false
		) {
			return;
		}

		wp_enqueue_media();

		wp_register_script(
			'ct-events',
			get_template_directory_uri() . '/static/bundle/events.js',
			[],
			$theme->get('Version'),
			true
		);

		$deps = apply_filters('blocksy-options-scripts-dependencies', [
			'underscore',
			'react',
			'react-dom',
			'wp-element',
			'wp-date',
			'wp-i18n',
			'ct-events',
			'wp-media-utils'
			// 'wp-polyfill'
		]);

		global $wp_customize;

		if (! isset($wp_customize)) {
			if ($current_screen->base !== 'edit') {
				$deps[] = 'wp-components';
			}

			if (
				$current_screen->base === 'nav-menus'
				||
				(
					$current_screen->base === 'post'
					&&
					$current_screen->is_block_editor
				)
			) {
				wp_enqueue_editor();
			}

			wp_enqueue_script(
				'ct-options-scripts',
				get_template_directory_uri() . '/static/bundle/options.js',
				$deps,
				$theme->get('Version')
			);
		}

		$locale_data_ct = blocksy_get_jed_locale_data('blocksy');

		wp_add_inline_script(
			'wp-i18n',
			'wp.i18n.setLocaleData( ' . wp_json_encode($locale_data_ct) . ', "blocksy" );'
		);

		wp_enqueue_style(
			'ct-options-styles',
			get_template_directory_uri() . '/static/bundle/options.min.css',
			['wp-components'],
			$theme->get('Version')
		);

		if (is_rtl()) {
			wp_enqueue_style(
				'ct-options-rtl-styles',
				get_template_directory_uri() . '/static/bundle/options-rtl.min.css',
				['ct-options-styles'],
				$theme->get('Version')
			);
		}

		wp_localize_script(
			'ct-options-scripts',
			'ct_localizations',
			[
				'gradients' => get_theme_support('editor-gradient-presets')[0],
				'is_dev_mode' => !! (
					defined('BLOCKSY_DEVELOPMENT_MODE')
					&&
					BLOCKSY_DEVELOPMENT_MODE
				),
				'nonce' => wp_create_nonce('ct-ajax-nonce'),
				'public_url' => get_template_directory_uri() . '/static/bundle/',
				'static_public_url' => get_template_directory_uri() . '/static/',
				'dismissed_google_fonts_notice' => get_option(
					'dismissed-blocksy_google_fonts_notice',
					'no'
				) === 'yes',
				'ajax_url' => admin_url('admin-ajax.php'),
				'rest_url' => get_rest_url(),
				'customizer_url' => admin_url('/customize.php?autofocus'),
				'product_name' => blocksy_get_wp_theme()->get('Name'),
				'customizer_sync' => [
					'svg_patterns' => blocksy_get_patterns_svgs_list()
				]
			]
		);
	},
	50
);

