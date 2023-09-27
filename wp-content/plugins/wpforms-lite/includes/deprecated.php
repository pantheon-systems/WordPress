<?php
// phpcs:ignoreFile
/**
 * To be compatible with both WP 4.9 (that can run on PHP 5.2+) and WP 5.3+ (PHP 5.6+)
 * we need to rewrite some core WP classes and tweak our own skins to not use PHP 5.6 splat operator (...$args)
 * that were introduced in WP 5.3 in \WP_Upgrader_Skin::feedback().
 * This alias is a safeguard to those developers who decided to use our internal class WPForms_Install_Silent_Skin,
 * which we deleted.
 *
 * @since 1.5.6.1
 */
class_alias( 'WPForms\Helpers\PluginSilentUpgraderSkin', 'WPForms_Install_Silent_Skin' );

/**
 * Legacy `WPForms_Addons` class was refactored and moved to the new `WPForms\Pro\Admin\Pages\Addons` class.
 * This alias is a safeguard to those developers who use our internal class WPForms_Addons,
 * which we deleted.
 *
 * @since 1.6.7
 */
class_alias( wpforms()->is_pro() ? 'WPForms\Pro\Admin\Pages\Addons' : 'WPForms\Lite\Admin\Pages\Addons', 'WPForms_Addons' );

/**
 * This alias is a safeguard to those developers who decided to use our internal class WPForms_Smart_Tags,
 * which we deleted.
 *
 * @since 1.6.7
 */
class_alias( wpforms()->is_pro() ? 'WPForms\Pro\SmartTags\SmartTags' : 'WPForms\SmartTags\SmartTags', 'WPForms_Smart_Tags' );

/**
 * This alias is a safeguard to those developers who decided to use our internal class \WPForms\Providers\Loader,
 * which we deleted.
 *
 * @since 1.7.3
 */
class_alias( '\WPForms\Providers\Providers', '\WPForms\Providers\Loader' );

/**
 * Legacy `\WPForms\Admin\Notifications` class was refactored and moved to the new `\WPForms\Admin\Notifications\Notifications` class.
 * This alias is a safeguard to those developers who use our internal class \WPForms\Admin\Notifications,
 * which we deleted.
 *
 * @since 1.7.5
 */
class_alias( '\WPForms\Admin\Notifications\Notifications', '\WPForms\Admin\Notifications' );

/**
 * Legacy `\WPForms\Migrations` class was refactored and moved to the new `\WPForms\Migrations\Migrations` class.
 * This alias is a safeguard to those developers who use our internal class \WPForms\Migrations, which we deleted.
 *
 * @since 1.7.5
 */
class_alias( '\WPForms\Migrations\Migrations', '\WPForms\Migrations' );

if ( wpforms()->is_pro() ) {
	/**
	 * Legacy `\WPForms\Pro\Migrations` class was refactored and moved to the new `\WPForms\Pro\Migrations\Migrations` class.
	 * This alias is a safeguard to those developers who use our internal class \WPForms\Migrations, which we deleted.
	 *
	 * @since 1.7.5
	 */
	class_alias( '\WPForms\Pro\Migrations\Migrations', '\WPForms\Pro\Migrations' );
}

/**
 * Get notification state, whether it's opened or closed.
 *
 * @deprecated 1.4.8
 *
 * @since 1.4.1
 *
 * @param int $form_id         Form ID.
 * @param int $notification_id Notification ID.
 *
 * @return string
 */
function wpforms_builder_notification_get_state( $form_id, $notification_id ) {

	_deprecated_function( __FUNCTION__, '1.4.8 of the WPForms addon', 'wpforms_builder_settings_block_get_state()' );

	return wpforms_builder_settings_block_get_state( $form_id, $notification_id, 'notification' );
}

/**
 * Convert bytes to megabytes (or in some cases KB).
 *
 * @deprecated 1.6.2
 *
 * @since 1.0.0
 *
 * @param int $bytes Bytes to convert to a readable format.
 *
 * @return string
 */
function wpforms_size_to_megabytes( $bytes ) {

	_deprecated_function( __FUNCTION__, '1.6.2 of the WPForms plugin', 'size_format()' );

	return size_format( $bytes );
}
