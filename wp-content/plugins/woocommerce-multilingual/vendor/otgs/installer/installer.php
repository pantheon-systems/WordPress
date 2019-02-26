<?php
// included from \wpml_installer_instance_delegator

include_once untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/includes/class-otgs-installer-wp-share-local-components-setting.php';

if ( version_compare( $delegate['version'], '1.8.12', '>=' ) ) {
	define( 'WP_INSTALLER_VERSION', $delegate['version'] );
}

$plugin_path = dirname( __FILE__ );

include_once $plugin_path . '/includes/functions-core.php';
include_once $plugin_path . '/includes/class-otgs-installer-subscription.php';
include_once $plugin_path . '/includes/class-wp-installer.php';

include_once WP_Installer()->plugin_path() . '/includes/class-wp-installer-api.php';
include_once WP_Installer()->plugin_path() . '/includes/class-translation-service-info.php';
include_once WP_Installer()->plugin_path() . '/includes/class-installer-dependencies.php';
include_once WP_Installer()->plugin_path() . '/includes/class-wp-installer-channels.php';

include_once WP_Installer()->plugin_path() . '/includes/class-otgs-installer-php-functions.php';

include_once WP_Installer()->plugin_path() . '/includes/class-otgs-installer-wp-components-sender.php';
include_once WP_Installer()->plugin_path() . '/includes/class-otgs-installer-wp-components-storage.php';
include_once WP_Installer()->plugin_path() . '/includes/class-otgs-installer-wp-components-hooks.php';

include_once WP_Installer()->plugin_path() . '/templates/template-service/interface-iotgs-installer-template-service.php';
include_once WP_Installer()->plugin_path() . '/templates/template-service/class-otgs-installer-twig-template-service.php';
include_once WP_Installer()->plugin_path() . '/templates/template-service/class-otgs-installer-twig-template-service-loader.php';

include_once WP_Installer()->plugin_path() . '/includes/class-otgs-installer-wp-components-setting-resources.php';
include_once WP_Installer()->plugin_path() . '/includes/class-otgs-installer-plugins-page-notice.php';
include_once WP_Installer()->plugin_path() . '/includes/class-otgs-installer-wp-components-setting-ajax.php';
include_once WP_Installer()->plugin_path() . '/includes/class-otgs-installer-filename-hooks.php';
include_once WP_Installer()->plugin_path() . '/includes/class-otgs-installer-icons.php';
include_once WP_Installer()->plugin_path() . '/includes/class-otgs-installer-wp-share-local-components-setting-hooks.php';
include_once WP_Installer()->plugin_path() . '/includes/class-otgs-installer-factory.php';

include_once WP_Installer()->plugin_path() . '/includes/functions-templates.php';
include_once WP_Installer()->plugin_path() . '/includes/class-otgs-twig-autoloader.php';

// Initialization
WP_Installer();
WP_Installer_Channels();

$installer_factory = get_OTGS_Installer_Factory();

$installer_factory->create_resources()
				  ->add_hooks();
$installer_factory->create_settings_hooks()
				  ->add_hooks();
$installer_factory->create_wp_components_hooks()
				  ->add_hooks();
$installer_factory->create_local_components_ajax_setting()
				  ->add_hooks();
$installer_factory->create_filename_hooks()
				  ->add_hooks();
$installer_factory->create_icons()
				  ->add_hooks();

