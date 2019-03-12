<?php
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$settings_link = '<a href="' . admin_url( 'admin.php?page=wc-order-export&tab=tools' ) . '" target=_blank>' . __( 'settings', 'woo-order-export-lite' )  . '</a>';
$snippets_link = '<a href="https://algolplus.com/plugins/snippets-plugins/" target=_blank>' . __( 'code snippets', 'woo-order-export-lite' ) . '</a>';
$samples_link = '<a href="https://algolplus.com/plugins/code-samples/" target=_blank>' . __( 'this page', 'woo-order-export-lite' ) . '</a>';
?>
<div class="clearfix"></div>
<div id="woe-admin" class="container-fluid wpcontent">
<br>
<p><?php _e( 'Need help? Create ticket in', 'woo-order-export-lite' ) ?> <a href="https://algolplus.freshdesk.com" target=_blank><?php _e( 'helpdesk system', 'woo-order-export-lite' ) ?></a>. 
<br>
<br>
<?php echo sprintf( __( "Don't forget to attach your %s or some screenshots. It will significantly reduce reply time :)", 'woo-order-export-lite' ), $settings_link); ?></p>
<br>
<p><?php echo sprintf( __('Look at %s for popular plugins or check %s to study how to extend the plugin.', 'woo-order-export-lite' ), $snippets_link, $samples_link); ?></p>
</div>