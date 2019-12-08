<?php

namespace Google\Site_Kit_Dependencies\Psr\Log;

/**
 * Describes a logger-aware instance.
 */
interface LoggerAwareInterface
{
    /**
     * Sets a logger instance on the object.
     *
     * @param LoggerInterface $logger
     *
     * @return void
     */
    public function setLogger(\Google\Site_Kit_Dependencies\Psr\Log\LoggerInterface $logger);
}
