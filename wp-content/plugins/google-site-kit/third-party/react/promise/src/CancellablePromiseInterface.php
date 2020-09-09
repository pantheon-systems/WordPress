<?php

namespace Google\Site_Kit_Dependencies\React\Promise;

interface CancellablePromiseInterface extends \Google\Site_Kit_Dependencies\React\Promise\PromiseInterface
{
    /**
     * The `cancel()` method notifies the creator of the promise that there is no
     * further interest in the results of the operation.
     *
     * Once a promise is settled (either fulfilled or rejected), calling `cancel()` on
     * a promise has no effect.
     *
     * @return void
     */
    public function cancel();
}
