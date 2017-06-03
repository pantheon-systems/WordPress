#!/usr/bin/env bash

#clean up
rm -rf $WP_CORE_DIR
mysqladmin drop -f wordpress_test --user="root" --password="root"