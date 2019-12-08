<?php

/**
 * Class to control the VCL handling.
 */
class Vcl_Handler
{

    /** VCL data to be processed */
    protected $_vcl_data;

    /** Condition data to be processed */
    protected $_condition_data;

    /** Setting data to be processed */
    protected $_setting_data;

    /** Fastly Header data to be processed */
    protected $_header_data;

    /** Fastly API endpoint */
    protected $_hostname;

    /** Fastly API Key */
    protected $_api_key;

    /** Fastly Service ID */
    protected $_service_id;

    /** Fastly API URL version base */
    protected $_version_base_url;

    /** Headers used for GET requests */
    protected $_headers_get;

    /** Headers used for POST, PUT requests */
    protected $_headers_post;

    /** Last active version data */
    protected $_last_version_data;

    /** Next cloned version number */
    public $_next_cloned_version_num = null;

    /** Last active version number */
    public $_last_active_version_num = null;

    /** Last cloned version number */
    protected $_last_cloned_version;


    /** Errors */
    protected $_errors = array();

    /**
     * Sets data to be processed, sets Credentials
     * Vcl_Handler constructor.
     */
    public function __construct($data)
    {
        $this->_vcl_data = !empty($data['vcl']) ? $data['vcl'] : false;
        $this->_condition_data = !empty($data['condition']) ? $data['condition'] : false;
        $this->_setting_data = !empty($data['setting']) ? $data['setting'] : false;
        $this->_header_data = !empty($data['header']) ? $data['header'] : false;
        $this->_response_object_data = !empty($data['response']) ? $data['response'] : false;

        $this->_hostname = purgely_get_option('fastly_api_hostname');
        $this->_service_id = purgely_get_option('fastly_service_id');
        $this->_api_key = purgely_get_option('fastly_api_key');

        $connection = test_fastly_api_connection($this->_hostname, $this->_service_id, $this->_api_key);
        if (!$connection['status']) {
            $this->add_error(__($connection['message']));
            return;
        }

        // Set credentials based data (API url, headers, last version)
        $this->_version_base_url = trailingslashit($this->_hostname) . 'service/' . $this->_service_id . '/version';
        $this->_headers_get = array(
            'Fastly-Key' => $this->_api_key,
            'Accept' => 'application/json'
        );
        $this->_headers_post = array(
            'Fastly-Key' => $this->_api_key,
            'Accept' => 'application/json',
            'Content-Type' => 'application/x-www-form-urlencoded'
        );

        $this->_last_version_data = $this->get_last_version();

        if ($this->_last_version_data) {
            $this->_last_active_version_num = $this->_last_version_data->number;
        }

        if(!$this->_last_cloned_version) {
            $this->_last_cloned_version = $this->_last_active_version_num;
        }

        return;
    }

    /**
     * Main execute function, takes values inserted into constructor, builds requests and sends them via Fastly API
     * @activate bool
     * @return bool
     */
    public function execute($activate = false)
    {
        // Check if there are connection errors from construct
        $errors = $this->get_errors();
        if (!empty($errors)) {
            return false;
        }

        // Check if last version is fetched
        if ($this->_last_version_data === false) {
            $this->add_error(__('Last version does not exist'));
            return false;
        }

        // Check if any of the data is set
        if (empty($this->_vcl_data) &&
            empty($this->_condition_data) &&
            empty($this->_setting_data) &&
            empty($this->_response_object_data) &&
            empty($this->_header_data)
        ) {
            $this->add_error(__('No update data set, please specify, vcl, condition or setting data'));
            return false;
        }

        try {
            if (false === $this->clone_last_active_version()) {
                $this->add_error(__('Unable to clone last version'));
                return false;
            }

            $requests = array();

            if (!empty($this->_vcl_data)) {
                $requests = array_merge($requests, $this->prepare_vcl());
            }

            if (!empty($this->_condition_data)) {
                $conditions = $this->prepare_condition();
                if (false === $conditions) {
                    $this->add_error(__('Unable to insert new condition'));
                    return false;
                }
                $requests = array_merge($requests, $conditions);
            }

            if (!empty($this->_header_data)) {
                $requests = array_merge($requests, $this->prepare_header());
            }

            if (!empty($this->_setting_data)) {
                $requests = array_merge($requests, $this->prepare_setting());
            }

            if (!empty($this->_response_object_data)) {
                $requests = array_merge($requests, $this->prepare_response_object());
            }

            if (!$this->validate_version()) {
                $this->add_error(__('Version not validated'));
                return false;
            }

            // Set Request Headers
            foreach ($requests as $key => $request) {
                if (in_array($request['type'], array(Requests::POST, Requests::PUT))) {
                    $requests[$key]['headers'] = $this->_headers_post;
                } else {
                    $requests[$key]['headers'] = $this->_headers_get;
                }
            }

            // Send Requests
            $responses = Requests::request_multiple($requests);

            $pass = true;
            foreach ($responses as $response) {
                if (!$response->success) {
                    $pass = false;
                    $this->add_error(__('Some of the API requests failed, enable debugging and check logs for more information.'));

                    $message = 'VCL update failed : ' . $response->body;
                    handle_logging($response, $message);
                }
            }

            // Activate version if vcl is successfully uploaded
            if ($pass && $activate) {
                $request = $this->prepare_activate_version();

                $response = Requests::request($request['url'], $request['headers'], array(), $request['type']);
                if (!$response->success) {
                    $pass = false;
                    $this->add_error(__('Some of the API requests failed, enable debugging and check logs for more information.'));

                    $message = 'Activation of new version failed : ' . $response->body;
                    handle_logging($response, $message);
                } else {
                    $message = 'VCL updated, version activated : ' . $this->_last_cloned_version;
                    send_web_hook($message);
                }
            } elseif ($pass && !$activate) {
                $message = 'VCL updated, but not activated.';
                send_web_hook($message);
            }

        } catch (Exception $e) {
            $this->add_error(__('Some of the API requests failed, enable debugging and check logs for more information.'));
            $message = 'VCL update failed : ' . $e->getMessage();
            send_web_hook($message);
            error_log($message);// Force log this, possibly no response object

            return false;
        }

        return $pass;
    }

