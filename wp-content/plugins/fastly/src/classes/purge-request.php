<?php

/**
 * Issue a purge request for a resource.
 *
 * This is the main class to handle all purge related activities. This class will handle individual URL, key, and all
 * purges. Additionally, it can set soft purges and purge links related to the passed URL.
 */
class Purgely_Purge
{

    /** Purge all **/
    const ALL = 'all';

    /** Purge url **/
    const URL = 'url';

    /** Purge by Surrogate-Key collection **/
    const KEY_COLLECTION = 'key-collection';

    /** PURGE request method */
    const PURGE = 'PURGE';

    /**
     * Collection of possible purges
     * @var array
     */
    static protected $_purge_types = array(self::ALL, self::URL, self::KEY_COLLECTION);

    /**
     * The url or surrogate key to purge.
     *
     * @var string The thing that will be purged.
     */
    protected $_thing = '';

    /**
     * The type of purge request, which is 'url', 'key-collection', or 'all'.
     *
     * @var string The type of purge request.
     */
    protected $_type = null;

    /**
     * Issue the purge request.
     *
     * @param  string $type The type of purge request.
     * @param  string|array $thing The identifier for the item to purge.
     * @return array|bool|WP_Error The response from the purge request.
     */
    public function purge($type = self::URL, $thing = '')
    {

        if (!in_array($type, self::get_purge_types())) {
            return false;
        }

        $this->set_type($type);
        $this->set_thing($thing);

        // Build up headers & request url
        $headers = $this->_build_headers();
        $request_uri = $this->_build_request_uri_for_purge($type);
        $request_method = $this->_build_request_method_type($type);

        if (($request_uri && !empty($thing)) || ($request_uri && $type = self::ALL)) {
            try {
                $response = Requests::request($request_uri, $headers, array(), $request_method);

                // Do logging where needed
                $message = $this->_get_purge_data_message();
                handle_logging($response, $message);

                return $response->success;
            } catch (Exception $e) {
                error_log($e->getMessage());
            }
        }
        return false;
    }

    /**
     * Builds request headers
     *
     * @return array
     */
    protected function _build_headers()
    {
        $headers = array();

        // Credentials
        $headers['Fastly-Key'] = Purgely_Settings::get_setting('fastly_api_key');

        // Purge type
        if (Purgely_Settings::get_setting('default_purge_type') === 'soft') {
            $headers['Fastly-Soft-Purge'] = 1;
        }

        // Add Surrogate-Key header
        $thing = $this->get_thing();
        if (!empty($thing) && $this->get_type() === Purgely_Purge::KEY_COLLECTION) {
            $keys = implode(' ', $this->get_thing());
            $headers['Surrogate-Key'] = $keys;
        }

        return $headers;
    }

    /**
     * Build the URI for the purge request.
     *
     * @type string Type of the purge request (key, url, all)
     * @return string The purge URI to purge all items.
     */
    protected function _build_request_uri_for_purge($type)
    {
        $api_endpoint = Purgely_Settings::get_setting('fastly_api_hostname');
        $fastly_service_id = Purgely_Settings::get_setting('fastly_service_id');

        switch ($type) {
            case 'key-collection':
                return trailingslashit($api_endpoint) . 'service/' . $fastly_service_id . '/purge';
                break;
            case 'url':
                return $this->get_thing();
                break;
            case 'all':
                return trailingslashit($api_endpoint) . 'service/' . $fastly_service_id . '/purge_all';
                break;
            default :
                return false;
        }
    }

    /**
     * Sets Request method type
     * @param $type
     * @return string
     */
    protected function _build_request_method_type($type)
    {
        if ($type === Purgely_Purge::URL) {
            return Purgely_Purge::PURGE;
        } else {
            return Requests::POST;
        }
    }

    /**
     * Set the thing to purge.
     *
     * @param string|array $thing The identifier for the purged item.
     * @return void
     */
    public function set_thing($thing)
    {
        $this->_thing = $thing;
    }

    /**
     * Get the thing to purge.
     *
     * @return string|array The identifier for the purged item.
     */
    public function get_thing()
    {
        return $this->_thing;
    }

    /**
     * Set the type of purge.
     *
     * @param string $type The type of purge to perform.
     * @return void
     */
    public function set_type($type)
    {
        $this->_type = $type;
    }

    /**
     * Get the type of purge.
     *
     * @return string The type of purge being performed.
     */
    public function get_type()
    {
        return $this->_type;
    }

    /**
     * Prepare message for logging with data being purged
     * @return string
     */
    protected function _get_purge_data_message()
    {
        if ($this->get_type() === self::URL) {
            $msg = "Purging URL - " . $this->get_thing();
        } elseif ($this->get_type() === self::KEY_COLLECTION) {
            $msg = "Purging Keys *" . implode(' ', $this->get_thing()) . "*";
        } else {
            $msg = 'Initiated Purge All';
        }
        return $msg;
    }

    /**
     * Get possible purge types
     * @return array
     */
    static function get_purge_types()
    {
        return self::$_purge_types;
    }
}
