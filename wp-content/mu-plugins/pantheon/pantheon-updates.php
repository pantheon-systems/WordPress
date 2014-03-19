<?php
// Only in Test and Live Environments...
if ( in_array( $_ENV['PANTHEON_ENVIRONMENT'], Array('test', 'live') ) ) {
  //
  // Disable Core Updates EVERYWHERE (use git upstream)
  //
  add_filter( 'pre_site_transient_update_core', create_function( '$a', "return null;" ) );

  //
  // Disable Plugin Updates
  //
  add_action('admin_menu','hide_admin_notices');
  function hide_admin_notices() {
    remove_action( 'admin_notices', 'update_nag', 3 );
  }

  remove_action( 'load-update-core.php', 'wp_update_plugins' );
  add_filter( 'pre_site_transient_update_plugins', create_function( '$a', "return null;" ) );

  //
  // Disable Theme Updates
  //
  remove_action( 'load-update-core.php', 'wp_update_themes' );
  add_filter( 'pre_site_transient_update_themes', create_function( '$a', "return null;" ) );
}