    /**
     * Prepares VCL request
     * @return array|bool
     */
    public function prepare_vcl()
    {
        // Prepare VCL data content

        $requests = array();
        foreach ($this->_vcl_data as $key => $single_vcl_data) {
            if (!empty($single_vcl_data['type'])) {

                // Append subdirectory to name if it exists
                if(!empty($single_vcl_data['subdirectory'])) {
                    $single_vcl_data['name'] = Upgrades::WORDPRESS_MODULE_NAME . '_' . $single_vcl_data['subdirectory']. '_' . $single_vcl_data['type'];
                    $single_vcl_data['vcl_dir'] = $single_vcl_data['vcl_dir'] . DIRECTORY_SEPARATOR . $single_vcl_data['subdirectory'];
                    unset($single_vcl_data['subdirectory']);
                } else {
                    $single_vcl_data['name'] = Upgrades::WORDPRESS_MODULE_NAME . '_' . $single_vcl_data['type'];
                }

                if(!isset($single_vcl_data['dynamic'])) {
                    $single_vcl_data['dynamic'] = 0;
                }

                if(!isset($single_vcl_data['priority'])) {
                    $single_vcl_data['priority'] = 60;
                }

                if (file_exists($single_vcl_data['vcl_dir'] . DIRECTORY_SEPARATOR . $single_vcl_data['type'] . '.vcl')) {
                    $single_vcl_data['content'] = file_get_contents($single_vcl_data['vcl_dir'] . DIRECTORY_SEPARATOR . $single_vcl_data['type'] . '.vcl');
                    unset($single_vcl_data['vcl_dir']);
                } else {
                    $this->add_error(__('VCL file does not exist.'));
                    return false;
                }

                if(!isset($single_vcl_data['delete'])) {
                    if ($this->check_if_vcl_exists($single_vcl_data['name'])) {
                        $requests[] = $this->prepare_update_vcl($single_vcl_data);
                    } else {
                        $requests[] = $this->prepare_insert_vcl($single_vcl_data);
                    }
                } else {
                    // Delete VCL snippet
                    $requests[] = $this->prepare_delete_vcl($single_vcl_data['name']);
                }
            } else {
                $this->add_error(__('VCL type not set.'));
                return false;
            }
        }

        return $requests;
    }

    /**
     * Checks if VCL exists
     * @name string
     * @return bool
     */
    public function check_if_vcl_exists($name)
    {
        if (empty($this->_last_version_data)) {
            return false;
        }

        $url = $this->_version_base_url . '/' . $this->_last_cloned_version . '/snippet/' . $name;
        $response = Requests::get($url, $this->_headers_get);

        return $response->success;
    }

