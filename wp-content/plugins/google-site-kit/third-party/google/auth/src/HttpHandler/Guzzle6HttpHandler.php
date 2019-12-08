<?php

namespace Google\Site_Kit_Dependencies\Google\Auth\HttpHandler;

use Google\Site_Kit_Dependencies\GuzzleHttp\ClientInterface;
use Google\Site_Kit_Dependencies\Psr\Http\Message\RequestInterface;
use Google\Site_Kit_Dependencies\Psr\Http\Message\ResponseInterface;
class Guzzle6HttpHandler
{
    /**
     * @var ClientInterface
     */
    private $client;
    /**
     * @param ClientInterface $client
     */
    public function __construct(\Google\Site_Kit_Dependencies\GuzzleHttp\ClientInterface $client)
    {
        $this->client = $client;
    }
    /**
     * Accepts a PSR-7 request and an array of options and returns a PSR-7 response.
     *
     * @param RequestInterface $request
     * @param array $options
     *
     * @return ResponseInterface
     */
    public function __invoke(\Google\Site_Kit_Dependencies\Psr\Http\Message\RequestInterface $request, array $options = [])
    {
        return $this->client->send($request, $options);
    }
    /**
     * Accepts a PSR-7 request and an array of options and returns a PromiseInterface
     *
     * @param RequestInterface $request
     * @param array $options
     *
     * @return \GuzzleHttp\Promise\Promise
     */
    public function async(\Google\Site_Kit_Dependencies\Psr\Http\Message\RequestInterface $request, array $options = [])
    {
        return $this->client->sendAsync($request, $options);
    }
}
