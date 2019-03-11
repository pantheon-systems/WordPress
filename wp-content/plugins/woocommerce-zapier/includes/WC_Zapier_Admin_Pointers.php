<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**
 * Administration (dashboard) pointers/help
 *
 * Class WC_Zapier_Admin_Pointers
 */
class WC_Zapier_Admin_Pointers {

	public function __construct() {
		global $pagenow;

		// Display the setup help/pointer on all dashboard screens except the add/edit screen.
		if (
				! defined( 'DOING_AJAX' )
				&& 'post-new.php' !== $pagenow
				&& 'post.php' !== $pagenow
				&& 0 === WC_Zapier_Feed_Factory::get_number_of_enabled_feeds()
		) {
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		}

	}

	public function admin_enqueue_scripts() {
		// Using Pointers
		wp_enqueue_style( 'wp-pointer' );
		wp_enqueue_script( 'wp-pointer' );

		// Register our action
		add_action( 'admin_print_footer_scripts', array( $this, 'admin_print_footer_scripts' ) );
	}

	public function admin_print_footer_scripts() {
		$pointer_content = '<h3>' . __( 'Integrate WooCommerce &amp; Zapier', 'wc_zapier' ) . '</h3>';
		$pointer_content .= '<p>' . sprintf( __( 'To integrate WooCommerce with Zapier, you must have at least one active Zapier Feed.', 'wc_zapier' ), admin_url( 'post-new.php?post_type=wc_zapier_feed' ) ) . '</br />';
		$pointer_content .= '<p>' . sprintf( __( '<a href="%s">Click here to create one</a>.', 'wc_zapier' ), admin_url( 'post-new.php?post_type=wc_zapier_feed' ) ) . '</p>';
		$pointer_content .= '<p>' . sprintf( __( '<a href="%s" target="_blank">View Documentation</a>.', 'wc_zapier' ), WC_Zapier::documentation_url ) . '</p>';
		?>
		<script type="text/javascript">
			//<![CDATA[
			jQuery(document).ready(function ($) {
				$('#toplevel_page_woocommerce').pointer({
					content: '<?php echo wp_kses_post( $pointer_content ); ?>',
					position: 'top',
					close: function () {
						// Once the close button is hit
						// TODO
					}
				}).pointer('open');
			});
			//]]>
		</script>
	<?php
	}

}