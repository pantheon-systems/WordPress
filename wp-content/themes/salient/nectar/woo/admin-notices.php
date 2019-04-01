<?php 

/* admin notice for left over uneeded template files */
if( get_option( 'nectar_dismiss_older_woo_templates_notice' ) != true ) {
    add_action( 'admin_notices', 'nectar_add_dismissible_woo_notice' );
}

function nectar_add_dismissible_woo_notice() { ?>
      <div class='notice notice-error nectar-dismiss-notice is-dismissible'>
          <p><?php echo esc_html__('There are some outdated WooCommerce template files in your salient theme directory that are likely lingering around from a previous version of the theme. Salient no longer includes any files in the salient/woocommerce/cart directory. Please ensure none of the following files are present in your theme directory (wp-content/themes/salient).','salient'); ?></p>
          <p>
          <?php echo esc_html__('salient/woocommerce/cart/cart.php', 'salient'); ?> <br />
          <?php echo esc_html__('salient/woocommerce/cart/cart-shipping.php', 'salient'); ?> <br />
          <?php echo esc_html__('salient/woocommerce/cart/cart-totals.php', 'salient'); ?> <br />
          <?php echo esc_html__('salient/woocommerce/cart/shipping-calculator.php', 'salient'); ?> <br />
          </p>
      </div>
<?php }


add_action( 'admin_enqueue_scripts', 'nectar_add_admin_notice_script' );
function nectar_add_admin_notice_script() {
	
		global $nectar_get_template_directory_uri;
		
	  wp_register_script( 'nectar-woo-admin-notice-update', $nectar_get_template_directory_uri . '/nectar/woo/js/admin_notices.js','','1.0', false );
	  
	  wp_localize_script( 'nectar-woo-admin-notice-update', 'notice_params', array(
	      'ajaxurl' => esc_url(get_admin_url()) . 'admin-ajax.php', 
	  ));
	  
	  wp_enqueue_script(  'nectar-woo-admin-notice-update' );
		
}

add_action( 'wp_ajax_nectar_dismiss_older_woo_templates_notice', 'nectar_dismiss_older_woo_templates_notice' );
function nectar_dismiss_older_woo_templates_notice() {
      update_option( 'nectar_dismiss_older_woo_templates_notice', true );
}