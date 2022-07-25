<?php
// Get paths for imports
$path  = $_SERVER['DOCUMENT_ROOT'] . '/private/data';

// Enable Ocean theme
echo "Enabling WP plugins and themes...\n";
passthru("wp theme enable oceanwp --activate");
passthru("wp plugin install pantheon-advanced-page-cache wp-native-php-sessions ocean-extra wordpress-importer --activate");


// Import data into WordPress
echo "Importing default content...\n";
passthru("wp import $path/sample-data.xml --authors=skip &");
echo "Import complete.\n";
