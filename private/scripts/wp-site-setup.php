<?php

// Enable Ocean theme and plugins
echo "Enabling WP plugins and themes...\n";
passthru("wp theme install oceanwp --activate");
passthru("wp plugin install pantheon-advanced-page-cache wp-native-php-sessions ocean-extra wordpress-importer --activate");
