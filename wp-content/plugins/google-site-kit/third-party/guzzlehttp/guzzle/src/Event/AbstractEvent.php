<?php

namespace Google\Site_Kit_Dependencies\GuzzleHttp\Event;

/**
 * Basic event class that can be extended.
 */
abstract class AbstractEvent implements \Google\Site_Kit_Dependencies\GuzzleHttp\Event\EventInterface
{
    private $propagationStopped = \false;
    public function isPropagationStopped()
    {
        return $this->propagationStopped;
    }
    public function stopPropagation()
    {
        $this->propagationStopped = \true;
    }
}
