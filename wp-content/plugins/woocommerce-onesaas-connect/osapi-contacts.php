<?php
/*
  osapi-contacts.php
  OneSaas Connect API 2.0.6.43 for WooCommerce v2.0.20
  http://www.onesaas.com
  Copyright (c) 2014 oneSaas
*/

function addContacts() {
	global $PageSize, $wpdb, $OrderCreatedTime, $LastUpdatedTime, $Page, $Action, $xml, $wpdb;

	$user_id_query = "SELECT id, user_registered FROM ". $wpdb->prefix . "users LIMIT " . $Page*$PageSize . ", " . $PageSize; 
	$user_id_array = array();
	$results = $wpdb->get_results($user_id_query);
	foreach($results as $row){
		if (strtotime($row->user_registered.'UTC') >= $LastUpdatedTime)
		{
			$user_id_array[] = $row->id;			
		}
	}
	
	if (sizeof($user_id_array)>0) {
		$filters = array('include' => $user_id_array);
		
		$user_query = new WP_User_Query( $filters );

		if ( ! empty( $user_query->results ) ) {
			foreach ( $user_query->results as $user ) {
				$contact = $xml->AddChild('Contact');
				$contact->addAttribute('id', $user->ID);
				$contact->addAttribute('LastUpdated', $user_last_modified_before_array[$user->ID]);
				$contact->FirstName = htmlspecialchars($user->get('first_name'));
				$contact->LastName = htmlspecialchars($user->get('last_name'));
				$contact->MobilePhone = $user->get('billing_phone');
				$contact->Email = $user->get('user_email');
				$contact->OrganizationName = htmlspecialchars($user->get('billing_company'));
				$contact->Url = admin_url() . 'user-edit.php?user_id=' . $user->ID;
				$addresses = $contact->addChild('Addresses');
				$billing_address = $addresses->addChild('Address');
				$billing_address->addAttribute('type','Billing');
				$billing_address->Line1 = htmlspecialchars($user->get('billing_address_1'));
				$billing_address->Line2 = htmlspecialchars($user->get('billing_address_2'));
				$billing_address->City = htmlspecialchars($user->get('billing_city'));
				$billing_address->PostCode = $user->get('billing_postcode');
				$billing_address->State = htmlspecialchars($user->get('billing_state'));
				$billing_address->CountryCode = htmlspecialchars($user->get('billing_country'));
				$shipping_address = $addresses->addChild('Address');
				$shipping_address->addAttribute('type','Shipping');
				$shipping_address->Line1 = htmlspecialchars($user->get('shipping_address_1'));
				$shipping_address->Line2 = htmlspecialchars($user->get('shipping_address_2'));
				$shipping_address->City = htmlspecialchars($user->get('shipping_city'));
				$shipping_address->PostCode = $user->get('shipping_postcode');
				$shipping_address->State = htmlspecialchars($user->get('shipping_state'));
				$shipping_address->CountryCode = htmlspecialchars($user->get('shipping_country'));
			}
		}
	}
}
?>
