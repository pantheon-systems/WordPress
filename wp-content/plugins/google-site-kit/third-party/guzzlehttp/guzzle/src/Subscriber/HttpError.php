<?php

namespace Google\Site_Kit_Dependencies\GuzzleHttp\Subscriber;

use Google\Site_Kit_Dependencies\GuzzleHttp\Event\CompleteEvent;
use Google\Site_Kit_Dependencies\GuzzleHttp\Event\RequestEvents;
use Google\Site_Kit_Dependencies\GuzzleHttp\Event\SubscriberInterface;
use Google\Site_Kit_Dependencies\GuzzleHttp\Exception\RequestException;
/**
 * Throws exceptions when a 4xx or 5xx response is received
 */
class HttpError implements \Google\Site_Kit_Dependencies\GuzzleHttp\Event\SubscriberInterface
{
    public function getEvents()
    {
        return ['complete' => ['onComplete', \Google\Site_Kit_Dependencies\GuzzleHttp\Event\RequestEvents::VERIFY_RESPONSE]];
    }
    /**
     * Throw a RequestException on an HTTP protocol error
     *
     * @param CompleteEvent $event Emitted event
     * @throws RequestException
     */
    public function onComplete(\Google\Site_Kit_Dependencies\GuzzleHttp\Event\CompleteEvent $event)
    {
        $code = (string) $event->getResponse()->getStatusCode();
        // Throw an exception for an unsuccessful response
        if ($code[0] >= 4) {
            throw \Google\Site_Kit_Dependencies\GuzzleHttp\Exception\RequestException::create($event->getRequest(), $event->getResponse());
        }
    }
}