    /**
     * Prepares request for updating existing VCL
     * @data array
     * @return array
     */
    public function prepare_update_vcl($data)
    {
        $url = $this->_version_base_url . '/' . $this->_last_cloned_version . '/snippet/' . $data['name'];

        $request = array(
            'url' => $url,
            'data' => $data,
            'type' => Requests::PUT
        );

        return $request;
    }

    /**
     * Prepare request for inserting new VCL
     * @data array
     * @return array
     */
    public function prepare_insert_vcl($data)
    {
        $url = $this->_version_base_url . '/' . $this->_last_cloned_version . '/snippet';

        $request = array(
            'url' => $url,
            'data' => $data,
            'type' => Requests::POST
        );

        return $request;
    }

    /**
     * Prepare request for delete VCL
     * @param $name
     * @return array
     */
    public function prepare_delete_vcl($name)
    {
        $url = $this->_version_base_url . '/' . $this->_last_cloned_version . '/snippet/' . $name;

        $request = array(
            'url' => $url,
            'type' => Requests::DELETE
        );

        return $request;
    }

    /**
     * Fetch last service version
     * @return bool|int
     */
    public function get_last_version()
    {
        $url = $this->_version_base_url;
        $response = Requests::get($url, $this->_headers_get);
        $response_data = json_decode($response->body);

        $this->_next_cloned_version_num = count($response_data) + 1;

        foreach ($response_data as $key => $version_data) {
            if ($version_data->active) {
                return $version_data;
            }
        }
        return false;
    }


    /**
     * Creates and returns cloned version number
     * @return bool
     */
    public function clone_last_active_version()
    {
        if (empty($this->_last_version_data)) {
            return false;
        }

        $version_number = $this->_last_version_data->number;
        $url = $this->_version_base_url . '/' . $version_number . '/clone';
        $response = Requests::put($url, $this->_headers_post);

        $response_data = json_decode($response->body);
        $cloned_version_number = isset($response_data->number) ? $response_data->number : false;
        $this->_last_cloned_version = $cloned_version_number;

        return $cloned_version_number;
    }

    /**
     * Prepares condition for insertion
     * @return array|bool
     */
    public function prepare_condition()
    {
        // Prepare condition content
        $requests = array();
        foreach ($this->_condition_data as $single_condition_data) {
            if (empty($single_condition_data['name']) ||
                empty($single_condition_data['statement']) ||
                empty($single_condition_data['type']) ||
                empty($single_condition_data['priority'])
            ) {
                $this->add_error(__('Condition data not properly set.'));
                return false;
            } else {
                if(!isset($single_condition_data['delete'])) {
                    if ($this->get_condition($single_condition_data['name'])) {
                        $requests[] = $this->prepare_update_condition($single_condition_data);
                    } else {
                        // Do insert here because condition is needed before setting (requests are not sent in order)
                        return $this->insert_condition($single_condition_data);
                    }
                } else {
                    // Delete Condition
                    return $this->delete_condition($single_condition_data['name']);
                }

            }
        }
        return $requests;
    }

    /**
     * Fetches condition by condition name
     * @name string
     * @return bool
     */
    public function get_condition($name)
    {
        $url = $this->_version_base_url . '/' . $this->_last_cloned_version . '/condition/' . $name;
        $response = Requests::get($url, $this->_headers_get);
        return $response->success;
    }

    /**
     * Prepare condition for update
     * @data array
     * @return array
     */
    public function prepare_update_condition($data)
    {
        $url = $this->_version_base_url . '/' . $this->_last_cloned_version . '/condition/' . $data['name'];

        $request = array(
            'url' => $url,
            'data' => $data,
            'type' => Requests::PUT
        );

        return $request;
    }

    /**
     * Prepare condition for insert
     * @data
     * @return array
     */
    public function insert_condition($data)
    {
        $url = $this->_version_base_url . '/' . $this->_last_cloned_version . '/condition';

        $request = array(
            'url' => $url,
            'data' => $data,
            'type' => Requests::POST
        );

        $response = Requests::request($request['url'], $this->_headers_post, $request['data'], $request['type']);

        if ($response->success) {
            return array();
        } else {
            return false;
        }
    }

    /**
     * Prepare condition for deletion
     * @param $name
     * @return array|bool
     * @throws Requests_Exception
     */
    public function delete_condition($name)
    {
        $url = $this->_version_base_url . '/' . $this->_last_cloned_version . '/condition/' . $name;

        $request = array(
            'url' => $url,
            'type' => Requests::DELETE
        );

        $response = Requests::request($request['url'], $this->_headers_post, array(), $request['type']);

        // If condition does not exist, thats ok too
        return array();
    }

