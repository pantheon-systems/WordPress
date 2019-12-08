<?php

/**
 * Class to control the registration and handling of all settings values.
 */
class Purgely_Settings
{

    const FASTLY_CONFIGURATION_LIST_GENERAL = 'general';
    const FASTLY_CONFIGURATION_LIST_ADVANCED = 'advanced';
    const FASTLY_CONFIGURATION_LIST_WEBHOOKS = 'webhooks';

    static $lists = array(
        self::FASTLY_CONFIGURATION_LIST_GENERAL =>  'fastly-settings-general',
        self::FASTLY_CONFIGURATION_LIST_ADVANCED => 'fastly-settings-advanced',
        self::FASTLY_CONFIGURATION_LIST_ADVANCED => 'fastly-settings-io',
        self::FASTLY_CONFIGURATION_LIST_WEBHOOKS => 'fastly-settings-webhooks'
    );

    /**
     * Possible pixel ratio sizes
     */
    const POSSIBLE_PIXEL_RATIOS = array('1x', '1.5x', '2x', '3x', '3.5x', '4x');

    /**
     * The settings values for the plugin.
     *
     * @var array Holds all of the individual settings for the plugin.
     */
    public static $settings = array();

    /**
     * Get the valid settings for the plugin.
     *
     * @return array The valid settings including default values and sanitize callback.
     */
    public static function get_registered_settings()
    {
        return array(
            'fastly_api_key' => array(
                'sanitize_callback' => 'purgely_sanitize_key',
                'default' => PURGELY_FASTLY_KEY,
            ),
            'fastly_service_id' => array(
                'sanitize_callback' => 'purgely_sanitize_key',
                'default' => PURGELY_FASTLY_SERVICE_ID,
            ),
            'allow_purge_all' => array(
                'sanitize_callback' => 'purgely_sanitize_checkbox',
                'default' => PURGELY_ALLOW_PURGE_ALL,
            ),
            'fastly_log_purges' => array(
                'sanitize_callback' => 'purgely_sanitize_checkbox',
                'default' => PURGELY_FASTLY_LOG_PURGES,
            ),
            'fastly_vcl_version' => array(
                'sanitize_callback' => 'purgely_sanitize_checkbox',
                'default' => PURGELY_FASTLY_VCL_VERSION,
            ),
            'fastly_debug_mode' => array(
                'sanitize_callback' => 'purgely_sanitize_checkbox',
                'default' => PURGELY_FASTLY_DEBUG_MODE,
            ),
            'fastly_api_hostname' => array(
                'sanitize_callback' => 'esc_url',
                'default' => PURGELY_API_ENDPOINT,
            ),
            'enable_stale_while_revalidate' => array(
                'sanitize_callback' => 'purgely_sanitize_checkbox',
                'default' => PURGELY_ENABLE_STALE_WHILE_REVALIDATE,
            ),
            'stale_while_revalidate_ttl' => array(
                'sanitize_callback' => 'absint',
                'default' => PURGELY_STALE_WHILE_REVALIDATE_TTL,
            ),
            'enable_stale_if_error' => array(
                'sanitize_callback' => 'purgely_sanitize_checkbox',
                'default' => PURGELY_ENABLE_STALE_IF_ERROR,
            ),
            'stale_if_error_ttl' => array(
                'sanitize_callback' => 'absint',
                'default' => PURGELY_STALE_IF_ERROR_TTL,
            ),
            'use_fastly_cache_tags' => array(
                'sanitize_callback' => 'purgely_sanitize_checkbox',
                'default' => PURGELY_USE_FASTLY_CACHE_TAGS,
            ),
            'use_fastly_cache_tags_for_custom_post_type' => array(
                'sanitize_callback' => 'purgely_sanitize_checkbox',
                'default' => PURGELY_USE_FASTLY_CACHE_TAGS_FOR_CUSTOM_POST_TYPE,
            ),
            'always_purged_keys' => array(
                'sanitize_callback' => 'purgely_sanitize_keys',
                'default' => PURGELY_ALWAYS_PURGED_KEYS,
            ),
            'surrogate_control_ttl' => array(
                'sanitize_callback' => 'absint',
                'default' => PURGELY_SURROGATE_CONTROL_TTL,
            ),
            'cache_control_ttl' => array(
                'sanitize_callback' => 'absint',
                'default' => PURGELY_CACHE_CONTROL_TTL,
            ),
            'default_purge_type' => array(
                'sanitize_callback' => 'sanitize_key',
                'default' => PURGELY_DEFAULT_PURGE_TYPE,
            ),
            'sitecode' => array(
                'sanitize_callback' => 'sanitize_key',
                'default' => FASTLY_SITECODE,
            ),
            'custom_ttl_templates' => array(
                'sanitize_callback' => 'purgely_sanitize_ttl_templates',
                'default' => array(),
            ),
            'io_adaptive_pixel_ratios' => array(
                'sanitize_callback' => 'purgely_sanitize_checkbox',
                'default' => PURGELY_USE_FASTLY_IO_ADAPTIVE_PIXELS,
            ),
            'io_enable_wp' => array(
                'sanitize_callback' => 'purgely_sanitize_checkbox',
                'default' => PURGELY_USE_FASTLY_IO_WORDPRESS,
            ),
            'io_adaptive_pixel_ratios_content' => array(
                'sanitize_callback' => 'purgely_sanitize_checkbox',
                'default' => PURGELY_USE_FASTLY_IO_ADAPTIVE_PIXELS_CONTENT,
            ),
            'io_adaptive_pixel_ratio_sizes' => array(
                'sanitize_callback' => 'purgely_sanitize_pixel_ratios',
                'default' => PURGELY_FASTLY_IO_ADAPTIVE_PIXEL_SIZES,
            ),
            'webhooks_url_endpoint' => array(
                'sanitize_callback' => 'esc_url',
                'default' => PURGELY_WEBHOOKS_URL_ENDPOINT,
            ),
            'webhooks_username' => array(
                'sanitize_callback' => 'sanitize_key',
                'default' => PURGELY_WEBHOOKS_USERNAME,
            ),
            'webhooks_channel' => array(
                'sanitize_callback' => 'sanitize_key',
                'default' => PURGELY_WEBHOOKS_CHANNEL,
            ),
            'webhooks_activate' => array(
                'sanitize_callback' => 'purgely_sanitize_checkbox',
                'default' => PURGELY_WEBHOOKS_ACTIVATE,
            ),
        );
    }

