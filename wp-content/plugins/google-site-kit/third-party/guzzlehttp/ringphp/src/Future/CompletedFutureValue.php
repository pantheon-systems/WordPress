<?php

namespace Google\Site_Kit_Dependencies\GuzzleHttp\Ring\Future;

use Google\Site_Kit_Dependencies\React\Promise\FulfilledPromise;
use Google\Site_Kit_Dependencies\React\Promise\RejectedPromise;
/**
 * Represents a future value that has been resolved or rejected.
 */
class CompletedFutureValue implements \Google\Site_Kit_Dependencies\GuzzleHttp\Ring\Future\FutureInterface
{
    protected $result;
    protected $error;
    private $cachedPromise;
    /**
     * @param mixed      $result Resolved result
     * @param \Exception $e      Error. Pass a GuzzleHttp\Ring\Exception\CancelledFutureAccessException
     *                           to mark the future as cancelled.
     */
    public function __construct($result, \Exception $e = null)
    {
        $this->result = $result;
        $this->error = $e;
    }
    public function wait()
    {
        if ($this->error) {
            throw $this->error;
        }
        return $this->result;
    }
    public function cancel()
    {
    }
    public function promise()
    {
        if (!$this->cachedPromise) {
            $this->cachedPromise = $this->error ? new \Google\Site_Kit_Dependencies\React\Promise\RejectedPromise($this->error) : new \Google\Site_Kit_Dependencies\React\Promise\FulfilledPromise($this->result);
        }
        return $this->cachedPromise;
    }
    public function then(callable $onFulfilled = null, callable $onRejected = null, callable $onProgress = null)
    {
        return $this->promise()->then($onFulfilled, $onRejected, $onProgress);
    }
}
