<?php
/*
  osapi.php
  OneSaas Connect API 2.0.6.43 for WooCommerce v2.0.20
  http://www.onesaas.com
  Copyright (c) 2014 oneSaas
*/
// Error Mgmt
$original_error_reporting = error_reporting();
error_reporting( E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);

// Init
define('WP_USE_THEMES', false);
//require('../../../wp-load.php');
// OS-4728
$path = preg_replace('/wp-content(?!.*wp-content).*/','',__DIR__);
include($path.'wp-load.php');
$wp->init();
$wp->parse_request();
$wp->register_globals();
$wp->send_headers();
include_once(ABSPATH.'wp-admin/includes/plugin.php');
include("onesaas-functions.php");
os_init();

//global $OrderCreatedTime, $LastUpdatedTime, $Page, $PageSize, $Action, $xml;
	
if (verifyApiKey()) {
	switch ($Action) {
		case "Contacts":
			include("osapi-contacts.php");
			addContacts();
		break;
		
		case "Products": 
			include("osapi-products.php");
			addProducts();
		break;
		
		case "ProductById": 
			include("osapi-productbyib.php");
			addProductById();
		break;	
		
		case "Orders":
			include("osapi-orders.php");
			addOrders();
		break;
		
		case "Settings":
			include("osapi-settings.php");
			addSettings();
		break;
		
		case "ShippingTracking":
			include("osapi-ShippingTracking.php");
			process_request();
		break;
		
		case "UpdateStock":
			include("osapi-UpdateStock.php");
			process_request();
		break;
		
		default:
			$xml->addChild('Error','Invalid Action Request');
		break;
	}
} else {
	$xml->addChild('Error','Invalid API AccessKey');
}

// Display Response
print($xml->asXML());

// Error Mgmt
error_reporting($original_error_reporting);
?>