    /**
     * Get an array of settings values.
     *
     * This method negotiates the database values and the constant values to determine what the current value should be.
     * The database value takes precedence over the constant value.
     *
     * @return array The current settings values.
     */
    public static function get_settings()
    {
        $negotiated_settings = self::$settings;

        if (empty($negotiated_settings)) {
            $registered_settings = self::get_registered_settings();
            $saved_settings = get_option('fastly-settings-general', array());
            $saved_settings = array_merge($saved_settings, get_option('fastly-settings-advanced', array()));
            $saved_settings_io = get_option('fastly-settings-io', array());
            if($saved_settings_io && is_array($saved_settings_io)) {
                $saved_settings = array_merge($saved_settings, $saved_settings_io);
            }
            $saved_settings = array_merge($saved_settings, get_option('fastly-settings-webhooks', array()));
            $negotiated_settings = array();

            foreach ($registered_settings as $key => $values) {
                $value = '';

                if (isset($saved_settings[$key])) {
                    $value = $saved_settings[$key];
                } else if (isset($values['default'])) {
                    $value = $values['default'];
                }

                if (isset($values['sanitize_callback'])) {
                    $value = call_user_func($values['sanitize_callback'], $value);
                }

                $negotiated_settings[$key] = $value;
            }

            self::set_settings($negotiated_settings);
        }

        return $negotiated_settings;
    }

    /**
     * Get an array of settings section strictly from database.
     *
     * @param $section
     * @return array
     */
    public static function get_database_section_settings($section)
    {
        return get_option($section, array());
    }

    /**
     * Get the value of an individual setting.
     *
     * @param  string $setting The setting name.
     * @return mixed           The setting value.
     */
    public static function get_setting($setting)
    {
        $value = '';

        $negotiated_settings = self::get_settings();
        $registered_settings = self::get_registered_settings();

        if (isset($negotiated_settings[$setting])) {
            $value = $negotiated_settings[$setting];
        } elseif (isset($registered_settings[$setting]['default'])) {
            $value = $registered_settings[$setting]['default'];
        }

        return $value;
    }

    /**
     * Set the settings values.
     *
     * @param  array $settings The current settings values.
     * @return void
     */
    public static function set_settings($settings)
    {
        self::$settings = $settings;
    }
}
