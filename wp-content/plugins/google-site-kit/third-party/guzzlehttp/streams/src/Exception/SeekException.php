<?php

namespace Google\Site_Kit_Dependencies\GuzzleHttp\Stream\Exception;

use Google\Site_Kit_Dependencies\GuzzleHttp\Stream\StreamInterface;
/**
 * Exception thrown when a seek fails on a stream.
 */
class SeekException extends \RuntimeException
{
    private $stream;
    public function __construct(\Google\Site_Kit_Dependencies\GuzzleHttp\Stream\StreamInterface $stream, $pos = 0, $msg = '')
    {
        $this->stream = $stream;
        $msg = $msg ?: 'Could not seek the stream to position ' . $pos;
        parent::__construct($msg);
    }
    /**
     * @return StreamInterface
     */
    public function getStream()
    {
        return $this->stream;
    }
}
