<?php

// Enable Ocean theme and plugins
echo "Enabling WP plugins and themes...\n";
passthru("wp theme activate blocksy");
passthru("wp plugin install pantheon-advanced-page-cache wp-native-php-sessions wordpress-importer --activate");
