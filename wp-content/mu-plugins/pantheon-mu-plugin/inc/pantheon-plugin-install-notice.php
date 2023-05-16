<?php
/**
 * If a Pantheon site is in Git mode, hide the Plugin installation functionality and show a notice.
 */

if ( ! wp_is_writable( WP_PLUGIN_DIR ) ) {
    if ( ! defined( 'DISALLOW_FILE_MODS' ) ) {
        define( 'DISALLOW_FILE_MODS', true );
    }

    add_action( 'admin_notices', '_pantheon_plugin_install_notice' );
    add_action( 'network_admin_notices', '_pantheon_plugin_install_notice' );
}

function _pantheon_plugin_install_notice() {
    $screen = get_current_screen(); 
    // Only show this notice on the plugins page.
    if ( 'plugins' === $screen->id || 'plugins-network' === $screen->id ) { ?>
        <div class="update-nag notice notice-warning is-dismissible" style="margin: 5px 6em 15px 0;">
            <p style="font-size: 14px; margin: 0;">
                <?php 
                // Translators: %s is a URL to the user's Pantheon Dashboard.
                echo wp_kses_post( sprintf( __( 'If you wish to update or add plugins using the WordPress UI, switch your site to SFTP mode from <a href="%s">your Pantheon dashboard</a>.', 'pantheon-systems' ), 'https://dashboard.pantheon.io/sites/' . $_ENV['PANTHEON_SITE'] ) ); 
                ?>
            </p>
        </div>
        <?php
    }
}