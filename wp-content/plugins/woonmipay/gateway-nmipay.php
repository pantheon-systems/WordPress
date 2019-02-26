<?php
/**
 * Plugin Name: Network Merchants Inc Gateway for WooCommerce
 * Plugin URI: http://www.patsatech.com/
 * Description: WooCommerce Plugin for accepting payment through NMI Gateway.
 * Version: 1.7.6
 * Author: PatSaTECH
 * Author URI: http://www.patsatech.com
 * Contributors: patsatech
 * Requires at least: 4.5
 * Tested up to: 4.9.8
 * WC requires at least: 3.0.0
 * WC tested up to: 3.5.1
 *
 * Text Domain: woo-nmi-patsatech
 * Domain Path: /lang/
 *
 * @package NMI Gateway for WooCommerce
 * @author PatSaTECH
 */


 add_action('plugins_loaded', 'init_woocommerce_nmipay', 0);

 function init_woocommerce_nmipay() {

   if ( ! class_exists( 'WC_Payment_Gateway_CC' ) || ! class_exists( 'WC_Payment_Gateway_eCheck' ) ) { return; }

   load_plugin_textdomain('woo-nmi-patsatech', false, dirname( plugin_basename( __FILE__ ) ) . '/lang');

   include "includes/cc.php";

   include "includes/echeck.php";

   /**
    * Add the gateway to WooCommerce
    **/
   function add_nmipay_gateway( $methods ) {
     $methods[] = 'woocommerce_nmipay';
     $methods[] = 'woocommerce_nmipay_echeck';
     return $methods;
   }
   add_filter('woocommerce_payment_gateways', 'add_nmipay_gateway' );
 }

?>
