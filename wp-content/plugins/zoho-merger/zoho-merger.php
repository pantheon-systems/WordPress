<?php
/**
 * Plugin Name: Zoho WooCommerce Merger for Elixinol
 * Description: Merges contacts, leads, and accounts to Zoho
 * Version: 1.0
 * Author: Unknown
 * License: GPL2
 */

//Custom cron jobs
add_action( 'zoho_merge_contact', 'merge_contact' );
function merge_contact() {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,"https://elixinol.com/zoho/merge-contacts.php");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$server_output = curl_exec ($ch);
	curl_close ($ch);
}

add_action( 'zoho_merge_lead', 'merge_lead' );
function merge_lead() {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,"https://elixinol.com/zoho/merge-leads.php");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$server_output = curl_exec ($ch);
	curl_close ($ch);
}

add_action( 'zoho_merge_account', 'merge_account' );
function merge_account() {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,"https://elixinol.com/zoho/merge-accounts.php");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$server_output = curl_exec ($ch);
	curl_close ($ch);
}