    /**
     * Prepares header for insertion
     * @return array|bool
     */
    public function prepare_header()
    {
        // Prepare condition content
        $requests = array();
        foreach ($this->_header_data as $single_header_data) {
            if (empty($single_header_data['name']) ||
                empty($single_header_data['type']) ||
                empty($single_header_data['action']) ||
                empty($single_header_data['dst']) ||
                empty($single_header_data['src']) ||
                !isset($single_header_data['ignore_if_set']) ||
                empty($single_header_data['priority'])
            ) {
                $this->add_error(__('Header data not properly set.'));
                return false;
            } else {
                if(!isset($single_header_data['delete'])) {
                    if ($this->get_header($single_header_data['name'])) {
                        $requests[] = $this->prepare_update_header($single_header_data);
                    } else {
                        // Do insert here because condition is needed before setting (requests are not sent in order)
                        return $this->insert_header($single_header_data);
                    }
                } else {
                    // Delete Condition
                    return $this->delete_header($single_header_data['name']);
                }
            }
        }
        return $requests;
    }

    /**
     * Fetches header by name
     * @name string
     * @return bool
     */
    public function get_header($name)
    {
        $url = $this->_version_base_url . '/' . $this->_last_cloned_version . '/header/' . $name;
        $response = Requests::get($url, $this->_headers_get);
        return $response->success;
    }

    /**
     * Prepare header for update
     * @data array
     * @return array
     */
    public function prepare_update_header($data)
    {
        $url = $this->_version_base_url . '/' . $this->_last_cloned_version . '/header/' . $data['name'];

        $request = array(
            'url' => $url,
            'data' => $data,
            'type' => Requests::PUT
        );

        return $request;
    }

    /**
     * Prepare header for insert
     * @data
     * @return array
     */
    public function insert_header($data)
    {
        $url = $this->_version_base_url . '/' . $this->_last_cloned_version . '/header';

        $request = array(
            'url' => $url,
            'data' => $data,
            'type' => Requests::POST
        );

        $response = Requests::request($request['url'], $this->_headers_post, $request['data'], $request['type']);

        if ($response->success) {
            return array();
        } else {
            return false;
        }
    }

    /**
     * Prepare header for deletion
     * @param $name
     * @return array|bool
     * @throws Requests_Exception
     */
    public function delete_header($name)
    {
        $url = $this->_version_base_url . '/' . $this->_last_cloned_version . '/header/' . $name;

        $request = array(
            'url' => $url,
            'type' => Requests::DELETE
        );

        $response = Requests::request($request['url'], $this->_headers_post, array(), $request['type']);

        // If condition does not exist, thats ok too
        return array();
    }

    /**
     * Prepares setting for insertion
     * @return array|bool
     */
    public function prepare_setting()
    {
        // Prepare setting content
        $requests = array();
        foreach ($this->_setting_data as $single_setting_data) {
            if (empty($single_setting_data['name']) ||
                empty($single_setting_data['request_condition'])
            ) {
                $this->add_error(__('Setting data not properly set.'));
                return false;
            } else {

                if(isset($single_setting_data['service_id'])) {
                    $single_setting_data['service_id'] = $this->_service_id;
                }

                if(isset($single_setting_data['version'])) {
                    $single_setting_data['version'] = $this->_last_active_version_num;
                }

                if(!isset($single_setting_data['delete'])) {
                    if ($this->get_setting($single_setting_data['name'])) {
                        $requests[] = $this->prepare_update_setting($single_setting_data);
                    } else {
                        $requests[] = $this->prepare_insert_setting($single_setting_data);
                    }
                } else {
                    // Remove setting
                    $requests[] = $this->prepare_delete_setting($single_setting_data['name']);
                }
            }
        }
        return $requests;
    }

    /**
     * Fetches setting by condition name
     * @name string
     * @return bool
     */
    public function get_setting($name)
    {
        $url = $this->_version_base_url . '/' . $this->_last_cloned_version . '/request_settings/' . $name;
        $response = Requests::get($url, $this->_headers_get);
        return $response->success;
    }

    /**
     * Prepares update setting data
     * @data array
     * @return array
     */
    public function prepare_update_setting($data)
    {
        $url = $this->_version_base_url . '/' . $this->_last_cloned_version . '/request_settings/' . $data['name'];

        $request = array(
            'url' => $url,
            'data' => $data,
            'type' => Requests::PUT
        );

        return $request;
    }

