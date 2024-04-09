<?php
/**
 * Pantheon mu-plugin helper functions
 *
 * @package pantheon
 */

namespace Pantheon;

/**
 * Helper function that returns the current WordPress version.
 *
 * @return string
 */
function _pantheon_get_current_wordpress_version(): string {
	include ABSPATH . WPINC . '/version.php';
	return $wp_version; // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
}
