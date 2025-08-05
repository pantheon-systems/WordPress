<?php
/**
 * Class DeleteFileFix
 *
 * @package Pantheon\Compatibility\Fixes
 */

namespace Pantheon\Compatibility\Fixes;

/**
 * Class DeleteFileFix
 *
 * @package Pantheon\Compatibility\Fixes
 */
class DeleteFileFix {
	/**
	 * Apply the fix
	 *
	 * @return void
	 */
	public static function apply( $files ) {
		if ( ! function_exists( 'wp_delete_file' ) ) {
			require_once ABSPATH . 'wp-includes/functions.php';
		}
		if ( is_array( $files ) ) {
			foreach ( $files as $file ) {
				if ( file_exists( $file ) ) {
					wp_delete_file( $file );
				}
			}
		}

		if ( is_string( $files ) ) {
			if ( file_exists( $files ) ) {
				wp_delete_file( $files );
			}
		}
	}
}