    /**
     * Prepares Insert setting data
     * @data array
     * @return array
     */
    public function prepare_insert_setting($data)
    {
        $url = $this->_version_base_url . '/' . $this->_last_cloned_version . '/request_settings';

        $request = array(
            'url' => $url,
            'data' => $data,
            'type' => Requests::POST
        );

        return $request;
    }

    /**
     * Prepares Insert setting data
     * @data array
     * @return array
     */
    public function prepare_delete_setting($name)
    {
        $url = $this->_version_base_url . '/' . $this->_last_cloned_version . '/request_settings/' . $name;

        $request = array(
            'url' => $url,
            'type' => Requests::DELETE
        );

        return $request;
    }

    /**
     * Prepares request object for insertion
     * @return array|bool
     */
    public function prepare_response_object()
    {
        // Prepare setting content
        $requests = array();
        foreach ($this->_response_object_data as $single_response_object_data) {
            if (empty($single_response_object_data['name']) ||
                empty($single_response_object_data['request_condition']) ||
                empty($single_response_object_data['content'])
            ) {
                $this->add_error(__('Setting data not properly set.'));
                return false;
            } else {
                if ($this->get_response_object($single_response_object_data['name'])) {
                    $requests[] = $this->prepare_update_response_object($single_response_object_data);
                } else {
                    $requests[] = $this->prepare_insert_response_object($single_response_object_data);
                }
            }
        }
        return $requests;
    }

    /**
     * Fetches response object by name
     * @name string
     * @return bool
     */
    public function get_response_object($name)
    {
        $url = $this->_version_base_url . '/' . $this->_last_cloned_version . '/response_object/' . $name;
        $response = Requests::get($url, $this->_headers_get);
        return $response->success;
    }

    /**
     * Fetches response object data by name
     * @name string
     * @return bool
     */
    public function get_response_object_data($name)
    {
        $version_num = !empty($this->_last_version_data->number) ? $this->_last_version_data->number : false;
        if(!$version_num || !$name) {
            return false;
        }
        $url = $this->_version_base_url . '/' . $version_num . '/response_object/' . $name;
        $response = Requests::get($url, $this->_headers_get);
        return $response;
    }

    /**
     * Prepares update response object data
     * @data array
     * @return array
     */
    public function prepare_update_response_object($data)
    {
        $url = $this->_version_base_url . '/' . $this->_last_cloned_version . '/response_object/' . $data['name'];

        $request = array(
            'url' => $url,
            'data' => $data,
            'type' => Requests::PUT
        );

        return $request;
    }

    /**
     * Prepares insert response object data
     * @data array
     * @return array
     */
    public function prepare_insert_response_object($data)
    {
        $url = $this->_version_base_url . '/' . $this->_last_cloned_version . '/response_object';

        $request = array(
            'url' => $url,
            'data' => $data,
            'type' => Requests::POST
        );

        return $request;
    }

    /**
     * Validates last cloned version
     * @return bool
     */
    public function validate_version()
    {
        $url = $this->_version_base_url . '/' . $this->_last_cloned_version . '/validate';
        $response = Requests::get($url, $this->_headers_get);
        return $response->success;
    }

    /**
     * Activates last cloned version
     * @return array
     */
    public function prepare_activate_version()
    {
        $url = $this->_version_base_url . '/' . $this->_last_cloned_version . '/activate';

        $request = array(
            'url' => $url,
            'type' => Requests::PUT,
            'headers' => $this->_headers_get
        );

        return $request;
    }

    /**
     * Checks if Image optimization possible (enabled on Fastly sales side)
     * @return bool
     */
    public function check_io_possible()
    {
        if(!$this->_hostname || !$this->_service_id) {
            return false;
        }
        try {
            $url = trailingslashit($this->_hostname) . 'service/' . $this->_service_id . '/dynamic_io_settings';
            $response = Requests::get($url, $this->_headers_get);
            return $response->success;
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
        return false;
    }

    /**
     * Check if Image optimization active on Fastly management (header and condition)
     * @return bool
     */
    public function check_io_active_on_fastly()
    {
        $header = $this->get_header(Upgrades::WORDPRESS_MODULE_NAME . '_image_optimization');
        $condition = $this->get_condition(Upgrades::WORDPRESS_MODULE_NAME . '_image_optimization');
        return $header && $condition;
    }

    /**
     * Adds new error to error array
     * @param $message
     */
    public function add_error($message)
    {
        $this->_errors[] = $message;
    }

    /**
     * Fetches logged errors
     */
    public function get_errors()
    {
        return $this->_errors;
    }
}
