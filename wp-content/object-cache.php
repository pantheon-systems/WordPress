<?php
// Path to the wp-redis object-cache.php file.
$wpredis_file = WP_CONTENT_DIR . '/plugins/wp-redis/object-cache.php';
// Requires the file only if present (i.e. if the wp-redis plugin is present).
if (file_exists($wpredis_file)) {
  require_once $wpredis_file;
}
