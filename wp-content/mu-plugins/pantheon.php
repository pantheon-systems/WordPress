<?php
/*
  Plugin Name: Pantheon
  Plugin URI: http://www.getpantheon.com/
  Description: Building on Pantheon's and WordPress's strengths, together.
  Version: 0.1
  Author: Pantheon
  Author URI: http://getpantheon.com
*/

if ( isset( $_ENV['PANTHEON_ENVIRONMENT'] ) ) :

require_once( 'pantheon/pantheon-cache.php' );
require_once( 'pantheon/pantheon-updates.php' );

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once dirname( __FILE__ ) . '/pantheon/pantheon-cache-cli.php';
}

endif; # Ensuring that this is on Pantheon
