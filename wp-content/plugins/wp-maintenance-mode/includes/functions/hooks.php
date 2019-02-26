<?php

/**
 * Add extra plugin headers (used by get_file_data function)
 * 
 * @since 2.2.1
 * @param array $headers
 * @return array
 */
function wpmm_add_extra_plugin_headers($headers) {
	$headers[] = 'GitHub Plugin URI';
	$headers[] = 'Twitter';

	return $headers;
}

/**
 * Change email content type
 * 
 * @since 2.2.1
 * @param string $content_type
 * @return string
 */
function wpmm_change_mail_content_type($content_type) {
	return 'text/html';
}