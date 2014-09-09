<?php

/**
 *
 * Add file last modified time to version param of of enqueued scripts & styles
 * 
 * This automagically busts cache whenever there is a change in a file.
 */

add_action( 'wp_enqueue_scripts', function() {

    global $wp_styles, $wp_scripts;

    // Find path of site root. Accounts for WP in subdir.
    $wp_dir = str_replace( home_url(), '', site_url() );
    $site_root_path = str_replace( $wp_dir, '', ABSPATH );

    foreach ( array( 'wp_styles', 'wp_scripts' ) as $resource ) {

        foreach ( (array) $$resource->queue as $name ) {

            if ( empty( $$resource->registered[$name] ) )
                continue;

            $src = $$resource->registered[$name]->src;

            // Admin scripts use path relative to site_url.
            if ( 0 === strpos( $src , '/' ) )
                $src = site_url( $src );
            
            // Skip external scripts.
            if ( false === strpos( $src, home_url() ) )
                continue;

            $file = str_replace( home_url( '/' ), $site_root_path, $src );

            if ( ! file_exists( $file ) )
                continue;

            $mtime = filectime( $file );
            $$resource->registered[$name]->ver = $$resource->registered[$name]->ver . '-' . $mtime;
            
        }
    }

}, 100 );
