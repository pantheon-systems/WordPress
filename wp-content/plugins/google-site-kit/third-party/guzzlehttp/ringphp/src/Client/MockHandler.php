<?php

namespace Google\Site_Kit_Dependencies\GuzzleHttp\Ring\Client;

use Google\Site_Kit_Dependencies\GuzzleHttp\Ring\Core;
use Google\Site_Kit_Dependencies\GuzzleHttp\Ring\Future\CompletedFutureArray;
use Google\Site_Kit_Dependencies\GuzzleHttp\Ring\Future\FutureArrayInterface;
/**
 * Ring handler that returns a canned response or evaluated function result.
 */
class MockHandler
{
    /** @var callable|array|FutureArrayInterface */
    private $result;
    /**
     * Provide an array or future to always return the same value. Provide a
     * callable that accepts a request object and returns an array or future
     * to dynamically create a response.
     *
     * @param array|FutureArrayInterface|callable $result Mock return value.
     */
    public function __construct($result)
    {
        $this->result = $result;
    }
    public function __invoke(array $request)
    {
        \Google\Site_Kit_Dependencies\GuzzleHttp\Ring\Core::doSleep($request);
        $response = \is_callable($this->result) ? \call_user_func($this->result, $request) : $this->result;
        if (\is_array($response)) {
            $response = new \Google\Site_Kit_Dependencies\GuzzleHttp\Ring\Future\CompletedFutureArray($response + ['status' => null, 'body' => null, 'headers' => [], 'reason' => null, 'effective_url' => null]);
        } elseif (!$response instanceof \Google\Site_Kit_Dependencies\GuzzleHttp\Ring\Future\FutureArrayInterface) {
            throw new \InvalidArgumentException('Response must be an array or FutureArrayInterface. Found ' . \Google\Site_Kit_Dependencies\GuzzleHttp\Ring\Core::describeType($request));
        }
        return $response;
    }
}
