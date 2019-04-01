<?php
// included from \wpml_installer_instance_delegator

include_once untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/includes/class-otgs-installer-wp-share-local-components-setting.php';

if ( version_compare( $delegate['version'], '1.8.12', '>=' ) ) {
	define( 'WP_INSTALLER_VERSION', $delegate['version'] );
}

include_once dirname( __FILE__ ) . '/includes/class-otgs-installer-autoloader.php';

$autoload = new OTGS_Installer_Autoloader();
$autoload->initialize();

WP_Installer();
WP_Installer_Channels();

$installer_loader = new OTGS_Installer_Loader( get_OTGS_Installer_Factory() );
$installer_loader->init();