<?php
/*
  osapi-contacts.php
  OneSaas Connect API 2.0.6.43 for WooCommerce v2.0.20
  http://www.onesaas.com
  Copyright (c) 2014 oneSaas
*/

function process_request() {
	global $wpdb, $woocommerce, $xmlRequest, $xml;
	
	if ($xmlRequest->getName()==='OrderShippingTracking') {
		foreach ($xmlRequest->attributes() as $attr) {
			if ($attr->getName() === 'Id') {
				$OrderId = 0 + $attr;
			}
		}
		// Init variables to avoid Notices
		$Date = '';
		$TrackingCode = '';
		$TrackingUrl = '';
		$CarrierCode = '';
		$CarrierName = '';
		$Notes = '';
		foreach ($xmlRequest->children() as $child) {
			switch ($child->getName()) {
				case 'OrderNumber':
					$OrderNumber = $child;
					break;
				case 'Date':
					$Date = $child;
					break;
				case 'TrackingCode':
					$TrackingCode = $child;
					break;
				case 'TrackingUrl':
					$TrackingUrl = $child;
					break;
				case 'CarrierCode':
					$CarrierCode = $child;
					break;
				case 'CarrierName':
					$CarrierName = $child;
					break;
				case 'Notes':
					$Notes = $child;
					break;
				default:
					// Not interested
					break;
			}
		}

		if ($OrderId != null) {
			$order = new WC_Order();
			if ($order->get_order($OrderId)) {
				$shipping_note = '';
				if ($Date != '') {$shipping_note .= 'Date: ' . $Date . '<br>';}
				if ($CarrierName != '') {$shipping_note .= 'CarrierName: ' . $CarrierName . '<br>';}
				if ($CarrierCode != '') {$shipping_note .= 'CarrierCode: ' . $CarrierCode . '<br>';}
				if ($TrackingCode != '') {$shipping_note .= 'TrackingCode: ' . $TrackingCode . '<br>';}
				if ($TrackingUrl != '') {$shipping_note .= 'TrackingUrl: <a href="' . $TrackingUrl . '">' . $TrackingUrl . '</a><br>';}
				if ($Notes != '') {$shipping_note .= 'Notes: ' . $Notes . '<br>';}
				if ($shipping_note != '') {$order->add_order_note($shipping_note);}				
				if(is_plugin_active('woocommerce-shipment-tracking/shipment-tracking.php'))
					{	
						wc_st_add_tracking_number($OrderId, $TrackingCode, $CarrierName, $date_shipped = null, $custom_url = false);
					}					
				try {			
					$order->update_status('completed');
					$xml->addChild('Success','Operation Succeeded.');					
				} catch (Exception $e) {
					$xml->addChild('Error','Error in updating order ' . $OrderId . '. Internal Exception: ' . $e->getMessage());
				}
			} else {
				$xml->addChild('Error','Wrong Paramenters. OrderId = ' . $OrderId);
			}
		} else {
			$xml->addChild('Error','Wrong Paramenters. OrderNumber = ' . $OrderNumber);
		}
	} else {
		$xml->addChild('Error','Wrong xml request format');
	}
}
?>
