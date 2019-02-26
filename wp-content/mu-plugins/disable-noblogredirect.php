<?php

/**
 * Plugin Name: Disable NoBlogRedirect
 * Plugin URI: https://gist.github.com/dejanmarkovic/8323792
 * Description: Disables the redirect for the main site if a path is not found.
 * Author: dejanmarkovic
 */
remove_action( 'template_redirect', 'maybe_redirect_404' );
