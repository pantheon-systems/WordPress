#!/usr/bin/env bash

# When running local tests, make sure you set ABSPATH to define( 'ABSPATH', '/tmp/wordpress/' ); in
# /tmp/wordpress-tests-lib/wp-tests-config.php
sudo cp -r * .[^.]* /tmp/wordpress/wp-content/plugins/wp-all-export-pro/
phpunit