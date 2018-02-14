<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // disable direct access
}

/**
 * Check for common issues with the server environment and WordPress install.
 */
class MetaSliderSystemCheck {

    var $options = array();

    /**
     * Constructor
     */
    public function __construct() {
        $this->options = get_site_option( 'metaslider_systemcheck' );
    }

    /**
     * Check the system
     */
    public function check() {
        $this->dismissMessages();
        $this->checkWordPressVersion();
        $this->checkImageLibrary();
        $this->checkRoleScoper();
        // $this->checkWpFooter();
        $this->updateSystemCheck();
    }

    /**
     * Disable a message
     */
    private function dismissMessages() {
        if ( isset( $_REQUEST['dismissMessage'] ) && isset( $_REQUEST['_wpnonce'] ) ) {
            $nonce = $_REQUEST['_wpnonce'];
            $key = $_REQUEST['dismissMessage'];

            if ( wp_verify_nonce( $nonce, "metaslider-dismiss-{$key}" ) ) {
                $this->options[$key] = false;
                update_site_option( 'metaslider_systemcheck', $this->options );
            }
        }
    }

    /**
     * Update our stored messages
     */
    private function updateSystemCheck() {
        update_site_option( 'metaslider_systemcheck', $this->options );
    }

    /**
     * Check the WordPress version.
     */
    private function checkWordPressVersion() {
        if ( isset( $this->options['wordPressVersion'] ) && $this->options['wordPressVersion']  === false ) {
            return;
        }

        if ( !function_exists( 'wp_enqueue_media' ) ) {
            $error = "MetaSlider requires WordPress 3.5 or above. Please upgrade your WordPress installation.";
            $this->printMessage( $error, 'wordPressVersion' );
        } else {
            $this->options['wordPressVersion'] = false;
        }
    }

    /**
     * Check GD or ImageMagick library exists
     */
    private function checkImageLibrary() {
        if ( isset( $this->options['imageLibrary'] ) && $this->options['imageLibrary'] === false ) {
            return;
        }

        if ( ( !extension_loaded( 'gd' ) || !function_exists( 'gd_info' ) ) && ( !extension_loaded( 'imagick' ) || !class_exists( 'Imagick' ) || !class_exists( 'ImagickPixel' ) ) ) {
            $error = "MetaSlider requires the GD or ImageMagick PHP extension. Please contact your hosting provider";
            $this->printMessage( $error, 'imageLibrary' );
        } else {
            $this->options['imageLibrary'] = false;
        }
    }

    /**
     * Detect the role scoper plugin
     */
    private function checkRoleScoper() {
        if ( isset( $this->options['roleScoper'] ) && $this->options['roleScoper'] === false ) {
            return;
        }

        if ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'role-scoper/role-scoper.php' ) ) {

            $access_types = get_option( 'scoper_disabled_access_types' );

            if ( isset( $access_types['front'] ) && !$access_types['front'] ) {
                $error = 'Role Scoper Plugin Detected. Please go to Roles > Options. Click the Realm Tab, scroll down to "Access Types" and uncheck the "Viewing content (front-end)" setting.';
                $this->printMessage( $error, 'roleScoper' );
            }
        }
    }

    /**
     * Check the theme has a call to 'wp_footer'
     */
    private function checkWpFooter() {
        $current_theme = wp_get_theme();
        $theme_name = $current_theme->Template;

        $key = 'wpFooter:' . $theme_name;

        if ( isset( $this->options[$key] ) && $this->options[$key] === false ) {
            return;
        }

        $child_footer = get_stylesheet_directory() . '/footer.php';
        $parent_footer = TEMPLATEPATH . '/footer.php';
        $theme_type = 'parent';

        if ( file_exists( $child_footer ) ) {
            $theme_type = 'child';
            $footer_file = file_get_contents( $child_footer );

            if ( strpos( $footer_file, 'wp_footer()' ) ) {
                return;
            }
        } else if ( file_exists( $parent_footer . '/footer.php' ) ) {
                $theme_type = 'parent';
                $footer_file = file_get_contents( $parent_footer . '/footer.php' );

                if ( strpos( $footer_file, 'wp_footer()' ) ) {
                    return;
                }
            }

        if ( $theme_type == 'parent' ) {
            $file_path = $parent_footer;
        } else {
            $file_path = $child_footer;
        }

        $error = "Required call to wp_footer() not found in file <b>{$file_path}</b>. <br /><br />Please check the <a href='http://codex.wordpress.org/Function_Reference/wp_footer'>wp_footer()</a> documentation and make sure your theme has a call to wp_footer() just above the closing </body> tag.";
        $this->printMessage( $error, $key );
    }

    /**
     * Print a warning message to the screen
     *
     * @param string $message Warning message to be shown
     * @param string $key     Message Key
     */
    private function printMessage( $message, $key ) {
        $nonce = wp_create_nonce( "metaslider-dismiss-{$key}" );
        echo "<div id='message' class='updated'><p><b>Warning:</b> {$message}<br /><br /><a class='button' href='?page=metaslider&dismissMessage={$key}&_wpnonce={$nonce}'>Hide</a></p></div>";
    }
}