<?php
/**
 * Check if current requirements are met
 *
 * @package RocketLazyloadPlugin
 */

defined('ABSPATH') || die('Cheatin&#8217; uh?');

/**
 * Class to check if the current WordPress and PHP versions meet our requirements
 *
 * @since 2.0
 * @author Remy Perona
 */
class Rocket_Lazyload_Requirements_Check
{
    /**
     * Plugin Name
     *
     * @var string
     */
    private $plugin_name;

    /**
     * Plugin version
     *
     * @var string
     */
    private $plugin_version;

    /**
     * Required WordPress version
     *
     * @var string
     */
    private $wp_version;

    /**
     * Required PHP version
     *
     * @var string
     */
    private $php_version;

    /**
     * Constructor
     *
     * @since 3.0
     * @author Remy Perona
     *
     * @param array $args {
     *     Arguments to populate the class properties.
     *
     *     @type string $plugin_name Plugin name.
     *     @type string $wp_version  Required WordPress version.
     *     @type string $php_version Required PHP version.
     * }
     */
    public function __construct($args)
    {
        foreach (array('plugin_name', 'plugin_version', 'wp_version', 'php_version') as $setting) {
            if (isset($args[ $setting ])) {
                $this->$setting = $args[ $setting ];
            }
        }
    }

    /**
     * Checks if all requirements are ok, if not, display a notice and the rollback
     *
     * @since 3.0
     * @author Remy Perona
     *
     * @return bool
     */
    public function check()
    {
        if (! $this->phpPasses() || ! $this->wpPasses()) {
            add_action('admin_notices', array($this, 'notice'));

            return false;
        }

        return true;
    }

    /**
     * Checks if the current PHP version is equal or superior to the required PHP version
     *
     * @since 3.0
     * @author Remy Perona
     *
     * @return bool
     */
    private function phpPasses()
    {
        return version_compare(PHP_VERSION, $this->php_version) >= 0;
    }

    /**
     * Checks if the current WordPress version is equal or superior to the required PHP version
     *
     * @since 3.0
     * @author Remy Perona
     *
     * @return bool
     */
    private function wpPasses()
    {
        global $wp_version;

        return version_compare($wp_version, $this->wp_version) >= 0;
    }

    /**
     * Displays a notice if requirements are not met.
     *
     * @since 2.0
     * @author Remy Perona
     */
    public function notice()
    {
        if (! current_user_can('manage_options')) {
            return;
        }

        // Translators: %1$s = Plugin name, %2$s = Plugin version.
        $message = '<p>' . sprintf(__('To function properly, %1$s %2$s requires at least:', 'rocket-lazy-load'), $this->plugin_name, $this->plugin_version) . '</p><ul>';

        if (! $this->phpPasses()) {
            // Translators: %1$s = PHP version required.
            $message .= '<li>' . sprintf(__('PHP %1$s. To use this %2$s version, please ask your web host how to upgrade your server to PHP %1$s or higher.', 'rocket-lazy-load'), $this->php_version, $this->plugin_name) . '</li>';
        }

        if (! $this->wpPasses()) {
            // Translators: %1$s = WordPress version required.
            $message .= '<li>' . sprintf(__('WordPress %1$s. To use this %2$s version, please upgrade WordPress to version %1$s or higher.', 'rocket-lazy-load'), $this->wp_version, $this->plugin_name) . '</li>';
        }

        echo '<div class="notice notice-error">' . $message . '</div>';
    }
}
