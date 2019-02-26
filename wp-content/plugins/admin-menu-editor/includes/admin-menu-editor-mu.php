<?php
/*
Plugin Name: Admin Menu Editor [Multisite module]
Plugin URI: http://adminmenueditor.com/
Description: Lets you edit the WordPress admin menu. To access the editor, go to the Dashboard of one of your network sites and open the Settings -&gt; Menu Editor page.
Author: Janis Elsts
Author URI: http://w-shadow.com/
*/

/**
To install Admin Menu Editor as a global plugin in WPMU :
	1) Place the "admin-menu-editor" directory into your "mu-plugins" directory.
	2) Move this file, admin-menu-editor-mu.php, from the "admin-menu-editor" directory
	   to your "mu-plugins" directory.
	   
The resulting directory structure should look like this : 

mu-plugins/
	admin-menu-editor-mu.php
	admin-menu-editor/ 
		menu-editor.php
		menu-editor-core.php
		...and other Admin Menu Editor files

**/

//Load the plugin
$ws_menu_editor_filename = dirname(__FILE__) . '/admin-menu-editor/menu-editor.php';
$ws_menu_editor_pro_filename = dirname(__FILE__) . '/admin-menu-editor-pro/menu-editor.php';
if ( file_exists($ws_menu_editor_filename) ) {
	require $ws_menu_editor_filename;
} elseif ( file_exists($ws_menu_editor_pro_filename) ) {
	require $ws_menu_editor_pro_filename;
} else {
	add_action('admin_notices', 'ws_ame_installation_error');
}

function ws_ame_installation_error(){
	if ( !is_super_admin() ) return;
?>
<div class="error fade"><p>
		<strong>Admin Menu Editor is installed incorrectly!</strong>
		</p>
		<p>
		Please copy the entire <code>admin-menu-editor</code> directory to your <code>mu-plugins</code> 
		directory, then move only the admin-menu-editor-mu.php file from
		<code>admin-menu-editor/includes</code> to <code>mu-plugins</code>.
		</p> 
</div>
<?php
}

//Add the license management link(s) to our must-use module.
function ws_ame_add_mu_license_link($actions) {
	global $ameLicensingUi;
	if ( isset($ameLicensingUi) && is_callable(array($ameLicensingUi, 'addLicenseActionLink')) ) {
		$actions = $ameLicensingUi->addLicenseActionLink($actions);
	}
	return $actions;
}
add_filter('network_admin_plugin_action_links_' . basename(__FILE__), 'ws_ame_add_mu_license_link');
