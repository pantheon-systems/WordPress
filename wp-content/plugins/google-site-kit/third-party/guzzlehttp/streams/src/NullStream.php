<?php

namespace Google\Site_Kit_Dependencies\GuzzleHttp\Stream;

use Google\Site_Kit_Dependencies\GuzzleHttp\Stream\Exception\CannotAttachException;
/**
 * Does not store any data written to it.
 */
class NullStream implements \Google\Site_Kit_Dependencies\GuzzleHttp\Stream\StreamInterface
{
    public function __toString()
    {
        return '';
    }
    public function getContents()
    {
        return '';
    }
    public function close()
    {
    }
    public function detach()
    {
    }
    public function attach($stream)
    {
        throw new \Google\Site_Kit_Dependencies\GuzzleHttp\Stream\Exception\CannotAttachException();
    }
    public function getSize()
    {
        return 0;
    }
    public function isReadable()
    {
        return \true;
    }
    public function isWritable()
    {
        return \true;
    }
    public function isSeekable()
    {
        return \true;
    }
    public function eof()
    {
        return \true;
    }
    public function tell()
    {
        return 0;
    }
    public function seek($offset, $whence = \SEEK_SET)
    {
        return \false;
    }
    public function read($length)
    {
        return \false;
    }
    public function write($string)
    {
        return \strlen($string);
    }
    public function getMetadata($key = null)
    {
        return $key ? null : [];
    }
}
