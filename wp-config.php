<?php
/**
 * This config file is yours to hack on. It will work out of the box on Pantheon
 * but you may find there are a lot of neat tricks to be used here.
 *
 * See our documentation for more details:
 *
 * https://pantheon.io/docs
 */

/**
 * Local configuration information.
 *
 * If you are working in a local/desktop development environment and want to
 * keep your config separate, we recommend using a 'wp-config-local.php' file,
 * which you should also make sure you .gitignore.
 */
if (file_exists(dirname(__FILE__) . '/wp-config-local.php') && !isset($_ENV['PANTHEON_ENVIRONMENT'])):
  # IMPORTANT: ensure your local config does not include wp-settings.php
  require_once(dirname(__FILE__) . '/wp-config-local.php');

/**
 * Pantheon platform settings. Everything you need should already be set.
 */
else:
  if (isset($_ENV['PANTHEON_ENVIRONMENT'])):
    // ** MySQL settings - included in the Pantheon Environment ** //
    /** The name of the database for WordPress */
    define('DB_NAME', $_ENV['DB_NAME']);

    /** MySQL database username */
    define('DB_USER', $_ENV['DB_USER']);

    /** MySQL database password */
    define('DB_PASSWORD', $_ENV['DB_PASSWORD']);

    /** MySQL hostname; on Pantheon this includes a specific port number. */
    define('DB_HOST', $_ENV['DB_HOST'] . ':' . $_ENV['DB_PORT']);

    /** Database Charset to use in creating database tables. */
    define('DB_CHARSET', 'utf8');

    /** The Database Collate type. Don't change this if in doubt. */
    define('DB_COLLATE', '');

    /**#@+
     * Authentication Unique Keys and Salts.
     *
     * Change these to different unique phrases!
     * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
     * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
     *
     * Pantheon sets these values for you also. If you want to shuffle them you
     * must contact support: https://pantheon.io/docs/getting-support 
     *
     * @since 2.6.0
     */
    define('AUTH_KEY',         $_ENV['AUTH_KEY']);
    define('SECURE_AUTH_KEY',  $_ENV['SECURE_AUTH_KEY']);
    define('LOGGED_IN_KEY',    $_ENV['LOGGED_IN_KEY']);
    define('NONCE_KEY',        $_ENV['NONCE_KEY']);
    define('AUTH_SALT',        $_ENV['AUTH_SALT']);
    define('SECURE_AUTH_SALT', $_ENV['SECURE_AUTH_SALT']);
    define('LOGGED_IN_SALT',   $_ENV['LOGGED_IN_SALT']);
    define('NONCE_SALT',       $_ENV['NONCE_SALT']);
    /**#@-*/

    /** A couple extra tweaks to help things run well on Pantheon. **/
    if (isset($_SERVER['HTTP_HOST'])) {
        // HTTP is still the default scheme for now. 
        $scheme = 'http';
        // If we have detected that the end use is HTTPS, make sure we pass that
        // through here, so <img> tags and the like don't generate mixed-mode
        // content warnings.
        if (isset($_SERVER['HTTP_USER_AGENT_HTTPS']) && $_SERVER['HTTP_USER_AGENT_HTTPS'] == 'ON') {
            $scheme = 'https';
            $_SERVER['HTTPS'] = 'on';
        }
        define('WP_HOME', $scheme . '://' . $_SERVER['HTTP_HOST']);
        define('WP_SITEURL', $scheme . '://' . $_SERVER['HTTP_HOST']);
    }
    // Don't show deprecations; useful under PHP 5.5
    error_reporting(E_ALL ^ E_DEPRECATED);
    /** Define appropriate location for default tmp directory on Pantheon */
    define('WP_TEMP_DIR', $_SERVER['HOME'] .'/tmp');

    // FS writes aren't permitted in test or live, so we should let WordPress know to disable relevant UI
    if ( in_array( $_ENV['PANTHEON_ENVIRONMENT'], array( 'test', 'live' ) ) && ! defined( 'DISALLOW_FILE_MODS' ) ) :
        define( 'DISALLOW_FILE_MODS', true );
    endif;

  else:
    /**
     * This block will be executed if you have NO wp-config-local.php and you
     * are NOT running on Pantheon. Insert alternate config here if necessary.
     *
     * If you are only running on Pantheon, you can ignore this block.
     */
    define('DB_NAME',          'database_name');
    define('DB_USER',          'database_username');
    define('DB_PASSWORD',      'database_password');
    define('DB_HOST',          'database_host');
    define('DB_CHARSET',       'utf8');
    define('DB_COLLATE',       '');
    define('AUTH_KEY',         'put your unique phrase here');
    define('SECURE_AUTH_KEY',  'put your unique phrase here');
    define('LOGGED_IN_KEY',    'put your unique phrase here');
    define('NONCE_KEY',        'put your unique phrase here');
    define('AUTH_SALT',        'put your unique phrase here');
    define('SECURE_AUTH_SALT', 'put your unique phrase here');
    define('LOGGED_IN_SALT',   'put your unique phrase here');
    define('NONCE_SALT',       'put your unique phrase here');
  endif;
endif;

/** Standard wp-config.php stuff from here on down. **/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * You may want to examine $_ENV['PANTHEON_ENVIRONMENT'] to set this to be
 * "true" in dev, but false in test and live.
 */
if ( ! defined( 'WP_DEBUG' ) ) {
    define('WP_DEBUG', false);
}

/* That's all, stop editing! Happy Pressing. */




/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
