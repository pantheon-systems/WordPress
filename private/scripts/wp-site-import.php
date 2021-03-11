<?php

// Enable Ocean theme
echo "Enabling WP plugins and themes...\n";
passthru("wp theme enable oceanwp --activate");
passthru("wp plugin activate pantheon-advanced-page-cache wp-native-php-sessions ocean-extra");


// Import data into WordPress
echo "Importing default content...\n";
passthru("wp import ../data/sample-data.xml --authors=skip");
echo "Import complete.\n";
