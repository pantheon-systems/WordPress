<?php
/**
 * Dashboard API actions
 *
 * @copyright 2019-present Creative Themes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @package   Blocksy
 */

defined( 'ABSPATH' ) || die( "Don't run this file directly!" );

class Blocksy_Admin_Dashboard_API {
	protected $ajax_actions = [
		'get_latest_changelog'
	];

	public function __construct() {
		$this->attach_ajax_actions();
	}

	public function attach_ajax_actions() {
		foreach ($this->ajax_actions as $action) {
			add_action(
				'wp_ajax_' . $action,
				[$this, $action]
			);
		}
	}

	public function get_latest_changelog() {
		$changelog = null;
		$access_type = get_filesystem_method();

		if ($access_type === 'direct') {
			$creds = request_filesystem_credentials(
				site_url() . '/wp-admin/',
				'', false, false,
				[]
			);

			if (WP_Filesystem($creds)) {
				global $wp_filesystem;

				$changelog = $wp_filesystem->get_contents(
					get_template_directory() . '/changelog.txt'
				);
			}
		}

		wp_send_json_success([
			'changelog' => apply_filters(
				'blocksy_changelogs_list',
				[
					[
						'title' => __('Theme', 'blocksy'),
						'changelog' => $changelog,
					]
				]
			)
		]);
	}
}

