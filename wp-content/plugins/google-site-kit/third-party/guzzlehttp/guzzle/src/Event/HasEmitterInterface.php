<?php

namespace Google\Site_Kit_Dependencies\GuzzleHttp\Event;

/**
 * Holds an event emitter
 */
interface HasEmitterInterface
{
    /**
     * Get the event emitter of the object
     *
     * @return EmitterInterface
     */
    public function getEmitter();
}
