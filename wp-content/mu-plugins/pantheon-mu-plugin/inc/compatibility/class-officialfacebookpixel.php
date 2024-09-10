<?php
/**
 * Compatibility fix for Official Facebook Pixel plugin.
 *
 * @link https://docs.pantheon.io/plugins-known-issues#facebook-for-wordpress-official-facebook-pixel
 * @package Pantheon\Compatibility
 */

namespace Pantheon\Compatibility;

use Pantheon\Compatibility\Fixes\DeleteFileFix;

/**
 * Class OfficialFacebookPixel
 */
class OfficialFacebookPixel extends Base {
	/**
	 * Run fix on plugin activation flag.
	 *
	 * @var bool
	 */
	protected $run_on_plugin_activation = true;

	/**
	 * @return void
	 */
	public function apply_fix() {
		DeleteFileFix::apply( ABSPATH . 'wp-content/plugins/official-facebook-pixel/vendor/techcrunch/wp-async-task/.gitignore' );
	}

	/**
	 * @return void
	 */
	public function remove_fix() {}
}
