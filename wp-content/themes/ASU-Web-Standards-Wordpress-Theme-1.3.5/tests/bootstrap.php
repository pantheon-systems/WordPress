<?php
$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
  $_tests_dir = '/tmp/wordpress-tests-lib'; }
require_once $_tests_dir . '/includes/functions.php';
function _manually_load_plugin() {
  global $wp_theme_directories;

  // hack to force wordpress to look into our directory
  register_theme_directory( dirname( dirname( __FILE__ ) ) . '/' );
  register_theme_directory( dirname( dirname( dirname( __FILE__ ) ) ) . '/' );
  switch_theme( 'ASU-Web-Standards-Wordpress-Theme' );
  $theme_name = wp_get_theme();
}
// Add the gios2-php and the plugin root to the include path:
$api_path         = dirname( dirname( __FILE__ ) ).'/.standards/gios2-php';
$plugin_path      = dirname( dirname( __FILE__ ) );
$new_include_path = get_include_path(). PATH_SEPARATOR . $plugin_path. PATH_SEPARATOR . $api_path;
// @codingStandardsIgnoreStart
// echo 'setting php include path to '.$new_include_path;
// @codingStandardsIgnoreEnd
// Set the current enviorment
set_include_path( $new_include_path );
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );
require $_tests_dir . '/includes/bootstrap.php';
require dirname( __FILE__ ) . '/data-loader.php';
