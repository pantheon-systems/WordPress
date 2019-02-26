<?php
if ( ! function_exists('wp_redirect_or_javascript')):
/**
 * For AJAX request outputs javascript specified, otherwise acts like wp_redirect 
 * @param string $location
 * @param string[optional] $javascript
 * @param int[optional] $status
 */
function wp_redirect_or_javascript($location, $javascript = NULL, $status = 302) {
	if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
		is_null($javascript) and $javascript = 'location.href="' . addslashes($location) . '";';
		echo '<script type="text/javascript">' . $javascript . '</script>';
	} else {
		return wp_redirect($location, $status);
	}
}
endif;