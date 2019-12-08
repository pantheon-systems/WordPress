<?php

/**
 * Class Upgrades
 *
 * Contains new schema upgrades that run automatically on version update, and some manual upgrade functions
 */
class Upgrades
{

    const WORDPRESS_MODULE_NAME = 'wordpressplugin';

    protected $_main_instance;

    /**
     * Upgrades constructor.
     *
     * Sets up main settings object for paths and version
     *
     * @param $object
     */
    public function __construct($object)
    {
        $this->_main_instance = $object;
    }


    /**
     * Check version and run new upgrades if there are any
     */
    public function check_and_run_upgrades()
    {
        // Upgrade to 1.1.1
        if (version_compare($this->_main_instance->current_version, '1.1.1', '<')) {
            $this->upgrade1_1_1();
        }
    }

    /**
     * Upgrades to 1.1.1 version
     *
     * @return void
     */
    protected function upgrade1_1_1()
    {
        // Convert old fastly credentials to new storing type
        $data_general = array();
        $data_advanced = array();
        $data_general['fastly_api_hostname'] = get_option('fastly_api_hostname', false);
        $data_general['fastly_api_key'] = get_option('fastly_api_key', false);
        $data_general['fastly_service_id'] = get_option('fastly_service_id', false);
        $data_advanced['fastly_log_purges'] = get_option('fastly_log_purges', false);

        foreach ($data_general as $k => $single) {
            if ($single === false || empty($single)) {
                unset($data_general[$k]);
            }
        }

        foreach ($data_advanced as $k => $single) {
            if ($single === false || empty($single)) {
                unset($data_advanced[$k]);
            }
        }

        // Update data
        update_option('fastly-settings-general', $data_general);
        update_option('fastly-settings-advanced', $data_advanced);

        // Update version
        update_option("fastly-schema-version", '1.1.1');
    }

    /**
     * Manual update of vcl, conditions and settings to 1.1.1 version
     * @param bool
     * @return bool|array
     */
    public function vcl_upgrade_1_1_1($activate)
    {
        // Update VCL
        $vcl_dir = $this->_main_instance->vcl_dir;
        $data = array(
            'vcl' => array(
                array(
                    'vcl_dir' => $vcl_dir,
                    'type' => 'recv'
                ),
                array(
                    'vcl_dir' => $vcl_dir,
                    'type' => 'deliver',
                ),
                array(
                    'vcl_dir' => $vcl_dir,
                    'type' => 'error',
                ),
                array(
                    'vcl_dir' => $vcl_dir,
                    'type' => 'fetch',
                )
            ),
            'condition' => array(
                array(
                    'name' => self::WORDPRESS_MODULE_NAME . '_request1',
                    'statement' => 'req.http.x-pass',
                    'type' => 'REQUEST',
                    'priority' => 90
                )
            ),
            'setting' => array(
                array(
                    'name' => self::WORDPRESS_MODULE_NAME . '_setting1',
                    'action' => 'pass',
                    'request_condition' => 'wordpressplugin_request1'
                )
            )
        );

        $errors = array();

        $vcl = new Vcl_Handler($data);
        if (!$vcl->execute($activate)) {
            //Log if enabled
            if (Purgely_Settings::get_setting('fastly_debug_mode')) {
                foreach ($vcl->get_errors() as $error) {
                    error_log($error);
                }
            }

            $errors = array_merge($errors, $vcl->get_errors());
        }

        if (!empty($errors)) {
            return $errors;
        }

        return true;
    }

    /**
     * Update of maintenance/error page HTML
     * @param string
     * @param bool
     * @return bool|array
     */
    public function maintenance_html_update($html, $activate)
    {
        // Update HTML VCL snippets
        $vcl_dir = $this->_main_instance->vcl_dir;
        $data = array(
            'vcl' => array(
                array(
                    'vcl_dir' => $vcl_dir,
                    'subdirectory' => 'error_page',
                    'type' => 'deliver',
                ),
            ),
            'condition' => array(
                array(
                    'name' => self::WORDPRESS_MODULE_NAME . '_error_page_condition',
                    'statement' => 'req.http.ResponseObject == "WORDPRESS_ERROR_PAGE"',
                    'type' => 'REQUEST',
                    'priority' => 90,
                )
            ),
            'response' => array(
                array(
                    'name' => self::WORDPRESS_MODULE_NAME . '_error_page_response_object',
                    'request_condition' => self::WORDPRESS_MODULE_NAME . '_error_page_condition',
                    'content' => $html,
                    'status' => '503',
                    'response' => 'Service Temporarily Unavailable'
                )
            )
        );

        $errors = array();

        $vcl = new Vcl_Handler($data);
        if (!$vcl->execute($activate)) {
            //Log if enabled
            if (Purgely_Settings::get_setting('fastly_debug_mode')) {
                foreach ($vcl->get_errors() as $error) {
                    error_log($error);
                }
            }

            $errors = array_merge($errors, $vcl->get_errors());
        }

        if (!empty($errors)) {
            return $errors;
        }

        return true;
    }


    /**
     * Enable image optimization
     * @param string
     * @param bool
     * @return bool|array
     */
    public function image_optimization_toggle($activate)
    {
        // Update HTML VCL snippets
        $data = array(
            'condition' => array(
                array(
                    'name' => self::WORDPRESS_MODULE_NAME . '_image_optimization',
                    'statement' => 'req.url.ext ~ "(?i)^(gif|png|jpe?g|webp)$"',
                    'type' => 'REQUEST',
                    'priority' => 10,
                )
            ),
            'header' => array(
                array(
                    'name' => self::WORDPRESS_MODULE_NAME . '_image_optimization',
                    'type' => 'request',
                    'action' => 'set',
                    'dst' => 'http.x-fastly-imageopto-api',
                    'src' => '"fastly"',
                    'ignore_if_set' => 0,
                    'priority' => "1",
                    'request_condition' => self::WORDPRESS_MODULE_NAME . '_image_optimization'
                )
            )
        );

        $errors = array();
        $vcl = new Vcl_Handler(array());
        $io_enabled = $vcl->check_io_active_on_fastly();
        if($io_enabled) {
            // Set for deletion
            $data = array(
                'condition' => array(
                    array(
                        'name' => self::WORDPRESS_MODULE_NAME . '_image_optimization',
                        'statement' => 'req.url.ext ~ "(?i)^(gif|png|jpe?g|webp)$"',
                        'type' => 'REQUEST',
                        'priority' => 10,
                        'delete' => true
                    )
                ),
                'header' => array(
                    array(
                        'name' => self::WORDPRESS_MODULE_NAME . '_image_optimization',
                        'type' => 'request',
                        'action' => 'set',
                        'dst' => 'http.x-fastly-imageopto-api',
                        'src' => '"fastly"',
                        'ignore_if_set' => 0,
                        'priority' => "1",
                        'request_condition' => self::WORDPRESS_MODULE_NAME . '_image_optimization',
                        'delete' => true
                    )
                )
            );
        }

        $vcl = new Vcl_Handler($data);

        if (!$vcl->execute($activate)) {
            //Log if enabled
            if (Purgely_Settings::get_setting('fastly_debug_mode')) {
                foreach ($vcl->get_errors() as $error) {
                    error_log($error);
                }
            }

            $errors = array_merge($errors, $vcl->get_errors());
        }

        if (!empty($errors)) {
            return $errors;
        }

        return true;
    }
}
