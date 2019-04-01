<?php
/*
  osapi-contacts.php
  OneSaas Connect API 2.0.6.43 for WooCommerce v2.0.20
  http://www.onesaas.com
  Copyright (c) 2014 oneSaas
*/

function addSettings() {
	global $PageSize, $wpdb, $OrderCreatedTime, $LastUpdatedTime, $Page, $Action, $xml, $wpdb, $woocommerce;
	
	// Plugin Features
	$features = array('BatchStockUpdates'=>'true');
	if(is_plugin_active('woocommerce-shipment-tracking/shipment-tracking.php'))
		{
			$features['Woocommerce Shipping Tracking'] = 'true';
		}
	else
		{
			$features['Woocommerce Shipping Tracking'] = 'false';
		}
	$price_setting = get_option('woocommerce_prices_include_tax');
	if($price_setting === 'no')
		{
			$features['IsSellingPriceIncludingTax'] = 'false';
		}
	else
		{
			$features['IsSellingPriceIncludingTax'] = 'true';
		}			
	$features_xml = $xml->addChild('PluginFeatures');
	foreach($features as $feature => $status) {
		$feature_xml = $features_xml->addChild('PluginFeature');
		$feature_xml->Name = $feature;
		$feature_xml->Value = $status;
	}
	
	//Times
	$times_xml = $xml->addChild('Times');
	$timezone =  get_option('timezone_string');	
	$offset = get_option('gmt_offset');	
	$localtime = current_time('mysql',$timezone);
	$localtime = Date('Y-m-d\TH:i:s', strtotime($localtime));
	$localtime = $localtime.($offset < 0 ? '-':'+').(abs($offset) > 9 ? '':'0').+abs($offset).':00';
	$localUTCtime = current_time('mysql','UTC');
	$localUTCtime = Date('Y-m-d\TH:i:sP', strtotime($localUTCtime));	
	$times_xml->LocalTime = $localtime;
	$times_xml->LocalUTCTime = $localUTCtime;
	
	// Taxes
	$tax_query = "SELECT * FROM " . $wpdb->prefix . "woocommerce_tax_rates";
	$taxes = $wpdb->get_results($tax_query);
	$tax_rates_xml = $xml->addChild('TaxCodes');
	$fr = new WC_Tax();
	
	foreach($taxes as $tax_rate)
	{
		$tax_rate_xml = $tax_rates_xml->addChild('TaxCode');
		$tax_rate_xml->Name = $tax_rate->tax_rate_name;
		$tax_rate_xml->CountryCode = $tax_rate->tax_rate_country;
		$tax_rate_xml->StateCode = $tax_rate->tax_rate_state;
		// TODO: Locations
		$tax_rate_xml->Rate = $tax_rate->tax_rate/100;
		$tax_rate_xml->IsCompound = ($tax_rate->tax_rate_compound=="0")?"false":"true";
	}
	
	// Shipping Methods
	$shipping_methods_xml = $xml->addChild('ShippingMethods');
	$sm = new WC_Shipping();
	foreach($sm->get_shipping_classes() as $shipping_method)
	{
		$shipping_method_xml = $shipping_methods_xml->addChild('ShippingMethod');
		//$shipping_method_xml->Name = $shipping_method->title;
		//$shipping_method_xml->Description = $shipping_method->description;
		$shipping_method_xml->Obj = print_r($shipping_method,1);
	}
	
	// Payment Methods
	$payment_gateways_xml = $xml->addChild('PaymentGateways');
	$pg = new WC_Payment_Gateways();
	foreach($pg->get_available_payment_gateways() as $payment_gateway)
	{
		$payment_gateway_xml = $payment_gateways_xml->addChild('PaymentGateway');
		$payment_gateway_xml->Name = $payment_gateway->title;
		$payment_gateway_xml->Description = $payment_gateway->title;
		$payment_gateway_xml->LongDescription = $payment_gateway->description;
	}
	
	$payment_gateway_xml = $payment_gateways_xml->addChild('PaymentGateway');
	$payment_gateway_xml->Name = "Other";
	$payment_gateway_xml->Description = "Other";
	$payment_gateway_xml->LongDescription = "Other Payment Methods.";
	
	// Order Statuses
	$order_statuses_xml = $xml->addChild('OrderStatuses');
	$order_statuses = array(
		'pending' => 'Order received (unpaid)',
		'failed' => 'Payment failed or was declined (unpaid)',
		'processing' => 'Payment received and stock has been reduced- the order is awaiting fulfilment',
		'completed' => 'Order fulfilled and complete – requires no further action',
		'on-hold' => 'Awaiting payment – stock is reduced, but you need to confirm payment',
		'cancelled' => 'Cancelled by an admin or the customer – no further action required',
		'refunded' => 'Refunded by an admin - no further action required',
		'pre-order' => 'Order received (pre-order)');
	foreach($order_statuses as $name => $description) {
		$order_status_xml = $order_statuses_xml->addChild('OrderStatus');
		$order_status_xml->Name = $name;
		$order_status_xml->Description = $description;
	}
	
	// Capabilities
	$capabilities = array(
		'Order'=>'Pull',
		'Product'=>'Pull',
		'ProductById'=>'Pull',
		'Contact'=>'Pull',
		'ProductStock'=>'Push',
		'ShippingTracking'=>'Push');
	$capabilities_xml = $xml->addChild('PluginCapabilities');
	foreach($capabilities as $type => $direction) {
		$capability_xml = $capabilities_xml->addChild('Capability');
		$capability_xml->Type = $type;
		$capability_xml->Direction = $direction;
	}
}
?>
