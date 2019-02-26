<?php
/*
  osapi-contacts.php
  OneSaas Connect API 2.0.6.43 for WooCommerce v2.0.20
  http://www.onesaas.com
  Copyright (c) 2014 oneSaas
*/

function process_request() {
	global $wpdb, $xmlRequest, $xml;
	
	$stockUpdateRequests = array();
	$batchMode='false';
	//xml_adopt($xml, $xmlRequest);
	
	if ($xmlRequest->getName()==='ProductStockUpdate') {
		// Single product stock update
		$stockUpdateRequests[] = parseSingleStockUpdateRequest($xmlRequest);
	} elseif ($xmlRequest->getName()==='ProductStockUpdates') {
		// Multiple product stock update
		$batchMode='true';
		foreach ($xmlRequest->children() as $aXmlRequest) {
			$stockUpdateRequests[] = parseSingleStockUpdateRequest($aXmlRequest);
		}
	} else {
		// Wrong format
		$xml->Error = "Wrong request format";
	}
	
	foreach ($stockUpdateRequests as $stockUpdateRequest) {
		$psu = $xml->addChild('Response');
		$psu->addAttribute('Id', $stockUpdateRequest['ProductCode']);

		if ($stockUpdateRequest['ProductCode'] != ''){
			try {
				$pf = new WC_Product_Factory();  
				$product = $pf->get_product($stockUpdateRequest['ProductCode']);
				
				if ($product != null) {
					$updated_stock = $product->set_stock(0.0 + $stockUpdateRequest['StockAvailable']);
					$psu->addChild('Success','Operation Succeeded. New Stock ' . $updated_stock);
				} else {
					$psu->addChild('Error','Product id ' . $stockUpdateRequest['ProductCode'] . ' does not exists in Woocommerce');
				}
			} catch (Exception $e) {
				$psu->addChild('Error','Error in updating product ' . $stockUpdateRequest['ProductCode'] . '. Internal Exception: ' . $e->getMessage());
			}
		} else {
			$psu->addChild('Error','Wrong Paramenters. ProductCode=' . $stockUpdateRequest['ProductCode']);
		}
	}	
}
?>
