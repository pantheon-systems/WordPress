<?php

/**
 * Sanitize the DB on database clone, switch between Drupal and WordPress.
 */

// Don't ever sanitize the database on the live environment. Doing so would
// destroy the canonical version of the data.
if (defined('PANTHEON_ENVIRONMENT') && (PANTHEON_ENVIRONMENT !== 'live')) {

    // Switch between environments.
    switch ($_ENV['FRAMEWORK']) {

        case 'drupal':
            // Run sanitizer.
            echo "Sanitizing the database...\n";
            passthru('drush sql-sanitize -y');
            echo "Database sanitization complete.\n";
            break;

        case 'wordpress':
            echo "Sanitizing the database...\n";
            // Bootstrap WordPress
            require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
            global $wpdb;
            // Adapted from http://crackingdrupal.com/blog/greggles/creating-sanitized-drupal-database-dump#comment-164
            $wpdb->query("UPDATE wp_users SET user_email = CONCAT(user_login, '@localhost'), user_pass = MD5(CONCAT('MILDSECRET', user_login)), user_activation_key = '';");
            echo "Database sanitization complete.\n";
            break;
    }
}
