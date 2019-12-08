<?php

namespace Google\Site_Kit_Dependencies\GuzzleHttp\Exception;

use Google\Site_Kit_Dependencies\GuzzleHttp\Message\ResponseInterface;
/**
 * Exception when a client is unable to parse the response body as XML or JSON
 */
class ParseException extends \Google\Site_Kit_Dependencies\GuzzleHttp\Exception\TransferException
{
    /** @var ResponseInterface */
    private $response;
    public function __construct($message = '', \Google\Site_Kit_Dependencies\GuzzleHttp\Message\ResponseInterface $response = null, \Exception $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->response = $response;
    }
    /**
     * Get the associated response
     *
     * @return ResponseInterface|null
     */
    public function getResponse()
    {
        return $this->response;
    }
}
