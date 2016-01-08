<?php

function pantheon_admin_theme_style() {
    wp_enqueue_style('pantheon-admin-theme', plugins_url('wp-admin.css', __FILE__));
}
add_action('admin_enqueue_scripts', 'pantheon_admin_theme_style');
add_action('login_enqueue_scripts', 'pantheon_admin_theme_style');

if (defined('PANTHEON_ENVIRONMENT')) {
  // Only when filesystem credentials are requested when in development environments in Git mode
  if(has_filter('request_filesystem_credentials')){
  apply_filter('request_filesystem_credentials');
}
    // Stop users from trying to install plugins in Git mode
    add_filter( 'request_filesystem_credentials', 'pantheon_modify_ftp_request', 8 );
        function pantheon_modify_ftp_request () {
          $PANTHEON_DASHBOARD = 'https://dashboard.pantheon.io/sites/'.$_ENV['PANTHEON_SITE'].'#'.$_ENV['PANTHEON_ENVIRONMENT'].'/code';
    echo 'Plugins and Themes cannot be installed while this environment is in Git mode. Please return to the <a href="'.$PANTHEON_DASHBOARD.'">Pantheon Site Dashboard</a> and switch the connection mode to SFTP.';
      }
    }
