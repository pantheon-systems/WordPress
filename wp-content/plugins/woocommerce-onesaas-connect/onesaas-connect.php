<?php
/*
 * Plugin Name: OneSaas Connect
 * Description: OneSaas Connect exposes an API that allows OneSaas to integate with WooCommerce.
 * To allow OneSaas to connect to WooCommerce please Activate this plugin then configure it from Settings > OneSaas Connect menu.
 * Version: 2.0.6.43
 * Author: OneSaas
 * Author URI: http://www.onesaas.com/
 * Copyright 2014 OneSaas (www.OneSaas.com, support@onesaas.com)
 */
 global $wpdb;
 
function ppc_install() {
	// Create table osapi_last_modified if it does not exist
	global $wpdb;
		$collate = '';
	if ($wpdb->has_cap('collation')) {
		if(!empty($wpdb->charset))
			$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
		if(!empty($wpdb->collate ) )
			$collate .= " COLLATE $wpdb->collate";
	}
	$sql = "
	CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . "osapi_last_modified (
		object_type ENUM('customer') NOT NULL, 
		id bigint(20) NOT NULL, 
		hash VARCHAR(255) not null, 
		last_modified_before DATETIME NOT NULL, 
		PRIMARY KEY(object_type, id)
	) $collate;";

	$wpdb->query($sql);
}

register_activation_hook(__FILE__,'ppc_install');
function onesaas_connect_menu() {
	add_options_page( 'OneSaas Connect', 'OneSaas Connect', 'manage_options' , __FILE__, 'onesaas_connect_options' );
}

function regenerate_key(){
	$newkey = md5(mt_rand()).md5(mt_rand());

	add_action( 'admin_notices', 'showAdminMessages');

	update_option('wc-onesaas-apikey', $newkey);

	return $newkey;
}

$regenerate = isset($_POST['regenerate']) ? $_POST['regenerate']:"";
if($regenerate)
{
	regenerate_key();
}

function showMessage($message, $errormsg = false){
  	if ($errormsg) {
  		echo '<div id="message" class="error">';
  	} else {
  		echo '<div id="message" class="updated failed">';
  	}
  	echo "<p><strong>$message</strong></p></div>";
}

function showAdminMessages(){
	// Shows as an error message. You could add a link to the right page if you wanted.
	showMessage("ApiKey was successfully updated. Please reconfigure OneSaas with the new ApiKey.", true);
}



function onesaas_connect_options() {
	$key = get_option('wc-onesaas-apikey');
	if(!$key)
	{
		$key = regenerate_key();
	}
	$configkey = base64_encode(json_encode(array('ApiUrl' => site_url(), 'ApiToken' => $key)));
?>
	<div class="wrap">
		<p>Please copy the following Configuration Key into <a href="" title="OneSaas">OneSaas</a> configuration to get connected</p>
      	<textarea cols="200" rows="4" onclick="this.focus();this.select()" readonly><?php echo $configkey ?></textarea>
	</div>
<?php
}//end

add_action ( 'admin_menu', 'onesaas_connect_menu');

function ppcDelete()
{
	// Drop table osapi_last_modified
	global $wpdb;
	$sql = "drop table if exists " . $wpdb->prefix . "osapi_last_modified;";

	$wpdb->query($sql);
}
register_uninstall_hook( __FILE__, 'ppcDelete' ) ;
?>
