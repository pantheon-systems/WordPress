<?php

namespace Google\Site_Kit_Dependencies;

use Google\Site_Kit_Dependencies\Google\Auth\CredentialsLoader;
use Google\Site_Kit_Dependencies\Google\Auth\HttpHandler\HttpHandlerFactory;
use Google\Site_Kit_Dependencies\Google\Auth\FetchAuthTokenCache;
use Google\Site_Kit_Dependencies\Google\Auth\Middleware\AuthTokenMiddleware;
use Google\Site_Kit_Dependencies\Google\Auth\Middleware\ScopedAccessTokenMiddleware;
use Google\Site_Kit_Dependencies\Google\Auth\Middleware\SimpleMiddleware;
use Google\Site_Kit_Dependencies\GuzzleHttp\Client;
use Google\Site_Kit_Dependencies\GuzzleHttp\ClientInterface;
use Google\Site_Kit_Dependencies\Psr\Cache\CacheItemPoolInterface;
/**
* This supports Guzzle 6
*/
class Google_AuthHandler_Guzzle6AuthHandler
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
        $middleware = new \Google\Site_Kit_Dependencies\Google\Auth\Middleware\AuthTokenMiddleware($credentials, $authHttpHandler, $tokenCallback);
        $config = $http->getConfig();
        $config['handler']->remove('google_auth');
        $config['handler']->push($middleware, 'google_auth');
        $config['auth'] = 'google_auth';
        $http = new \Google\Site_Kit_Dependencies\GuzzleHttp\Client($config);
        return $http;
    }
    public function attachToken(\Google\Site_Kit_Dependencies\GuzzleHttp\ClientInterface $http, array $token, array $scopes)
    {
        $tokenFunc = function ($scopes) use($token) {
            return $token['access_token'];
        };
        $middleware = new \Google\Site_Kit_Dependencies\Google\Auth\Middleware\ScopedAccessTokenMiddleware($tokenFunc, $scopes, $this->cacheConfig, $this->cache);
        $config = $http->getConfig();
        $config['handler']->remove('google_auth');
        $config['handler']->push($middleware, 'google_auth');
        $config['auth'] = 'scoped';
        $http = new \Google\Site_Kit_Dependencies\GuzzleHttp\Client($config);
        return $http;
    }
    public function attachKey(\Google\Site_Kit_Dependencies\GuzzleHttp\ClientInterface $http, $key)
    {
        $middleware = new \Google\Site_Kit_Dependencies\Google\Auth\Middleware\SimpleMiddleware(['key' => $key]);
        $config = $http->getConfig();
        $config['handler']->remove('google_auth');
        $config['handler']->push($middleware, 'google_auth');
        $config['auth'] = 'simple';
        $http = new \Google\Site_Kit_Dependencies\GuzzleHttp\Client($config);
        return $http;
    }
    private function createAuthHttp(\Google\Site_Kit_Dependencies\GuzzleHttp\ClientInterface $http)
    {
        return new \Google\Site_Kit_Dependencies\GuzzleHttp\Client(['base_uri' => $http->getConfig('base_uri'), 'exceptions' => \true, 'verify' => $http->getConfig('verify'), 'proxy' => $http->getConfig('proxy')]);
    }
}
