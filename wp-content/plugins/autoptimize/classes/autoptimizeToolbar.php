<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class autoptimizeToolbar
{
    public function __construct()
    {
        // If Cache is not available we don't add the Autoptimize Toolbar
        if( ! autoptimizeCache::cacheavail() ) {
            return;
        }
        // Load admin toolbar feature once WordPress, all plugins, and the theme are fully loaded and instantiated.
        add_action( 'wp_loaded', array( $this, 'load_toolbar' ) );
    }

    public function load_toolbar()
    {
        // Check permissions and that toolbar is not hidden via filter
        if ( current_user_can( 'manage_options' ) && apply_filters( 'autoptimize_filter_toolbar_show', true ) ) {
            // Create a handler for the AJAX toolbar requests
            add_action( 'wp_ajax_autoptimize_delete_cache', array( $this, 'delete_cache' ) );

            // Load custom styles, scripts and menu only when needed
            if ( is_admin_bar_showing() ) {
                if ( is_admin() ) {
                    // in the case of back-end
                    add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
                } else {
                    // in the case of front-end
                    add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
                }
                // Add the Autoptimize Toolbar to the Admin bar
                add_action( 'admin_bar_menu', array( $this, 'add_toolbar' ), 100 );
            }
        }
    }

    public function add_toolbar()
    {
        global $wp_admin_bar;

        // Retrieve the Autoptimize Cache Stats information
        $stats = autoptimizeCache::stats();

        // Set the Max Size recommended for cache files
        $max_size = apply_filters( 'autoptimize_filter_cachecheck_maxsize', 512 * 1024 * 1024 );

        // Retrieve the current Total Files in cache
        $files = $stats[0];
        // Retrieve the current Total Size of the cache
        $bytes = $stats[1];

        $size = $this->format_filesize($bytes);

        // We calculated the percentage of cache used
        $percentage = ceil( $bytes / $max_size * 100 );
        if ( $percentage > 100 ) {
            $percentage = 100;
        }
        // We define the type of color indicator for the current state of cache size.
        // "green" if the size is less than 80% of the total recommended
        // "orange" if over 80%
        // "red" if over 100%
        $color = ( $percentage == 100 ) ? 'red' : ( ( $percentage > 80 ) ? 'orange' : 'green' );

        // Create or add new items into the Admin Toolbar.
        // Main Autoptimize node
        $wp_admin_bar->add_node( array(
            'id'    => 'autoptimize',
            'title' => '<span class="ab-icon"></span><span class="ab-label">' . __( "Autoptimize", 'autoptimize' ) . '</span>',
            'href'  => admin_url( 'options-general.php?page=autoptimize' ),
            'meta'  => array( 'class' => 'bullet-' . $color )
        ));

        // Cache Info node
        $wp_admin_bar->add_node( array(
            'id'     => 'autoptimize-cache-info',
            'title'  => '<p>' . __( "Cache Info", 'autoptimize' ) . '</p>' .
                   '<div class="autoptimize-radial-bar" percentage="' . $percentage . '">' .
                   '<div class="circle">'.
                   '<div class="mask full"><div class="fill bg-' . $color . '"></div></div>'.
                   '<div class="mask half"><div class="fill bg-' . $color . '"></div></div>'.
                   '<div class="shadow"></div>'.
                   '</div>'.
                   '<div class="inset"><div class="percentage"><div class="numbers ' . $color . '">' . $percentage . '%</div></div></div>'.
                   '</div>' .
                   '<table>' .
                   '<tr><td>' . __( "Size", 'autoptimize' ) . ':</td><td class="size ' . $color . '">' . $size . '</td></tr>' .
                   '<tr><td>' . __( "Files", 'autoptimize' ) . ':</td><td class="files white">' . $files . '</td></tr>' .
                   '</table>',
            'parent' => 'autoptimize'
        ));

        // Delete Cache node
        $wp_admin_bar->add_node( array(
            'id'     => 'autoptimize-delete-cache',
            'title'  => __( "Delete Cache", 'autoptimize' ),
            'parent' => 'autoptimize'
        ));
    }

    public function delete_cache()
    {
        check_ajax_referer( 'ao_delcache_nonce', 'nonce' );
        $result = false;
        if ( current_user_can( 'manage_options' ) )
        {
            // We call the function for cleaning the Autoptimize cache
            $result = autoptimizeCache::clearall();
        }
        wp_send_json( $result );
    }

    public function enqueue_scripts()
    {
        // Autoptimize Toolbar Styles
        wp_enqueue_style( 'autoptimize-toolbar', plugins_url( '/static/toolbar.css', __FILE__ ), array(), time(), 'all' );
        // Autoptimize Toolbar Javascript
        wp_enqueue_script( 'autoptimize-toolbar', plugins_url( '/static/toolbar.js', __FILE__ ), array( 'jquery' ), time(), true );
        // Localizes a registered script with data for a JavaScript variable. (We need this for the AJAX work properly in the front-end mode)
        wp_localize_script( 'autoptimize-toolbar', 'autoptimize_ajax_object', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'error_msg' => sprintf( __( 'Your Autoptimize cache might not have been purged successfully, please check on the <a href=%s>Autoptimize settings page</a>.', 'autoptimize' ), admin_url( 'options-general.php?page=autoptimize' ) . ' style="white-space:nowrap;"' ),
            'dismiss_msg' => __( 'Dismiss this notice.' ),
            'nonce' => wp_create_nonce( 'ao_delcache_nonce' )
        ) );
    }

    public function format_filesize($bytes, $decimals = 2)
    {
        $units = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB' );
        for ($i = 0; ($bytes / 1024) > 0.9; $i++, $bytes /= 1024) {}
        return sprintf( "%1.{$decimals}f %s", round( $bytes, $decimals ), $units[$i] );
    }
}
