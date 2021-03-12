<?php
// Get paths for imports
$path  = $_SERVER['DOCUMENT_ROOT'] . '/private/data';

// Enable Ocean theme
echo "Enabling WP plugins and themes...\n";
system("wp theme enable oceanwp --activate");
system("wp plugin activate pantheon-advanced-page-cache wp-native-php-sessions ocean-extra");


// Import data into WordPress
echo "Importing default content...\n";
system("wp import ${path}/sample-data.xml --authors=skip");
echo "Import complete.\n";
