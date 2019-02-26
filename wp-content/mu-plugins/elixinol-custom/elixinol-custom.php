<?php
/**
 * Plugin Name: Elixinol Custom
 * Description: This Plugin adds or removes features required for the Elixinol multisite network.
 * Author: Christopher Cook (chris.cook@elixinol.com)
 */

// Remove the WooCommerce network widgets
function elixinol_custom_remove_dashboard_meta() {
  remove_meta_box( 'woocommerce_network_orders', 'dashboard-network', 'normal' );
  remove_meta_box( 'woocommerce_network_orders', 'dashboard', 'normal' );
}
add_action( 'admin_init', 'elixinol_custom_remove_dashboard_meta' );
