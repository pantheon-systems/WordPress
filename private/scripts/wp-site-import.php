<?php
// Get paths for imports
$path  = $_SERVER['DOCUMENT_ROOT'] . '/private/data';

$req = pantheon_curl('https://api.live.getpantheon.com/sites/self/attributes', NULL, 8443);
$meta = json_decode($req['body'], true);

// Install from profile.
echo "Installing WordPress core...\n";
$title = $meta['label'];
$email = $_POST['user_email'];
system("wp core install --title='{$title}' --admin_user=superuser --admin_email='{$email}'");
echo "WordPress install complete.\n";

// Enable Ocean theme
echo "Enabling WP plugins and themes...\n";
system("wp theme enable oceanwp --activate");
system("wp plugin install pantheon-advanced-page-cache wp-native-php-sessions ocean-extra wordpress-importer --activate");

// Import data into WordPress
echo "Importing default content...\n";
system("wp import ${path}/sample-data.xml --authors=skip");
echo "Import complete.\n";
