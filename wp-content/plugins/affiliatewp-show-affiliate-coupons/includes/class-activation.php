<?php
/**
 * Activation handler
 *
 * @package     AffiliateWP\ActivationHandler
 * @since       1.0.0
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * AffiliateWP Activation Handler Class
 *
 * @since       1.0.0
 */
class AffiliateWP_Activation {

    public $plugin_name, $plugin_path, $plugin_file, $has_affiliatewp;

    /**
     * Setup the activation class
     *
     * @access      public
     * @since       1.0.0
     * @return      void
     */
    public function __construct( $plugin_path, $plugin_file ) {
        // We need plugin.php!
        require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

        $plugins = get_plugins();

        // Set plugin directory
        $plugin_path = array_filter( explode( '/', $plugin_path ) );
        $this->plugin_path = end( $plugin_path );

        // Set plugin file
        $this->plugin_file = $plugin_file;

        // Set plugin name
        if ( isset( $plugins[$this->plugin_path . '/' . $this->plugin_file]['Name'] ) ) {
            $this->plugin_name = str_replace( 'AffiliateWP - ', '', $plugins[$this->plugin_path . '/' . $this->plugin_file]['Name'] );
        } else {
            $this->plugin_name = __( 'This plugin', 'affiliate-wp' );
        }

        // Is AffiliateWP installed?
        foreach ( $plugins as $plugin_path => $plugin ) {
            if ( $plugin['Name'] == 'AffiliateWP' ) {
                $this->has_affiliatewp = true;
                break;
            }
        }
    }


    /**
     * Process plugin deactivation
     *
     * @access      public
     * @since       1.0.0
     * @return      void
     */
    public function run() {
        // Display notice
        add_action( 'admin_notices', array( $this, 'missing_affiliatewp_notice' ) );
    }

    /**
     * Display notice if AffiliateWP isn't installed
     *
     * @access      public
     * @since       1.0.0
     * @return      string The notice to display
     */
    public function missing_affiliatewp_notice() {

        if ( $this->has_affiliatewp ) {
           echo '<div class="error"><p>' .  $this->plugin_name . sprintf( __( ' requires %sAffiliateWP%s. Please activate it to continue.', 'affiliate-wp' ), '<a href="http://affiliatewp.com/" title="AffiliateWP" target="_blank">', '</a>' ) . '</p></div>'; 

        } else {
            echo '<div class="error"><p>' . $this->plugin_name . sprintf( __( ' requires %sAffiliateWP%s. Please install it to continue.', 'affiliate-wp' ), '<a href="http://affiliatewp.com/" title="AffiliateWP" target="_blank">', '</a>' ) . '</p></div>';
        }
    }
}
