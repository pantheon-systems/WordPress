<?php

/**
 * Copyright 2015 Google Inc. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace Google\Site_Kit_Dependencies\Google\Auth\HttpHandler;

use Google\Site_Kit_Dependencies\GuzzleHttp\Client;
use Google\Site_Kit_Dependencies\GuzzleHttp\ClientInterface;
class HttpHandlerFactory
{
    /**
     * Builds out a default http handler for the installed version of guzzle.
     *
     * @param ClientInterface $client
     * @return Guzzle5HttpHandler|Guzzle6HttpHandler|Guzzle7HttpHandler
     * @throws \Exception
     */
    public static function build(\Google\Site_Kit_Dependencies\GuzzleHttp\ClientInterface $client = null)
    {
        $client = $client ?: new \Google\Site_Kit_Dependencies\GuzzleHttp\Client();
        $version = null;
        if (\defined('Google\\Site_Kit_Dependencies\\GuzzleHttp\\ClientInterface::MAJOR_VERSION')) {
            $version = \Google\Site_Kit_Dependencies\GuzzleHttp\ClientInterface::MAJOR_VERSION;
        } elseif (\defined('Google\\Site_Kit_Dependencies\\GuzzleHttp\\ClientInterface::VERSION')) {
            $version = (int) \substr(\Google\Site_Kit_Dependencies\GuzzleHttp\ClientInterface::VERSION, 0, 1);
        }
        switch ($version) {
            case 5:
                return new \Google\Site_Kit_Dependencies\Google\Auth\HttpHandler\Guzzle5HttpHandler($client);
            case 6:
                return new \Google\Site_Kit_Dependencies\Google\Auth\HttpHandler\Guzzle6HttpHandler($client);
            case 7:
                return new \Google\Site_Kit_Dependencies\Google\Auth\HttpHandler\Guzzle7HttpHandler($client);
            default:
                throw new \Exception('Version not supported');
        }
    }
}
