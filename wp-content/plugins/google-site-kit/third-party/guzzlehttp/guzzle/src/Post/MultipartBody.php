<?php

namespace Google\Site_Kit_Dependencies\GuzzleHttp\Post;

use Google\Site_Kit_Dependencies\GuzzleHttp\Stream\AppendStream;
use Google\Site_Kit_Dependencies\GuzzleHttp\Stream\Stream;
use Google\Site_Kit_Dependencies\GuzzleHttp\Stream\StreamDecoratorTrait;
use Google\Site_Kit_Dependencies\GuzzleHttp\Stream\StreamInterface;
/**
 * Stream that when read returns bytes for a streaming multipart/form-data body
 */
class MultipartBody implements \Google\Site_Kit_Dependencies\GuzzleHttp\Stream\StreamInterface
{
    use StreamDecoratorTrait;
    private $boundary;
    /**
     * @param array  $fields   Associative array of field names to values where
     *                         each value is a string or array of strings.
     * @param array  $files    Associative array of PostFileInterface objects
     * @param string $boundary You can optionally provide a specific boundary
     * @throws \InvalidArgumentException
     */
    public function __construct(array $fields = [], array $files = [], $boundary = null)
    {
        $this->boundary = $boundary ?: \uniqid();
        $this->stream = $this->createStream($fields, $files);
    }
    /**
     * Get the boundary
     *
     * @return string
     */
    public function getBoundary()
    {
        return $this->boundary;
    }
    public function isWritable()
    {
        return \false;
    }
    /**
     * Get the string needed to transfer a POST field
     */
    private function getFieldString($name, $value)
    {
        return \sprintf("--%s\r\nContent-Disposition: form-data; name=\"%s\"\r\n\r\n%s\r\n", $this->boundary, $name, $value);
    }
    /**
     * Get the headers needed before transferring the content of a POST file
     */
    private function getFileHeaders(\Google\Site_Kit_Dependencies\GuzzleHttp\Post\PostFileInterface $file)
    {
        $headers = '';
        foreach ($file->getHeaders() as $key => $value) {
            $headers .= "{$key}: {$value}\r\n";
        }
        return "--{$this->boundary}\r\n" . \trim($headers) . "\r\n\r\n";
    }
    /**
     * Create the aggregate stream that will be used to upload the POST data
     */
    protected function createStream(array $fields, array $files)
    {
        $stream = new \Google\Site_Kit_Dependencies\GuzzleHttp\Stream\AppendStream();
        foreach ($fields as $name => $fieldValues) {
            foreach ((array) $fieldValues as $value) {
                $stream->addStream(\Google\Site_Kit_Dependencies\GuzzleHttp\Stream\Stream::factory($this->getFieldString($name, $value)));
            }
        }
        foreach ($files as $file) {
            if (!$file instanceof \Google\Site_Kit_Dependencies\GuzzleHttp\Post\PostFileInterface) {
                throw new \InvalidArgumentException('All POST fields must ' . 'implement PostFieldInterface');
            }
            $stream->addStream(\Google\Site_Kit_Dependencies\GuzzleHttp\Stream\Stream::factory($this->getFileHeaders($file)));
            $stream->addStream($file->getContent());
            $stream->addStream(\Google\Site_Kit_Dependencies\GuzzleHttp\Stream\Stream::factory("\r\n"));
        }
        // Add the trailing boundary with CRLF
        $stream->addStream(\Google\Site_Kit_Dependencies\GuzzleHttp\Stream\Stream::factory("--{$this->boundary}--\r\n"));
        return $stream;
    }
}
