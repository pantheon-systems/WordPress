<?php

namespace Google\Site_Kit_Dependencies\GuzzleHttp\Exception;

use Google\Site_Kit_Dependencies\GuzzleHttp\Message\ResponseInterface;
/**
 * Exception when a client is unable to parse the response body as XML
 */
class XmlParseException extends \Google\Site_Kit_Dependencies\GuzzleHttp\Exception\ParseException
{
    /** @var \LibXMLError */
    protected $error;
    public function __construct($message = '', \Google\Site_Kit_Dependencies\GuzzleHttp\Message\ResponseInterface $response = null, \Exception $previous = null, \LibXMLError $error = null)
    {
        parent::__construct($message, $response, $previous);
        $this->error = $error;
    }
    /**
     * Get the associated error
     *
     * @return \LibXMLError|null
     */
    public function getError()
    {
        return $this->error;
    }
}
