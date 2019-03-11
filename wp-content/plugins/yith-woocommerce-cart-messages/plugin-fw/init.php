<?php
/**
 * Framework Name: YIT Plugin Framework
 * Version: 3.1.23 
 * Author: YITHEMES
 * Text Domain: yith-plugin-fw
 * Domain Path: /languages/
 *
 * @author  Your Inspiration Themes
 * @version 3.1.21
 */
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined ( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( ! function_exists ( 'yit_maybe_plugin_fw_loader' ) ) {
    /**
     * yit_maybe_plugin_fw_loader
     *
     * @since 1.0.0
     */
    function yit_maybe_plugin_fw_loader ( $plugin_path ) {
        global $plugin_fw_data;

        $default_headers = array (
            'Name'       => 'Framework Name',
            'Version'    => 'Version',
            'Author'     => 'Author',
            'TextDomain' => 'Text Domain',
            'DomainPath' => 'Domain Path',
        );

        $framework_data      = get_file_data ( trailingslashit ( $plugin_path ) . 'plugin-fw/init.php', $default_headers );
        $plugin_fw_main_file = trailingslashit ( $plugin_path ) . 'plugin-fw/yit-plugin.php';

        if ( ! empty( $plugin_fw_data ) ) {
            foreach ( $plugin_fw_data as $version => $path ) {
                if ( version_compare ( $version, $framework_data[ 'Version' ], '<' ) ) {
                    $plugin_fw_data = array ( $framework_data[ 'Version' ] => $plugin_fw_main_file );
                }
            }
        } else {
            $plugin_fw_data = array ( $framework_data[ 'Version' ] => $plugin_fw_main_file );
        }
    }
}


