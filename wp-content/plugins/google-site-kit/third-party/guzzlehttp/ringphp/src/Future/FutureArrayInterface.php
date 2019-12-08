<?php

namespace Google\Site_Kit_Dependencies\GuzzleHttp\Ring\Future;

/**
 * Future that provides array-like access.
 */
interface FutureArrayInterface extends \Google\Site_Kit_Dependencies\GuzzleHttp\Ring\Future\FutureInterface, \ArrayAccess, \Countable, \IteratorAggregate
{
}
