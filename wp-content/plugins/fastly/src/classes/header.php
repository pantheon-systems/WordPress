<?php

/**
 * Abstract class that defines basic header behavior.
 *
 * A big part of this plugin is defining response headers that control the Fastly caching behavior. This class
 * simplifies some of the basic functionality of the header classes.
 */
abstract class Purgely_Header
{
    /**
     * The header name that will be set.
     *
     * @var string The header name.
     */
    protected $_header_name = '';

    /**
     * Headers used.
     *
     * @var array Headers that will be built and set
     */
    protected $_headers = array();

    /**
     * The surrogate key value.
     *
     * @var string The surrogate key that will be set.
     */
    protected $_value = '';

    /**
     * Construct the object.
     */
    public function __construct()
    {
        $this->set_header_name($this->_header_name);
        $this->build_original_headers();
    }

    /**
     * Sets original headers
     */
    public function build_original_headers()
    {
    }

    /**
     * Send the key by setting the header.
     *
     * @return void
     */
    public function send_header()
    {
        header($this->_header_name . ': ' . $this->get_value(), false);
    }

    /**
     * Set the header name.
     *
     * @param  string $header_name The header name.
     * @return void
     */
    public function set_header_name($header_name)
    {
        $this->_header_name = $header_name;
    }

    /**
     * Build header string based on current headers
     *
     * @return string
     */
    public function build_header_value()
    {
        $headers = '';
        foreach ($this->_headers as $name => $val) {
            $headers .= $name . '=' . $val . ', ';
        }

        return rtrim($headers, ', ');
    }

    /**
     * Set the header value.
     *
     * @param  string $value The header value.
     * @return void
     */
    public function set_value($value)
    {
        $this->_value = $value;
    }

    /**
     * Return the value of the header.
     *
     * @return string The header value.
     */
    public function get_value()
    {
        return $this->build_header_value();
    }

    /**
     * Edit headers - allows to overwrite or add new headers
     *
     * @param array $key
     */
    public function edit_headers($key)
    {
        if (is_array($key)) {
            foreach ($key as $k => $val) {
                $this->_headers[$k] = $val;
            }
        }
    }

    /**
     * Remove wanted headers
     *
     * @param array|string $key
     */
    public function unset_headers($key)
    {
        if (is_array($key)) {
            foreach ($key as $k) {
                if (!empty($this->_headers[$k])) {
                    unset($this->_headers[$k]);
                }
            }
        } else {
            if (!empty($this->_headers[$key])) {
                unset($this->_headers[$key]);
            }
        }
    }
}
