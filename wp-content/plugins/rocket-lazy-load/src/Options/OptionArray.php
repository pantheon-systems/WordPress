<?php
/**
 * Manages the plugin options data
 *
 * @package RocketLazyloadPlugin
 */

namespace RocketLazyLoadPlugin\Options;

/**
 * Manages the data inside an option.
 *
 * @since 2.0
 * @author Remy Perona
 */
class OptionArray
{
    /**
     * Option data
     *
     * @var Array Array of data inside the option
     */
    private $options;

    /**
     * Constructor
     *
     * @param Array $options Array of data coming from an option.
     */
    public function __construct($options)
    {
        $this->options = $options;
    }

    /**
     * Checks if the provided key exists in the option data array.
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @param string $key key name.
     * @return boolean
     */
    public function has($key)
    {
        return isset($this->options[ $key ]);
    }

    /**
     * Gets the value associated with a specific key.
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @param string $key     key name.
     * @param mixed  $default default value to return if key doesn't exist.
     * @return mixed
     */
    public function get($key, $default = '')
    {
        if (! $this->has($key)) {
            return $default;
        }

        return $this->options[ $key ];
    }

    /**
     * Sets the value associated with a specific key.
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @param string $key   key name.
     * @param mixed  $value Value to set.
     * @return void
     */
    public function set($key, $value)
    {
        $this->options[ $key ] = $value;
    }

    /**
     * Sets multiple values.
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @param array $options An array of key/value pairs to set.
     * @return void
     */
    public function setValues($options)
    {
        foreach ($options as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * Gets the option array.
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}
