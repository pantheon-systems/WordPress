<?php
/**
* @package ElixStatusBacksync
* Plugin Name: Elix Status Backsync
* Plugin URI: http://elixinol.com
* Description: Zoho status-backsync cron.
* Author: Zvi Epner
* Version: 1.0
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Todo: convert to key using Keys plugin.
define("ZOHO_AUTH_TOKEN","8fc8e1c3dcdf70ce805e24310d28f7fc");

// Register Cron on activation.
register_activation_hook(__FILE__, 'elix_status_backsync_activation');
function elix_status_backsync_activation() {
	if( !wp_next_scheduled( 'elix_status_backsync_cronjob' ) ) {
	   wp_schedule_event( time(), 'hourly', 'elix_status_backsync_cronjob' );  
	}
}

// Payload callback.
add_action('elix_status_backsync_cronjob', 'elix_status_backsync_cron'); 
function elix_status_backsync_cron() {

	global $wpdb;
	// Fetch order in Processing state.
	$results = $wpdb->get_results( "SELECT ID, post_status 
		FROM wp_posts 
		WHERE post_type = 'shop_order' AND post_status IN ('wc-processing', '')
		AND post_modified NOT LIKE '" . date("Y-m-d h:m") . "%' 
		ORDER BY RAND() LIMIT 0,20", OBJECT );

	foreach ($results as $result) {
		$id = $result->ID;
		// Remote call to Zoho:CRM:Invoices.
        $invoice = _esb_fetch_invoice($id);
        // If nodata.. updated modified date.
        if (isset($invoice['response']['nodata'])) {
			return _esb_update_modified($id);
		}
        // If a corresponding record exists.. update order status, add note.
        if (isset($invoice['response']['result'])) {
            $updated_status = _esb_get_updated_status($invoice['response']['result']['Invoices']['row']['FL']);
            return _esb_set_updated_status($id, $updated_status);
        }
	}
}

// Unregister Cron on de-activation.
register_deactivation_hook(__FILE__, 'elix_status_backsync_deactivate');
function elix_status_backsync_deactivate() {	
	$timestamp = wp_next_scheduled('elix_status_backsync_cronjob');
	wp_unschedule_event($timestamp, 'elix_status_backsync_cronjob');
} 

/**
 * Helper functions.
 */
function _esb_fetch_invoice($order_id) {
    $criteria = '(WooCom. ID:' . $order_id . ')';
    $url = 'json/Invoices/searchRecords?authtoken=' . ZOHO_AUTH_TOKEN . '&scope=crmapi&criteria=' . urlencode($criteria);
    $data = '';
    $seach_record = _esb_zoho_curl($url, 'GET', $data);
    $json_dec_search = json_decode($seach_record, TRUE);
    return $json_dec_search;
}

function _esb_set_updated_status($id, $updated_status) {

	global $wpdb;

	$wpdb->update( 
		'wp_posts', 
		array( 
			'post_status' => $updated_status,
			'post_modified' => date("Y-m-d H:i:s"),
			'post_modified_gmt' => date("Y-m-d H:i:s")
		), 
		array( 'ID' => $id )
	);


    $msg = 'Order status updated from Zoho: ' . $updated_status . ' (WOO' . $id . ').';
    _esb_insert_note($id, $msg);
    return $msg;
}

function _esb_update_modified($id) {

	global $wpdb;
	$wpdb->update( 
		'wp_posts', 
		array( 
			'post_modified' => date("Y-m-d H:i:s"),
			'post_modified_gmt' => date("Y-m-d H:i:s")
		), 
		array( 'ID' => $id )
	);
    return 'No data for Order WOO' . $id;
}

function _esb_get_updated_status($invoice_metas) {
    $status_map = _esb_status_map();
    foreach ($invoice_metas as $invoice_meta) {
        if ($invoice_meta['val'] == 'Status') {
            $invoice_status = $invoice_meta['content'];
            return $status_map[$invoice_status];
        }
    }
}

function _esb_status_map() {
    return [
        'Completed' => 'wc-completed',
        'Created' => 'wc-processing',
        'Pending Payment' => 'wc-processing',
        'Delivered' => 'wc-completed',
        'On Hold' => 'wc-on-hold',
        'Refunded' => 'wc-refunded',
        'Failed' => 'wc-failed',
        'Cancelled' => 'wc-cancelled'
    ];
}

function _esb_insert_note($id, $msg) {
	
	global $wpdb;
	$wpdb->insert( 
		'wp_comments', 
		array( 
			'comment_post_id' => $id, 
			'comment_author' => 'WooCommerce',
			'comment_author_email' => 'woocommerce@elixinol.com',
			'comment_date' => date('Y-m-d H:i:s'),
			'comment_date_gmt' => date('Y-m-d H:i:s'),
			'comment_content' => $msg,
			'comment_approved' => 1,
			'comment_agent' => 'WooCommerce',
			'comment_type' => 'order_note'
		)
	);
}

// Test zoho json api
/*
$criteria = '(Subject:WOO115552 - WooCommerce Plugin order for Jeffrey Phelps)';
$url = 'json/Invoices/searchRecords?authtoken=' . ZOHO_AUTH_TOKEN . '&scope=crmapi&selectColumns=Invoice(Subject,Status)&criteria=' . urlencode($criteria);
$data = '';
$seach_record = zoho_curl($url, 'GET', $data);
$json_dec_search = json_decode($seach_record, TRUE);
print_r($json_dec_search);
exit;
*/

function _esb_zoho_curl($url, $method, $data){
	$ch = curl_init('https://crm.zoho.com/crm/private/' . $url);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	if ($method == "POST") {
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);	
	}
	$response = curl_exec($ch);
	return $response;
	curl_close($ch);
}

if (!function_exists('curl_file_create')) {
     function curl_file_create($filename, $mimetype = '', $postname = ''){
        return "@$filename;filename=". ($postname ?$postname : basename($filename)). ($mimetype ? ";type=$mimetype" : '');
    }
}