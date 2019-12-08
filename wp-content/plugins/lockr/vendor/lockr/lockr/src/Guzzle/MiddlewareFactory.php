<?php
namespace Lockr\Guzzle;

use GuzzleHttp;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class MiddlewareFactory
{
    /**
     * @return callable
     */
    public static function retry()
    {
        return GuzzleHttp\Middleware::retry(
            self::retryDecider(),
            self::retryDelay()
        );
    }

    /**
     * @return callable
     */
    private static function retryDelay()
    {
        return function ($retries) {
            return (1000 * $retries) + mt_rand(0, 300);
        };
    }

    /**
     * @return callable
     */
    private static function retryDecider()
    {
        return function (
            $retries,
            RequestInterface $req,
            ResponseInterface $resp = null,
            RequestException $ex = null
        ) {
            if ($retries >= 5) {
                return false;
            }

            if ($ex instanceof ConnectException) {
                return true;
            }

            if ($resp) {
                if ($resp->getStatusCode() >= 500) {
                    return true;
                }
            }

            return false;
        };
    }
}

// ex: ts=4 sts=4 sw=4 et:
