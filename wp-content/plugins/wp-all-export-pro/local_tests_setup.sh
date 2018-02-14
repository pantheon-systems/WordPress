#!/usr/bin/env bash

WP_VERSION=latest
WP_MULTISITE=0
WP_TEST_URL=http://localhost:12000
WP_TEST_USER=test
WP_TEST_USER_PASS=test

  # install wordpress
bash ./bin/install-wp-tests.sh wordpress_test root '' localhost $WP_VERSION
mkdir /tmp/wordpress/wp-content/plugins/wp-all-export-pro/
cp -r * .[^.]* /tmp/wordpress/wp-content/plugins/wp-all-export-pro/
mkdir /tmp/wordpress/wp-content/plugins/wpai-woocommerce-add-on/
mkdir /tmp/wordpress/wp-content/plugins/wpai-user-add-on/
mkdir /tmp/wordpress/wp-content/plugins/wp-all-import-pro/
git clone https://8aca4d1dc7c8b89cfd52f94a6a27612b3930bd9b@github.com/soflyy/wp-all-import-pro.git /tmp/wordpress/wp-content/plugins/wp-all-import-pro/
git clone https://8aca4d1dc7c8b89cfd52f94a6a27612b3930bd9b@github.com/soflyy/wpai-woocommerce-add-on.git /tmp/wordpress/wp-content/plugins/wpai-woocommerce-add-on/
git clone https://8aca4d1dc7c8b89cfd52f94a6a27612b3930bd9b@github.com/soflyy/wpai-user-add-on.git /tmp/wordpress/wp-content/plugins/wpai-user-add-on/
cd /tmp/wordpress/

wp --info
wp core config --dbname=wordpress_test --dbuser=root
wp core install --url=$WP_TEST_URL --title=Test --admin_user=$WP_TEST_USER --admin_password=$WP_TEST_USER_PASS --admin_email=$WP_TEST_USER@wordpress.dev
wp rewrite structure '/%year%/%monthnum%/%postname%'
wp plugin activate wp-all-export-pro
wp plugin activate wp-all-import-pro
wp plugin install woocommerce --activate