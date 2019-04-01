<?php
/*
	osapi-contacts.php
	OneSaas Connect API 2.0.6.43 for WooCommerce v2.0.20
	http://www.onesaas.com
	Copyright (c) 2014 oneSaas
	
	2.0.6.5 - OS-286 include contact details into orders
*/

function addOrders() {
	global $PageSize, $wpdb, $OrderCreatedTime, $LastUpdatedTime, $Page, $Action, $xml;
	
	$filters = array(
		'post_status' => 'any',
		'post_type'	=> 'shop_order',
		'posts_per_page' => $PageSize,
		'paged' => $Page+1,
		'orderby' =>'modified',
		'order' => 'DESC'
	);
	
	$loop = new WP_Query( $filters );
	
	while ( $loop->have_posts() ) {
		$loop->the_post();
		$order = new WC_Order($loop->post->ID);
		
		$lastModified = strtotime($loop->post->post_modified_gmt.'UTC');
		$created = strtotime($loop->post->post_date_gmt.'UTC');
		if ($lastModified<$LastUpdatedTime || $created<$OrderCreatedTime ){
			continue;
		}
		$order_xml = $xml->addChild("Order");
		$order_xml->addAttribute("id", $order->id);
		$order_xml->addAttribute("LastUpdated", $loop->post->post_modified_gmt);
		$order_xml->OrderNumber = $order->get_order_number();
		$order_xml->Date = $loop->post->post_date_gmt;
		$order_xml->Type = 'Order';
		$order_xml->Status = $order->status;
		$order_xml->OriginalStatus = $order->status;
		$order_xml->CurrencyCode = $order->get_order_currency();
		$note_string = $order->customer_note;
		$order_notes = $order->get_customer_order_notes();
		if (($order_notes != null) && (is_array($order_notes)) &&(sizeof($order_notes)>0)) {
			
			foreach ($order_notes as $note) {
				$note_string .= "; " . $note->comment_content;
			}
		}
		$order_xml->Notes = $note_string;
		$order_xml->Total = $order->get_total();
		$order_xml->Url = admin_url() . 'post.php?post=' . $order->id . '&action=edit';
		
		// Addresses
		$addresses_xml = $order_xml->addChild("Addresses");
		$billing_address = $addresses_xml->addChild('Address');
		$billing_address->addAttribute('type','Billing');
		$billing_address->FirstName = htmlspecialchars($order->billing_first_name);
		$billing_address->LastName = htmlspecialchars($order->billing_last_name);
		$billing_address->OrganizationName = htmlspecialchars($order->billing_company);			
		$billing_address->Line1 = htmlspecialchars($order->billing_address_1);
		$billing_address->Line2 = htmlspecialchars($order->billing_address_2);
		$billing_address->City = htmlspecialchars($order->billing_city);
		$billing_address->PostCode = $order->billing_postcode;
		$billing_address->State = htmlspecialchars($order->billing_state);
		$billing_address->CountryCode = htmlspecialchars($order->billing_country);
		$shipping_address = $addresses_xml->addChild('Address');
		$shipping_address->addAttribute('type','Shipping');
		$shipping_address->FirstName = htmlspecialchars($order->shipping_first_name);
		$shipping_address->LastName = htmlspecialchars($order->shipping_last_name);
		$shipping_address->OrganizationName = htmlspecialchars($order->shipping_company);		
		$shipping_address->Line1 = htmlspecialchars($order->shipping_address_1);
		$shipping_address->Line2 = htmlspecialchars($order->shipping_address_2);
		$shipping_address->City = htmlspecialchars($order->shipping_city);
		$shipping_address->PostCode = $order->shipping_postcode;
		$shipping_address->State = htmlspecialchars($order->shipping_state);
		$shipping_address->CountryCode = htmlspecialchars($order->shipping_country);
		
		// Contact
		$contact_xml = $order_xml->addChild("Contact");
		if (($order->customer_user != null) && ($order->customer_user != "0")) {
			$contact_xml->addAttribute("id", $order->customer_user);
		}

		// Add contact details
		$contact_xml->FirstName = htmlspecialchars($order->billing_first_name);
		$contact_xml->LastName = htmlspecialchars($order->billing_last_name);
		$contact_xml->Email = $order->billing_email;
		$contact_xml->MobilePhone = $order->billing_phone;
		$contact_xml->OrganizationName = $order->billing_company;
		xml_adopt($contact_xml, $addresses_xml);
		
		$lineItems_xml = $order_xml->addChild("Items");
		
		foreach ($order->get_items() as $lineItem) {
			$lineItem_xml = $lineItems_xml->addChild("Item");
			//$lineItem_xml->Obj = print_r($lineItem,1);
			//$lineItem_xml->MetaObj = print_r($order->get_item_meta($lineItem->order_item_id),1);
			if ($lineItem['variation_id'] != 0 && $lineItem['variation_id'] != NULL && $lineItem['variation_id'] != "") 
			{
				$product_id = $lineItem['variation_id'];
				$lineItem_xml->ProductId = $product_id;
				$product = new WC_Product_Variation($product_id);
				$lineItem_xml->ProductCode = getProductCode($product);
				$lineItem_xml->ProductName = $product->get_title() . '(';
				foreach($product->get_variation_attributes() as $attribute)
				{
					$lineItem_xml->ProductName .= $attribute.',';
				}
				$lineItem_xml->ProductName .= ')';
			} else {
				$product_id = $lineItem['product_id'];
				$lineItem_xml->ProductId = $product_id;
				$product = new WC_Product($product_id);
				$lineItem_xml->ProductCode = getProductCode($product);
				$lineItem_xml->ProductName = $product->get_title();
			}
			$lineItem_xml->Quantity = $lineItem['qty'];
			$lineItem_xml->Price = item_subtotal($lineItem, true, true);
			$lineItem_xml->UnitPriceExTax = item_subtotal($lineItem, false, true);
			$lineItem_xml->Shipping;
			$lineItem_xml->Discount = (0.0 + item_subtotal($lineItem, false, true) - item_total($lineItem, false, true));
			$lineItem_xml->DiscountName;
			$OrderId = $order->id;
			$UnitPriceExTax = item_subtotal($lineItem, false, true);
			$lineitem_tax_amount = $lineItem['line_tax'];	
			$tax_id_query = "SELECT order_item_id FROM " . $wpdb->prefix . "woocommerce_order_items WHERE order_id = '" . $OrderId . "' AND order_item_type = 'tax'";
			$order_item_tax_ids = $wpdb->get_results($tax_id_query);
			if($lineItem['line_tax'] != 0)
			{
			$taxes_xml = $lineItem_xml->addChild("Taxes");
				foreach($order_item_tax_ids as $order_item_tax_id)
				{	
					$TaxName = wc_get_order_item_meta( $order_item_tax_id->order_item_id, "label");
					$TaxRateId = wc_get_order_item_meta( $order_item_tax_id->order_item_id, "rate_id");
					$TaxRate = get_rate_percent($TaxRateId);				
					$line_tax_data = maybe_unserialize($lineItem['line_tax_data']);
					if(isset($line_tax_data['total']))
					{
						foreach($line_tax_data['total'] as $tax_rate_id => $tax)
						{
							if($tax_rate_id == $TaxRateId && $tax !=0)
							{
								$tax_xml = $taxes_xml->addChild("Tax");
								$tax_xml->TaxName = $TaxName;
								$tax_xml->TaxRate = $TaxRate/100;	
								$tax_xml->TaxAmount = $tax;
							}	
						}						
					}
				}
			}
			if($lineItem['line_tax'] == 0)
			{
				foreach($order_item_tax_ids as $order_item_tax_id)
				{
					$TaxRateId = wc_get_order_item_meta( $order_item_tax_id->order_item_id, "rate_id");
					$TaxRate = get_rate_percent($TaxRateId);	
					if( $TaxRate == 0 )
					{
						$taxes_xml = $lineItem_xml->addChild("Taxes");
						$tax_xml = $taxes_xml->addChild("Tax");	
						$tax_xml->TaxName = wc_get_order_item_meta( $order_item_tax_id->order_item_id, "label");	
						$tax_xml->TaxRate = $TaxRate/100;	
						$tax_xml->TaxAmount = 0;
					}						
				}	
			}			
/*				foreach ($tax_rate_names as $tax_rate_name) {
					$tax_xml = $taxes_xml->addChild("Tax");
					foreach ($tax_rates as $tax_rate) {
						$taxName_country = $tax_rate->tax_rate_country;
						$taxName_state = $tax_rate->tax_rate_state;
						$taxName_name = $tax_rate->tax_rate_name;
						if($taxName_state != "")
						{
							$TaxName = $taxName_country ."-". $taxName_state ."-". $taxName_name;
						}
						else
						{
							$TaxName = $taxName_country ."-". $taxName_name;	
						}	
						$length = strlen($TaxName);
						if($TaxName === $tax_rate_name || $TaxName === substr($tax_rate_name, 0, $length))
						{
							$TaxRate = $tax_rate->tax_rate;
							$tax_xml->TaxRate = $TaxRate;
							$tax_xml->TaxName = $tax_rate->tax_rate_name;
						}
					}
					$tax_xml->TaxAmount = $lineitem_tax_amount;
				}
				$tax_query = "SELECT tax_rate_class FROM " . $wpdb->prefix . "woocommerce_tax_rates group by tax_rate_class";
				$tax_classes = $wpdb->get_results($tax_query);
				$tax_helper = new WC_Tax();
				$state_helper = new WC_Countries();
				$states = $state_helper->get_states($CountryCode);
				if($states != FALSE)
				{
					foreach ($states as $key=>$stateName) {
						if($stateName == $order->billing_state)
							{					
								$stateCode = $key;
								break;
							}
					}
				}			
				else
					{
						$stateCode = $order->billing_state;
					}	
				$tax_filter = array(
					'country'   => $order->billing_country,
					'state'     => $stateCode,
					'city'      => $order->billing_city,
					'postcode'  => $order->billing_postcode
				);
				// Try for each tax_class to determine which one was applied
				foreach($tax_classes as $tax_class) {
					
					$tax_filter['tax_class'] = $tax_class->tax_rate_class;
					$tax_rates = $tax_helper->find_rates($tax_filter);
					$calculated_tax_amount = 0.0;
					$last_rate = 0.0;
					$last_label = "";
					foreach($tax_rates as $tax_rate) {
						$last_rate = (0.0+$tax_rate['rate'])/100;
						$last_label = $tax_rate['label'];
						$calculated_tax_amount += (0.0+$lineItem['line_total'])*(0.0+$tax_rate['rate'])/100;
					}
					if (abs($calculated_tax_amount - (0.0 +$lineItem['line_tax']))<=0.01 && sizeof($tax_rates)==1) {
						$tax_xml->TaxName = $tax_class->tax_rate_class . "-" . $last_label;
						$tax_xml->TaxRate = $last_rate;
						break;
					}
				}*/			
			$lineItem_xml->LineTotalIncTax = item_subtotal($lineItem, true, true)*$lineItem['qty'];
		}
		$order_xml->Discounts; //= $order->get_total_discount();
		//$order_xml->DiscountsIncTax = 'false';
		
		$shipping_xml = $order_xml->addChild("Shipping");
		$shipping_xml->ShippingMethod = $order->get_shipping_method();
		$shipping_xml->Amount = 0.0 + $order->get_total_shipping() + $order->get_shipping_tax();	
		if($order->get_shipping_tax() > 0)
		{
		$order_shipping_tax_ids = $wpdb->get_results($tax_id_query);
			$taxes_xml = $shipping_xml->addChild("Taxes");
				foreach($order_shipping_tax_ids as $order_shipping_tax_id)
				{	
					if(wc_get_order_item_meta($order_shipping_tax_id->order_item_id, "shipping_tax_amount") > 0)
					{
						$tax_xml = $taxes_xml->addChild("Tax");
						$tax_xml->TaxName = wc_get_order_item_meta( $order_shipping_tax_id->order_item_id, "label");
						$TaxRateId = wc_get_order_item_meta( $order_shipping_tax_id->order_item_id, "rate_id");
						$TaxRate = get_rate_percent($TaxRateId);
						$tax_xml->TaxRate = $TaxRate/100;
						$tax_xml->TaxAmount = wc_get_order_item_meta($order_shipping_tax_id->order_item_id, "shipping_tax_amount");
					}					
				}
		}
		
		// Other charges
		$othercharges_xml = $order_xml->addChild("OtherCharges");
		foreach($order->get_fees() as $charge) {
			$charge_xml = $othercharges_xml->addChild("Charge");
			$charge_xml->addAttribute("Name", $charge['name']);
			$charge_xml->Amount = 0.0 + $charge['line_total'] + $charge['line_tax'];
			$charge_xml->TaxAmount = $charge['line_tax'];
		}
		
		//Custom Fields
		$order_meta = get_post_custom( $order->id );
		$customfield_xml = $order_xml->addChild("CustomFields");
		foreach($order_meta as $custom_meta => $custom_meta_value){
			if($custom_meta[0] != '_' || $custom_meta == '_delivery_date')
			{
				$custom_meta_value = implode(',',$custom_meta_value);
				$sanitized_value = xml_entities($custom_meta_value);
				$order_customfield_xml = $customfield_xml->AddChild('CustomField', $sanitized_value);
				$order_customfield_xml->AddAttribute('Name',$custom_meta);				
			}
			else
			{
				//Skip the meta_keys that start with _
				continue;
			}			 

		}		
		
		if ($order->status == "completed" || $order->status == "processing" ) {
			if(isset($order->payment_method_title))
			{
			$payments_xml = $order_xml->addChild("Payments");
			$payment_xml = $payments_xml->addChild("PaymentMethod");
			$payment_xml->addAttribute("Name",$order->payment_method_title);
			$payment_xml->ReferenceNumber = $order->get_transaction_id();
			$payment_xml->Amount = $order->get_total();
			
			}
			else{
			//no payment method has been found 
			}
		}
	}
}
?>
