<?php

namespace Google\Site_Kit_Dependencies\GuzzleHttp\Event;

/**
 * Trait that implements the methods of HasEmitterInterface
 */
trait HasEmitterTrait
{
    /** @var EmitterInterface */
    private $emitter;
    public function getEmitter()
    {
        if (!$this->emitter) {
            $this->emitter = new \Google\Site_Kit_Dependencies\GuzzleHttp\Event\Emitter();
        }
        return $this->emitter;
    }
}
