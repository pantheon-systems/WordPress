FROM php:5-apache

ARG WORDPRESS_DB_USER=root
ARG WORDPRESS_DB_PASSWORD=root
ARG WORDPRESS_DB_NAME=wordpress
ARG WORDPRESS_DB_HOST=mysql

# install the PHP extensions we need
RUN set -ex; \
    \
    apt-get update; \
    apt-get install -y --force-yes --no-install-recommends \
      libjpeg-dev \
      libpng-dev \
      mysql-client \
    ; \
    rm -rf /var/lib/apt/lists/*; \
    \
    docker-php-ext-configure gd --with-png-dir=/usr --with-jpeg-dir=/usr; \
    docker-php-ext-install gd mysqli opcache pdo pdo_mysql

RUN { \
		echo 'opcache.memory_consumption=128'; \
		echo 'opcache.interned_strings_buffer=8'; \
		echo 'opcache.max_accelerated_files=4000'; \
		echo 'opcache.revalidate_freq=2'; \
		echo 'opcache.fast_shutdown=1'; \
		echo 'opcache.enable_cli=1'; \
	} > /usr/local/etc/php/conf.d/opcache-recommended.ini

RUN a2enmod rewrite expires

WORKDIR /var/www/html

# Download WP-CLI, install and configure Wordpress
RUN curl -O "https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar"; \
    chmod +x wp-cli.phar; \
    mv wp-cli.phar /usr/local/bin/wp; \
    wp --info --allow-root --debug; \
    wp core download --allow-root --force --debug; \
    wp core config --dbname=$WORDPRESS_DB_NAME --dbuser=$WORDPRESS_DB_USER --dbpass=$WORDPRESS_DB_PASSWORD --dbhost=$WORDPRESS_DB_HOST --force --allow-root --debug --skip-check --extra-php="define( 'WP_DEBUG', true );define( 'WP_DEBUG_LOG', true );define( 'FS_METHOD', 'direct' );";

COPY wait-for-it.sh /usr/local/bin/
COPY entrypoint.sh /usr/local/bin/

RUN chmod +x /usr/local/bin/wait-for-it.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

ENTRYPOINT /usr/local/bin/entrypoint.sh
