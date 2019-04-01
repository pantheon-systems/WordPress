<?php

namespace DeliciousBrains\WP_Offload_Media\Aws3\Aws\S3;

use DeliciousBrains\WP_Offload_Media\Aws3\Aws\CommandInterface;
use DeliciousBrains\WP_Offload_Media\Aws3\GuzzleHttp\Psr7;
use DeliciousBrains\WP_Offload_Media\Aws3\Psr\Http\Message\RequestInterface;
/**
 * Apply required or optional MD5s to requests before sending.
 *
 * IMPORTANT: This middleware must be added after the "build" step.
 *
 * @internal
 */
class ApplyChecksumMiddleware
{
    private static $md5 = ['DeleteObjects', 'PutBucketCors', 'PutBucketLifecycle', 'PutBucketLifecycleConfiguration', 'PutBucketPolicy', 'PutBucketTagging', 'PutBucketReplication'];
    private static $sha256 = ['PutObject', 'UploadPart'];
    private $nextHandler;
    /**
     * Create a middleware wrapper function.
     *
     * @return callable
     */
    public static function wrap()
    {
        return function (callable $handler) {
            return new self($handler);
        };
    }
    public function __construct(callable $nextHandler)
    {
        $this->nextHandler = $nextHandler;
    }
    public function __invoke(\DeliciousBrains\WP_Offload_Media\Aws3\Aws\CommandInterface $command, \DeliciousBrains\WP_Offload_Media\Aws3\Psr\Http\Message\RequestInterface $request)
    {
        $next = $this->nextHandler;
        $name = $command->getName();
        $body = $request->getBody();
        if (in_array($name, self::$md5) && !$request->hasHeader('Content-MD5')) {
            // Set the content MD5 header for operations that require it.
            $request = $request->withHeader('Content-MD5', base64_encode(\DeliciousBrains\WP_Offload_Media\Aws3\GuzzleHttp\Psr7\hash($body, 'md5', true)));
        } elseif (in_array($name, self::$sha256) && $command['ContentSHA256']) {
            // Set the content hash header if provided in the parameters.
            $request = $request->withHeader('X-Amz-Content-Sha256', $command['ContentSHA256']);
        }
        return $next($command, $request);
    }
}
