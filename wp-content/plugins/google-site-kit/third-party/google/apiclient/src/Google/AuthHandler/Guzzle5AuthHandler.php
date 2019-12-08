<?php

namespace Google\Site_Kit_Dependencies;

use Google\Site_Kit_Dependencies\Google\Auth\CredentialsLoader;
use Google\Site_Kit_Dependencies\Google\Auth\HttpHandler\HttpHandlerFactory;
use Google\Site_Kit_Dependencies\Google\Auth\FetchAuthTokenCache;
use Google\Site_Kit_Dependencies\Google\Auth\Subscriber\AuthTokenSubscriber;
use Google\Site_Kit_Dependencies\Google\Auth\Subscriber\ScopedAccessTokenSubscriber;
use Google\Site_Kit_Dependencies\Google\Auth\Subscriber\SimpleSubscriber;
use Google\Site_Kit_Dependencies\GuzzleHttp\Client;
use Google\Site_Kit_Dependencies\GuzzleHttp\ClientInterface;
use Google\Site_Kit_Dependencies\Psr\Cache\CacheItemPoolInterface;
/**
*
*/
class Google_AuthHandler_Guzzle5AuthHandler
{
    protected $cache;
    protected $cacheConfig;
    public function __construct(\Google\Site_Kit_Dependencies\Psr\Cache\CacheItemPoolInterface $cache = null, array $cacheConfig = [])
    {
        $this->cache = $cache;
        $this->cacheConfig = $cacheConfig;
    }
    public function attachCredentials(\Google\Site_Kit_Dependencies\GuzzleHttp\ClientInterface $http, \Google\Site_Kit_Dependencies\Google\Auth\CredentialsLoader $credentials, callable $tokenCallback = null)
    {
        // use the provided cache
        if ($this->cache) {
            $credentials = new \Google\Site_Kit_Dependencies\Google\Auth\FetchAuthTokenCache($credentials, $this->cacheConfig, $this->cache);
        }
        // if we end up needing to make an HTTP request to retrieve credentials, we
        // can use our existing one, but we need to throw exceptions so the error
        // bubbles up.
        $authHttp = $this->createAuthHttp($http);
        $authHttpHandler = \Google\Site_Kit_Dependencies\Google\Auth\HttpHandler\HttpHandlerFactory::build($authHttp);
        $subscriber = new \Google\Site_Kit_Dependencies\Google\Auth\Subscriber\AuthTokenSubscriber($credentials, $authHttpHandler, $tokenCallback);
        $http->setDefaultOption('auth', 'google_auth');
        $http->getEmitter()->attach($subscriber);
        return $http;
    }
    public function attachToken(\Google\Site_Kit_Dependencies\GuzzleHttp\ClientInterface $http, array $token, array $scopes)
    {
        $tokenFunc = function ($scopes) use($token) {
            return $token['access_token'];
        };
        $subscriber = new \Google\Site_Kit_Dependencies\Google\Auth\Subscriber\ScopedAccessTokenSubscriber($tokenFunc, $scopes, $this->cacheConfig, $this->cache);
        $http->setDefaultOption('auth', 'scoped');
        $http->getEmitter()->attach($subscriber);
        return $http;
    }
    public function attachKey(\Google\Site_Kit_Dependencies\GuzzleHttp\ClientInterface $http, $key)
    {
        $subscriber = new \Google\Site_Kit_Dependencies\Google\Auth\Subscriber\SimpleSubscriber(['key' => $key]);
        $http->setDefaultOption('auth', 'simple');
        $http->getEmitter()->attach($subscriber);
        return $http;
    }
    private function createAuthHttp(\Google\Site_Kit_Dependencies\GuzzleHttp\ClientInterface $http)
    {
        return new \Google\Site_Kit_Dependencies\GuzzleHttp\Client(['base_url' => $http->getBaseUrl(), 'defaults' => ['exceptions' => \true, 'verify' => $http->getDefaultOption('verify'), 'proxy' => $http->getDefaultOption('proxy')]]);
    }
}
