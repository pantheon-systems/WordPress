<?php
/*
  Plugin Name: Pantheon
  Plugin URI: https://pantheon.io/
  Description: Building on Pantheon's and WordPress's strengths, together.
  Version: 0.1
  Author: Pantheon
  Author URI: https://pantheon.io/
*/

if ( isset( $_ENV['PANTHEON_ENVIRONMENT'] ) ) :

require_once( 'pantheon/pantheon-page-cache.php' );
require_once( 'pantheon/pantheon-updates.php' );
require_once( 'pantheon/pantheon-login-form-mods.php' );

endif; # Ensuring that this is on Pantheon
