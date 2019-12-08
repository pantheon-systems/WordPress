<?php

/**
 * Control the surrogate control header.
 *
 * The surrogate control header controls the TTL for objects on Fastly. This class extends the basic header class and
 * ensures Surrogate-Control header is set.
 */
class Purgely_Surrogate_Control_Header extends Purgely_Header
{

    /**
     * Header name.
     *
     * @var string
     */
    protected $_header_name = 'Surrogate-Control';

    /**
     * Sets original headers
     */
    public function build_original_headers()
    {
        if (true === Purgely_Settings::get_setting('enable_stale_while_revalidate')) {
            $this->_headers['max-age'] = absint(Purgely_Settings::get_setting('surrogate_control_ttl'));
        }
        if (true === Purgely_Settings::get_setting('enable_stale_while_revalidate')) {
            $this->_headers['stale-while-revalidate'] = absint(Purgely_Settings::get_setting('stale_while_revalidate_ttl'));
        }
        if (true === Purgely_Settings::get_setting('enable_stale_if_error')) {
            $this->_headers['stale-if-error'] = absint(Purgely_Settings::get_setting('stale_if_error_ttl'));
        }
    }
}
