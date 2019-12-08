<?php

namespace Google\Site_Kit_Dependencies\GuzzleHttp\Stream;

/**
 * Stream decorator that prevents a stream from being seeked
 */
class NoSeekStream implements \Google\Site_Kit_Dependencies\GuzzleHttp\Stream\StreamInterface
{
    use StreamDecoratorTrait;
    public function seek($offset, $whence = \SEEK_SET)
    {
        return \false;
    }
    public function isSeekable()
    {
        return \false;
    }
    public function attach($stream)
    {
        $this->stream->attach($stream);
    }
}
