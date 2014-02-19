<?php
// Pantheon-specific configuration.
// NOTE: the $_ENV values are automatically part of the runtime container.
if (isset($_ENV['PANTHEON_ENVIRONMENT'])) {
  define('DB_NAME', $_ENV['DB_NAME']);
  define('DB_USER', $_ENV['DB_USER']);
  define('DB_PASSWORD', $_ENV['DB_PASSWORD']);
  define('DB_HOST', $_ENV['DB_HOST'] . ':' . $_ENV['DB_PORT']);
  define('DB_CHARSET', 'utf8');
  define('DB_COLLATE', '');
  define('UPLOADS', $_ENV['FILEMOUNT']);
  define('AUTH_KEY', $_ENV['AUTH_KEY']);
  define('SECURE_AUTH_KEY', $_ENV['SECURE_AUTH_KEY']);
  define('LOGGED_IN_KEY', $_ENV['LOGGED_IN_KEY']);
  define('NONCE_KEY', $_ENV['NONCE_KEY']);
  define('AUTH_SALT', $_ENV['AUTH_SALT']);
  define('SECURE_AUTH_SALT', $_ENV['SECURE_AUTH_SALT']);
  define('LOGGED_IN_SALT', $_ENV['LOGGED_IN_SALT']);
  define('NONCE_SALT', $_ENV['NONCE_SALT']);
  define('COOKIEHASH', md5($_ENV['AUTH_KEY']));

  // Additional tweaks to run well on Pantheon
  if (isset($_SERVER['HTTP_HOST'])) {
    define('WP_HOME', 'http://' . $_SERVER['HTTP_HOST']);
    define('WP_SITEURL', 'http://' . $_SERVER['HTTP_HOST']);
  }
  // Don't show deprecations
  error_reporting(E_ALL ^ E_DEPRECATED);
}

// If you would like to keep a local development configuration file, use
// 'wp-config-local.php'. This will be ignored by git and never pushed
// up to Pantheon.
if (!isset($_ENV['PANTHEON_ENVIRONMENT']) &&
    file_exists('wp-config-local.php')) {
  include_once('wp-config-local.php');
}
elseif (!isset($_ENV['PANTHEON_ENVIRONMENT'])) {
  // Otherwise, code within this block will be used in non-Pantheon environments.
  define('DB_NAME', 'local_db_database');
  /** MySQL database username */
  define('DB_USER', 'local_db_username');
  /** MySQL database password */
  define('DB_PASSWORD', 'local_db_password');
  /** MySQL hostname */
  define('DB_HOST', 'localhost');
  /** Database Charset to use in creating database tables. */
  define('DB_CHARSET', 'utf8');
  /** The Database Collate type. Don't change this if in doubt. */
  define('DB_COLLATE', '');
  define('AUTH_KEY', 'unique key here');
  define('SECURE_AUTH_KEY', 'unique key here');
  define('LOGGED_IN_KEY', 'unique key here');
  define('NONCE_KEY', 'unique key here');
  define('AUTH_SALT', 'unique key here');
  define('SECURE_AUTH_SALT', 'unique key here');
  define('LOGGED_IN_SALT', 'unique key here');
  define('NONCE_SALT', 'unique key here');
}

////////////////////////////////////
// Global WordPress Configuration //
////////////////////////////////////
$table_prefix = 'wp_';
define('WPLANG', '');
define('WP_DEBUG', false);
if (!defined('ABSPATH'))
	define('ABSPATH', dirname(__FILE__) . '/');
require_once (ABSPATH . 'wp-settings.php');
