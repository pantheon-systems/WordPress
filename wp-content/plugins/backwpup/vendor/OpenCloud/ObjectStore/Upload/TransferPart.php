<?php
/**
 * Copyright 2012-2014 Rackspace US, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace OpenCloud\ObjectStore\Upload;

use Guzzle\Http\Message\Response;
use Guzzle\Http\Url;
use OpenCloud\Common\Constants\Header;

/**
 * Represents an individual part of the EntityBody being uploaded.
 *
 * @codeCoverageIgnore
 */
class TransferPart
{
    /**
     * @var int Its position in the upload queue.
     */
    protected $partNumber;

    /**
     * @var string This upload's ETag checksum.
     */
    protected $eTag;

    /**
     * @var int The length of this upload in bytes.
     */
    protected $contentLength;

    /**
     * @var string The API path of this upload.
     */
    protected $path;

    /**
     * @param int $contentLength
     * @return $this
     */
    public function setContentLength($contentLength)
    {
        $this->contentLength = $contentLength;

        return $this;
    }

    /**
     * @return int
     */
    public function getContentLength()
    {
        return $this->contentLength;
    }

    /**
     * @param  string $etag
     * @return $this
     */
    public function setETag($etag)
    {
        $this->etag = $etag;

        return $this;
    }

    /**
     * @return string
     */
    public function getETag()
    {
        return $this->etag;
    }

    /**
     * @param int $partNumber
     * @return $this
     */
    public function setPartNumber($partNumber)
    {
        $this->partNumber = $partNumber;

        return $this;
    }

    /**
     * @return int
     */
    public function getPartNumber()
    {
        return $this->partNumber;
    }

    /**
     * @param $path
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Create the request needed for this upload to the API.
     *
     * @param EntityBody $part    The entity body being uploaded
     * @param int        $number  Its number/position, needed for name
     * @param OpenStack  $client  Client responsible for issuing requests
     * @param array      $options Set by the Transfer object
     * @return OpenCloud\Common\Http\Request
     */
    public static function createRequest($part, $number, $client, $options)
    {
        $name = sprintf('%s/%s/%d', $options['objectName'], $options['prefix'], $number);
        $url = clone $options['containerUrl'];
        $url->addPath($name);

        $headers = array(
            Header::CONTENT_LENGTH => $part->getContentLength(),
            Header::CONTENT_TYPE   => $part->getContentType()
        );

        if ($options['doPartChecksum'] === true) {
            $headers['ETag'] = $part->getContentMd5();
        }

        $request = $client->put($url, $headers, $part);

        if (isset($options['progress'])) {
            $request->getCurlOptions()->add('progress', true);
            if (is_callable($options['progress'])) {
                $request->getCurlOptions()->add('progressCallback', $options['progress']);
            }
        }

        return $request;
    }

    /**
     * Construct a TransferPart from a HTTP response delivered by the API.
     *
     * @param Response $response
     * @param int      $partNumber
     * @return TransferPart
     */
    public static function fromResponse(Response $response, $partNumber = 1)
    {
        $responseUri = Url::factory($response->getEffectiveUrl());

        $object = new self();

        $object->setPartNumber($partNumber)
            ->setContentLength($response->getHeader(Header::CONTENT_LENGTH))
            ->setETag($response->getHeader(Header::ETAG))
            ->setPath($responseUri->getPath());

        return $object;
    }
}
