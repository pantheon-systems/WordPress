<?php

namespace Google\Site_Kit_Dependencies\GuzzleHttp\Subscriber;

use Google\Site_Kit_Dependencies\GuzzleHttp\Cookie\CookieJar;
use Google\Site_Kit_Dependencies\GuzzleHttp\Cookie\CookieJarInterface;
use Google\Site_Kit_Dependencies\GuzzleHttp\Event\BeforeEvent;
use Google\Site_Kit_Dependencies\GuzzleHttp\Event\CompleteEvent;
use Google\Site_Kit_Dependencies\GuzzleHttp\Event\RequestEvents;
use Google\Site_Kit_Dependencies\GuzzleHttp\Event\SubscriberInterface;
/**
 * Adds, extracts, and persists cookies between HTTP requests
 */
class Cookie implements \Google\Site_Kit_Dependencies\GuzzleHttp\Event\SubscriberInterface
{
    /** @var CookieJarInterface */
    private $cookieJar;
    /**
     * @param CookieJarInterface $cookieJar Cookie jar used to hold cookies
     */
    public function __construct(\Google\Site_Kit_Dependencies\GuzzleHttp\Cookie\CookieJarInterface $cookieJar = null)
    {
        $this->cookieJar = $cookieJar ?: new \Google\Site_Kit_Dependencies\GuzzleHttp\Cookie\CookieJar();
    }
    public function getEvents()
    {
        // Fire the cookie plugin complete event before redirecting
        return ['before' => ['onBefore'], 'complete' => ['onComplete', \Google\Site_Kit_Dependencies\GuzzleHttp\Event\RequestEvents::REDIRECT_RESPONSE + 10]];
    }
    /**
     * Get the cookie cookieJar
     *
     * @return CookieJarInterface
     */
    public function getCookieJar()
    {
        return $this->cookieJar;
    }
    public function onBefore(\Google\Site_Kit_Dependencies\GuzzleHttp\Event\BeforeEvent $event)
    {
        $this->cookieJar->addCookieHeader($event->getRequest());
    }
    public function onComplete(\Google\Site_Kit_Dependencies\GuzzleHttp\Event\CompleteEvent $event)
    {
        $this->cookieJar->extractCookies($event->getRequest(), $event->getResponse());
    }
}
