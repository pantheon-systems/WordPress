<?php

/**
 * Set the Surrogate Keys header for a request.
 *
 * This class gathers, sanitizes and sends all of the Surrogate Keys for a request.
 */
class Purgely_Surrogate_Keys_Header extends Purgely_Header
{

    /**
     * Header name.
     *
     * @var string
     */
    protected $_header_name = 'Surrogate-Key';

    /**
     * The lists that will compose the Surrogate-Keys header value.
     *
     * @var array    List of Surrogate Keys.
     */
    protected $_keys = array();

    /**
     * Add multiple keys to the list.
     *
     * @param  string $keys The keys to add to the list.
     */
    public function add_keys($keys)
    {
        $current_keys = $this->get_keys();

        // Combine keys.
        $keys = array_merge($current_keys, $keys);

        // De-dupe keys.
        $keys = array_unique($keys);

        // Rekey the keys.
        $keys = array_values($keys);

        $this->set_keys($keys);
    }

    /**
     * Add a key to the list.
     *
     * @param  string $key The key to add to the list.
     * @return array       The full list of keys.
     */
    public function add_key($key)
    {
        $keys = $this->get_keys();
        $keys[] = $key;

        $this->set_keys($keys);
        return $keys;
    }

    /**
     * Return the value of the header, overwritten from parent for Keys special case
     * Also test header size, if too big, set key that will always be purged
     *
     * @return string The header value.
     */
    public function get_value()
    {
        $keys_string = $this->prepare_keys();
        $header_string = $this->_header_name . ': ' . $keys_string;
        $header_size_bytes = mb_strlen($header_string, '8bit');
        if ($header_size_bytes >= FASTLY_MAX_HEADER_SIZE) {
            // Set to be always purged
            $siteId = false;
            if(is_multisite()) {
                $siteId = get_current_blog_id();
            } elseif($sitecode = Purgely_Settings::get_setting('sitecode')) {
                $siteId = $sitecode;
            }
            if($siteId) {
                return $siteId . '-' . 'holos';
            }
            return 'holos';
        }
        return $keys_string;
    }

    /**
     * Prepare the keys into a header value string.
     *
     * @return string Space delimited list of sanitized keys.
     */
    public function prepare_keys()
    {
        $keys = $this->get_keys();

        $siteId = false;
        if(is_multisite()) {
            $siteId = get_current_blog_id();
        } elseif($sitecode = Purgely_Settings::get_setting('sitecode')) {
            $siteId = $sitecode;
        }
        if($siteId) {
            foreach($keys as $index => $key) {
                $keys[$index] = $siteId . '-' . $key;
            }
        }

        $keys = array_map(array($this, 'sanitize_key'), $keys);
        return rtrim(implode(' ', $keys), ' ');
    }

    /**
     * Sanitize a surrogate key.
     *
     * @param  string $key The unsanitized key.
     * @return string The sanitized key.
     */
    public function sanitize_key($key)
    {
        return purgely_sanitize_surrogate_key($key);
    }

    /**
     * Set the keys for the Surrogate Keys header.
     *
     * @param  array $keys The keys for the header.
     * @return void
     */
    public function set_keys($keys)
    {
        $this->_keys = $keys;
    }

    /**
     * Key the list of Surrogate Keys.
     *
     * @return array The list of Surrogate Keys.
     */
    public function get_keys()
    {
        return $this->_keys;
    }
}
