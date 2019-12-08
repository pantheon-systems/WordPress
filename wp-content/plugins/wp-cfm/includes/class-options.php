<?php

class WPCFM_Options
{

    public $is_network = false;


    function __construct() {

        // WP-CLI --network handler
        $argv = isset( $_SERVER['argv'] ) ? $_SERVER['argv'] : array();
        $has_network_flag = ( false !== array_search( '--network', $argv ) );

        // Network admin
        if ( is_network_admin() || $has_network_flag ) {
            $this->is_network = true;
        }
    }


    /**
     * Get the WP-CFM settings (multi-site support)
     * @since 1.3.0
     */
    function get( $option ) {
        return ( $this->is_network ) ?
            get_site_option( $option ) :
            get_option( $option );
    }


    /**
     * Update the WP-CFM settings (multi-site support)
     * @since 1.3.0
     */
    function update( $option, $value ) {
        return ( $this->is_network ) ?
            update_site_option( $option, $value ) :
            update_option( $option, $value );
    }
}
