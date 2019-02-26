<?php
/**
 * WooCommerce Plugin Framework
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the plugin to newer
 * versions in the future. If you wish to customize the plugin for your
 * needs please refer to http://www.skyverge.com
 *
 * @package   SkyVerge/WooCommerce/Plugin/Classes
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2018, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

if ( ! class_exists( 'SV_WC_Hook_Deprecator' ) ) :

/**
 * SkyVerge Hook Deprecator Class
 *
 * This class handles triggering PHP notices for deprecated and removed hooks
 *
 * @since 4.3.0
 */
class SV_WC_Hook_Deprecator {


	/** @var string plugin name */
	protected $plugin_name;

	/** @var array deprecated/removed hooks */
	protected $hooks;


	/**
	 * Setup class
	 *
	 * @param string $plugin_name
	 * @param array $hooks
	 */
	public function __construct( $plugin_name, $hooks ) {

		$this->plugin_name = $plugin_name;
		$this->hooks       = array_map( array( $this, 'set_hook_defaults' ), $hooks );

		$this->map_deprecated_hooks();

		add_action( 'shutdown', array( $this, 'trigger_deprecated_errors' ), 999 );
	}


	/**
	 * Sets the deprecated hook defaults.
	 *
	 * @since 4.5.0
	 * @param array $hook_params the hook parameters
	 * @return array
	 */
	protected function set_hook_defaults( $hook_params ) {

		$defaults = array(
			'removed'     => false,
			'map'         => false,
			'replacement' => '',
		);

		return wp_parse_args( $hook_params, $defaults );
	}


	/**
	 * Map each deprecated hook to its replacement.
	 *
	 * @since 4.5.0
	 */
	protected function map_deprecated_hooks() {

		foreach ( $this->hooks as $old_hook => $hook ) {

			if ( ! empty( $hook['replacement'] ) && $hook['removed'] && $hook['map'] ) {
				add_filter( $hook['replacement'], array( $this, 'map_deprecated_hook' ), 10, 10 );
			}
		}
	}


	/**
	 * Map a deprecated/renamed hook to a new one.
	 *
	 * This method works by hooking into the new, renamed version of the action/filter
	 * and checking if any actions/filters are hooked into the old hook. It then runs
	 * these and applies the data modifications in the new hook.
	 *
	 * @since 4.5.0
	 * @return mixed
	 */
	public function map_deprecated_hook() {

		$args     = func_get_args();
		$data     = $args[0];
		$new_hook = current_filter();

		$new_hooks = wp_list_pluck( $this->hooks, 'replacement' );

		// check if there is a matching old hook for the current hook
		if ( $old_hook = array_search( $new_hook, $new_hooks ) ) {

			// check if there are any hooks added to the old hook
			if ( has_filter( $old_hook ) ) {

				// prepend old hook name to the args
				array_unshift( $args, $old_hook );

				// apply the hooks attached to the old hook to $data
				$data = call_user_func_array( 'apply_filters', $args );
			}
		}

		return $data;
	}


	/**
	 * Trigger a notice when other actors have attached callbacks to hooks that
	 * are either deprecated or removed. This only runs when WP_DEBUG is on.
	 *
	 * @since 4.3.0
	 */
	public function trigger_deprecated_errors() {
		global $wp_filter;

		// follow WP core behavior for showing deprecated notices and only do so when WP_DEBUG is on
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG && apply_filters( 'sv_wc_plugin_framework_show_deprecated_hook_notices', true ) ) {

			// sanity check
			if ( ! is_array( $wp_filter ) || empty( $wp_filter ) ) {
				return;
			}

			foreach ( $this->hooks as $old_hook_tag => $hook ) {

				// if other actors have attached a callback to the deprecated/removed hook...
				if ( isset( $wp_filter[ $old_hook_tag ] ) ) {

					$this->trigger_error( $old_hook_tag, $hook );
				}
			}
		}
	}


	/**
	 * Trigger the deprecated/removed notice
	 *
	 * @since 4.3.0
	 * @param string $old_hook_name deprecated/removed hook name
	 * @param array $hook {
	 *   @type string $version version the hook was deprecated/removed in
	 *   @type bool $removed if present and true, the message will indicate the hook was removed instead of deprecated
	 *   @type string|bool $replacement if present and a string, the message will indicate the replacement hook to use,
	 *     otherwise (if bool and false) the message will indicate there is no replacement available.
	 * }
	 */
	protected function trigger_error( $old_hook_name, $hook ) {

		// e.g. WooCommerce Memberships: "wc_memberships_some_hook" was deprecated in version 1.2.3.
		$message = sprintf( '%1$s: action/filter "%2$s" was %3$s in version %4$s. ',
			$this->plugin_name,
			$old_hook_name,
			$hook['removed'] ? 'removed' : 'deprecated',
			$hook['version']
		);

		// e.g. Use "wc_memberships_some_new_hook" instead.
		$message .= ! empty( $hook['replacement'] ) ? sprintf( 'Use %1$s instead.', $hook['replacement'] ) : 'There is no replacement available.';

		// triggers as E_USER_NOTICE
		SV_WC_Helper::trigger_error( $message );
	}


}


endif; // class exists check
