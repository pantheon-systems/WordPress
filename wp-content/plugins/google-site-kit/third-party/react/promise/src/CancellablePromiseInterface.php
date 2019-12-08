<?php

namespace Google\Site_Kit_Dependencies\React\Promise;

interface CancellablePromiseInterface extends \Google\Site_Kit_Dependencies\React\Promise\PromiseInterface
{
    /**
     * @return void
     */
    public function cancel();
}
