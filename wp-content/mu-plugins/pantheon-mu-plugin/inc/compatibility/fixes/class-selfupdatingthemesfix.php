<?php
/**
 * Self Updating Themes Fix
 *
 * @package Pantheon\Compatibility\Fixes
 */

namespace Pantheon\Compatibility\Fixes;

use const ABSPATH;

/**
 * Self Updating Themes Fix
 */
class SelfUpdatingThemesFix {
	/**
	 * @return void
	 */
	public static function apply() {
		/** Disable theme FTP form */
		DefineConstantFix::apply( 'FS_CHMOD_DIR', ( 0755 & ~umask() ) );
		DefineConstantFix::apply( 'FS_CHMOD_FILE', ( 0755 & ~umask() ) );
		DefineConstantFix::apply( 'FTP_BASE', ABSPATH );
		DefineConstantFix::apply( 'FTP_CONTENT_DIR', ABSPATH . '/wp-content/' );
		DefineConstantFix::apply( 'FTP_PLUGIN_DIR', ABSPATH . '/wp-content/plugins/' );
	}
}
