<?php
/**
 * Links
 *
 * @package Ocean_Extra
 * @category Core
 * @author OceanWP
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Start Class
class Ocean_Extra_Links {

	/**
	 * Start things up
	 */
	public function __construct() {
		add_action( 'admin_menu', 		array( $this, 'add_page' ), 12 );
		add_action( 'admin_footer', 	array( $this, 'links_blank' ) );
	}

	/**
	 * Add sub menu page
	 *
	 * @since 1.0.0
	 */
	public function add_page() {
		global $submenu;

		// Affiliate link
		$ref_url = '';
		$aff_ref = apply_filters( 'ocean_affiliate_ref', $ref_url );

	    $submenu[ 'oceanwp-panel' ][] = array( '<div class="oceanwp-link">' . esc_html__( 'Documentation', 'ocean-extra' ) . '</div>', 'manage_options', 'http://docs.oceanwp.org/' );
	    $submenu[ 'oceanwp-panel' ][] = array( '<div class="oceanwp-link">' . esc_html__( 'Support', 'ocean-extra' ) . '</div>', 'manage_options', 'https://oceanwp.org/support/'. $aff_ref .'' );
	}

	/**
	 * Open links in new window
	 *
	 * @since 1.0.0
	 */
	public function links_blank() { ?>
	    <script type="text/javascript">
		    jQuery( document ).ready( function($) {
		        $( '.oceanwp-link' ).parent().attr( 'target', '_blank' );
		    });
	    </script>
    <?php
	}

}
new Ocean_Extra_Links();