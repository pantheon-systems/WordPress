<?php

namespace DeliciousBrains\WP_Offload_Media\Aws3\Aws\S3;

use DeliciousBrains\WP_Offload_Media\Aws3\Aws\Api\Parser\PayloadParserTrait;
use DeliciousBrains\WP_Offload_Media\Aws3\Aws\CommandInterface;
use DeliciousBrains\WP_Offload_Media\Aws3\Aws\Exception\AwsException;
use DeliciousBrains\WP_Offload_Media\Aws3\Aws\HandlerList;
use DeliciousBrains\WP_Offload_Media\Aws3\Aws\ResultInterface;
use DeliciousBrains\WP_Offload_Media\Aws3\Aws\S3\Exception\S3Exception;
use DeliciousBrains\WP_Offload_Media\Aws3\GuzzleHttp\Promise\PromiseInterface;
use DeliciousBrains\WP_Offload_Media\Aws3\GuzzleHttp\Promise\RejectedPromise;
/**
 * A trait providing S3-specific functionality. This is meant to be used in
 * classes implementing \Aws\S3\S3ClientInterface
 */
trait S3ClientTrait
{
    use PayloadParserTrait;
    /**
     * @see S3ClientInterface::upload()
     */
    public function upload($bucket, $key, $body, $acl = 'private', array $options = [])
    {
        return $this->uploadAsync($bucket, $key, $body, $acl, $options)->wait();
    }
    /**
     * @see S3ClientInterface::uploadAsync()
     */
    public function uploadAsync($bucket, $key, $body, $acl = 'private', array $options = [])
    {
        return (new \DeliciousBrains\WP_Offload_Media\Aws3\Aws\S3\ObjectUploader($this, $bucket, $key, $body, $acl, $options))->promise();
    }
    /**
     * @see S3ClientInterface::copy()
     */
    public function copy($fromB, $fromK, $destB, $destK, $acl = 'private', array $opts = [])
    {
        return $this->copyAsync($fromB, $fromK, $destB, $destK, $acl, $opts)->wait();
    }
    /**
     * @see S3ClientInterface::copyAsync()
     */
    public function copyAsync($fromB, $fromK, $destB, $destK, $acl = 'private', array $opts = [])
    {
        $source = ['Bucket' => $fromB, 'Key' => $fromK];
        if (isset($opts['version_id'])) {
            $source['VersionId'] = $opts['version_id'];
        }
        $destination = ['Bucket' => $destB, 'Key' => $destK];
        return (new \DeliciousBrains\WP_Offload_Media\Aws3\Aws\S3\ObjectCopier($this, $source, $destination, $acl, $opts))->promise();
    }
    /**
     * @see S3ClientInterface::registerStreamWrapper()
     */
    public function registerStreamWrapper()
    {
        \DeliciousBrains\WP_Offload_Media\Aws3\Aws\S3\StreamWrapper::register($this);
    }
    /**
     * @see S3ClientInterface::deleteMatchingObjects()
     */
    public function deleteMatchingObjects($bucket, $prefix = '', $regex = '', array $options = [])
    {
        $this->deleteMatchingObjectsAsync($bucket, $prefix, $regex, $options)->wait();
    }
    /**
     * @see S3ClientInterface::deleteMatchingObjectsAsync()
     */
    public function deleteMatchingObjectsAsync($bucket, $prefix = '', $regex = '', array $options = [])
    {
        if (!$prefix && !$regex) {
            return new \DeliciousBrains\WP_Offload_Media\Aws3\GuzzleHttp\Promise\RejectedPromise(new \RuntimeException('A prefix or regex is required.'));
        }
        $params = ['Bucket' => $bucket, 'Prefix' => $prefix];
        $iter = $this->getIterator('ListObjects', $params);
        if ($regex) {
            $iter = \DeliciousBrains\WP_Offload_Media\Aws3\Aws\filter($iter, function ($c) use($regex) {
                return preg_match($regex, $c['Key']);
            });
        }
        return \DeliciousBrains\WP_Offload_Media\Aws3\Aws\S3\BatchDelete::fromIterator($this, $bucket, $iter, $options)->promise();
    }
    /**
     * @see S3ClientInterface::uploadDirectory()
     */
    public function uploadDirectory($directory, $bucket, $keyPrefix = null, array $options = [])
    {
        $this->uploadDirectoryAsync($directory, $bucket, $keyPrefix, $options)->wait();
    }
    /**
     * @see S3ClientInterface::uploadDirectoryAsync()
     */
    public function uploadDirectoryAsync($directory, $bucket, $keyPrefix = null, array $options = [])
    {
        $d = "s3://{$bucket}" . ($keyPrefix ? '/' . ltrim($keyPrefix, '/') : '');
        return (new \DeliciousBrains\WP_Offload_Media\Aws3\Aws\S3\Transfer($this, $directory, $d, $options))->promise();
    }
    /**
     * @see S3ClientInterface::downloadBucket()
     */
    public function downloadBucket($directory, $bucket, $keyPrefix = '', array $options = [])
    {
        $this->downloadBucketAsync($directory, $bucket, $keyPrefix, $options)->wait();
    }
    /**
     * @see S3ClientInterface::downloadBucketAsync()
     */
    public function downloadBucketAsync($directory, $bucket, $keyPrefix = '', array $options = [])
    {
        $s = "s3://{$bucket}" . ($keyPrefix ? '/' . ltrim($keyPrefix, '/') : '');
        return (new \DeliciousBrains\WP_Offload_Media\Aws3\Aws\S3\Transfer($this, $s, $directory, $options))->promise();
    }
    /**
     * @see S3ClientInterface::determineBucketRegion()
     */
    public function determineBucketRegion($bucketName)
    {
        return $this->determineBucketRegionAsync($bucketName)->wait();
    }
    /**
     * @see S3ClientInterface::determineBucketRegionAsync()
     *
     * @param string $bucketName
     *
     * @return PromiseInterface
     */
    public function determineBucketRegionAsync($bucketName)
    {
        $command = $this->getCommand('HeadBucket', ['Bucket' => $bucketName]);
        $handlerList = clone $this->getHandlerList();
        $handlerList->remove('s3.permanent_redirect');
        $handlerList->remove('signer');
        $handler = $handlerList->resolve();
        return $handler($command)->then(static function (\DeliciousBrains\WP_Offload_Media\Aws3\Aws\ResultInterface $result) {
            return $result['@metadata']['headers']['x-amz-bucket-region'];
        }, function (\DeliciousBrains\WP_Offload_Media\Aws3\Aws\Exception\AwsException $e) {
            $response = $e->getResponse();
            if ($response === null) {
                throw $e;
            }
            if ($e->getAwsErrorCode() === 'AuthorizationHeaderMalformed') {
                $region = $this->determineBucketRegionFromExceptionBody($response->getBody());
                if (!empty($region)) {
                    return $region;
                }
                throw $e;
            }
            return $response->getHeaderLine('x-amz-bucket-region');
        });
    }
    private function determineBucketRegionFromExceptionBody($responseBody)
    {
        try {
            $element = $this->parseXml($responseBody);
            if (!empty($element->Region)) {
                return (string) $element->Region;
            }
        } catch (\Exception $e) {
            // Fallthrough on exceptions from parsing
        }
        return false;
    }
    /**
     * @see S3ClientInterface::doesBucketExist()
     */
    public function doesBucketExist($bucket)
    {
        return $this->checkExistenceWithCommand($this->getCommand('HeadBucket', ['Bucket' => $bucket]));
    }
    /**
     * @see S3ClientInterface::doesObjectExist()
     */
    public function doesObjectExist($bucket, $key, array $options = [])
    {
        return $this->checkExistenceWithCommand($this->getCommand('HeadObject', ['Bucket' => $bucket, 'Key' => $key] + $options));
    }
    /**
     * Determines whether or not a resource exists using a command
     *
     * @param CommandInterface $command Command used to poll for the resource
     *
     * @return bool
     * @throws S3Exception|\Exception if there is an unhandled exception
     */
    private function checkExistenceWithCommand(\DeliciousBrains\WP_Offload_Media\Aws3\Aws\CommandInterface $command)
    {
        try {
            $this->execute($command);
            return true;
        } catch (S3Exception $e) {
            if ($e->getAwsErrorCode() == 'AccessDenied') {
                return true;
            }
            if ($e->getStatusCode() >= 500) {
                throw $e;
            }
            return false;
        }
    }
    /**
     * @see S3ClientInterface::execute()
     */
    public abstract function execute(\DeliciousBrains\WP_Offload_Media\Aws3\Aws\CommandInterface $command);
    /**
     * @see S3ClientInterface::getCommand()
     */
    public abstract function getCommand($name, array $args = []);
    /**
     * @see S3ClientInterface::getHandlerList()
     *
     * @return HandlerList
     */
    public abstract function getHandlerList();
    /**
     * @see S3ClientInterface::getIterator()
     *
     * @return \Iterator
     */
    public abstract function getIterator($name, array $args = []);
}
